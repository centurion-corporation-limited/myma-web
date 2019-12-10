<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Helper\Enets;
use App\User;
use JWTAuth, DB, Auth, Input;
use App\Models\Address;
use App\Models\Restaurant;
use App\Models\Merchant;
use App\Models\Terminal;
use App\Models\FoodMenu;
use App\Models\FoodMerchant;
use App\Models\Dormitory;
use App\Models\Order;
use App\Models\Batch;
use App\Models\Share;
use App\Models\Advertisement;
use App\Models\Category;
use App\Models\FoodCategory;
use App\Models\OrderItem;
use App\Models\Subscription;
use App\Models\Coupon;
use App\Models\Transactions;
use Cart;
use Carbon\Carbon;
use App\Http\Requests\PaymentRequest;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;

class CustomerController extends Controller
{

    public function getInvoiceListing($token, Request $request)
    {
        if(Auth::guest() || $token != ''){
            $auth_user = JWTAuth::toUser($token);
            $user = Auth::loginUsingId($auth_user->id);

        }else{
            $user = Auth::user();
        }

        $items = Transactions::where('user_id', $user->id)->get();
        // dd($items);
        return view('frontend.invoice.list', compact('items'));
    }

    public function getInvoiceView($id, Request $request)
    {
        if($id != ''){
            $item = Transactions::find($id);
            $item->invoice_id = 'MyMA-'.$item->merchant_name.'-'.$item->id;
            
            if($item && $item->type == 'food'){
                $order = Order::findOrFail($item->ref_id);
                if($order){
                  $restaurant = [];
   
                  foreach($order->items as $item){
                      if($item->item && $item->item->restaurant){
                          $restaurant = $item->item->restaurant;
                          break;
                      }
                  }
                  $start_date = $end_date = '';
                  if($order->type == 'package'){
                    $start = Subscription::where('order_id', $order->id)->orderby('delivery_date','asc')->first();
                    $end = Subscription::where('order_id', $order->id)->orderby('delivery_date','desc')->first();
                    if($start)
                      $start_date = Carbon::parse($start->delivery_date)->format('d/m/Y');
                    if($end)
                      $end_date = Carbon::parse($end->delivery_date)->format('d/m/Y');
                  }
                  if($order && $order->type == 'single'){
                    $order->delivery_time = Carbon::parse($order->delivery_time)->format('h:i A');
                  }
                  return view('frontend.customer.order_detail', compact('auth_user', 'order', 'restaurant', 'start_date', 'end_date'));
                
                } 
            }
        }else{
            abort('404');
        }

        return view('frontend.invoice.view', compact('item'));
    }

    public function getInvoicePrint($id, Request $request)
    {
        if($id != ''){
            $item = Transactions::find($id);
            $item->invoice_id = 'MYMA-'.$item->merchant_name.'-'.$item->id;
        }else{
            abort('404');
        }

        return view('frontend.invoice.print', compact('item'));
    }

    public function getDashboard(Request $request)
    {
        $token = Input::get('token');
        if(Auth::guest() || $token != ''){
            $auth_user = JWTAuth::toUser($token);
            $user = Auth::loginUsingId($auth_user->id);

        }else{
            $user = Auth::user();
        }

        Cart::restore(Auth::id());
        $cart_count = Cart::count();

        $restaurant_ids = Restaurant::where('blocked', '1')->pluck('id');

        $cuisine = FoodCategory::where('approved', '1')->orderBy('order', 'asc')->limit(4)->get();
        $recommended = FoodMenu::select('id','image')->where('recommended', '1')->whereNotIn('restaurant_id', $restaurant_ids)->get();
        if($recommended->count() == 0){
            $recommended = FoodMenu::where('recommended', '0')->limit(1)->get();
        }else{
          foreach ($recommended as $value) {
            $imgs = explode(',', $value->image);
             foreach ($imgs as $ky => $val) {
              if($val != ''){
                $value->image = $val;
                break;
              }
             }
          }
        }
        // dd($recommended);
        $ads = Advertisement::where('status', 'running')->where('type', 'food')->get();
        // if(!$ads){
        //   $ads = Advertisement::where('type', 'food')->first();
        // }

        $menu = FoodMenu::where('published', '1')->whereNotIn('restaurant_id', $restaurant_ids)->orderBy('id');

        if($category_name = Input::get('type')){
            $category = FoodCategory::where('slug', strtolower($category_name))->first();
            if($category){
                $category_id = $category->id;
                $menu->whereHas('tags', function($q) use($category_id){
                    $q->where('category_id', $category_id);
                });
            }
        }

        if($search = Input::get('search')){
            $search = strtolower($search);
            $menu->where(function($q) use($search){
                $q->where('name', 'like', '%'.$search.'%')->orWhere('description', 'like', '%'.$search.'%')
                ->orWhereHas('tags', function($qq) use($search){
                    $qq->whereHas('category', function($qqq) use($search){
                        $qqq->where('slug', 'like', '%'.$search.'%');
                    });
                });
            });
            $category_name = $search;
        }

        $menu = $menu->get();

        foreach($menu as $item){
            $tt = [];
            foreach($item->tags as $tag){
              if($tag->category){
                $tt[] = $tag->category->name;
              }else{
                // dd($tag);
              }

            }
            $item->tags_text = implode(' - ',$tt);

            $percent = 0;
            if($item->total_orders != '' && $item->total_rating != ''){
                $percent = number_format(($item->total_rating/($item->total_orders*6) * 100),0);
            }
            $item->percent = $percent;
            $item->ratings = $item->total_orders;

            if($item->image){
                $img = explode(',', $item->image);
                $item->image = url($img[0]);
                // $item->image = url($item->image);
            }else{
                $item->image = url('customer/images/img_place.png');
            }

        }

        $cats = FoodCategory::where('approved','1')->pluck('name', 'slug');

        return view('frontend.customer.home', compact('user', 'ads', 'cuisine', 'recommended', 'cart_count', 'menu', 'category_name', 'cats'));
    }

    public function addCart(Request $request)
    {
        $id = $request->input('id');
        $qty = $request->input('qty', 1);
        $item = FoodMenu::findOrFail($id);
        Cart::restore(Auth::id());

        $cart = Cart::content();
        $type = '';
        foreach($cart as $cart_item){
            $type = $cart_item->options['type'];
            break;
        }
        if($type != ''){
            session(['item_type' => $type]);
        }
        if($type != '' && ($type != $item->type)){
            $data['success'] = false;
            $data['message'] = 'You can select either a package or a la carte in a single checkout.';
            return json_encode($data);
        }

        $cartItem = Cart::add($id, $item->name, $qty, $item->price, ['type' => $item->type]);
        $cartItem->associate('FoodMenu');

        Cart::store(Auth::id());
        $data['success'] = true;
        $data['content'] = Cart::content();
        $data['count'] = Cart::count();
        return json_encode($data);
    }

