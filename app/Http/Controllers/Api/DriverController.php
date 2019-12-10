<?php

namespace App\Http\Controllers\Api;

define('BREAKFAST_TIME', '07:00:00');
define('LUNCH_TIME', '12:00:00');
define('DINNER_TIME', '19:00:00');

use Illuminate\Http\Request;
use App\Helper\Enets;
use JWTAuth, DB, Auth, Input;
use App\User;
use App\Models\Restaurant;
use App\Models\Status;
use App\Models\FoodMenu;
use App\Models\FoodCategory;
use App\Models\FoodCourse;
use Carbon\Carbon;
use App\Models\Trip;
use App\Models\TripReject;
use App\Models\TripOrders;
use App\Models\TripPickup;
use App\Models\Batch;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderReject;
use App\Models\Subscription;
use App\Models\DeliverySign;

class DriverController extends Controller
{

    public function getDashboard(Request $request)
    {
        try{

          // $user = User::find(47);
          // $message = 'Order status has been updated to packed';
          // $type = 'notifications';
          // $id = '';
          // $link = '';
          // $ret = sendSingleLocal($user, $message, $type, $id, $link, 'merchant');

            $user = JWTAuth::toUser($request->input('token'));

            $now = Carbon::now();
            // $now->addDay();
            $time = $now->toTimeString();
            $label = '';
            $breakfast = $lunch = $dinner = false;
            if($time > "19:00:00"){
              //$now->addDay();
                //return response()->json(['status' => 'success', 'data' => [], 'trip' => [], 'message'=> 'Success'], 200);
            }
            if($time > "00:00:00" && $time <= "09:00:00"){
                $time = "07:00:00";
                $label = 'batch_b';
                $breakfast = true;
            }
            elseif($time > "09:00:00" && $time <= "14:00:00"){
                $time = "12:00:00";
                $label = 'batch_l';
                $lunch = true;
            }
            elseif($time > "14:00:00" && $time <= "24:00:00"){
                $time = "19:00:00";
                $label = 'batch_d';
                $dinner = true;
            }
            $date = $now->toDateString();

            $trip = Trip::where('assigned_to', $user->id)->whereDate('trip_date', $date)->where('trip_time', $time)->orderBy('created_at', 'desc')->first();
            $trips = Trip::where('assigned_to', $user->id)->whereDate('trip_date', $date)->orderBy('created_at', 'desc')->get();
            // return response()->json(['status' => 'success', 'data' => $trip, 'trip' => $user, 'message'=> 'Success'], 200);

            $data = $tripOrders = [];
            foreach($trips as $trip){
                $pickups = $trip->pickups->pluck('id');

                $tripOrders = TripOrders::with('order')->whereIn('trip_pick_id', $pickups)->get();
                
                foreach($tripOrders as $key => $order){
                    $item_tt = 0;
                    $status_id = $order->order->status_id;
                    if($order->order->type == 'package'){
                      $status_id = 8;

                      foreach($order->order->items as $item){

                        $sub = Subscription::where('order_id', $order->order_id)->where('item_id', $item->item_id)->where('delivery_date', $now->toDateString())->first();

                        if(!$sub)
                        continue;
                        if($trip->trip_time == '07:00:00' && $sub->b_allowed){
                          $status_id = $sub->b_status != ''?$sub->b_status:8;

                        }elseif($trip->trip_time == '12:00:00' && $sub->l_allowed){
                          $status_id = $sub->l_status != ''?$sub->l_status:8;
                        }elseif($trip->trip_time == '19:00:00' && $sub->d_allowed){
                          $status_id = $sub->d_status != ''?$sub->d_status:8;
                        }else{
                          continue;
                        }
                        if($status_id < 11){
                          $item_tt++;
                        }
                      }
                      if($item_tt == 0){
                        //continue;
                      }
                    }
                    
                    
                    if($status_id < 11 || true){
                        $status_m = Status::find($status_id);

                        $batch = Batch::where(function($q) use($order){
                          if($order->order->address != ''){
                            $q->where('address', $order->order->address);
                          }else{
                            $q->where('dormitory_id', $order->order->dormitory_id);
                          }
                        })->whereDate('batch_date', $now->format('Y-m-d'))->orderBy('created_at', 'desc')->first();
                        if(!$batch){
                          $batch = Batch::orderBy('created_at', 'desc')->orderBy('id', 'desc')->first();
                          if($order->order->address != ''){
                            $dd['address'] =  $order->order->address;
                          }else{
                            $dd['dormitory_id'] =  $order->order->dormitory_id;
                          }

                          $num = str_pad($batch->id+1, 7, '0', STR_PAD_LEFT);
                          $dd['batch_b'] = 'BB'.$num;
                          $dd['batch_l'] = 'BL'.$num;
                          $dd['batch_d'] = 'BD'.$num;
                          $dd['batch_date'] = $now->toDateString();
                          $batch = Batch::create($dd);
                        }
                        // \Log::debug("order -".$order->order_id);
                        // \Log::debug($batch);
                        if($order->order->delivery_type == 'reception' || $order->order->delivery_type == 'inperson'){
                            $address = $order->order->dormitory->name;
                        }else{
                            $address = $order->order->address;
                        }

                        if($batch){
                          if($trip->trip_time == '07:00:00'){
                            $data[$batch->id]['type'] = 'breakfast';
                            $data[$batch->id]['batch_id'] = $batch->batch_b;
                          }elseif($trip->trip_time == '12:00:00'){
                            $data[$batch->id]['type'] = 'lunch';
                            $data[$batch->id]['batch_id'] = $batch->batch_l;
                          }else{
                            $data[$batch->id]['type'] = 'dinner';
                            $data[$batch->id]['batch_id'] = $batch->batch_d;
                          }
                          $data[$batch->id]['pickups'] = (isset($data[$batch->id]['pickups']) && $data[$batch->id]['pickups'] > 0)? ++$data[$batch->id]['pickups']:1;
                          $data[$batch->id]['address'] = $address;
                          $data[$batch->id]['trip_id'] = $trip->id;
                          
                          $data[$batch->id]['status_id'] = $status_id;
                          $data[$batch->id]['status_name'] = @$status_m->name;
                        }
                    }

                }

                $data = array_values($data);

            }

            return response()->json(['status' => 'success', 'data' => $data, 'trip' => $tripOrders, 'message'=> 'Success'], 200);

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }

    }

