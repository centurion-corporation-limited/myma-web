<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Models\Trip;
use App\Models\TripPickup;
use App\Models\TripOrders;
use App\Models\Order;
use App\Models\Restaurant;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use \Carbon\Carbon;

class TripController extends Controller
{
    public function getList(Request $request)
    {
      $auth_user = Auth::user();
      $items = Trip::query();

      if($auth_user->hasRole('restaurant-owner-package')){
          $items->where('created_by', $auth_user->id);
      }
      if ($id = $request->input('id')) {
          $items->where('id', $id);
      }

      $limit = 50;
      $items = $items->sortable(['id' => 'desc'])->paginate($limit);
      $paginate_data = $request->except('page');

      return view('admin.trip.list', compact('items', 'auth_user', 'paginate_data', 'limit'));
    }


    public function getAdd()
    {
        $auth_user = \Auth::user();

        if($auth_user->hasRole('restaurant-owner-package')){
            $locations = Restaurant::where('merchant_id', $auth_user->id)->get();
        }else{
            $mer = User::whereHas('roles', function($q){
                $q->where('slug', 'restaurant-owner-package');
            })->get()->pluck('id');
            $locations = Restaurant::whereNotIn('merchant_id', $mer)->get();
        }
        $data = [];
        $delivery_date = \Input::get('trip_date');
        $delivery_time = \Input::get('trip_time');

        $or_count = -1;
        $bor_count = -1;
        if($delivery_date != '' && $delivery_time != ''){
            $month_start = Carbon::now()->startOfMonth()->toDateString();
            $delivery_time = date('H:00:00', strtotime($delivery_time));

            $or_count = Order::where(function($q) use($delivery_date, $delivery_time){
                $q->where('type', 'single')->where('delivery_date', $delivery_date)
                ->where('delivery_time', $delivery_time);
            })->orWhere(function($q) use($delivery_date, $month_start){
                $q->where('type', 'package')->whereBetween('delivery_date', [$month_start, $delivery_date]);
            })->get()->count();
            if($or_count){
                foreach($locations as $location){
                    $orders = Order::WhereHas('items', function($q) use($location){
                        $q->whereHas('item', function($qq) use($location){
                            $qq->where('restaurant_id', $location->id);
                        });
                    });

                    $orders->where(function($qq) use($delivery_date, $delivery_time, $month_start){
                        $qq->where(function($q) use($delivery_date, $delivery_time){
                            $q->where('type', 'single')->where('delivery_date', $delivery_date)
                            ->where('delivery_time', $delivery_time);
                        })->orWhere(function($q) use($delivery_date, $month_start){
                            $q->where('type', 'package')->whereBetween('delivery_date', [$month_start, $delivery_date]);
                        });
                    });
                    $orders = $orders->get();
                    $bor_count = $orders->count();

                    $count = 0;
                    foreach($orders as $order ){
                        $trip_order = TripOrders::where('order_id', $order->id)->first();
                        if($trip_order){
                            $count++;
                            $order->checked = true;
                            $order->disabled = true;
                        }else{
                            $order->checked = false;
                            $order->disabled = false;
                        }

                    }
                    if($orders->count() == $count){
                        $data[$location->id]['total'] = true;
                    }else{
                        $data[$location->id]['total'] = false;
                    }
                    $data[$location->id]['orders'] = $orders;
                }

            }
        }
        // dd($bor_count);
        return view('admin.trip.add', compact('auth_user', 'locations', 'data', 'delivery_date', 'delivery_time', 'or_count', 'bor_count'));
    }