    public function updateCart(Request $request)
    {
        $id = $request->input('id');
        $qty = $request->input('qty', 1);
        Cart::restore(Auth::id());
        // $item = FoodMenu::findOrFail($id);
        $cartItem = Cart::update($id, $qty);
        Cart::store(Auth::id());
        $data['content'] = Cart::content();
        $data['count'] = Cart::count();
        $data['total'] = Cart::total();
        return json_encode($data);
    }

    public function removeCart(Request $request)
    {
        $id = $request->input('id');
        Cart::restore(Auth::id());
        $cartItem = Cart::remove($id);
        // $cartItem = Cart::update($id, $qty);

        Cart::store(Auth::id());
        $data['content'] = Cart::content();
        $data['count'] = Cart::count();
        $data['total'] = Cart::total();
        return json_encode($data);
    }

    public function getDiscount(Request $request)
    {
        $auth_user = Auth::user();
        $coupons = Coupon::whereDate('expiry', '>=', Carbon::now())->get();

        return view('frontend.customer.discount', compact('auth_user', 'coupons'));
    }

    public function applyDiscount(Request $request)
    {
        $auth_user = Auth::user();
        $coupons = Coupon::whereDate('expiry', '>=', Carbon::now())->get();

        return view('frontend.customer.discount', compact('auth_user', 'coupons'));
    }

    public function getMyOrder(Request $request)
    {
        $auth_user = Auth::user();
        $month_start = Carbon::now()->startOfMonth()->toDateString();
        $month_end = Carbon::now()->toDateString();

        $orders_p = Order::where('type', 'package')->where('user_id', $auth_user->id)
        ->where('delivery_date', '>=',$month_start)->where('delivery_date', '<=', $month_end)->orderBy('created_at', 'desc')->get();
        $order_ids = [];

        $now = Carbon::now();
        $time = $now->toTimeString();
        $pass = false;
        $label = '';
        $breakfast = $lunch = $dinner = false;
        // if($time > "19:00:00"){
        //     $now->addDay();
        //     $pass = true;
        // }
        if($time > "19:00:00" || $time <= "07:00:00"){
            $time = "07:00:00";
            $label = 'b_status';
            $breakfast = true;
        }
        elseif($time > "07:00:00" && $time <= "12:00:00"){
            $time = "12:00:00";
            $label = 'l_status';
            $lunch = true;
        }
        elseif($time > "12:00:00" && $time <= "24:00:00"){
            $time = "19:00:00";
            $label = 'd_status';
            $dinner = true;
        }

        foreach($orders_p as $order){
          $sub = Subscription::where('order_id', $order->id)->where('delivery_date', $month_end)->first();
          $order->created_at = $month_end;
          if($sub){
            $order->status_id = $sub->{$label};

          }
          if($order->status_id == ''){
            $order->status_id = 8;
          }
          $order_ids[] = $order->id;
        }

        $orders_c = Order::where('type', 'single')->where('user_id', $auth_user->id)
        ->where('delivery_date', '>', $month_end)->orderBy('created_at', 'desc')->get();
        $order_ids_c = $orders_c->pluck('id');

        $orders = Order::whereNotIn('id', $order_ids)->whereNotIn('id', $order_ids_c)->where('user_id', $auth_user->id)->orderBy('created_at', 'desc')->get();

        return view('frontend.customer.my_order', compact('auth_user', 'orders', 'orders_p', 'orders_c'));
    }

    public function getOrderDetail($id, Request $request)
    {
        $auth_user = Auth::user();
        $order = Order::findOrFail($id);
        $restaurant = [];

        // {
        //   'name' => 'name',
        //   'address' => 'address'
        // };
        foreach($order->items as $item){
            if($item->item && $item->item->restaurant){
                $restaurant = $item->item->restaurant;
                break;
            }
        }
        $start_date = $end_date = '';
        if($order->type == 'package'){
          $start = Subscription::where('order_id', $order->id)->orderby('delivery_date','asc')->first();
          $end = Subscription::where('order_id', $order->id)->orderby('delivery_date','desc')->first();
          if($start)
            $start_date = Carbon::parse($start->delivery_date)->format('d/m/Y');
          if($end)
            $end_date = Carbon::parse($end->delivery_date)->format('d/m/Y');
        }
        if($order && $order->type == 'single'){
          $order->delivery_time = Carbon::parse($order->delivery_time)->format('h:i A');
        }
        return view('frontend.customer.order_detail', compact('auth_user', 'order', 'restaurant', 'start_date', 'end_date'));
    }

    public function getInvoice($id, Request $request)
    {
        $auth_user = Auth::user();
        $order = Order::findOrFail($id);
        if($order->invoice_id == ''){
          $invoice = 'NS-';
          $restaurant_name = '';
          foreach($order->items as $items){
            $restaurant_name = @$items->item->restaurant->name;
            if($restaurant_name != ''){
              break;
            }
          }
          $restaurant_name = str_replace(' ','', strtoupper($restaurant_name));
          $invoice .= $restaurant_name.'-'.$order->id;
          $order->invoice_id = $invoice;
        }
        $start_date = $end_date = '';
        if($order->type == 'package'){
          $start = Subscription::where('order_id', $order->id)->orderby('delivery_date','asc')->first();
          $end = Subscription::where('order_id', $order->id)->orderby('delivery_date','desc')->first();
          if($start)
            $start_date = Carbon::parse($start->delivery_date)->format('d/m/Y');
          if($end)
          $end_date = Carbon::parse($end->delivery_date)->format('d/m/Y');
        }
        $restaurant = [];

        foreach($order->items as $item){
            if($item->item && $item->item->restaurant){
                $restaurant = $item->item->restaurant;
                break;
            }
        }

        return view('frontend.customer.invoice', compact('auth_user', 'order', 'restaurant', 'start_date','end_date'));
    }