    public function getOrder(Request $request)
    {
        try{
            $id = $request->input('trip_id');
            $batch_id = $request->input('batch_id');
            $s_order_ids = $order_ids = [];
            $trip = Trip::find($id);
            $type = $trip->driver->type;

            if($batch_id != ''){
              if($type == 'package'){
                $order_ids = Subscription::where(function($q) use($batch_id){
                    $q->where('breakfast', $batch_id)
                    ->orWhere('lunch', $batch_id)
                    ->orWhere('dinner', $batch_id);
                })->pluck('order_id')->toArray();

              }else{
                $order_ids = Order::where('batch_id', $batch_id)->pluck('id')->toArray();
              }
            }
            $triporder_ids = [];
            if(count($order_ids)){
              $trip_pick_ids = $trip->tripPickup()->pluck('id');
              $triporder_ids = TripOrders::whereIn('trip_pick_id', $trip_pick_ids)->whereIn('order_id', $order_ids)->pluck('trip_pick_id');
              $pickups = TripPickup::where('trip_id', $id)->whereIn('id', $triporder_ids)->get();

            }else{
                $pickups = TripPickup::where('trip_id', $id)->get();
            }
            // \Log::debug($order_ids);
            foreach($pickups as $pick){
                if(count($order_ids)){
                  $ups = TripOrders::where('trip_pick_id',$pick->id)->whereIn('order_id', $order_ids)->get();
                }else{
                  $ups = TripOrders::where('trip_pick_id',$pick->id)->get();
                }

                $total = 0;
                $total_picked = 0;
                $arr = [];
                foreach($ups as $order){
                    // $total += $order->order->items->count();
                    if($order->order){
                      foreach($order->order->items as $item){
                        if(!in_array($item->item_id, $arr)){
                          $arr[] = $item->item_id;

                          if($order->order->type == 'single'){
                            $total++;
                            if($item->agent_status){
                              $total_picked++;
                            }
                          }else{
                            $sub = Subscription::where('delivery_date', $trip->trip_date)->where('order_id', $order->order_id)->where('item_id', $item->item_id)->first();
                            if($sub){
                              if($trip->trip_time == '07:00:00'){
                                if($sub->b_status >= 10 && $sub->b_allowed){
                                  $total_picked++;
                                }
                              }elseif($trip->trip_time == '12:00:00' && $sub->l_allowed){
                                if($sub->l_status >= 10){
                                  $total_picked++;
                                }
                              }elseif($trip->trip_time == '19:00:00' && $sub->d_allowed){
                                if($sub->d_status >= 10){
                                  $total_picked++;
                                }
                              }else{
                                continue;
                              }
                              $total++;
                            }
                          }
                        }
                      }
                    }
                }
                $pick->total = $total;
                $pick->total_picked = $total_picked;
                $pick->address = $pick->pickup->address;
            }
            return response()->json(['status' => 'success', 'data' => $pickups, 'message'=> 'Success'], 200);

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }

    }

    public function getOrderById(Request $request)
    {
        try{
            $id = $request->input('order_id');
            $order = Order::findOrFail($id);
            $items = $order->items;
            foreach($items as $item){
              $item->item_name = $item->item->name;
            }
            return response()->json(['status' => 'success', 'data' => $items, 'message'=> 'Success'], 200);

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }

    }

