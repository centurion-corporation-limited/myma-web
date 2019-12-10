<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Helper\Enets;
use JWTAuth, DB, Auth, Input;

use App\Models\Restaurant;
use App\Models\FoodMenu;
use App\Models\FoodInvoices;
use App\Models\FoodCategory;
use App\Models\FoodCourse;
use App\Models\FoodTag;
use App\Models\Order;
use App\Models\Batch;
use App\Models\Subscription;
use App\Models\OrderItem;
use Carbon\Carbon;

class MerchantController extends Controller
{

    public function getLogin(Request $request)
    {
        // $auth_user = JWTAuth::toUser(Input::get('token'));
        // $user = Auth::loginUsingId($auth_user->id);

        return view('frontend.merchant.login', compact('user'));
    }

    public function getDashboard(Request $request)
    {
        $morning_limit = Carbon::createFromTimeString('11:00:00');
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
        elseif($time > "12:00:00" && $time <= "19:00:00"){
            $time = "19:00:00";
            $label = 'batch_d';
            $dinner = true;
        }

        $morning = $now->diffInSeconds($morning_limit, false);

        $auth_user = Auth::user();
        $restaurant = Restaurant::where('merchant_id', $auth_user->id)->select('id')->first();
        if($restaurant)
            $restaurant_id = $restaurant->id;
        else{
            dd('Ask admin to add restaurant information to you account to proceed.');
        }

        $orders = Order::where('delivery_date', $now->toDateString())->whereHas('items', function($q) use($restaurant_id){
            $q->whereHas('item', function($qq) use($restaurant_id){
                    $qq->where('restaurant_id', $restaurant_id);
            });
        })->whereNotIn('status_id', ['10', '11','12','13'])->whereNotNull('delivery_time')->orderBy('created_at', 'desc')->get();

        $start = Carbon::now()->startOfMonth()->toDateString();

        $orders_p = Order::whereBetween('delivery_date', [$start,$now->toDateString()])->whereHas('items', function($q) use($restaurant_id){
            $q->whereHas('item', function($qq) use($restaurant_id){
                    $qq->where('restaurant_id', $restaurant_id);
            });
        })->whereNotIn('status_id', ['10', '11','12','13'])->whereNull('delivery_time')->orderBy('created_at', 'desc')->get();

        foreach($orders as $order){
            if($order->dormitory_id){
                $batch = Batch::where('dormitory_id', $order->dormitory_id)->whereDate('created_at', $now->toDateString())->first();
                if(!$batch){
                    $batch = Batch::orderBy('created_at', 'decs')->orderBy('id', 'decs')->first();
                    $dd['dormitory_id'] = $order->dormitory_id;
                    $num = str_pad($batch->id+1, 7, '0', STR_PAD_LEFT);
                    $dd['batch_b'] = 'BB'.$num;
                    $dd['batch_l'] = 'BL'.$num;
                    $dd['batch_d'] = 'BD'.$num;
                    $batch = Batch::create($dd);

                }
            }else{
                $batch = Batch::where('address','like', $order->address)->whereDate('created_at', $now->toDateString())->first();
                if(!$batch){
                    $batch = Batch::orderBy('created_at', 'decs')->orderBy('id', 'decs')->first();
                    $dd['address'] = $order->address;
                    $num = str_pad($batch->id+1, 7, '0', STR_PAD_LEFT);
                    $dd['batch_b'] = 'BB'.$num;
                    $dd['batch_l'] = 'BL'.$num;
                    $dd['batch_d'] = 'BD'.$num;
                    $batch = Batch::create($dd);

                }
            }

            $order->batch_id = $batch->{$label};

            $count = 0;
            $c_status = 0;
            foreach($order->items as $item){
                if($item->item->restaurant_id == $restaurant_id){
                    $count++;
                    if($item->restaurant_status_id == '9'){
                        $c_status++;
                    }
                }
            }
            $order->item_count = $count;
        }

        foreach($orders_p as $order){
            if($order->dormitory_id){
                $batch = Batch::where('dormitory_id', $order->dormitory_id)->whereDate('created_at', $now->toDateString())->first();
                if(!$batch){
                    $batch = Batch::orderBy('created_at', 'decs')->orderBy('id', 'decs')->first();
                    $dd['dormitory_id'] = $order->dormitory_id;
                    $num = str_pad($batch->id+1, 7, '0', STR_PAD_LEFT);
                    $dd['batch_b'] = 'BB'.$num;
                    $dd['batch_l'] = 'BL'.$num;
                    $dd['batch_d'] = 'BD'.$num;
                    $batch = Batch::create($dd);

                }
            }else{
                $batch = Batch::where('address','like', $order->address)->whereDate('created_at', $now->toDateString())->first();
                if(!$batch){
                    $batch = Batch::orderBy('created_at', 'decs')->orderBy('id', 'decs')->first();
                    $dd['address'] = $order->address;
                    $num = str_pad($batch->id+1, 7, '0', STR_PAD_LEFT);
                    $dd['batch_b'] = 'BB'.$num;
                    $dd['batch_l'] = 'BL'.$num;
                    $dd['batch_d'] = 'BD'.$num;
                    $batch = Batch::create($dd);

                }
            }

            $order->batch_id = $batch->{$label};

            $count = 0;
            $c_status = 0;
            foreach($order->items as $item){
                if($item->item->restaurant_id == $restaurant_id){
                    $count++;
                    if($item->restaurant_status_id == '9'){
                        $c_status++;
                    }
                }

                $subs = Subscription::where('item_id', $item->item_id)->where('order_id', $order->id)->whereDate('created_at', $now->toDateString())->first();
                if(!$subs){
                    $sd['item_id'] = $item->item_id;
                    $sd['order_id'] = $order->id;
                    if($breakfast)
                        $sd['breakfast'] = $batch->batch_b;
                    if($lunch)
                        $sd['lunch'] = $batch->batch_l;
                    if($dinner)
                        $sd['dinner'] = $batch->batch_d;

                    Subscription::create($sd);
                }else{
                    if($breakfast && $subs->breakfast != ''){
                        $sd['breakfast'] = $batch->batch_b;
                        $subs->update($sd);
                    }
                    if($lunch && $subs->lunch != ''){
                        $sd['lunch'] = $batch->batch_l;
                        $subs->update($sd);
                    }
                    if($dinner && $subs->dinner != ''){
                        $sd['dinner'] = $batch->batch_d;
                        $subs->update($sd);
                    }
                }
            }
            $order->item_count = $count;
        }

        return view('frontend.merchant.dashboard', compact('orders', 'orders_p', 'morning', 'count', 'c_status'));
    }