    public function getPrintInvoice($id, Request $request)
    {

        $order = Order::findOrFail($id);
        if($order->invoice_id == ''){
          $invoice = 'NS-';
          $restaurant_name = '';
          foreach($order->items as $items){
            $restaurant_name = @$items->item->restaurant->name;
            if($restaurant_name != ''){
              break;
            }
          }
          $restaurant_name = str_replace(' ','', strtoupper($restaurant_name));
          $invoice .= $restaurant_name.'-'.$order->id;
          $order->invoice_id = $invoice;
        }
        $restaurant = [];

        foreach($order->items as $item){
            if($item->item && $item->item->restaurant){
                $restaurant = $item->item->restaurant;
                break;
            }
        }

        return view('frontend.customer.print_invoice', compact('order', 'restaurant'));
    }

    public function orderAgain(Request $request)
    {
        $id = $request->input('id');
        $auth_user = Auth::user();
        Cart::restore($auth_user->id);
        $order = Order::findOrFail($id);
        $data['msg'] = "Invalid request.";
        if($order){
            foreach($order->items as $item){
                Cart::add($item->id, $item->item->name, $item->quantity, $item->item->price);
            }
            $data['msg'] = "Product added to cart.";
        }
        Cart::store($auth_user->id);

        return json_encode($data);


    }

    public function addRating(Request $request)
    {
        $id = $request->input('item_id');
        $score = $request->input('score');
        $auth_user = Auth::user();
        $order = Order::findOrFail($id);
        $data['msg'] = "Invalid request.";
        $data['status'] = false;
        if($order){
            foreach($order->items as $item){
                $rate = $item->item->total_rating;
                $rate += $score;

                $total_orders = $item->item->total_orders + 1;
                $dd = $item->item->update(['total_orders' => $total_orders, 'total_rating' => $rate]);

            }
            $order->update(['rating' => $score]);
            $data['msg'] = "Rated.";$data['status'] = true;
        }

        return json_encode($data);
    }

    public function getCuisine(Request $request)
    {
        $cuisine = FoodCategory::where('approved', '1')->orderBy('order', 'asc')->get();

        return view('frontend.customer.cuisine_list', compact('user','cuisine'));
    }

    public function getFoodListing(Request $request)
    {
        $restaurant_ids = Restaurant::where('blocked', '1')->pluck('id');
        $menu = FoodMenu::where('published', '1')->whereNotIn('restaurant_id', $restaurant_ids);

        if($category_name = Input::get('type')){
          if(strtolower($category_name) == 'catering'){

            // $category = FoodCategory::where('slug', strtolower($category_name))->first();
            // if($category){
                // $category_id = $category->id;
                $menu->where('type', 'package');

            // }
            $menu->orderBy('sequence', 'asc');
          }else{
            $category = FoodCategory::where('slug', strtolower($category_name))->first();
            if($category){
                $category_id = $category->id;
                $menu->whereHas('tags', function($q) use($category_id){
                    $q->where('category_id', $category_id);
                })->where('type', '!=','package');
            }
            $menu->orderBy('id', 'desc');
          }

        }

        $menu = $menu->get();
        // if($_SERVER['REMOTE_ADDR'] == '202.150.214.66'){
        //     dd($menu);
        // }
        foreach($menu as $item){
            $tt = [];
            foreach($item->tags as $tag){
                $tt[] = $tag->category->name;
            }
            $item->tags_text = implode(' - ',$tt);

            $percent = 0;
            if($item->total_orders != '' && $item->total_rating != ''){
                $percent = number_format(($item->total_rating/($item->total_orders*6) * 100),0);
            }
            $item->percent = $percent;
            $item->ratings = $item->total_orders;

            if($item->image){
                $img = explode(',', $item->image);
                $ii = url('customer/images/img_place.png');
                foreach($img as $is){
                  if($is != ''){
                    $ii = $is;
                    break;
                  }
                }
                $item->image = url($ii);
                // $item->image = url($item->image);
            }else{
                $item->image = url('customer/images/img_place.png');
            }
        }

        $cats = FoodCategory::where('approved', '1')->pluck('name', 'slug');

        return view('frontend.customer.food_list', compact('user', 'menu', 'category_name', 'cats'));
    }

    public function getFoodDetail($id, Request $request)
    {
        $item = FoodMenu::findOrFail($id);
        $tt = [];
        $item->tags_text = '';

        foreach($item->tags as $tag){
            $tt[] = $tag->category->name;
        }
        $item->tags_text = implode(' - ',$tt);
        $item->tags_array = $tt;

        $percent = 0;
        if($item->total_orders != '' && $item->total_rating != ''){
            $percent = number_format(($item->total_rating/($item->total_orders*6) * 100),0);
        }
        $item->percent = $percent;
        $item->ratings = $item->total_orders;

        if($item->image){
             $img = explode(',', $item->image);
             $ig = [];
            foreach($img as $ii){
              if($ii != ''){
                $ig[] = $ii;
              }
            }
            $item->image = $ig;
            // $item->image = url($item->image);
        }else{
            $item->image = ['customer/images/img_place.png'];
        }

        return view('frontend.customer.food_detail', compact('user', 'item'));
    }

    public function getCart(Request $request)
    {
        $request->session()->forget('is_logged_in');
        Cart::restore(Auth::id());
        Cart::store(Auth::id());

        $cart = Cart::content();
        $discount = false;
        $dicount_value = [];
        $dicount_type = [];
        $dis_val = 0;
        foreach($cart as $cart_item){

            // $item = FoodMenu::findOrFail($cart_item->id);
            if(isset($cart_item->options['discount_type']) && $cart_item->options['discount_type'] != ""){
                $discount = true;
                $discount_type =  $cart_item->options['discount_type'];
                $discount_value =  $cart_item->options['value'];
                $item_price = $cart_item->price;
                if($discount_type == 'direct'){
                    $dis_val += $discount_value;
                }else{
                    $dis_val += ($item_price*$discount_value)/100;
                }

                // break;
            }
        }

        foreach($cart as $cart_item){
            $item = FoodMenu::find($cart_item->id);
            if($item && $item->image){
                $image = explode(',', $item->image);
                $ii =  'customer/images/img_place.png';
                foreach($image as $is){
                  if($is != ''){
                    $ii = $is;
                    break;
                  }
                }
                $cart_item->image = $ii;
            }else{
                $cart_item->image = 'customer/images/img_place.png';
            }
        }
        // dd($cart);
        // $dis_val = 0;

        $sub_total = Cart::total();
        $total = str_replace(',','',$sub_total);
        // if($discount){
        //     if($discount_type == 'direct'){
        //         $dis_val = $discount_value;
        //     }else{
        //         $dis_val = ($total*$discount_value)/100;
        //     }
        // }
        return view('frontend.customer.cart', compact('user', 'cart', 'total', 'dis_val', 'discount', 'sub_total'));
    }