    public function getOrderDetail(Request $request)
    {
        try{
            $user = JWTAuth::toUser($request->input('token'));

            $now = Carbon::now();
            $time = $now->toTimeString();

            $breakfast = $lunch = $dinner = false;
            if($time > "00:00:00" && $time <= "09:00:00"){
                $breakfast = true;
            }
            elseif($time > "09:00:00" && $time <= "14:00:00"){
                $lunch = true;
            }
            elseif($time > "14:00:00" && $time <= "24:00:00"){
                $dinner = true;
            }

            $id = $request->input('id');
            $trip = Trip::find($id);
            $batch_id = $request->input('batch_id');
            $s_order_ids = $order_ids = [];
            if($batch_id != ''){
                $order_ids = Order::where('batch_id', $batch_id)->pluck('id')->toArray();
                if(count($order_ids) == 0){
            //   if($user->type == 'package'){
                $order_ids = Subscription::where(function($q) use($batch_id){
                    $q->where('breakfast', $batch_id)
                    ->orWhere('lunch', $batch_id)
                    ->orWhere('dinner', $batch_id);
                })->pluck('order_id')->toArray();
                }
            //   }else{
                
            //   }
            }

            if(count($order_ids)){
              $pickups = TripOrders::where('trip_pick_id',$id)->whereIn('order_id', $order_ids)->get();
            }else{
              $pickups = TripOrders::where('trip_pick_id',$id)->get();
            }
            $dd = [];
            $total_packed = 0;
            $data = [];
            foreach($pickups as $order){
                foreach($order->order->items as $item){
                    $item_id = $item->item_id;

                    if($order->order->type == 'package'){
                      $subs = Subscription::where('delivery_date', $now->toDateString())->where('order_id', $order->order_id)->where('item_id', $item->item_id)->first();
                      \Log::debug($subs);
                      if($subs){
                        if($trip->trip_time == '07:00:00' && $subs->b_allowed){
                          $status = @$subs->bstatus?@$subs->bstatus->name:'In Progress';
                        }elseif($trip->trip_time == '12:00:00' && $subs->l_allowed) {
                          $status = @$subs->lstatus?@$subs->lstatus->name:'In Progress';
                        }elseif($trip->trip_time == '19:00:00' && $subs->d_allowed) {
                          $status = @$subs->dstatus?@$subs->dstatus->name:'In Progress';
                        }else{
                          continue;
                        }
                      }else{
                        continue;
                      }

                    }else{
                      $status = ($item->agent_status != null)?$item->agent_status->name:(($item->restaurant_status != null)?$item->restaurant_status->name:'');
                    }
                    
                    $data[$item_id]['qty'] = isset($data[$item_id]['qty'])?($data[$item_id]['qty']+$item->quantity):$item->quantity;
                    $data[$item_id]['name'] = $item->item->name;
                    if(@$data[$item_id]['ids'] != ''){
                        $data[$item_id]['ids'] .= ','.$item->id;
                    }else{
                        $data[$item_id]['ids'] = $item->id;
                        if($status == "Picked" || $status == 'Delivered'){
                          $total_packed++;
                        }
                    }
                    
                    $data[$item_id]['status'] = $status;
                    $data[$item_id]['customer_name'] = '';
                    $data[$item_id]['address'] = '';
                }
            }
            // \Log::debug($data);
            $delivery_types = [];
            $enable_deliver = false;
            if($total_packed == count($data)){
                $enable_deliver = true;
                if($user->type == 'free'){
                  //$data = [];
                  $d = [];
                  foreach($pickups as $order){
                      $delivery_type = @$order->order->delivery_type;
                      $address = @$order->order->address;
                      if($address == ''){
                        $address = @$order->order->dormitory->name;
                      }
                      $name = @$order->order->user->name;
                      foreach($order->order->items as $item){
                          $item_id = $item->item_id;

                        //   $status = ($item->agent_status != null)?$item->agent_status->name:(($item->restaurant_status != null)?$item->restaurant_status->name:'');
                            if($order->order->type == 'package'){
                              $subs = Subscription::where('delivery_date', $now->toDateString())->where('order_id', $order->order_id)->where('item_id', $item->item_id)->first();
                              \Log::debug($subs);
                              if($subs){
                                if($trip->trip_time == '07:00:00' && $subs->b_allowed){
                                  $status = @$subs->bstatus?@$subs->bstatus->name:'In Progress';
                                }elseif($trip->trip_time == '12:00:00' && $subs->l_allowed) {
                                  $status = @$subs->lstatus?@$subs->lstatus->name:'In Progress';
                                }elseif($trip->trip_time == '19:00:00' && $subs->d_allowed) {
                                  $status = @$subs->dstatus?@$subs->dstatus->name:'In Progress';
                                }else{
                                  continue;
                                }
                              }else{
                                continue;
                              }
        
                            }else{
                              $status = ($item->agent_status != null)?$item->agent_status->name:(($item->restaurant_status != null)?$item->restaurant_status->name:'');
                            }
                            
                          if(isset($d[$delivery_type][$item_id])){
                            $d[$delivery_type][$item_id]['qty'] = isset($d[$delivery_type][$item_id]['qty'])?($d[$delivery_type][$item_id]['qty']+$item->quantity):$item->quantity;
                            $d[$delivery_type][$item_id]['name'] = $item->item->name;
                            if(@$d[$delivery_type][$item_id]['ids'] != ''){
                                $d[$delivery_type][$item_id]['ids'] .= ','.$item->id;
                            }else{
                                $d[$delivery_type][$item_id]['ids'] = $item->id;
                            }
                            $d[$delivery_type][$item_id]['status'] = $status;
                          }else{
                            $d[$delivery_type][$item_id]['delivery_type'] = $delivery_type;
                            $d[$delivery_type][$item_id]['qty'] = isset($d[$delivery_type][$item_id]['qty'])?($d[$delivery_type][$item_id]['qty']+$item->quantity):$item->quantity;
                            $d[$delivery_type][$item_id]['name'] = $item->item->name;
                            if(@$d[$delivery_type][$item_id]['ids'] != ''){
                                $d[$delivery_type][$item_id]['ids'] .= ','.$item->id;
                            }else{
                                $d[$delivery_type][$item_id]['ids'] = $item->id;
                            }
                            $d[$delivery_type][$item_id]['status'] = $status;
                            $d[$delivery_type][$item_id]['address'] = $address;
                            $d[$delivery_type][$item_id]['customer_name'] = $name;

                          }
                      }
                  }
                  $data = [];
                  foreach($d as $del){
                    foreach($del as $it){
                      $data[] = $it;
                    }
                  }
                }
            }
            $data = array_values($data);
            // \Log::debug($data);
            // $d = $data;
            // $dd['pickups'] = $pickups;
            // $dd['data'] = $data;
            // $dd['enable_deliver'] = $enable_deliver;

            return response()->json(['status' => 'success', 'data' => $data, 'enable_deliver' => $enable_deliver, 'pickups' => $pickups, 'message'=> 'Success'], 200);

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }

    }

    public function getTripDetail(Request $request)
    {
        try{
            $id = $request->input('id');
            $trips = Trip::where('id',$id)->get();
            $add_pick = [];
            $add_drop = [];
            $add_drop_d = [];

            foreach($trips as $trip){
                foreach($trip->pickups as $tripPickUp){
                    $add_pick[] = $tripPickUp->pickup->address;

                    foreach($tripPickUp->tripOrders as $order){
                        $add_drop[] = ($order->order->address != '')?$order->order->address:$order->order->dormitory->name;
                    }
                }
            }

            $add_drops = array_unique($add_drop);
            $drops = [];
            foreach($add_drops as $dd){
              $drops[] = $dd;
            }
            return response()->json(['status' => 'success', 'pick' => $add_pick, 'drop' => $drops, 'message'=> 'Success'], 200);

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }

    }

    public function getTripOrderDetail($id, Request $request)
    {
        try{
            $pickups = TripOrders::where('trip_pick_id',$id)->get();
            $data = [];
            foreach($pickups as $order){
                foreach($order->order->items as $item){
                    $item_id = $item->item_id;
                    $data[$item_id]['qty'] = isset($data[$item_id]['qty'])?($data[$item_id]['qty']+$item->quantity):$item->quantity;
                    $data[$item_id]['name'] = $item->item->name;
                    if(@$data[$item_id]['ids'] != ''){
                        $data[$item_id]['ids'] .= ','.$item->id;
                    }else{
                        $data[$item_id]['ids'] = $item->id;
                    }
                    $data[$item_id]['status'] = ($item->agent_status != null)?$item->agent_status->name:(($item->restaurant_status != null)?$item->restaurant_status->name:'');
                }
            }
            $dd['pickups'] = $pickups;
            $dd['data'] = $data;
            return response()->json(['status' => 'success', 'data' => $dd, 'message'=> 'Success'], 200);

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }

    }