    public function viewOrder($id, Request $request)
    {
        // $auth_user = JWTAuth::toUser(Input::get('token'));
        // $user = Auth::loginUsingId($auth_user->id);
        $auth_user = Auth::user();
        // dd($auth_user);
        $restaurant = Restaurant::where('merchant_id', $auth_user->id)->select('id')->first();
        $restaurant_id = $restaurant->id;

        $items = OrderItem::where('order_id', $id)->whereHas('item', function($q) use($restaurant_id){
            $q->where('restaurant_id', $restaurant_id);
        })->get();

        return view('frontend.merchant.batch_detail', compact('items'));
    }

    public function viewHistory(Request $request)
    {
        // $auth_user = JWTAuth::toUser(Input::get('token'));
        // $user = Auth::loginUsingId($auth_user->id);
        $orders = Order::whereIn('status_id', ['11','12'])->orderBy('created_at', 'desc')->get();

        // foreach($orders as $order){
        //     $count = 0;
        //     foreach($order->items as $item){
        //         if($item->item->restaurant_id == $restaurant_id){
        //             $count++;
        //         }
        //     }
        //     $order->item_count = $count;
        // }

        // dd($orders);
        return view('frontend.merchant.history', compact('orders'));
    }

    public function packageSubscribed(Request $request)
    {
        // $auth_user = JWTAuth::toUser(Input::get('token'));
        // $user = Auth::loginUsingId($auth_user->id);
        $auth_user = Auth::user();
        $restaurant = Restaurant::where('merchant_id', $auth_user->id)->select('id')->first();
        $restaurant_id = $restaurant->id;

        $orders = OrderItem::whereHas('item', function($q) use($restaurant_id){
                $q->where('restaurant_id', $restaurant_id)
                ->where('type', 'package');
        })->get();

        // foreach($orders as $order){
        //     $count = 0;
        //     foreach($order->items as $item){
        //         if($item->item->restaurant_id == $restaurant_id){
        //             $count++;
        //         }
        //     }
        //     $order->item_count = $count;
        // }

        // dd($orders);
        return view('frontend.merchant.package', compact('orders'));
    }