    public function getCheckout(Request $request)
    {
        $charges = 0;
        $count = Cart::count();
        if($count < 1){
            return redirect()->route('food.customer.cart');
        }
        $auth_user = Auth::user();
        $dormitory = Dormitory::pluck('name','id')->all();
        $cart_total = Cart::total();
        $sub_total = $total = str_replace(',', '', $cart_total);

        $cart = Cart::content();
        $type = '';
        foreach($cart as $cart_item){
            $item = FoodMenu::findOrFail($cart_item->id);
            if($item){
                $type = $cart_item->options['type'];
                break;
            }
            else{
                return redirect()->back()
                    ->with([
                        'flash_level'   => 'error',
                        'flash_message' => 'Review your cart. Some products have been removed',
                    ]);
            }
        }
        $discount = false;
        $dis_val = 0;
        foreach($cart as $cart_item){
          if(isset($cart_item->options['discount_type']) && $cart_item->options['discount_type'] != ""){
              $discount = true;
              $discount_type =  $cart_item->options['discount_type'];
              $discount_value =  $cart_item->options['value'];
              $item_price = $cart_item->price;
              if($discount_type == 'direct'){
                  $dis_val += $discount_value;
              }else{
                  $dis_val += ($item_price*$discount_value)/100;
              }

              // break;
          }
        }

        $merchant = '';
        if($type == 'single'){
          $merchant = Merchant::findOrFail(12);
        }else{
          $merchant = Merchant::findOrFail(13);
        }
        $flexm_per = 0;
        if($merchant){
          $flexm_per = $merchant->myma_transaction_share;
        }
        if($flexm_per == 0){
          $flexm_per = 2.5;
        }

        if($discount){
            $total = $total-$dis_val;
        }

        //timing blocked to have a diffrence of etleast 24 hour to place an order
        $day = 1;
        $block = ['07:00 AM' => '07:00 AM', '12:00 PM' => '12:00 PM', '19:00 PM' => '19:00 PM'];
        //if($type == 'single'){
            $time = Carbon::now()->toTimeString();
            if($time >= "19:00:00"){
                $day = 2;
            }elseif($time >= "12:00:00" && $time < "19:00:00"){
                $block = ['07:00 PM' => '07:00 PM'];
            }elseif($time >= "00:00:00" && $time < "12:00:00"){
                $block = ['07:00 AM' => '07:00 AM', '12:00 PM' => '12:00 PM'];
            }
        //}
        if($type == 'package'){
            $day = 1;
        }
        //end

        $pay = 0;
        $is_logged_in = 0;
        if($request->session()->has('is_logged_in')){
            $pay = 1;
            $is_logged_in = 1;
            $naanstap = session('cart_data.naanstap');
            if($naanstap != '' || $naanstap != 0){
              $total += $naanstap;
            }
        }
        if($request->session()->has('charges')){
            $charges = session('charges');
        }
        if($request->session()->has('wallet')){
            $wallet = session('wallet');
        }

        $naanstap_std_charge = getOption('dormitory_standard_rate', 0);
        $saved_address = [];
        $saved_address = Address::where('user_id', $auth_user->id)->get();

        return view('frontend.customer.checkout', compact('charges','is_logged_in', 'pay', 'block', 'auth_user', 'dormitory', 'sub_total',
        'total', 'type', 'discount', 'dis_val', 'day', 'wallet', 'naanstap_std_charge', 'flexm_per', 'saved_address'));
    }