    public function updateStatus(Request $request)
    {
        try{
            $now = Carbon::now();
            $time = $now->toTimeString();
            $trip_id = $request->input('trip_id');
            $trip = "";
            if($trip_id){
                $trip = Trip::find($trip_id);
            }
            \Log::debug("request data");
            \Log::debug($request->all());
            \Log::debug($trip);
            $breakfast = $lunch = $dinner = false;
            if($time > "00:00:00" && $time <= "09:00:00"){
                $breakfast = true;
            }
            elseif($time > "09:00:00" && $time <= "14:00:00"){
                $lunch = true;
            }
            elseif($time > "14:00:00" && $time <= "24:00:00"){
                $dinner = true;
            }

            $data = $request->only('item_ids', 'status_id', 'name');

            if($request->input('sign')) {
              $file = $request->input('sign');
              if (preg_match('/^data:image\/(\w+);base64,/', $file, $type)) {
                  $file = substr($file, strpos($file, ',') + 1);
                  $type = strtolower($type[1]); // jpg, png, gif

                  if (!in_array($type, [ 'jpg', 'jpeg', 'gif', 'png' ])) {
                      return response()->json(['status' => 'error', 'data' => [], 'message'=> 'Invalid image type supplied'], 200);
                      // throw new \Exception('invalid image type');
                  }

                  $decode = base64_decode($file);

                  if ($decode === false) {
                      return response()->json(['status' => 'error', 'data' => [], 'message'=> 'Decode failed'], 200);
                      // throw new \Exception('base64_decode failed');
                  }
                  $folder = "files/sign";

                  $path = savePhoto($file, $folder, $type);
                  $sign = $path;
              } else {
                  return response()->json(['status' => 'error', 'data' => [], 'message'=> 'Did not match data URI with image data'], 200);
              }
            }

            $item_ids = explode(',', $data['item_ids']);
            foreach($item_ids as $id){
                $item = OrderItem::find($id);
                $order = Order::find($item->order_id);

                if($order->type == 'package'){
                  $subs = Subscription::where('item_id', $item->item_id)->where('order_id', $item->order_id)->where('delivery_date', $now->toDateString())->first();
                  \Log::debug("order data");
                  \Log::debug($subs);
            
                  if($trip && $trip->trip_time == '07:00:00'){
                    $batch_id = $subs->breakfast;
                    $subs->update([
                      'b_status'  => $data['status_id']
                    ]);
                  }elseif($trip && $trip->trip_time == '12:00:00'){
                    $batch_id = $subs->lunch;
                    $subs->update([
                      'l_status'  => $data['status_id']
                    ]);
                  }elseif($trip && $trip->trip_time == '19:00:00'){
                    $batch_id = $subs->dinner;
                    $subs->update([
                      'd_status'  => $data['status_id']
                    ]);
                  }

                  $dd['subscription_id'] = $subs->id;
                  $dd['batch_id'] = $batch_id;
                  $dd['item_id'] = $id;
                  $dd['name'] = $data['name'];
                  $dd['sign'] = $sign;
                  if($data['status_id'] == 9)
                    $dd['type'] = 'merchant';
                  else
                    $dd['type'] = 'customer';
                  DeliverySign::create($dd);
                }else{
                  $dd['agent_status_id'] = $data['status_id'];
                  if($data['status_id'] == 10){
                    $dd['name'] = $data['name'];
                    $dd['sign'] = $sign;
                    unset($dd['deliver_name']);
                  }else{
                    unset($dd['name']);
                    $dd['deliver_name'] = $data['name'];
                    $dd['deliver_sign'] = $sign;
                  }
                  $item->update($dd);

                  $order_item_count = OrderItem::where('order_id', $item->order_id)->where('agent_status_id', 11)->get()->count();
                  $count_order_item = $order->items->count();
                  if($order_item_count == $count_order_item){
                    $order->update(['status_id' => $data['status_id']]);
                  }
                }
                $status = Status::find($data['status_id']);
                $user = $order->user;
                $message = 'Order status has been updated to '.$status->name;
                $type = 'notifications';
                $id = '';
                $link = '';
                if($user && $user->fcm_token){
                    sendSingleLocal($user, $message, $type, $id, $link, 'user');
                }
                $user = @$item->item->restaurant->merchant;
                if($user && $user->fcm_token){
                    sendSingleLocal($user, $message, $type, $id, $link, 'merchant');
                }
            }

            return response()->json(['status' => 'success', 'data' => [], 'message'=> 'Status updated successfully', 'status_id' => $data['status_id']], 200);

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }

    }