    public function packageSubscribers($id, Request $request)
    {
        // $auth_user = JWTAuth::toUser(Input::get('token'));
        // $user = Auth::loginUsingId($auth_user->id);
        $auth_user = Auth::user();
        $restaurant = Restaurant::where('merchant_id', $auth_user->id)->select('id')->first();
        $restaurant_id = $restaurant->id;

        $orders = Order::whereHas('items', function($q) use($id){
            $q->where('item_id', $id);
        })->orderBy('created_at', 'desc')->get();

        return view('frontend.merchant.subscribers', compact('orders', 'id'));
    }

    public function packageSubscription($id, $item_id, Request $request)
    {
        // $auth_user = JWTAuth::toUser(Input::get('token'));
        // $user = Auth::loginUsingId($auth_user->id);
        $auth_user = Auth::user();
        $orders = Order::find($id);

        // $restaurant = Restaurant::where('merchant_id', $orders->user_id)->select('id')->first();
        // $restaurant_id = $restaurant->id;
        $item = FoodMenu::find($item_id);

        // $order = OrderItem::findOrFail($id);
        $date = Carbon::parse($orders->delivery_date)->format('M d, Y');
        $end_date = Carbon::parse($orders->delivery_date)->addDays(max($item->breakfast, $item->lunch, $item->dinner));
        $end = new Carbon('last day of this month');
        if($end < $end_date){
            $end_date = $end;
        }
        $subs = Subscription::where('item_id', $item_id)->where('order_id', $id)->get()->toArray();

        return view('frontend.merchant.subscription', compact('orders', 'id', 'date', 'end_date', 'subs'));
    }

    public function getMenu(Request $request)
    {
        $auth_user = Auth::user();
        $restaurant = Restaurant::where('merchant_id', $auth_user->id)->select('id')->first();

        $menu = [];
        if($restaurant){
            $menu = FoodMenu::where('restaurant_id', $restaurant->id)->get();
        }
        return view('frontend.merchant.menu', compact('user', 'menu'));
    }

    public function viewProfile(Request $request)
    {
        $auth_user = Auth::user();
        $restaurant = Restaurant::where('merchant_id', $auth_user->id)->first();
        $prof_image = "merchant/images/img-profile.jpg";
        return view('frontend.merchant.profile', compact('auth_user', 'restaurant', 'prof_image'));
    }

    public function getProfile(Request $request)
    {
        $auth_user = Auth::user();
        $restaurant = Restaurant::where('merchant_id', $auth_user->id)->first();
        $prof_image = "merchant/images/img-profile.jpg";

        return view('frontend.merchant.edit_profile', compact('auth_user', 'restaurant', 'prof_image'));
    }