    public function postPayment(PaymentRequest $request)
    {
      // dd($request->all());
        $pay = $request->pay;
        $delivery_type = $request->deliver_type;
        $data['delivery_date'] = $request->delivery_date;
        session(['checkout.delivery_date' => $data['delivery_date']]);

        $start = explode('/',$data['delivery_date']);
        if(count($start) > 0){
            $start = \Carbon\Carbon::create($start[2],$start[1],$start[0]);
        }else{
            $start = \Carbon\Carbon::parse($start);
        }

        $data['delivery_date'] = $start->toDateString();
        $delivery_time = $request->delivery_time;
        session(['checkout.delivery_time' => $delivery_time]);
        if($delivery_time != ''){
            $delivery_time = date('H:00:00', strtotime($delivery_time));
        }
        $data['delivery_time'] = $delivery_time;
        $data['delivery_type'] = $delivery_type;
        $data['phone_no'] = str_replace('-','',$request->phone_no);
        if($delivery_type == 'reception'){
            $data['dormitory_id'] = $request->dormitory_id;
            $data['naanstap'] = 0;
        }else if($delivery_type == 'inperson'){
            $data['dormitory_id'] = $request->dormitory_id;
            $data['naanstap'] = $request->naanstap;
        }else{
          if($request->saved_address_id != ''){
            $address = Address::find($request->saved_address_id);
            $data['address'] = $address->address;
            $data['block_no'] = $address->block;
            $data['latitude'] = $address->latitude;
            $data['longitude'] = $address->longitude;
          }else{
            $data['address'] = $request->address;
            $data['block_no'] = '#'.$request->block_no_1.'-'.$request->block_no_2;
            $data['latitude'] = $request->latitude;
            $data['longitude'] = $request->longitude;
          }
          $data['naanstap'] = $request->naanstap;
          $data['distance'] = $request->distance;
        }

        $dormitory_id = $request->dormitory_id;
        // $batch = Batch::where('dormitory_id', $dormitory_id)->orderBy('created_at','desc')->first();

        $cart = Cart::content();
        $type = '';
        foreach($cart as $cart_item){
            $type = $cart_item->options['type'];
            break;
        }
        $coupon_id = '';
        $discount = false;
        $dis_val = 0;
        $coupon_id = '';
        foreach($cart as $cart_item){
          if(isset($cart_item->options['discount_type']) && $cart_item->options['discount_type'] != ""){
              $discount = true;
              $discount_type =  $cart_item->options['discount_type'];
              $discount_value =  $cart_item->options['value'];
              if($coupon_id == ''){
                $coupon_id =  $cart_item->options['coupon_id'];
              }
              $item_price = $cart_item->price;
              if($discount_type == 'direct'){
                  $dis_val += $discount_value;
              }else{
                  $dis_val += ($item_price*$discount_value)/100;
              }
          }
        }

        $sub_total = Cart::total();

        if($discount){
            // if($discount_type == 'direct'){
            //     $dis_val = $discount_value;
            // }else{
            //     $dis_val = ($sub_total*$discount_value)/100;
            // }
            $sub_total = $sub_total-$dis_val;
        }
        $charges = 0;

        $total = $sub_total+$data['naanstap'];
        $flexm_per = getOption('flexm_charges_app', '0.75');

        $cartItems = Cart::content();
        $merchant = '';
        if($type == 'single'){
          $merchant = Merchant::findOrFail(12);
        }else{
          $merchant = Merchant::findOrFail(13);
        }

        $food_merchant = '';
        foreach($cartItems as $item){
          $item_id = $item->id;
          $food = FoodMenu::find($item_id);
          if($food){
            $user_id = @$food->restaurant->merchant_id;
            if($user_id){
              $food_merchant = FoodMerchant::where('user_id', $user_id)->first();
              break;
            }
          }
        }
        if($food_merchant == ""){
          return redirect()->back()->with([
            'flash_level'   => 'danger',
            'flash_message' => 'Food merchant does not exist.',
          ]);
        }

        $myma_txns_share = 0;
        $wlc_share = 0;
        if($merchant){
          $myma_txns_share = $merchant->myma_transaction_share;
          $wlc_share = $merchant->merchant_share;
        }
        if($myma_txns_share == 0){
          $myma_txns_share = 2.5;
        }
        if($wlc_share == 0){
          $wlc_share = 8;
        }

        $flexm_charges = $myma_txns_share;

        // $charges = ($total/(1+($flexm_charges/100)));
        // $charges = $total-$charges;
        $charges = $total*($flexm_charges/100);

        session(['charges' => $charges]);
        session(['txns.total_per' => $flexm_charges]);
        session(['txns.flexm_per' => $flexm_per]);
        session(['txns.merchant_share' => $wlc_share]);
        session(['txns.merchant' => $merchant]);
        session(['txns.food_merchant' => $food_merchant]);

        $data['transaction_id'] = '';
        $data['user_id'] = Auth::id();
        $data['status_id'] = 6;
        $data['type'] = $type;
        $data['total'] = $total;//+$charges;
        $data['flexm'] = $charges;
        $data['discount'] = $dis_val;
        $data['coupon_id'] = $coupon_id;
        // $data['batch_id'] = $batch->batch_b;

        if($pay){
            $order = Order::create($data);
            // \Event::fire('order.created', [$order->id]);

            $cartItems = Cart::content();
            foreach($cartItems as $item){

                $dd['order_id'] = $order->id;
                $dd['item_id'] = $item->id;
                $dd['item_price'] = $item->price;
                $dd['quantity'] = $item->qty;

                OrderItem::create($dd);
            }
            Cart::restore(Auth::id());
            Cart::destroy();

            $request->session()->forget('cart_data');
            $request->session()->forget('checkout');
            $request->session()->forget('is_logged_in');

            return redirect()->route('food.customer.payment.success');
        }else{
            session(['cart_data' => $data]);
            //return $this->postPaymentPageLocal($total);
            return redirect()->route('flexm.login');
        }
    }