    public function viewProfile(Request $request)
    {
        try{
            $user = JWTAuth::toUser($request->input('token'));
            $auth_user = (object)[];
            $auth_user->email = $user->email;
            $auth_user->name = $user->name;
            $auth_user->type = $user->type;
            $prof_image = "merchant/images/img-profile.jpg";
            if($user->profile){
                $auth_user->phone_no = $user->profile->phone;
                $auth_user->fin_no = $user->profile->fin_no;
                $auth_user->vehicle_no = $user->profile->vehicle_no;
                $auth_user->address = $user->profile->street_address;
                $auth_user->gender = $user->profile->gender;
                $auth_user->signup_date = $user->created_at->format('d/m/Y');
                if($user->profile->profile_pic)
                    $auth_user->prof_image = url($user->profile->profile_pic);
                else
                    $auth_user->prof_image = url($prof_image);
            }else{
                $auth_user->phone_no = '';
                $auth_user->fin_no = '';
                $auth_user->address = '';
                $auth_user->gender = '';
                $auth_user->signup_date = $auth_user->created_at->format('d/m/Y');
                $auth_user->prof_image = url($prof_image);
            }
            return response()->json(['status' => 'success', 'data' => $auth_user, 'message'=> 'Success'], 200);

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function postProfile(Request $request)
    {
        try{
            $auth_user = JWTAuth::toUser($request->input('token'));
            $data_profile = $request->only('vehicle_no', 'street_address', 'gender', 'phone');

            if($request->input('image')) {
              $file = $request->input('image');
              if (preg_match('/^data:image\/(\w+);base64,/', $file, $type)) {
                  $file = substr($file, strpos($file, ',') + 1);
                  $type = strtolower($type[1]); // jpg, png, gif

                  if (!in_array($type, [ 'jpg', 'jpeg', 'gif', 'png' ])) {
                      return response()->json(['status' => 'error', 'data' => [], 'message'=> 'Invalid image type supplied'], 200);
                      // throw new \Exception('invalid image type');
                  }

                  $decode = base64_decode($file);

                  if ($decode === false) {
                      return response()->json(['status' => 'error', 'data' => [], 'message'=> 'Decode failed'], 200);
                      // throw new \Exception('base64_decode failed');
                  }
                  $folder = "files/profile";

                  $path = savePhoto($file, $folder, $type);
                  $data_profile['profile_pic'] = $path;
              } else {
                  return response()->json(['status' => 'error', 'data' => [], 'message'=> 'Did not match data URI with image data'], 200);
              }
            }

            $auth_user->profile->update($data_profile);
            return response()->json(['status' => 'success', 'data' => [], 'message'=> 'Profile updated successfully'], 200);

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function getEarning(Request $request)
    {
        try{

            $from = $request->input('from');
            $to = $request->input('to');

            $auth_user = JWTAuth::toUser($request->input('token'));
            $user_id = $auth_user->id;
            $now = Carbon::now()->toDateString();
            $trips = Trip::where('assigned_to', $user_id)->orderBy('trip_date', 'desc')->orderBy('trip_time', 'desc')->orderBy('id', 'desc');
            if($from != '' && $to != ''){
              $from = carbon::parse($from);
              $to = carbon::parse($to);
              $trips->where('trip_date', '>=', $from)->where('trip_date', '<=', $to);
            }
            $trips->where('trip_date', '<=', $now);
            $trips = $trips->get();
            foreach($trips as $trip){
                if($trip->status_id == 0){
                    $trip->trip_status = 'Pending';
                }else{
                    $trip->trip_status = 'Paid';
                }
                $trip->trip_date = Carbon::parse($trip->trip_date)->format('d/m/Y');
            }
            return response()->json(['status' => 'success', 'data' => $trips, 'message'=> 'Success'], 200);

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function getEarningDetail(Request $request)
    {
        try{
            $auth_user = JWTAuth::toUser($request->input('token'));
            $user_id = $auth_user->id;
            $id = $request->input('trip_id');
            $trip = Trip::find($id);
            if($trip->trip_time > "00:00:00" && $trip->trip_time <= "07:00:00"){
                $label = 'batch_b';
                $breakfast = true;
            }
            elseif($trip->trip_time > "07:00:00" && $trip->trip_time <= "12:00:00"){
                $label = 'batch_l';
                $lunch = true;
            }
            elseif($trip->trip_time > "12:00:00" && $trip->trip_time <= "24:00:00"){
                $label = 'batch_d';
                $dinner = true;
            }
            $data = [];
            if($trip){
                $pickups = $trip->pickups->pluck('id');
                $tripOrders = TripOrders::whereIn('trip_pick_id', $pickups)->get();
                $arr = [];
                foreach($tripOrders as $order){

                    $total = 0;
                    $batch = Batch::where(function($q) use($order){
                        $q->where('address', $order->order->address)->orWhere('dormitory_id', $order->order->dormitory_id);
                    })->whereDate('batch_date', date('Y-m-d'))->orderBy('created_at', 'desc')->first();
                    $batch_id = $batch->{$label};
                    foreach($order->order->items as $item){
                        // echo $item->item_id.'<br>';
                        if(!in_array($item->item_id, $arr)){
                            // echo "in<br>";
                            $arr[] = $item->item_id;
                            $total++;
                        }
                    }
                    $data[$batch_id]['batch_id'] = $batch_id;
                    $data[$batch_id]['total_items'] = @$data[$batch_id]['total_items'] + $total;
                    $data[$batch_id]['address'] = @$order->picktrip->pickup->address;
                    // $batch = Batch::where('batch_b', $batch_id)->orWhere('batch_l', $batch_id)->orWhere('batch_d', $batch_id)->first();
                    // $address = '';
                    // if($batch){
                    //     if($batch->dormitory_id){
                    //         $address = $batch->dormitory->name;
                    //     }else{
                    //         $address = $batch->address;
                    //     }
                    // }
                    // $data[$batch_id]['pickups'] = (isset($data[$batch_id]['pickups']) && $data[$batch_id]['pickups'] > 1)? $data[$batch_id]['pickups']++:1;
                    // $data[$batch_id]['address'] = $address;

                }
                $data = array_values($data);
            }
            $dd['trip'] = $trip;
            $dd['data'] = $data;
            return response()->json(['status' => 'success', 'data' => $dd, 'message'=> 'Item added to menu.'], 200);

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }

    }

    public function getTrip(Request $request)
    {
        try{
            $auth_user = JWTAuth::toUser($request->input('token'));
            $rejected = TripReject::where('user_id', $auth_user->id)->pluck('trip_id');

            $trips = Trip::whereNotIn('id', $rejected)->whereNull('assigned_to')->where('trip_date', '>=', Carbon::now()->toDateString())->get();
            foreach($trips as $trip){
                $count = 0;
                foreach($trip->tripPickup as $pickup){
                    foreach($pickup->tripOrders as $order){
                        foreach($order->order->items as $item){
                            $count += $item->quantity;
                        }
                    }
                }
                $trip->
                $trip->total_orders = $count;
            }
            return response()->json(['status' => 'success', 'data' => $trips, 'message'=> 'SUCCESS'], 200);

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }

    }

    public function getUpcomingTrip(Request $request)
    {
        try{
            $auth_user = JWTAuth::toUser($request->input('token'));
            $trip_date = Carbon::now()->addDay()->toDateString();
            $trips = Trip::where('assigned_to', $auth_user->id)->where('trip_date', '=', $trip_date)
            ->orderBy('trip_date','asc')
            ->orderBy('trip_time','asc')
            ->get();
            foreach($trips as $trip){
                $count = 0;
                foreach($trip->tripPickup as $pickup){
                    foreach($pickup->tripOrders as $order){
                        foreach($order->order->items as $item){
                            $count += $item->quantity;
                        }
                    }
                }
                $trip->total_orders = $count;
                $trip->trip_date = Carbon::parse($trip->trip_date)->format('d/m/Y');
            }
            return response()->json(['status' => 'success', 'data' => $trips, 'message'=> 'SUCCESS'], 200);

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function acceptTrip(Request $request)
    {
        try{
            $trip_id = $request->input('trip_id');
            $auth_user = JWTAuth::toUser($request->input('token'));
            $user_id = $auth_user->id;

            $trip = Trip::find($trip_id);
            if($trip){
                if($trip->assigned_to){
                    $data['status'] = 'error';
                    $data['msg'] = "Trip assigned to someone already.";
                }else{

                    $trip->update(['assigned_to' => $user_id, 'accepted_at' => Carbon::now()]);
                    $data['status'] = 'success';
                    $data['msg'] = "Trip assigned to you.";
                }
            }
            else{
                $data['status'] = 'error';
                $data['msg'] = "Invalid ID";
            }
            return response()->json(['status' => $data['status'], 'data' => [], 'message'=> $data['msg']], 200);

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function rejectTrip(Request $request)
    {
        try{
            $trip_id = $request->input('trip_id');
            $auth_user = JWTAuth::toUser($request->input('token'));
            $user_id = $auth_user->id;

            $trip = Trip::find($trip_id);
            if($trip){
                $rejected = TripReject::create(['trip_id' => $trip_id, 'user_id' => $user_id]);
                $data['status'] = 'success';
                $data['msg'] = "Trip rejected";
            }
            else{
                $data['status'] = 'error';
                $data['msg'] = "Invalid ID";
            }
            return response()->json(['status' => $data['status'], 'data' => $data, 'message'=> $data['msg']], 200);

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function getNewOrder(Request $request)
    {
        try{
            $auth_user = JWTAuth::toUser($request->input('token'));
            $rejected = OrderReject::where('user_id', $auth_user->id)->pluck('order_id');

            $orders = Order::whereNotIn('id', $rejected)->where('accepted', '1')->whereNull('driver_id')
            ->where('delivery_date', '>=', Carbon::now()->toDateString());
            if($auth_user->type == 'free'){
              $orders->where('type', 'single');
            }else{
              $orders->where('type', 'package');
            }
            $orders = $orders->withCount('items')->orderBy('created_at', 'desc')->get();

            $data = [];
            $time = Carbon::now()->totimeString();
            foreach($orders as $key => $order){
                $dd['id'] = $order->id;
                $dd['items_count'] = $order->items_count;
                $dd['delivery_date'] = Carbon::parse($order->delivery_date)->format('d/m/Y');
                if($order->type == 'package'){
                    if($time > "19:00:00" || $time <= "07:00:00"){
                        $dd['delivery_time'] = Carbon::parse("07:00:00")->format('h:i A');
                    }
                    elseif($time > "07:00:00" && $time <= "12:00:00"){
                        $dd['delivery_time'] = Carbon::parse("12:00:00")->format('h:i A');
                    }
                    elseif($time > "12:00:00" && $time <= "19:00:00"){
                        $dd['delivery_time'] = Carbon::parse("19:00:00")->format('h:i A');
                    }
                }else{
                    $dd['delivery_time'] = Carbon::parse($order->delivery_time)->format('h:i A');
                }
                $dd['delivery_type'] = $order->delivery_type;

                $dd['pickup'] = $order->restraAdd();
                if($order->type == 'package'){
                    $dd['drop_add'] = @$order->dormitory->name;
                }else{
                    $dd['drop_add'] = @$order->dormitory->name;
                }
                $data[] = $dd;
                // $count = 0;
                // foreach($trip->tripPickup as $pickup){
                //     foreach($pickup->tripOrders as $order){
                //         foreach($order->order->items as $item){
                //             $count += $item->quantity;
                //         }
                //     }
                // }
                // $trip->total_orders = $count;
            }
            return response()->json(['status' => 'success', 'data' => $data, 'message'=> 'SUCCESS'], 200);

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function acceptOrder(Request $request)
    {
        try{
            $order_id = $request->input('order_id');
            $auth_user = JWTAuth::toUser($request->input('token'));
            $user_id = $auth_user->id;

            $now = Carbon::now();
            $time = $now->toTimeString();

            $breakfast = $lunch = $dinner = false;
            if($time > "19:00:00"){
                $now->addDay();
            }
            if($time > "19:00:00" || $time <= "07:00:00"){
                $breakfast = true;
            }
            elseif($time > "07:00:00" && $time <= "12:00:00"){
                $lunch = true;
            }
            elseif($time > "12:00:00" && $time <= "19:00:00"){
                $dinner = true;
            }

            $order = Order::find($order_id);
            if($order){
                if($order->driver_id){
                    $data['status'] = 'error';
                    $data['msg'] = "Order assigned to someone already.";
                }else{
                    $order->update(['driver_id' => $user_id]);

                    $user = $order->user;
                    $message = 'Driver accepted order #'.$order->id;
                    $type = 'notifications';
                    $id = '';
                    $link = '';
                    $orderitem = OrderItem::where('order_id', $order->id)->first();
                    $user = @$orderitem->item->restaurant->merchant;
                    if($user && $user->fcm_token){
                      $ret = sendSingleLocal($user, $message, $type, $id, $link, 'merchant');
                    }

                    // $trip = TripOrders::where('order_id', $order_id)->get()->count();
                    $delivery_time = $order->delivery_time;
                    $restaurant_id = 0;
                    if($order->type == 'package'){
                        foreach($order->items as $item){
                            if($item->item && $item->item->restaurant_id){
                                $restaurant_id = $item->item->restaurant_id;
                            }
                            if($item->item && $item->item->breakfast && $breakfast){
                                $delivery_time = "07:00:00";
                                break;
                            }elseif($item->item && $item->item->lunch && $lunch){
                                $delivery_time = "12:00:00";
                                break;
                            }elseif($item->item && $item->item->dinner && $dinner){
                                $delivery_time = "19:00:00";
                                break;
                            }
                        }
                    }else{
                        foreach($order->items as $item){
                            if($item->item && $item->item->restaurant_id){
                                $restaurant_id = $item->item->restaurant_id;
                                break;
                            }
                        }
                    }
                    $trips = Trip::where('assigned_to', $auth_user->id)
                    ->where('trip_date', $order->delivery_date)
                    ->where('trip_time', $delivery_time)
                    ->get();

                    if($trips->count()){
                        $trip = $trips->first();
                        $trip_picks = TripPickup::where([
                            'trip_id' => $trip->id,
                            'pickup_id' => $restaurant_id,
                        ])->get();
                        if($trip_picks->count()){
                            $trip_pick = $trip_picks->first();
                        }else{
                            $trip_pick = TripPickup::create([
                                'trip_id' => $trip->id,
                                'pickup_id' => $restaurant_id,
                            ]);
                        }
                        TripOrders::create([
                            'order_id' => $order->id,
                            'trip_pick_id' => $trip_pick->id,
                        ]);
                    }else{
                        $trip = Trip::create([
                            'trip_date'   => $order->delivery_date,
                            'trip_time'   => $delivery_time,
                            'assigned_to' => $auth_user->id,
                            'created_by'  => $auth_user->id,
                            'price'       => 0,
                            'status'      => 0,
                        ]);
                        $trip_pick = TripPickup::create([
                            'trip_id' => $trip->id,
                            'pickup_id' => $restaurant_id,
                        ]);
                        TripOrders::create([
                            'order_id' => $order->id,
                            'trip_pick_id' => $trip_pick->id,
                        ]);
                    }
                    $data['status'] = 'success';
                    $data['msg'] = "Order assigned to you.";
                }
            }
            else{
                $data['status'] = 'error';
                $data['msg'] = "Invalid ID";
            }
            return response()->json(['status' => $data['status'], 'data' => [], 'message'=> $data['msg']], 200);

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function rejectOrder(Request $request)
    {
        try{
            $order_id = $request->input('order_id');
            $auth_user = JWTAuth::toUser($request->input('token'));
            $user_id = $auth_user->id;

            $order = Order::find($order_id);
            if($order){
                $rejected = OrderReject::create(['order_id' => $order_id, 'user_id' => $user_id]);
                $data['status'] = 'success';
                $data['msg'] = "Order rejected";
            }
            else{
                $data['status'] = 'error';
                $data['msg'] = "Invalid ID";
            }
            return response()->json(['status' => $data['status'], 'data' => [], 'message'=> $data['msg']], 200);

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function cronTrip(Request $request)
    {
        try{
            $now = Carbon::now()->toDateString();
            $now = Carbon::now()->endOfMonth()->toDateString();
            $start = Carbon::now()->startOfMonth()->toDateString();

            $start = Carbon::createFromDate(2018, 07, 01);
            $now = Carbon::createFromDate(2018, 07, 30);

            $orders = Order::whereBetween('delivery_date', [$start,$now])
            // ->whereNotNull('driver_id')
            ->where('type', 'package')
            ->whereNull('delivery_time')->orderBy('created_at', 'desc')->get();

            $dd = [];
            foreach($orders as $order){
                $todays_date = Carbon::createFromDate(2018, 07, 26);
                $start_date = Carbon::parse($order->delivery_date);
                $diff = $start_date->diffInDays($todays_date);

                foreach($order->items as $item){
                    $todays_date = $todays_date->addDay();

                    if($item->item && $item->item->breakfast && $item->item->breakfast <= $diff){
                        $restaurant_id = $item->item->restaurant_id;
                        $trip = Trip::where([
                            'trip_date'     => $todays_date->toDateString(),
                            'trip_time'     => BREAKFAST_TIME,
                            'assigned_to'   => $order->driver_id != ''?$order->driver_id:49,
                        ])->first();
                        if(!$trip){
                            $trip = Trip::create([
                                'trip_date'     => $todays_date->toDateString(),
                                'trip_time'     => BREAKFAST_TIME,
                                'assigned_to'   => $order->driver_id != ''?$order->driver_id:49,
                                'created_by'    => 0,
                                'price'         => 0,
                                'status'        => 0
                            ]);
                        }

                        $trip_pick = TripPickup::where([
                            'trip_id'     => $trip->id,
                            'pickup_id' => $restaurant_id,
                        ])->first();

                        if(!$trip_pick){
                            $trip_pick = TripPickup::create([
                                'trip_id' => $trip->id,
                                'pickup_id' => $restaurant_id,
                            ]);
                        }

                        $trip_order = TripOrders::where([
                            'order_id' => $order->id,
                            'trip_pick_id' => $trip_pick->id,
                        ])->first();

                        if(!$trip_order){
                            TripOrders::create([
                                'order_id' => $order->id,
                                'trip_pick_id' => $trip_pick->id,
                            ]);
                        }
                    }

                    if($item->item && $item->item->lunch && $item->item->lunch <= $diff){
                        $restaurant_id = $item->item->restaurant_id;
                        $trip = Trip::where([
                            'trip_date'     => $todays_date->toDateString(),
                            'trip_time'     => LUNCH_TIME,
                            'assigned_to'   => $order->driver_id != ''?$order->driver_id:49,
                        ])->first();
                        if(!$trip){
                            $trip = Trip::create([
                                'trip_date'     => $todays_date->toDateString(),
                                'trip_time'     => LUNCH_TIME,
                                'assigned_to'   => $order->driver_id != ''?$order->driver_id:49,
                                'created_by'    => 0,
                                'price'         => 0,
                                'status'        => 0
                            ]);
                        }

                        $trip_pick = TripPickup::where([
                            'trip_id'     => $trip->id,
                            'pickup_id' => $restaurant_id,
                        ])->first();

                        if(!$trip_pick){
                            $trip_pick = TripPickup::create([
                                'trip_id' => $trip->id,
                                'pickup_id' => $restaurant_id,
                            ]);
                        }

                        $trip_order = TripOrders::where([
                            'order_id' => $order->id,
                            'trip_pick_id' => $trip_pick->id,
                        ])->first();

                        if(!$trip_order){
                            TripOrders::create([
                                'order_id' => $order->id,
                                'trip_pick_id' => $trip_pick->id,
                            ]);
                        }
                    }

                    if($item->item && $item->item->dinner && $item->item->dinner <= $diff){
                        $restaurant_id = $item->item->restaurant_id;
                        $trip = Trip::where([
                            'trip_date'     => $todays_date->toDateString(),
                            'trip_time'     => DINNER_TIME,
                            'assigned_to'   => $order->driver_id != ''?$order->driver_id:49,
                        ])->first();
                        if(!$trip){
                            $trip = Trip::create([
                                'trip_date'     => $todays_date->toDateString(),
                                'trip_time'     => DINNER_TIME,
                                'assigned_to'   => $order->driver_id != ''?$order->driver_id:49,
                                'created_by'    => 0,
                                'price'         => 0,
                                'status'        => 0
                            ]);
                        }

                        $trip_pick = TripPickup::where([
                            'trip_id'     => $trip->id,
                            'pickup_id' => $restaurant_id,
                        ])->first();

                        if(!$trip_pick){
                            $trip_pick = TripPickup::create([
                                'trip_id' => $trip->id,
                                'pickup_id' => $restaurant_id,
                            ]);
                        }

                        $trip_order = TripPickup::where([
                            'order_id' => $order->id,
                            'trip_pick_id' => $trip_pick->id,
                        ])->first();

                        if(!$trip_order){
                            TripOrders::create([
                                'order_id' => $order->id,
                                'trip_pick_id' => $trip_pick->id,
                            ]);
                        }
                    }
                }
                $d['order'] = $order;
                $d['diff'] = $diff;
                $dd[] = $d;
            }
            return response()->json(['status' => 'success', 'data' => $dd, 'message'=> 'SUCCESS'], 200);

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function trip(){
      try{
        $now = Carbon::now()->addDay();
        $subs = Subscription::where('delivery_date', $now->toDateString())->get();
        foreach($subs as $sub){
          if(!$sub->order){
            continue;
          }
          $user_id = $sub->order->driver_id;
          if($user_id == ''){
            continue;
          }
          $start_date = Carbon::parse($sub->order->delivery_date);
          $diff = $start_date->diffInDays($now)+1;

          $food_item = FoodMenu::find($sub->item_id);

          if($food_item && $food_item->breakfast && $food_item->breakfast > 1 && $food_item->breakfast >= $diff){

            $time = "07:00:00";
            $restaurant_id = $food_item->restaurant_id;

            $trip = Trip::where('trip_date', $now->toDateString())->where('trip_time', $time)->where('assigned_to', $user_id)->first();
            if(!$trip){
              $trip = Trip::create([
                'created_by' => $user_id,
                'status'  => 0,
                'price' => 0,
                'assigned_to' => $user_id,
                'trip_date' => $now->toDateString(),
                'trip_time' => $time
              ]);
            }

            $trip_pick = TripPickup::where([
                'trip_id'     => $trip->id,
                'pickup_id' => $restaurant_id,
            ])->first();

            if(!$trip_pick){
                $trip_pick = TripPickup::create([
                    'trip_id' => $trip->id,
                    'pickup_id' => $restaurant_id,
                ]);
            }

            $trip_order = TripOrders::where([
                'order_id' => $sub->order_id,
                'trip_pick_id' => $trip_pick->id,
            ])->first();

            if(!$trip_order){
                TripOrders::create([
                    'order_id' => $sub->order_id,
                    'trip_pick_id' => $trip_pick->id,
                ]);
            }
          }

          if($food_item && $food_item->lunch && $food_item->lunch > 1 && $food_item->lunch >= $diff ){
            $time = "12:00:00";
            $restaurant_id = $food_item->restaurant_id;

            $trip = Trip::where('trip_date', $now->toDateString())->where('trip_time', $time)->where('assigned_to', $user_id)->first();
            if(!$trip){
              $trip = Trip::create([
                'created_by' => $user_id,
                'status'  => 0,
                'price' => 0,
                'assigned_to' => $user_id,
                'trip_date' => $now->toDateString(),
                'trip_time' => $time
              ]);
            }

            $trip_pick = TripPickup::where([
                'trip_id'     => $trip->id,
                'pickup_id' => $restaurant_id,
            ])->first();

            if(!$trip_pick){
                $trip_pick = TripPickup::create([
                    'trip_id' => $trip->id,
                    'pickup_id' => $restaurant_id,
                ]);
            }

            $trip_order = TripOrders::where([
                'order_id' => $sub->order_id,
                'trip_pick_id' => $trip_pick->id,
            ])->first();

            if(!$trip_order){
                TripOrders::create([
                    'order_id' => $sub->order_id,
                    'trip_pick_id' => $trip_pick->id,
                ]);
            }
          }

          if($food_item && $food_item->dinner && $food_item->dinner > 1 && $food_item->dinner >= $diff){
            $time = "19:00:00";
            $restaurant_id = $food_item->restaurant_id;

            $trip = Trip::where('trip_date', $now->toDateString())->where('trip_time', $time)->where('assigned_to', $user_id)->first();
            if(!$trip){
              $trip = Trip::create([
                'created_by' => $user_id,
                'status'  => 0,
                'price' => 0,
                'assigned_to' => $user_id,
                'trip_date' => $now->toDateString(),
                'trip_time' => $time
              ]);
            }

            $trip_pick = TripPickup::where([
                'trip_id'     => $trip->id,
                'pickup_id' => $restaurant_id,
            ])->first();

            if(!$trip_pick){
                $trip_pick = TripPickup::create([
                    'trip_id' => $trip->id,
                    'pickup_id' => $restaurant_id,
                ]);
            }

            $trip_order = TripOrders::where([
                'order_id' => $sub->order_id,
                'trip_pick_id' => $trip_pick->id,
            ])->first();

            if(!$trip_order){
                TripOrders::create([
                    'order_id' => $sub->order_id,
                    'trip_pick_id' => $trip_pick->id,
                ]);
            }
          }

        }

      }catch(Exception $e){
        dd('cron error');
      }
    }

    public function getInvoiceList(Request $request)
    {
        try{
            $batch_id = $request->input('batch_id');
            $orders = Order::where('batch_id', $batch_id)->where('type', 'single')->get();
            foreach($orders as $item){
              $restaurant = '';
              foreach($item->items as $food_item){
                $restaurant = @$food_item->restaurant->name;
                break;
              }
              $item->name = $item->user->name;
              if($restaurant != '')
                $item->invoice_id = 'NS-'.$restaurant.'-'.$item->id;
              else{
                $item->invoice_id = 'NS-'.$item->id;
              }

              if($item->delivery_type == 'reception'){
                $item->delivery_type = 'Reception';
              }elseif($item->delivery_type == 'inperson' || $item->delivery_type == 'address'){
                $item->delivery_type = 'In Person';
              }
            }
            return response()->json(['status' => 'success', 'data' => $orders, 'message'=> 'Success'], 200);

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }

    }

}