    public function postProfile(Request $request)
    {

        $data_restra = $request->only('name', 'address', 'open_at', 'closes_at','fin_no', 'phone_no');

        $auth_user = Auth::user();

        $restaurant = Restaurant::where('merchant_id', $auth_user->id)->first();
        $restaurant->update($data_restra);

        return redirect()->route('merchant.profile.view')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Profile updated successfully.',
        ]);

    }

    public function updateItem(Request $request)
    {
        $item_id = \Input::get('item_id');

        $item = OrderItem::where('id', $item_id)->first();
        if($item){
            $item->update(['restaurant_status_id' => 9]);
            $order_id = $item->order_id;
            $total = OrderItem::where('order_id', $order_id)->count();
            $total_packed = OrderItem::where('order_id', $order_id)->where('restaurant_status_id', 9)->count();
            if($total == $total_packed){
                $order = Order::where('id', $order_id)->first();
                $order->update(['status_id' => 9]);
            }
            $data['status'] = true;
        }
        else{
            $data['status'] = false;
        }
        return json_encode($data);
    }

    public function updateOrder(Request $request)
    {
        $item_id = \Input::get('item_id');
        $status_id = \Input::get('status_id');
        if($status_id == ''){
            $status_id = 9;
        }
        $auth_user = Auth::user();
        $item = Order::where('id', $item_id)->first();
        if($item){
            // $can = true;
            // foreach($item->items as $itm){
            //     if($itm->item->restaurant->merchant_id != $auth_user->id){
            //         $can = false;
            //     }
            // }
            // if($can){
                $item->update(['status_id' => $status_id]);
            // }else{
            //     foreach($item->items as $itm){
            //         if($itm->item->restaurant->merchant_id == $auth_user->id){
            //             $itm->update(['restaurant_status_id' => $status_id]);
            //         }
            //     }
            // }
            $data['status'] = true;
        }
        else{
            $data['status'] = false;
        }
        return json_encode($data);
    }



    public function viewItem($id, Request $request)
    {
        $item = FoodMenu::findOrFail($id);
        return view('frontend.merchant.menu_detail', compact('user', 'item'));
    }

    public function getItem()
    {
        $courses = FoodCourse::pluck('name', 'id')->all();
        $category = FoodCategory::pluck('name', 'id')->all();

        return view('frontend.merchant.create_menu', compact('user', 'courses', 'category'));
    }

    public function postItem(Request $request)
    {
        $data = $request->only('name', 'description', 'is_veg', 'is_halal', 'course_id', 'price', 'type');

        if($request->input('path')) {
          $file = $request->input('path');
          if (preg_match('/^data:image\/(\w+);base64,/', $file, $type)) {
              $file = substr($file, strpos($file, ',') + 1);
              $type = strtolower($type[1]); // jpg, png, gif

              if (!in_array($type, [ 'jpg', 'jpeg', 'gif', 'png' ])) {
                  throw new \Exception('invalid image type');
              }

              $decode = base64_decode($file);

              if ($decode === false) {
                  throw new \Exception('base64_decode failed');
              }
              $folder = "files/food";

              $path = savePhoto($file, $folder, $type);
              $data['image'] = $path;
          } else {
              throw new \Exception('did not match data URI with image data');
          }
        }

        $auth_user = Auth::user();
        $restaurant = Restaurant::where('merchant_id', $auth_user->id)->select('id')->first();
        $data['restaurant_id'] = $restaurant->id;
        $food = FoodMenu::create($data);
        $tags = $request->only('tags');
        if(count($tags)){
            foreach($tags as $tag){
                FoodTag::create([
                    'food_id' => $food->id,
                    'category_id' => $tag
                ]);
            }
        }
        return redirect()->route('merchant.menu.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Item Added to menu.',
        ]);
    }

    public function getItemEdit($id)
    {
        $item = FoodMenu::findOrFail($id);
        $courses = FoodCourse::pluck('name', 'id')->all();
        $category = FoodCategory::pluck('name', 'id')->all();

        return view('frontend.merchant.edit_menu', compact('user', 'courses', 'category', 'item'));
    }

    public function postItemEdit($id, Request $request)
    {
        $item = FoodMenu::findOrFail($id);

        $data = $request->only('name', 'description', 'is_veg', 'is_halal', 'course_id', 'category_id', 'price');
        if($request->input('path')) {
          $file = $request->input('path');

          if (preg_match('/^data:image\/(\w+);base64,/', $file, $type)) {
              $file = substr($file, strpos($file, ',') + 1);
              $type = strtolower($type[1]); // jpg, png, gif

              if (!in_array($type, [ 'jpg', 'jpeg', 'gif', 'png' ])) {
                  throw new \Exception('invalid image type');
              }

              $decode = base64_decode($file);

              if ($decode === false) {
                  throw new \Exception('base64_decode failed');
              }
              $folder = "files/food";

              $path = savePhoto($file, $folder, $type);
              $data['image'] = $path;
          } else {
              throw new \Exception('did not match data URI with image data');
          }
        }
        // $auth_user = Auth::user();
        // $restaurant = Restaurant::where('merchant_id', $auth_user->id)->select('id')->first();
        // $data['restaurant_id'] = $restaurant->id;
        $item->update($data);

        return redirect()->route('merchant.menu.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Item updated successfully.',
        ]);
    }

    public function addPackage()
    {
        $category = FoodCategory::pluck('name', 'id')->all();

        return view('frontend.merchant.create_package', compact('user', 'category'));
    }

    public function postPackage(Request $request)
    {
        $data = $request->only('name', 'description', 'is_veg', 'is_halal', 'breakfast', 'lunch', 'price', 'dinner', 'type');

        $auth_user = Auth::user();
        $restaurant = Restaurant::where('merchant_id', $auth_user->id)->select('id')->first();
        $data['restaurant_id'] = $restaurant->id;
        $food = FoodMenu::create($data);

        $tags = $request->only('tags');
        if(count($tags)){
            foreach($tags as $tag){
                FoodTag::create([
                    'food_id' => $food->id,
                    'category_id' => $tag
                ]);
            }
        }

        return redirect()->route('merchant.menu.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Package Added to menu.',
        ]);
    }

    public function editPackage($id)
    {
        $item = FoodMenu::findOrFail($id);
        $category = FoodCategory::pluck('name', 'id')->all();

        return view('frontend.merchant.edit_package', compact('user', 'category', 'item'));
    }

    public function updatePackage($id, Request $request)
    {
        $item = FoodMenu::findOrFail($id);

        $data = $request->only('name', 'description', 'is_veg', 'is_halal', 'breakfast', 'lunch', 'category_id', 'price','dinner', 'type');
        // $auth_user = Auth::user();
        // $restaurant = Restaurant::where('merchant_id', $auth_user->id)->select('id')->first();
        // $data['restaurant_id'] = $restaurant->id;
        $item->update($data);

        return redirect()->route('merchant.menu.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Package updated successfully.',
        ]);
    }

    public function getAccount(Request $request)
    {
        $auth_user = Auth::user();
        $restaurant = Restaurant::where('merchant_id', $auth_user->id)->select('id')->first();
        if($restaurant)
            $restaurant_id = $restaurant->id;
        else{
            dd('Ask admin to add restaurant information to you account to proceed.');
        }

        $orders = Order::where('batch_id', '!=', '')->whereHas('items', function($q) use($restaurant_id){
            $q->whereHas('item', function($qq) use($restaurant_id){
                    $qq->where('restaurant_id', $restaurant_id);
            });
        })/*->whereIn('status_id', ['10','11','13'])*/->orderBy('created_at', 'desc')->groupBy('batch_id')->get();

        foreach($orders as $order){
            $batch_id = $order->batch_id;

            $items = \DB::table('smsinves_wlc_portal.order_items')
            ->join('smsinves_wlc_portal.orders', function ($join) use($batch_id){
                $join->on('orders.id', '=', 'order_items.order_id')
                     ->where('orders.batch_id', $batch_id);
            })
            ->join('smsinves_wlc_portal.food_menu', 'food_menu.id', '=', 'order_items.item_id')
            ->select('order_items.*', 'food_menu.price')
            ->get();
            $total = 0;
            foreach($items as $item){
                $total += $item->quantity*$item->price;
            }

            $order->sub_total = $total;
        }

        return view('frontend.merchant.account', compact('user', 'orders'));
    }

    public function getAccountDetail($id, Request $request)
    {
        $auth_user = Auth::user();
        $restaurant = Restaurant::where('merchant_id', $auth_user->id)->select('id')->first();
        if($restaurant)
            $restaurant_id = $restaurant->id;
        else{
            dd('Ask admin to add restaurant information to you account to proceed.');
        }

        $batch_id = $id;

        $items = \DB::table('smsinves_wlc_portal.order_items')
        ->join('smsinves_wlc_portal.orders', function ($join) use($batch_id){
            $join->on('orders.id', '=', 'order_items.order_id')
                 ->where('orders.batch_id', $batch_id);
        })
        ->join('smsinves_wlc_portal.food_menu', 'food_menu.id', '=', 'order_items.item_id')
        ->select('order_items.*', 'food_menu.price','food_menu.name')
        ->get();

        return view('frontend.merchant.account_detail', compact('user', 'items', 'batch_id'));
    }

    public function getInvoiceDetail($id, Request $request)
    {
        $auth_user = Auth::user();
        $restaurant = Restaurant::where('merchant_id', $auth_user->id)->select('id')->first();
        if($restaurant)
            $restaurant_id = $restaurant->id;
        else{
            dd('Ask admin to add restaurant information to you account to proceed.');
        }

        $batch_id = $id;

        $items = \DB::table('smsinves_wlc_portal.order_items')
        ->join('smsinves_wlc_portal.orders', function ($join) use($batch_id){
            $join->on('orders.id', '=', 'order_items.order_id')
                 ->where('orders.batch_id', $batch_id);
        })
        ->join('smsinves_wlc_portal.food_menu', 'food_menu.id', '=', 'order_items.item_id')
        ->select('order_items.*', 'food_menu.price','food_menu.name', 'orders.batch_id')
        ->get();

        $invoice = FoodInvoices::where('batch_id', $batch_id)->first();
        if(!$invoice){
            $total = 0;
            foreach($items as $item){
                $total += $item->quantity*$item->price;
            }
            $data['batch_id'] = $batch_id;
            $data['status'] = 'pending';
            $data['user_id'] = $auth_user->id;
            $data['user_id'] = 'merchnat';
            $data['total'] = $total;
            $invoice = FoodInvoices::create($data);
        }

        return view('frontend.merchant.invoice', compact('user', 'items', 'invoice'));
    }

}