    public function getAddress(Request $request)
    {

        $client = new Client(); //GuzzleHttp\Client
        $skip = 0;
        $pageNo = $request->input('page');
        $term = $request->input('q');
        $result = $client->get('https://developers.onemap.sg/commonapi/search?searchVal='.$term.'&returnGeom=Y&getAddrDetails=Y&pageNum='.$pageNo);
        $code = $result->getStatusCode(); // 200
        $reason = $result->getReasonPhrase(); // OK
        if($code == "200" && $reason == "OK"){
            $body = $result->getBody();
            $content = json_decode($body->getContents());
            if($content->found){
                $data['total_count'] = $content->found;
                $data['total_pages'] = $content->totalNumPages;
                $data['current_page'] = $content->pageNum;
                $i = 1;
                foreach($content->results as $row){
                    $row->id = $i;
                    $data['items'][] = $row;
                    $i++;
                }

                return json_encode($data);
            }
            else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function getFlexmLogin(Request $request)
    {
        return view('frontend.customer.flexm');
    }

    public function getPaymentPage(Request $request)
    {
        return view('frontend.customer.payment');
    }

    public function make_payment($flexm_token, $merchant, $total = 0, $desc = ''){
      $BASE_URL = config('app.flexm_end_point');

      $merchant_code = $merchant->merchant_code;
      try{
          $terminal = Terminal::where('merchant_id', $merchant->id)->where('payment_mode', 1)->first();
          $data['mid'] = $merchant->mid;
          $data['tid'] = $terminal->tid;
          $data['tran_amount'] = $total; //
          $data['description'] = $desc; //
          $data['status'] = 1;
          $data['check'] = md5($data['mid'].''.$data['tid']);

          $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);

          $result = $client->post($BASE_URL.'user/wallets/payment/token',[
              'json' => $data
          ]);

          $code = $result->getStatusCode(); // 200
          $reason = $result->getReasonPhrase(); // OK
          if($code == "200" && $reason == "OK"){
              $body = $result->getBody();
              $content = json_decode($body->getContents());
              if($content->success){
                  $msg = $content->message;
                  $cont = $content->data;

                  if($cont){
                      $merchant_token = $cont->token;

                      $dataa['amount'] = $total;
                      $dataa['currency_code'] = 702;
                      $dataa['merchant_code'] = $merchant_code;
                      // $dataa['wallet_type_indicator'] = 'centurion';
                      $dataa['token'] = $merchant_token;

                      $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);

                      $result = $client->post($BASE_URL.'user/wallets/payment/auth', [
                          'json' => $dataa
                      ]);

                      $code = $result->getStatusCode(); // 200
                      $reason = $result->getReasonPhrase(); // OK
                      if($code == "200" && $reason == "OK"){
                          $body = $result->getBody();
                          $content = json_decode($body->getContents());
                          if($content->success){
                              $msg = $content->message;
                              $cont = $content->data;
                              return ['status' => true, 'data' => $cont, 'message'=> 'Payment has been made.'];

                          }else{
                              return ['status' => false, 'data' => $content, 'message'=> $content->message];
                          }
                      }
                      return ['status' => false, 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'];
                  }
                  return ['status' => false, 'data' => $cont, 'message'=> $msg];
              }else{
                  return ['status' => false,'data' => $content, 'message'=> $content->message];
              }
          }
          return ['status' => false, 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'];
      }catch (BadResponseException $ex) {
          $response = $ex->getResponse();
          $jsonBody = json_decode((string) $response->getBody());
          \Log::debug(json_encode($jsonBody));
          $msg = '';
          if(@$jsonBody->errors){
            foreach($jsonBody->errors as $err){
              $msg = $err[0];
              break;
            }
          }
          if($msg == ''){
            $msg = @$jsonBody->message;
          }
          return ['status' => false, 'data' => @$jsonBody->errors, 'message'=> $msg];
      }catch(GuzzleException $e){
          return ['status' => false, 'data' => '', 'message'=> $e->getMessage()];
      }catch(Exception $e){
          return ['status' => false, 'data' => '', 'message'=> $e->getMessage()];
      }

    }

    public function postPaymentPage(Request $request)
    {
        $auth_user = Auth::user();
        $startdate = Carbon::now()->startOfMonth();
        $enddate = Carbon::now()->endOfMonth();
        // DB::beginTransaction();
        // dd(session('cart_data'));
        if($request->session()->has('cart_data')){

            $wallet = session('wallet');
            $data = session('cart_data');

            $charges = session('charges', 0);
            $merchant = session('txns.merchant');
            $food_merchant = session('txns.food_merchant');

            $total = $request->total;

            if($total > $wallet){
              return redirect()->back()->with([
                'flash_level'   => 'danger',
                'flash_message' => 'Insufficient wallet balance.',
              ]);
            }
            \Log::debug($data);
            $order = Order::create($data);
            $desc = "Food order #".$order->id;
            $flexm_token = session('flexm_token');

            $cont = $this->make_payment($flexm_token, $merchant, $total, $desc);


            if(@$cont['status'] == false){
              $od = Order::find($order->id);
              if($od){
                $od->delete();
              }
              if(strpos($cont['message'], 'Please login again') !== false){
                  return redirect()->route('flexm.login.page')->with([
                    'flash_level'   => 'danger',
                    'flash_message' => $cont['message'],
                  ]);
              }else{
                  return redirect()->back()->with([
                    'flash_level'   => 'danger',
                    'flash_message' => $cont['message'],
                  ]);
              }
            }

            // \Event::fire('order.created', [$order->id]);

            $cartItems = Cart::content();
            foreach($cartItems as $item){

                $dd['order_id'] = $order->id;
                $dd['item_id'] = $item->id;
                $dd['item_price'] = $item->price;
                $dd['quantity'] = $item->qty;

                OrderItem::create($dd);
            }
            if(isset($data['address']) && $data['address'] != ''){
                $add = Address::where('address', $data['address'])->first();
                if(!$add){
                  Address::create([
                    'user_id' => $data['user_id'],
                    'address' => $data['address'],
                    'block' => $data['block_no'],
                    'latitude' => $data['latitude'],
                    'longitude' => $data['longitude']
                  ]);
                }
            }

            $total_per = session('txns.total_per');
            $flexm_per = session('txns.flexm_per');
            $actual_per = $flexm_per/$total_per;

            $flexm_part = $charges*$actual_per;
            $myma_part = $charges - $flexm_part;

            $wlc_share = session('txns.merchant_share');
            $gst_tax = getOption('gst_tax', '7');
            $gst = 0;//($data['total']*$gst_tax/100);

            //after deducting 8% of wlc
            $naanstap_pay = 0;
            $naanstap_share_per = $food_merchant->naanstap_share;
            if($naanstap_share_per == ""){
              $naanstap_share_per = 10;
            }
            if($data['type'] == 'package'){
                $myma_share = 0;
                $sub_tl = 0;
                $ord = Order::whereDate('created_at', '>=', $startdate)->whereDate('created_at', '<=', $enddate)->where('type', 'package')->get();
                foreach($ord as $or){
                  foreach($or->items as $it){
                    $sub_tl += $it->quantity;
                  }
                }
                $total_items = 0;
                foreach($order->items as $or_it){
                  $total_items += $or_it->quantity;
                }

                if($sub_tl > $food_merchant->sub_limit){
                    $myma_share = $total_items * ($food_merchant->per_sub_price - $food_merchant->naanstap_share);
                    $naanstap_pay = $total_items * $food_merchant->naanstap_share;
                }
                $gst = (($myma_share+$charges)*$gst_tax)/100;
                $merchant_share = $data['total'] - $gst - $charges - $myma_share;

            }else{

                $myma_share = $data['total']*($wlc_share/100);//need to subtract delivery charges
                $gst = (($myma_share+$charges)*$gst_tax)/100;

                $merchant_share = $data['total'] - $charges - $myma_share-$gst;

                $naanstap_pay = $merchant_share/(1+($naanstap_share_per/100));
                \Log::debug('one');
                \Log::debug($naanstap_pay);
                $naanstap_pay = $merchant_share - $naanstap_pay;
                \Log::debug('two');
                \Log::debug($naanstap_pay);
            }
            $naanstap_merchant_share = $merchant_share - $naanstap_pay;
            \Log::debug('three');
            \Log::debug($naanstap_merchant_share);

            $terminal = Terminal::where('merchant_id', $merchant->id)->where('payment_mode', '1')->first();

            $clientt = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);

            $BASE_URL = config('app.flexm_end_point');
            $resultt = $clientt->get($BASE_URL.'user/profile?debug=false');

            $codee = $resultt->getStatusCode(); // 200
            $reasonn = $resultt->getReasonPhrase(); // OK
            $mobile_no = isset(Auth::user()->profile)?Auth::user()->profile->phone:'';
            $user_name = '';
            if($codee == "200" && $reasonn == "OK"){
                $bodyy = $resultt->getBody();
                $contentt = json_decode($bodyy->getContents());
                if($contentt->success){
                    $msgg = $contentt->message;
                    $contt = $contentt->data;
                    $mobile_no = $contt->mobile;
                    $user_name = @$contt->profile->full_name;
                }
            }

            $trans = Transactions::create([
                'type' => 'food',
                'description' => @$desc,
                'ref_id' => $order->id,
                'phone_no' => $mobile_no,
                'wallet_user_name' => $user_name,
                'transaction_date' => $cont['data']->created_at,
                'transaction_amount' => $cont['data']->amount,
                'transaction_currency' => 'SGD',
                'transaction_ref_no' => $cont['data']->ref_id,
                'transaction_status' => $cont['data']->status_description,
                'transaction_code' => $cont['data']->status_code,
                'mid' => $merchant->mid,
                'tid' => $terminal->tid,
                'merchant_name' => $merchant->merchant_name,
                'food_merchant_id'  => @$food_merchant->id,
                'payment_mode' => 'InApp',
                'user_id' => Auth::id(),
                'flexm_part' => $flexm_part,
                'myma_part' => $myma_part,
                'myma_share' => $myma_share,
                'other_share' => $merchant_share,
                'food_share' => $naanstap_merchant_share,
                'naanstap_pay' => $naanstap_pay,
                'gst' => $gst,
                'response'  => json_encode($cont)
            ]);

            $order = Order::find($order->id);
            if($order->type == 'package'){
                $arr = [];
                foreach($order->items as $tt){

                  $breakfast = $tt->item->breakfast;
                  $lunch = $tt->item->lunch;
                  $dinner = $tt->item->dinner;
                  $max = max($breakfast, $lunch, $dinner);

                  $actual_date = Carbon::parse($order->delivery_date);
                  $delivery_date = Carbon::parse($order->delivery_date);
                  $end_date = Carbon::parse($order->delivery_date)->addDays($max-1);

                  if($end_date->month > $delivery_date->month){
                    //$end_date = $delivery_date->endOfMonth();
                  }
                  $b = $l = $d = 0;
                  while($actual_date <= $end_date){
                    if($b < $breakfast){
                      $bl = 1;
                      $b++;
                    }else{
                      $bl = 0;
                    }

                    if($l < $lunch){
                      $ll = 1;
                      $l++;
                    }else{
                      $ll = 0;
                    }

                    if($d < $dinner){
                      $dl = 1;
                      $d++;
                    }else{
                      $dl = 0;
                    }
                    $date = $actual_date->toDateString();
                    $dd = [
                      'delivery_date' => $date,
                      'order_id'      => $order->id,
                      'item_id'       => $tt->item_id,
                      'b_allowed'     => $bl,
                      'l_allowed'     => $ll,
                      'd_allowed'     => $dl
                    ];
                    \Log::debug($dd);
                    $subs = Subscription::where($dd)->first();
                    if(!$subs){
                      Subscription::create($dd);
                    }
                    $actual_date->addDay();
                  }
                }
            }
            $order->update(['transaction_id' => $cont['data']->ref_id, 'status_id' => 7, 'accepted' => '1']);
            $request->session()->forget('cart_data');
            $request->session()->forget('checkout');
            $request->session()->forget('charges');
            $request->session()->forget('is_logged_in');
            $request->session()->forget('txns');
            Cart::restore(Auth::id());
            Cart::destroy();

            $message = 'A new order has been placed #'.$order->id;
            $type = 'notifications';
            $id = '';
            $link = '';
            $user = User::where('id', $food_merchant->user_id)->first();
            if($user && $user->fcm_token){
              $ret = sendSingleLocal($user, $message, $type, $id, $link, 'merchant');
              \Log::debug($ret);
            }
            // DB::commit();
            return redirect()->route('food.customer.payment.success');
        }else{
            // DB::rollback();
            $request->session()->forget('cart_data');
            $request->session()->forget('checkout');
            $request->session()->forget('is_logged_in');
            $request->session()->forget('charges');
            return redirect()->route('food.customer.checkout');
        }
        // DB::rollback();

    }

    public function postPaymentPageLocal($total)
    {
        $auth_user = Auth::user();
        $startdate = Carbon::now()->startOfMonth();
        $enddate = Carbon::now()->endOfMonth();

        if(session()->has('cart_data')){

            $wallet = session('wallet');
            $data = session('cart_data');

            $charges = number_format(session('charges', 0),3);
            $merchant = session('txns.merchant');
            $food_merchant = session('txns.food_merchant');

            $total = $total;

            $order = Order::create($data);
            $desc = "Food order #".$order->id;
            $flexm_token = session('flexm_token');

            $cartItems = Cart::content();
            foreach($cartItems as $item){

                $dd['order_id'] = $order->id;
                $dd['item_id'] = $item->id;
                $dd['item_price'] = $item->price;
                $dd['quantity'] = $item->qty;
                OrderItem::create($dd);
            }

            $wlc_share = session('txns.merchant_share');
            $gst = 0;//($data['total']*7/100);

            $total_per = session('txns.total_per');
            $flexm_per = session('txns.flexm_per');
            $actual_per = $flexm_per/$total_per;

            $flexm_part = $charges*$actual_per;
            $myma_part = $charges - $flexm_part;
            $charges = number_format($charges,3);
            //after deducting 8% of wlc
            $naanstap_pay = 0;
            $naanstap_share_per = $food_merchant->naanstap_share;
            if($data['type'] == 'package'){
                $myma_share = 0;
                $sub_tl = 0;
                $ord = Order::whereDate('created_at', '>=', $startdate)->whereDate('created_at', '<=', $enddate)->where('type', 'package')->get();
                foreach($ord as $or){
                  foreach($or->items as $it){
                    $sub_tl += $it->quantity;
                  }
                }
                $total_items = 0;
                foreach($order->items as $or_it){
                  $total_items += $or_it->quantity;
                }

                if($sub_tl > $food_merchant->sub_limit){
                    $myma_share = $total_items * ($food_merchant->per_sub_price - $food_merchant->naanstap_share);
                    $naanstap_pay = $total_items * $food_merchant->naanstap_share;
                }
                $merchant_share = $data['total'] - $gst - $charges - $myma_share;

            }else{
              $del_charge = $data['naanstap'];
              if($del_charge > 0){
                  $del_charge = number_format(($data['naanstap']/(1+$total_per/100)), 2);
                  $del_flexm_charge = $data['naanstap']-$del_charge;
              }

              $myma_share = number_format((($data['total']-$data['naanstap'])*($wlc_share/100)),3);//need to subtract delivery charges
              $merchant_share = number_format(($data['total'] - $charges - $myma_share),3);

              $naanstap_pay = ($merchant_share-$del_charge)/(1+($naanstap_share_per/100));
              $naanstap_pay = number_format(($merchant_share - $naanstap_pay),3);
            }

            $naanstap_merchant_share = number_format(($merchant_share - $naanstap_pay),3);

            $trans = Transactions::create([
                'type' => 'food',
                'ref_id' => $order->id,
                'phone_no' => '',
                'wallet_user_name' => '',
                'transaction_date' => @$cont['data']->created_at,
                'transaction_amount' => $total,
                'transaction_currency' => 'SGD',
                'transaction_ref_no' => @$cont['data']->ref_id,
                'transaction_status' => @$cont['data']->status_description,
                'transaction_code' => @$cont['data']->status_code,
                'mid' => '',
                'tid' => '',
                'merchant_name' => @$merchant->merchant_name,
                'payment_mode' => 'InApp',
                'user_id' => Auth::id(),
                'flexm_part' => round($flexm_part,2),
                'myma_part' => round($myma_part,2),
                'myma_share' => round($myma_share,2),
                'other_share' => round($merchant_share,2),
                'food_share' => round($naanstap_merchant_share,2),
                'naanstap_pay' => round($naanstap_pay,2),
                'gst' => $gst,
                'response'  => ''
            ]);

            session()->forget('cart_data');
            session()->forget('checkout');
            session()->forget('charges');
            session()->forget('is_logged_in');
            session()->forget('txns');
            Cart::restore(Auth::id());
            Cart::destroy();
            // DB::commit();
            return redirect()->route('food.customer.payment.success');
        }else{
            // DB::rollback();
            $request->session()->forget('cart_data');
            $request->session()->forget('checkout');
            $request->session()->forget('is_logged_in');
            $request->session()->forget('charges');
            return redirect()->route('food.customer.checkout');
        }
        // DB::rollback();

    }

    public function loginFlexm(Request $request)
    {

        try{
            $app_url = config('app.url');

            $user = Auth::user();
            if((Auth::check() && $user->hasRole('app-user')) || session('spuul_user_id') == ''){
              // $token = $user->token;
              $token = JWTAuth::fromUser($user);
            }else{
                if(session('spuul_user_id')){
                  $user_id = session('spuul_user_id');
                  $user = User::find($user_id);
                  $token = JWTAuth::fromUser($user);

                }else{
                  abort('404');
                }
            }
            /*remove this when flexm login starts working*/
            // session(['flexm_token' => 'token']);
            // session(['is_logged_in'=> 1]);
            // return redirect()->route('food.customer.checkout');
            /*end*/
            $type = $request->input('type');

            $data['token'] = $token;
            $data['mobile_country_code'] = '65';
            $data['mobile'] = $request->input('mobile');
            $data['password'] = $request->input('password');
            // $data['user_type'] = 2;//web app user
            $data['device_signature'] = '{test}';

            $client = new Client();//['headers' => ['Content-type' => 'application/json']]

            $result = $client->post($app_url.'/api/v1/flexm/login', [
                'form_params' => $data,
            ]);

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                if($content->status == 'success'){
                    $msg = $content->message;
                    $flexm_token = $content->data->token;

                    session(['flexm_token' => $flexm_token]);
                    session(['is_logged_in'=> 1]);

                    //to get the wallet amount
                    $BASE_URL = config('app.flexm_end_point');;
                    $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);
                    $result = $client->get($BASE_URL.'user/wallets');

                    $code = $result->getStatusCode(); // 200
                    $reason = $result->getReasonPhrase(); // OK
                    if($code == "200" && $reason == "OK"){
                        $body = $result->getBody();
                        $content = json_decode($body->getContents());
                        if($content->success){
                            session(['wallet' => @$content->data->funds_available_amount]);
                        }
                    }
                    if($type == 'spuul'){
                        return redirect()->route('frontend.spuul.checkout');
                    }else{
                        return redirect()->route('food.customer.checkout');
                    }

                }else{
                    \Log::debug(json_encode($content->message));
                    return redirect()->back()->withErrors(
                        @$content->message
                    );
                }
            }
            return redirect()->back()->withErrors(
                'Something went wrong try again.'
            );

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());

