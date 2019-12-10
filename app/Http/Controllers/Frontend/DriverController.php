<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Helper\Enets;
use JWTAuth, DB, Auth, Input;

use App\Models\Restaurant;
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

class DriverController extends Controller
{

    public function getLogin(Request $request)
    {
        // $auth_user = JWTAuth::toUser(Input::get('token'));
        // $user = Auth::loginUsingId($auth_user->id);

        return view('frontend.driver.login', compact('user'));
    }

    public function getDashboard(Request $request)
    {
        $user = Auth::user();

        $now = Carbon::now();
        $time = $now->toTimeString();
        $label = '';
        $breakfast = $lunch = $dinner = false;
        if($time > "00:00:00" && $time <= "07:00:00"){
            $time = "07:00:00";
            $label = 'batch_b';
            $breakfast = true;
        }
        elseif($time > "07:00:00" && $time <= "12:00:00"){
            $time = "12:00:00";
            $label = 'batch_l';
            $lunch = true;
        }
        elseif($time > "12:00:00" && $time <= "24:00:00"){
            $time = "19:00:00";
            $label = 'batch_d';
            $dinner = true;
        }

        $trip = Trip::where('assigned_to', $user->id)->whereDate('trip_date', date('Y-m-d'))->where('trip_time', $time)->first();
        $data = [];
        if($trip){
            $pickups = $trip->pickups->pluck('id');
            $tripOrders = TripOrders::with('order')->whereIn('trip_pick_id', $pickups)->get();
            foreach($tripOrders as $key => $order){

                $batch = Batch::where(function($q) use($order){
                    $q->where('address', $order->order->address)->orWhere('dormitory_id', $order->order->dormitory_id);
                })->whereDate('created_at', date('Y-m-d'))->orderBy('created_at', 'desc')->first();

                if($order->order->delivery_type == 'reception'){
                    $address = $order->order->dormitory->name;
                }else{
                    $address = $order->order->address;
                }
                $data[$batch->id]['pickups'] = (isset($data[$batch->id]['pickups']) && $data[$batch->id]['pickups'] > 1)? $data[$batch->id]['pickups']++:1;
                $data[$batch->id]['address'] = $address;
                $data[$batch->id]['trip_id'] = $trip->id;
                $data[$batch->id]['batch_id'] = $batch->{$label};

            }
        }
        return view('frontend.driver.dashboard', compact('user', 'data', 'trip'));
    }

    public function getOrder($id, Request $request)
    {
        $pickups = TripPickup::where('trip_id', $id)->get();
        foreach($pickups as $pick){
            $ups = TripOrders::where('trip_pick_id',$pick->id)->get();
            $total = 0;
            $total_picked = 0;
            $arr = [];
            foreach($ups as $order){
                // $total += $order->order->items->count();
                foreach($order->order->items as $item){
                    if(!in_array($item->item_id, $arr)){
                        $arr[] = $item->item_id;
                        $total++;
                        if($item->agent_status){
                            $total_picked++;
                        }
                    }
                }
            }
            $pick->total = $total;
            $pick->total_picked = $total_picked;


        }

        // dd($pickups);
        return view('frontend.driver.order_list', compact('user', 'pickups'));
    }