    public function postAdd(Request $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $data = $request->only('trip_date','price', 'trip_time');
        $data['created_by'] = $auth_user->id;
        $pickup = $request->pickup;
        $orders = $request->orders;


        $trip = Trip::create($data);

        foreach($pickup as $up){
            $data_pickup['trip_id'] = $trip->id;
            $data_pickup['pickup_id'] = $up;

            $trip_pick_id = TripPickup::create($data_pickup);
            foreach($orders[$up] as $order){
                $pick_order['trip_pick_id'] = $trip_pick_id->id;
                $pick_order['order_id'] = $order;
                TripOrders::create($pick_order);
            }
        }


        return redirect()->route('admin.trip.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Trip added successfully.',
        ]);

    }
    public function getEdit($id, Request $request)
    {
    		    $auth_user = \Auth::user();
            $id = decrypt($id);
    		    $item = Trip::findOrFail($id);
            if($auth_user->hasRole('restaurant-owner-package')){
                $locations = Restaurant::where('merchant_id', $auth_user->id)->get();
            }else{
                $locations = Restaurant::all();
            }
            $data = [];
            $item->trip_time = date('h:i A', strtotime($item->trip_time));

            $now = Carbon::now()->toDateString();
            if($item->trip_date <= $now ){
              $order_ids = [];
              foreach($item->pickups as $pickup){
                foreach($pickup->tripOrders as $trip_order){
                  $order_ids[] = $trip_order->order_id;
                }
              }
              foreach($locations as $location){
                $orders = Order::whereIn('id', $order_ids)->whereHas('items', function($q) use($location){
                  $q->whereHas('item', function($qq) use($location){
                    $qq->where('restaurant_id', $location->id);
                  });
                })->get();
                $trip = TripPickup::where('trip_id', $id)->where('pickup_id', $location->id)->first();

                foreach($orders as $order ){
                  $trip_order = null;
                  if($trip){
                    $trip_order = TripOrders::where('order_id', $order->id)->where('trip_pick_id', $trip->id)->first();
                  }
                  $order->checked = true;
                  $order->disabled = true;
                  // if($trip_order){
                  //   $count++;
                  //   $order->checked = true;
                  //   $order->disabled = true;
                  // }else{
                  //   $order->checked = false;
                  //   $dis_order = TripOrders::where('order_id', $order->id)->first();
                  //   if($dis_order){
                  //     $order->disabled = true;
                  //   }
                  //   if($dis_order){
                  //     $order->disabled = false;
                  //   }
                  // }

                }
                $data[$location->id]['total'] = true;
                $data[$location->id]['disabled'] = true;
                // if($orders->count() == $count){
                //   $data[$location->id]['total'] = true;
                // }else{
                //   $data[$location->id]['total'] = false;
                // }
                $data[$location->id]['orders'] = $orders;
              }
            }else{
              foreach($locations as $location){
                $orders = Order::where(function($q){
                  $q->where(function($qq){
                    $now = Carbon::now()->toDateString();
                    $qq->where('type', 'single')->where('status_id', 6)->whereDate('delivery_date', '>=' , $now);
                  })->orWhere(function($qq){
                    $start = Carbon::now()->startOfMonth();
                    $end = Carbon::now()->endOfMonth();
                    $qq->where('type', 'package')->whereBetween('delivery_date', [$start,$end]);
                  });
                })->WhereHas('items', function($q) use($location){
                  $q->whereHas('item', function($qq) use($location){
                    $qq->where('restaurant_id', $location->id);
                  });
                })->get();

                // $or_arr = [];
                // foreach($orders as $order){
                //   if($order->type == 'single'){
                //     $or_arr[] = $order;
                //   }else{
                //
                //   }
                // }
                $trip = TripPickup::where('trip_id', $id)->where('pickup_id', $location->id)->first();

                $count = 0;
                foreach($orders as $order ){
                  $trip_order = null;
                  if($trip){
                    $trip_order = TripOrders::where('order_id', $order->id)->where('trip_pick_id', $trip->id)->first();
                  }
                  if($trip_order){
                    $count++;
                    $order->checked = true;
                    $order->disabled = false;
                  }else{
                    $order->checked = false;
                    $dis_order = TripOrders::where('order_id', $order->id)->first();
                    if($dis_order){
                      $order->disabled = true;
                    }
                    if($dis_order){
                      $order->disabled = false;
                    }
                  }

                  $order->disabled = true;

                }
                if($orders->count() == $count){
                  $data[$location->id]['total'] = true;
                }else{
                  $data[$location->id]['total'] = false;
                }
                $data[$location->id]['disabled'] = true;
                $data[$location->id]['orders'] = $orders;
              }

            }
            // dd($data);
        return view('admin.trip.edit', compact('item', 'locations', 'data'));
    }

    public function postEdit($id, Request $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $id = decrypt($id);

        $trip = Trip::findOrFail($id);
        $data = $request->only('price');
        $pickup = $request->pickup;
        $orders = $request->orders;

        $trip->update($data);

        // foreach($pickup as $up){
        //     $data_pickup['trip_id'] = $trip->id;
        //     $data_pickup['pickup_id'] = $up;
        //
        //     $trip_pick_id = TripPickup::create($data_pickup);
        //     foreach($orders[$up] as $order){
        //         $pick_order['trip_pick_id'] = $trip_pick_id->id;
        //         $pick_order['order_id'] = $order;
        //         TripOrders::create($pick_order);
        //     }
        // }

        return redirect()->route('admin.trip.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Trip details updated successfully.',
        ]);

    }

    public function getDelete($id)
    {
        $id = decrypt($id);
        Trip::destroy($id);
        return redirect()->route('admin.trip.list')->with([
            'flash_level'   => 'danger',
            'flash_message' => 'Trip Deleted',
        ]);
    }

    public function postDelete($id)
    {
        $id = decrypt($id);
        Trip::delete($id);
        return redirect()->route('admin.trip.list')->with([
          'flash_level'   => 'danger',
          'flash_message' => 'Trip Deleted',
        ]);
    }
}