            return redirect()->back()->withErrors(
                @$jsonBody->message
            );
        }catch(GuzzleException $e){
            return redirect()->back()->withErrors(
                $e->getMessage()
            );
            // dd($e->getMessage());
        }catch(Exception $e){

            return redirect()->back()->withErrors(
                $e->getMessage()
            );
        }

        // return view('frontend.customer.payment');
    }

    public function getPaymentSuccess(Request $request)
    {
        return view('frontend.customer.success');
    }

    public function getPackage(Request $request)
    {
        $order = Order::where('user_id', Auth::id())->pluck('id')->all();
        // dd($order);
        $orders = OrderItem::whereIn('order_id', $order)->whereHas('item', function($q){
            $q->where('type', 'package');
        })->join('orders', 'orders.id', 'order_items.order_id')->orderBy('orders.delivery_date', 'desc')
        ->select('order_items.*', 'orders.delivery_date')->get();
        $now = Carbon::now()->toDateString();
        foreach($orders as $order){
            $order->start_date = date('d/m/Y', strtotime($order->order->delivery_date));
            $end =  Carbon::now()->endOfMonth()->toDateString();

            $check_start = Carbon::parse($order->order->delivery_date);

            $start = Carbon::parse($order->order->delivery_date)->addDays(max($order->item->breakfast, $order->item->lunch, $order->item->dinner)-1);
            if($start < $end){
                $end = $start;
            }
            $order->end_date = date('d/m/Y', strtotime($end));

            $startt = Carbon::parse($order->order->delivery_date)->toDateString();
            $endd = Carbon::parse($end)->toDateString();
            if($now >= $startt && $now <= $endd){
              $order->status = 'Active';
            }
            if($now < $startt){
              $order->status = 'Upcoming';
            }
        }
        return view('frontend.customer.package', compact('user', 'orders'));
    }

    public function getSubscription($id, Request $request)
    {
        $order = OrderItem::findOrFail($id);
        // $date = $order->order->created_at->format('M d Y');
        // $end_date = $order->order->created_at->addDays(max($order->item->breakfast, $order->item->lunch, $order->item->dinner)-1);

        $subs = Subscription::where('item_id', $order->item_id)->where('order_id', $order->order_id);
        $subs = $subs->orderBy('delivery_date', 'asc')->get()->toArray();
        foreach($subs as $index => $sub){
            $subs[$index]['start_date'] = Carbon::parse($sub['delivery_date'])->format('M d Y');
        }
        $date = Carbon::parse($order->order->delivery_date)->format('M d, Y');
        $end_date = Carbon::parse($order->order->delivery_date)->addDays(max($order->item->breakfast, $order->item->lunch, $order->item->dinner)-1);
        $end = new Carbon('last day of this month');
        if($end < $end_date){
            $end_date = $end;
        }
        // $subs = Subscription::where('item_id', $order->item_id)->where('order_id', $order->order->id)->get()->toArray();

        return view('frontend.customer.subscription', compact('user', 'order', 'date', 'end_date', 'subs'));
    }

    public function getTnc()
    {
        $content = getOption('naanstap_terms');
        return view('frontend.pages.naanstap_tnc', compact('content'));
    }


}