    public function getOrderDetail($id, Request $request)
    {
        $pickups = TripOrders::where('trip_pick_id',$id)->get();
        $data = [];
        $total_packed = 0;
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
                if($data[$item_id]['status'] == "Picked"){
                    $total_packed++;
                }
            }
        }
        $enable_deliver = false;
        if($total_packed == count($data)){
            $enable_deliver = true;
        }

        return view('frontend.driver.order_detail', compact('user', 'pickups', 'data', 'enable_deliver'));
    }

    public function getTripOrderDetail($id, Request $request)
    {
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

        return view('frontend.driver.torder_detail', compact('user', 'pickups', 'data'));
    }

    public function updateStatus(Request $request)
    {

        // $data = $request->all();

        $data = $request->only('item_ids', 'status_id', 'name');
        // $auth_user = Auth::user();
        //
        $item_ids = explode(',', $data['item_ids']);
        foreach($item_ids as $id){

                $item = OrderItem::find($id);
                $item->update(['agent_status_id' => $data['status_id'], 'name' => $data['name'] ]);
                $order = Order::find($item->order_id);
                $order->update(['status_id' => $data['status_id']]);
        }

        return redirect()->back()->with([
            'flash_level'   => 'success',
            'flash_message' => 'Status updated successfully.',
        ]);

    }

    public function viewProfile(Request $request)
    {
        $auth_user = Auth::user();
        $prof_image = "merchant/images/img-profile.jpg";

        return view('frontend.driver.profile', compact('auth_user', 'prof_image'));
    }

    public function getProfile(Request $request)
    {
        $auth_user = Auth::user();
        $prof_image = "merchant/images/img-profile.jpg";

        return view('frontend.driver.edit_profile', compact('auth_user', 'prof_image'));
    }

    public function postProfile(Request $request)
    {

        $data = $request->only('name');
        $data_restra = $request->only('fin_no', 'vehicle_no', 'street_address', 'gender', 'phone');
        // $data = $request->only('fin_no', 'phone_no');
        // $auth_user = Auth::user();
        //
        // $restaurant = Restaurant::where('merchant_id', $auth_user->id)->first();
        // $restaurant->update($data_restra);

        return redirect()->route('driver.profile.view')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Profile updated successfully.',
        ]);

    }

    public function getEarning(Request $request)
    {
        $user_id = Auth::id();
        $trips = Trip::where('assigned_to', $user_id)->get();

        return view('frontend.driver.earning', compact('user', 'trips'));
    }

    public function getEarningDetail($id, Request $request)
    {
        $user_id = Auth::id();
        $trip = Trip::find($id);
        $data = [];
        if($trip){
            $pickups = $trip->pickups->pluck('id');
            $tripOrders = TripOrders::whereIn('trip_pick_id', $pickups)->get();
            $arr = [];
            foreach($tripOrders as $order){

                $total = 0;
                $batch_id = $order->order->batch_id;
                foreach($order->order->items as $item){
                    // echo $item->item_id.'<br>';
                    if(!in_array($item->item_id, $arr)){
                        // echo "in<br>";
                        $arr[] = $item->item_id;
                        $total++;
                    }
                }

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
        }
        return view('frontend.driver.trip', compact('user', 'trip', 'data'));
    }

    public function getTrip(Request $request)
    {
        $auth_user = Auth::user();
        $rejected = TripReject::where('user_id', $auth_user->id)->pluck('trip_id');
        // dd($rejected);
        $trips = Trip::whereNotIn('id', $rejected)->whereNull('assigned_to')->where('trip_date', '>=', Carbon::now())->get();

        return view('frontend.driver.trip_notification', compact('auth_user', 'trips'));
    }

    public function acceptTrip(Request $request)
    {
        $trip_id = \Input::get('id');
        $user_id = Auth::id();

        $trip = Trip::find($trip_id);
        if($trip){
            if($trip->assigned_to){
                $data['status'] = false;
                $data['msg'] = "Trip assigned to someone already.";
            }else{

                $trip->update(['assigned_to' => $user_id, 'accepted_at' => Carbon::now()]);
                $data['status'] = true;
                $data['msg'] = "Trip assigned to you.";
            }
        }
        else{
            $data['status'] = false;
            $data['msg'] = "Invalid ID";
        }
        return json_encode($data);
    }

    public function rejectTrip(Request $request)
    {
        $trip_id = \Input::get('id');
        $user_id = Auth::id();

        $trip = Trip::find($trip_id);
        if($trip){
            $rejected = TripReject::create(['trip_id' => $trip_id, 'user_id' => $user_id]);
            $data['status'] = true;
            $data['msg'] = "Success";
        }
        else{
            $data['status'] = false;
            $data['msg'] = "Invalid ID";
        }
        return json_encode($data);
    }

}
