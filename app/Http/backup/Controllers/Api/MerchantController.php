<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Helper\Enets;
use JWTAuth, DB, Auth, Input;

use App\Models\Restaurant;
use App\Models\Merchant;
use App\Models\FoodMerchant;
use App\Models\Transactions;
use App\Models\FoodMenu;
use App\Models\FoodInvoices;
use App\Models\FoodCategory;
use App\Models\FoodCourse;
use App\Models\FoodTag;
use App\Models\Order;
use App\Models\Batch;
use App\Models\Subscription;
use App\Models\OrderItem;
use App\User;
use Carbon\Carbon;

class MerchantController extends Controller
{

    public function getDashboard(Request $request)
    {
        $auth_user = JWTAuth::toUser($request->input('token'));

        // $user = User::find(8);
        // $message = 'Order status has been updated to packed';
        // $type = 'notifications';
        // $id = '';
        // $link = '';
        // $ret = sendSingleLocal($auth_user, $message, $type, $id, $link, 'merchant');
        //
        // $user = User::find(49);
        // $ret = $this->sendSingleLocal($user, $message, $type, $id, $link, true);
        // \Log::debug($ret);
        try{
            $morning_limit = Carbon::createFromTimeString('11:00:00');
            $now = Carbon::now();
            $time = $now->toTimeString();
            $label = '';
            $pass = $breakfast = $lunch = $dinner = false;
            if($time > "19:00:00"){
                // $now->addDay();
                $pass = true;
            }
            if($time <= "07:00:00"){
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

            // $auth_user = JWTAuth::toUser(Input::get('token'));
            // $user = Auth::loginUsingId($auth_user->id);

            $restaurant = Restaurant::where('merchant_id', $auth_user->id)->select('id')->first();
            if($restaurant)
                $restaurant_id = $restaurant->id;
            else{
                dd('Ask admin to add restaurant information to your account to proceed.');
            }

            $p_odd = $orders = [];
            $s_orders = $b_orders = [];
            $noww = Carbon::now();
            if(!$auth_user->hasRole('restaurant-owner-catering')){
              $orders = Order::where('delivery_date', $now->toDateString())->whereHas('items', function($q) use($restaurant_id){
                  $q->whereHas('item', function($qq) use($restaurant_id){
                          $qq->where('restaurant_id', $restaurant_id);
                  });
              })->whereNotIn('status_id', ['10', '11','12','13'])->where('delivery_time', $time)->where('accepted', '1')->orderBy('created_at', 'desc')->get();

              $start = Carbon::now()->startOfMonth()->toDateString();

              foreach($orders as $order){
                  if($order->dormitory_id){
                      $batch = Batch::where('dormitory_id', $order->dormitory_id)->whereDate('batch_date', $now->toDateString())->first();
                      if(!$batch){
                          $batch = Batch::orderBy('created_at', 'desc')->orderBy('id', 'desc')->first();
                          $dd['dormitory_id'] = $order->dormitory_id;
                          $num = str_pad($batch->id+1, 7, '0', STR_PAD_LEFT);
                          $dd['batch_b'] = 'BB'.$num;
                          $dd['batch_l'] = 'BL'.$num;
                          $dd['batch_d'] = 'BD'.$num;
                          $dd['batch_date'] = $now->toDateString();
                          $batch = Batch::create($dd);

                      }
                  }else{
                      $batch = Batch::where('address','like', '%'.$order->address.'%')->whereDate('batch_date', $now->toDateString())->first();
                      if(!$batch){
                          $batch = Batch::orderBy('created_at', 'desc')->orderBy('id', 'desc')->first();
                          $dd['address'] = $order->address;
                          $num = str_pad($batch->id+1, 7, '0', STR_PAD_LEFT);
                          $dd['batch_b'] = 'BB'.$num;
                          $dd['batch_l'] = 'BL'.$num;
                          $dd['batch_d'] = 'BD'.$num;
                          $dd['batch_date'] = $now->toDateString();
                          $batch = Batch::create($dd);

                      }
                  }
                  $batch_id = $batch->{$label};
                  $order->batch_id = $batch->{$label};
                  $order->save();
                  $count = 0;
                  $c_status = 0;
                  foreach($order->items as $item){
                      if($item->item->restaurant_id == $restaurant_id){
                          $count++;//= $item->quantity;
                          if($item->restaurant_status_id == '9'){
                              $c_status++;
                          }
                      }
                  }
                  $order->item_count = $count;

                  if(isset($s_orders[$batch_id])){
                      $s_orders[$batch_id]['id'] .= ','.$order->id;
                      $s_orders[$batch_id]['item_count'] += $count;
                  }else{
                      $s_orders[$batch_id]['batch_id'] = $batch_id;
                      $s_orders[$batch_id]['id'] = $order->id;
                      $s_orders[$batch_id]['item_count'] = $count;
                      $s_orders[$batch_id]['accepted'] = 1;
                      if($c_status == $count){
                        $s_orders[$batch_id]['status_id'] = 9;
                      }else{
                        $s_orders[$batch_id]['status_id'] = 8;
                      }
                  }
              }
            }
            else{
              // if($pass){
              //   $noww->addDay();
              // }
              if($breakfast){
                $order_ids = Subscription::where(function($q){
                  $q->whereNull('b_status')->orWhereNotIn('b_status', ['11','12','13']);
                })->where('delivery_date', $noww->toDateString())->get()->pluck('order_id');
              }elseif($lunch){
                $order_ids = Subscription::where(function($q){
                  $q->whereNull('l_status')->orWhereNotIn('b_status', ['11','12','13']);
                })->where('delivery_date', $noww->toDateString())->get()->pluck('order_id');
              }else{
                $order_ids = Subscription::where(function($q){
                  $q->whereNull('d_status')->orWhereNotIn('d_status', ['11','12','13']);
                })->where('delivery_date', $noww->toDateString())->pluck('order_id');
              }

              $orders_p = Order::whereIn('id', $order_ids)->whereHas('items', function($q) use($restaurant_id){
                  $q->whereHas('item', function($qq) use($restaurant_id){
                          $qq->where('restaurant_id', $restaurant_id);
                  });
              })->orderBy('created_at', 'desc')->get();

              // $orders_p = Order::whereBetween('delivery_date', [$start,$now->toDateString()])->whereHas('items', function($q) use($restaurant_id){
              //   $q->whereHas('item', function($qq) use($restaurant_id){
              //     $qq->where('restaurant_id', $restaurant_id);
              //   });
              // })->whereNotIn('status_id', ['10', '11','12','13'])->where('type', 'package')->orderBy('created_at', 'desc')->get();
              $update_status = false;
              $picked_count = 0;
              foreach($orders_p as $order){

                $delivery_date = Carbon::parse($order->delivery_date);
                $diff = $delivery_date->diffInDays($noww)+1;

                $continue = false;
                foreach($order->items as $item){
                  if($item->item->restaurant_id != $restaurant_id){
                    continue;
                  }
                  //wastage check start
                  if($breakfast){
                    if($item->item && $item->item->breakfast){
                      if($diff > $item->item->breakfast){
                        $continue = true;
                      }
                    }else{
                      $continue = true;
                    }
                  }elseif($lunch){
                    if($item->item && $item->item->lunch){
                      if($diff > $item->item->lunch){
                        $continue = true;
                      }
                    }else{
                      $continue = true;
                    }
                  }elseif($dinner){
                    if($item->item && $item->item->dinner){
                      if($diff > $item->item->dinner){
                        $continue = true;
                      }
                    }else{
                      $continue = true;
                    }
                  }
                }
                \Log::debug("continue");
                \Log::debug($continue);

                if($continue)
                continue;
                //wastage check end
                \Log::debug($order);

                if($order->dormitory_id){
                  $batch = Batch::where('dormitory_id', $order->dormitory_id)->whereDate('batch_date', $now->toDateString())->first();
                  if(!$batch){
                    $batch = Batch::orderBy('created_at', 'decs')->orderBy('id', 'decs')->first();
                    $dd['dormitory_id'] = $order->dormitory_id;
                    $num = str_pad($batch->id+1, 7, '0', STR_PAD_LEFT);
                    $dd['batch_b'] = 'BB'.$num;
                    $dd['batch_l'] = 'BL'.$num;
                    $dd['batch_d'] = 'BD'.$num;
                    $dd['batch_date'] = $now->toDateString();
                    $batch = Batch::create($dd);

                  }
                }
                else{
                  $batch = Batch::where('address','like', $order->address)->whereDate('batch_date', $now->toDateString())->first();
                  if(!$batch){
                    $batch = Batch::orderBy('created_at', 'decs')->orderBy('id', 'decs')->first();
                    $dd['address'] = $order->address;
                    $num = str_pad($batch->id+1, 7, '0', STR_PAD_LEFT);
                    $dd['batch_b'] = 'BB'.$num;
                    $dd['batch_l'] = 'BL'.$num;
                    $dd['batch_d'] = 'BD'.$num;
                    $dd['batch_date'] = $now->toDateString();
                    $batch = Batch::create($dd);

                  }
                }
                $order->batch_id = $batch->{$label};

                $count = 0;
                $c_status = 0;
                $status_id = '';
                foreach($order->items as $item){

                  //need to check this
                  if($item->item->restaurant_id == $restaurant_id){
                    $count++;//= $item->quantity;

                    // if($item->restaurant_status_id == '9'){
                    //   $c_status++;
                    // }
                  }
                  $subs = Subscription::where('item_id', $item->item_id)->where('order_id', $order->id)->whereDate('delivery_date', $noww->toDateString())->first();
                  //\Log::debug($subs);
                  if(!$subs){
                    $status_id = 8;
                    // no need to create as its created when accepted order
                    $sd['item_id'] = $item->item_id;
                    $sd['order_id'] = $order->id;
                    if($breakfast){
                      $sd['breakfast'] = $batch->batch_b;
                      $sd['b_status'] = 8;
                    }
                    if($lunch){
                      $sd['lunch'] = $batch->batch_l;
                      $sd['l_status'] = 8;
                    }
                    if($dinner){
                      $sd['dinner'] = $batch->batch_d;
                      $sd['d_status'] = 8;
                    }
                    $sd['delivery_date'] = $noww->toDateString();
                    $sub = Subscription::create($sd);
                    $subs = Subscription::find($sub->id);
                  }else{
                    if($breakfast){
                      $status_id = $subs->b_status;
                      // if(empty($subs->breakfast)){
                        $sd['breakfast'] = $batch->batch_b;
                        $subs->update($sd);
                      // }else{

                      // }
                    }
                    if($lunch){
                      $status_id = $subs->l_status;
                      // if(empty($subs->lunch)){
                        $sd['lunch'] = $batch->batch_l;
                        $subs->update($sd);
                      // }
                    }
                    if($dinner){
                      $status_id = $subs->d_status;
                      // if(empty($subs->dinner)){
                        $sd['dinner'] = $batch->batch_d;
                        $subs->update($sd);
                      // }
                    }
                    if($status_id >= 9){
                        $c_status++;
                    }
                  }
                }
                if($status_id < 9 || $status_id == ''){
                  $update_status = true;
                }

                $batch_id = $batch->{$label};
                $order->status_id = $status_id == ''?8:$status_id;
                $order->item_count = $count;
                //\Log::debug("count - ".$count);
                if($count){
                  $p_odd[] = $order;

                  if($pass){

                  }else{
                    if(isset($b_orders[$batch_id])){
                        $b_orders[$batch_id]['order_count'] += 1;
                        if($status_id == 10){
                          $b_orders[$batch_id]['picked_count'] += 1;
                        }
                        $b_orders[$batch_id]['id'] .= ','.$order->id;
                        $b_orders[$batch_id]['item_count'] += $count;

                        if($b_orders[$batch_id]['order_count'] == $b_orders[$batch_id]['picked_count']){
                          $b_orders[$batch_id]['status_id'] = $status_id;
                        }else{
                          $b_orders[$batch_id]['status_id'] = 9;
                        }

                        if($update_status){
                          $b_orders[$batch_id]['status_id'] = 8;
                        }
                    }else{
                        $b_orders[$batch_id]['picked_count'] = 0;
                        $b_orders[$batch_id]['order_count'] = 1;
                        if($status_id == 10){
                          $b_orders[$batch_id]['picked_count'] = 1;
                        }

                        $update_status = false;
                        $b_orders[$batch_id]['batch_id'] = $batch_id;
                        $b_orders[$batch_id]['id'] = $order->id;
                        $b_orders[$batch_id]['item_count'] = $count;
                        $b_orders[$batch_id]['accepted'] = 1;

                        // if($c_status == $count){
                        //   $b_orders[$batch_id]['status_id'] = 9;
                        // }else{
                        //   $b_orders[$batch_id]['status_id'] = 8;
                        // }

                        if($update_status){
                          $b_orders[$batch_id]['status_id'] = 8;
                        }else{
                          if($status_id == ""){
                            $status_id = 8;
                          }
                          $b_orders[$batch_id]['status_id'] = $status_id;
                        }
                    }
                  }
                }
              }
            }
            // \Log::debug($b_orders);
            $data['orders_p'] = array_values($b_orders);
            $data['orders'] = array_values($s_orders);//$orders;
            // $data['orders_p'] = $p_odd;
            $dt = Carbon::createFromFormat('Y-m-d H:i:s', $now->toDateString().' '.$time);
            $pickup_time = date('d/m/Y '.$time);

            $data['morning'] = $morning;

            $data['pickup_time'] = $dt->format('d/m/Y h:i:A');//$pickup_time;

            return response()->json(['status' => 'success', 'data' => $data, 'message'=> 'DATA'], 200);

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }

    }

    public function getNewOrder(Request $request)
    {
        $auth_user = JWTAuth::toUser($request->input('token'));
        try{
            $morning_limit = Carbon::createFromTimeString('11:00:00');
            $now = Carbon::now();
            $time2 = $time = $now->toTimeString();
            $label = '';
            $breakfast = $lunch = $dinner = false;
            if($time > "19:00:00"){
                $now->addDay();
            }
            if($time > "19:00:00" || $time <= "07:00:00"){
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

            // $auth_user = JWTAuth::toUser(Input::get('token'));
            // $user = Auth::loginUsingId($auth_user->id);

            $restaurant = Restaurant::where('merchant_id', $auth_user->id)->select('id')->first();
            if($restaurant)
                $restaurant_id = $restaurant->id;
            else{
                dd('Ask admin to add restaurant information to you account to proceed.');
            }
            $start = Carbon::now()->startOfMonth()->toDateString();
            $orders = $p_odd = [];

            if(!$auth_user->hasRole('restaurant-owner-catering')){
              $orders = Order::where('delivery_date', '>=',$now->toDateString())->whereHas('items', function($q) use($restaurant_id){
                  $q->whereHas('item', function($qq) use($restaurant_id){
                          $qq->where('restaurant_id', $restaurant_id);
                  });
              })->where('accepted', '0')->whereNotIn('status_id', ['10', '11','12','13'])->whereNotNull('delivery_time')->orderBy('created_at', 'desc')->get();
              foreach($orders as $order){
                  // if($order->dormitory_id){
                  //     $batch = Batch::where('dormitory_id', $order->dormitory_id)->whereDate('batch_date', $order->delivery_date)->first();
                  //     if(!$batch){
                  //         $batch = Batch::orderBy('created_at', 'desc')->orderBy('id', 'decs')->first();
                  //         $dd['dormitory_id'] = $order->dormitory_id;
                  //         $num = str_pad($batch->id+1, 7, '0', STR_PAD_LEFT);
                  //         $dd['batch_b'] = 'BB'.$num;
                  //         $dd['batch_l'] = 'BL'.$num;
                  //         $dd['batch_d'] = 'BD'.$num;
                  //         $dd['batch_date'] = $order->delivery_date;
                  //         $batch = Batch::create($dd);
                  //
                  //     }
                  // }else{
                  //     $batch = Batch::where('address','like', $order->address)->whereDate('batch_date', $order->delivery_date)->first();
                  //     if(!$batch){
                  //         $batch = Batch::orderBy('created_at', 'decs')->orderBy('id', 'decs')->first();
                  //         $dd['address'] = $order->address;
                  //         $num = str_pad($batch->id+1, 7, '0', STR_PAD_LEFT);
                  //         $dd['batch_b'] = 'BB'.$num;
                  //         $dd['batch_l'] = 'BL'.$num;
                  //         $dd['batch_d'] = 'BD'.$num;
                  //         $dd['batch_date'] = $order->delivery_date;
                  //         $batch = Batch::create($dd);
                  //
                  //     }
                  // }

                  $order->batch_id = '';//$batch->{$label};

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
                  $dt = Carbon::createFromFormat('Y-m-d H:i:s', $order->delivery_date.' '.$order->delivery_time);
                  $order->pickup_time = $dt->format('d/m/Y h:i:A');
              }

            }else{
              $orders_p = Order::where('delivery_date', '>=',$now->toDateString())->whereHas('items', function($q) use($restaurant_id){
                  $q->whereHas('item', function($qq) use($restaurant_id){
                          $qq->where('restaurant_id', $restaurant_id);
                  });
              })->where('accepted', '0')->whereNotIn('status_id', ['10', '11','12','13'])->whereNull('delivery_time')->orderBy('created_at', 'desc')->get();

              foreach($orders_p as $order){

                  // if($order->dormitory_id){
                  //     $batch = Batch::where('dormitory_id', $order->dormitory_id)->where('batch_date', date('Y-m-d', strtotime($order->delivery_date)))->first();
                  //
                  //     if(!$batch){
                  //         $batch = Batch::orderBy('created_at', 'decs')->orderBy('id', 'decs')->first();
                  //         $dd['dormitory_id'] = $order->dormitory_id;
                  //         $num = str_pad($batch->id+1, 7, '0', STR_PAD_LEFT);
                  //         $dd['batch_b'] = 'BB'.$num;
                  //         $dd['batch_l'] = 'BL'.$num;
                  //         $dd['batch_d'] = 'BD'.$num;
                  //         $dd['batch_date'] = $order->delivery_date;
                  //         $batch = Batch::create($dd);
                  //     }
                  // }
                  // else{
                  //     $batch = Batch::where('address','like', $order->address)->where('batch_date', date('Y-m-d', strtotime($order->delivery_date)))->first();
                  //     if(!$batch){
                  //         $batch = Batch::orderBy('created_at', 'decs')->orderBy('id', 'decs')->first();
                  //         $dd['address'] = $order->address;
                  //         $num = str_pad($batch->id+1, 7, '0', STR_PAD_LEFT);
                  //         $dd['batch_b'] = 'BB'.$num;
                  //         $dd['batch_l'] = 'BL'.$num;
                  //         $dd['batch_d'] = 'BD'.$num;
                  //         $dd['batch_date'] = $order->delivery_date;
                  //         $batch = Batch::create($dd);
                  //     }
                  // }
                  //
                  // $order->batch_id = $batch->{$label};

                  $dt = Carbon::createFromFormat('Y-m-d H:i:s', $order->delivery_date.' '.$time);
                  $order->pickup_time = $dt->format('d/m/Y h:i:A');
                  $order->batch_id = '';//$batch->{$label};
                  $count = 0;
                  $c_status = 0;
                  foreach($order->items as $item){
                      // if($breakfast){
                      //     if($item->item->breakfast){
                      //
                      //     }else{
                      //         continue;
                      //     }
                      // }elseif($lunch){
                      //     if($item->item->lunch){
                      //
                      //     }else{
                      //         continue;
                      //     }
                      // }elseif($dinner){
                      //     if($item->item->dinner){
                      //
                      //     }else{
                      //         continue;
                      //     }
                      // }
                      if($item->item->restaurant_id == $restaurant_id){
                          $count++;
                          if($item->restaurant_status_id == '9'){
                              $c_status++;
                          }
                      }

                      // $subs = Subscription::where('item_id', $item->item_id)->where('order_id', $order->id)->whereDate('created_at', $now->toDateString())->first();
                      // if(!$subs){
                      //     $sd['item_id'] = $item->item_id;
                      //     $sd['order_id'] = $order->id;
                      //     if($breakfast)
                      //         $sd['breakfast'] = $batch->batch_b;
                      //     if($lunch)
                      //         $sd['lunch'] = $batch->batch_l;
                      //     if($dinner)
                      //         $sd['dinner'] = $batch->batch_d;
                      //
                      //     Subscription::create($sd);
                      // }else{
                      //     if($breakfast && $subs->breakfast != ''){
                      //         $sd['breakfast'] = $batch->batch_b;
                      //         $subs->update($sd);
                      //     }
                      //     if($lunch && $subs->lunch != ''){
                      //         $sd['lunch'] = $batch->batch_l;
                      //         $subs->update($sd);
                      //     }
                      //     if($dinner && $subs->dinner != ''){
                      //         $sd['dinner'] = $batch->batch_d;
                      //         $subs->update($sd);
                      //     }
                      // }
                  }
                  $order->item_count = $count;
                  if($count)
                      $p_odd[] = $order;
              }

            }

            $data['orders'] = $orders;
            $data['orders_p'] = $p_odd;
            $dt = Carbon::createFromFormat('Y-m-d H:i:s', $now->toDateString().' '.$time);
            $data['pickup_time'] = $dt->format('d/m/Y h:i:A');//$pickup_time;
            return response()->json(['status' => 'success', 'data' => $data, 'message'=> 'DATA'], 200);

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function getUpcomingOrder(Request $request)
    {
        $auth_user = JWTAuth::toUser($request->input('token'));
        try{
            $morning_limit = Carbon::createFromTimeString('11:00:00');
            $now = Carbon::now();
            $noww = Carbon::now();
            $time2 = $noww->toTimeString();
            $time = $now->toTimeString();
            $pass = false;
            $label = '';
            $breakfast = $lunch = $dinner = false;
            if($time > "19:00:00"){
                $now->addDay();
                $pass = true;
            }
            if($time > "19:00:00" || $time <= "07:00:00"){
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
                if($auth_user->hasRole('restaurant-owner-catering')){
                  $noww->addDay();
                }
            }

            $morning = $now->diffInSeconds($morning_limit, false);

            // $auth_user = JWTAuth::toUser(Input::get('token'));
            // $user = Auth::loginUsingId($auth_user->id);

            $restaurant = Restaurant::where('merchant_id', $auth_user->id)->select('id')->first();
            if($restaurant)
                $restaurant_id = $restaurant->id;
            else{
                dd('Ask admin to add restaurant information to your account to proceed.');
            }
            $start = Carbon::now()->startOfMonth()->toDateString();

            $orders = $p_odd = [];
            if(!$auth_user->hasRole('restaurant-owner-catering')){
              $orders = Order::where('delivery_date', '>=',$now->toDateString())->whereHas('items', function($q) use($restaurant_id){
                  $q->whereHas('item', function($qq) use($restaurant_id){
                          $qq->where('restaurant_id', $restaurant_id);
                  });
              })->where('accepted', '1')->whereNotIn('status_id', ['10', '11','12','13'])->whereNotNull('delivery_time')->where('delivery_time', '>=', $time)->orderBy('created_at', 'desc')->get();

              foreach($orders as $order){
                  if($order->dormitory_id){
                      $batch = Batch::where('dormitory_id', $order->dormitory_id)->whereDate('batch_date', $order->delivery_date)->first();
                      if(!$batch){
                          $batch = Batch::orderBy('created_at', 'desc')->orderBy('id', 'decs')->first();
                          $dd['dormitory_id'] = $order->dormitory_id;
                          $num = str_pad($batch->id+1, 7, '0', STR_PAD_LEFT);
                          $dd['batch_b'] = 'BB'.$num;
                          $dd['batch_l'] = 'BL'.$num;
                          $dd['batch_d'] = 'BD'.$num;
                          $dd['batch_date'] = $order->delivery_date;
                          $batch = Batch::create($dd);

                      }
                  }else{
                      $batch = Batch::where('address','like', '%'.$order->address.'%')->whereDate('batch_date', $order->delivery_date)->first();
                      if(!$batch){
                          $batch = Batch::orderBy('created_at', 'decs')->orderBy('id', 'decs')->first();
                          $dd['address'] = $order->address;
                          $num = str_pad($batch->id+1, 7, '0', STR_PAD_LEFT);
                          $dd['batch_b'] = 'BB'.$num;
                          $dd['batch_l'] = 'BL'.$num;
                          $dd['batch_d'] = 'BD'.$num;
                          $dd['batch_date'] = $order->delivery_date;
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
                  $dt = Carbon::createFromFormat('Y-m-d H:i:s', $order->delivery_date.' '.$order->delivery_time);
                  $order->pickup_time = $dt->format('d/m/Y h:i:A');
              }

            }
            else{
              $pack_orders = [];
              $days = [];
              $end_date = Carbon::now()->addDays('5')->toDateString();
              $subs = Subscription::whereHas('item', function($q) use($restaurant_id){
                $q->where('restaurant_id', $restaurant_id);
              })
              ->where('delivery_date', '>=',$now->toDateString())->where('delivery_date', '<=', $end_date)->orderby('delivery_date', 'asc')->get();
              foreach($subs as $key => $sub){
                  $order = Order::where('id', $sub->order_id)->first();
                  if($sub->delivery_date == $now->toDateString()){
                    if($breakfast){
                        if($pass && $sub->b_allowed){
                          $tt = "07:00:00";
                          $dt = Carbon::createFromFormat('Y-m-d H:i:s', $sub->delivery_date.' '.$tt);
                          $days[] = $pickup_time = $dt->format('d/m/Y h:i:A');
                          $pack_orders[] = [
                            'id' => $sub->order_id,
                            'item_count' => $order->items->count(),
                            'pickup_time' => $pickup_time
                          ];
                        }
                        if($sub->l_allowed){
                          $tt = "12:00:00";
                          $dt = Carbon::createFromFormat('Y-m-d H:i:s', $sub->delivery_date.' '.$tt);
                          $days[] = $pickup_time = $dt->format('d/m/Y h:i:A');
                          $pack_orders[] = [
                            'id' => $sub->order_id,
                            'item_count' => $order->items->count(),
                            'pickup_time' => $pickup_time
                          ];
                        }
                        if($sub->d_allowed){
                          $tt = "19:00:00";
                          $dt = Carbon::createFromFormat('Y-m-d H:i:s', $sub->delivery_date.' '.$tt);
                          $days[] = $pickup_time = $dt->format('d/m/Y h:i:A');
                          $pack_orders[] = [
                            'id' => $sub->order_id,
                            'item_count' => $order->items->count(),
                            'pickup_time' => $pickup_time
                          ];
                        }
                    }
                    if($lunch){
                      if($sub->d_allowed){
                        $tt = "19:00:00";
                        $dt = Carbon::createFromFormat('Y-m-d H:i:s', $sub->delivery_date.' '.$tt);
                        $days[] = $pickup_time = $dt->format('d/m/Y h:i:A');
                        $pack_orders[] = [
                          'id' => $sub->order_id,
                          'item_count' => $order->items->count(),
                          'pickup_time' => $pickup_time
                        ];
                      }
                    }
                    if($dinner){
                      continue;
                    }
                  }else{
                    if($sub->b_allowed){
                      $tt = "07:00:00";
                      $dt = Carbon::createFromFormat('Y-m-d H:i:s', $sub->delivery_date.' '.$tt);
                      $days[] = $pickup_time = $dt->format('d/m/Y h:i:A');
                      $pack_orders[] = [
                        'id' => $sub->order_id,
                        'item_count' => $order->items->count(),
                        'pickup_time' => $pickup_time
                      ];
                    }
                    if($sub->l_allowed){
                      $tt = "12:00:00";
                      $dt = Carbon::createFromFormat('Y-m-d H:i:s', $sub->delivery_date.' '.$tt);
                      $days[] = $pickup_time = $dt->format('d/m/Y h:i:A');
                      $pack_orders[] = [
                        'id' => $sub->order_id,
                        'item_count' => $order->items->count(),
                        'pickup_time' => $pickup_time
                      ];
                    }
                    if($sub->d_allowed){
                      $tt = "19:00:00";
                      $dt = Carbon::createFromFormat('Y-m-d H:i:s', $sub->delivery_date.' '.$tt);
                      $days[] = $pickup_time = $dt->format('d/m/Y h:i:A');
                      $pack_orders[] = [
                        'id' => $sub->order_id,
                        'item_count' => $order->items->count(),
                        'pickup_time' => $pickup_time
                      ];
                    }
                  }

              }
              $odd = [];
              $p_orders = [];
              foreach($pack_orders as $od){
                if(!in_array($od['pickup_time'], $odd)){
                  $odd[] = $od['pickup_time'];
                  $p_orders[$od['pickup_time']] = [
                    'id' => $od['id'],
                    'item_count' => $od['item_count'],
                    'pickup_time' => $od['pickup_time']
                  ];
                }else{
                  $p_orders[$od['pickup_time']]['id'] .=  ', '.$od['id'];
                  $p_orders[$od['pickup_time']]['item_count'] +=  $od['item_count'];
                }
              }

              // array_multisort($days, SORT_ASC, $pack_orders);
              $p_odd = array_values($p_orders);
              // $orders_p = Order::where('delivery_date', '>=',$now->toDateString())->whereHas('items', function($q) use($restaurant_id){
              //     $q->whereHas('item', function($qq) use($restaurant_id){
              //             $qq->where('restaurant_id', $restaurant_id);
              //     });
              // })->where('accepted', '1')->whereNotIn('status_id', ['10', '11','12','13'])->whereNull('delivery_time')->orderBy('created_at', 'desc')->get();

              // foreach($orders_p as $order){
              //
              //     // $dt = Carbon::createFromFormat('Y-m-d H:i:s', $order->delivery_date.' '.$time);
              //     // $order->pickup_time = $dt->format('d/m/Y h:i:A');
              //     $count = 0;
              //     $c_status = 0;
              //     $flag = true;
              //     $aj_lunch = false;
              //     foreach($order->items as $item){
              //         $aj_lunch = false;
              //
              //         if($item->item && ($item->item->restaurant_id == $restaurant_id)){
              //             $count++;
              //             if($item->restaurant_status_id == '9'){
              //                 $c_status++;
              //             }
              //         }
              //         if($aj_lunch){
              //           continue;
              //         }
              //         $subs = Subscription::where('item_id', $item->item_id)->where('order_id', $order->id)->whereDate('delivery_date', $now->toDateString())->first();
              //         if(!$subs){
              //           $flag = false;
              //         }else{
              //             $dt = Carbon::createFromFormat('Y-m-d H:i:s', $subs->delivery_date.' '.$time);
              //             $order->pickup_time = $dt->format('d/m/Y h:i:A');
              //         }
              //     }
              //
              //     $order->item_count = $count;
              //     if($count && $flag && !$aj_lunch){
              //
              //       $p_odd[] = $order;
              //     }
              //
              // }
            }

            $data['orders'] = $orders;
            $data['orders_p'] = $p_odd;
            return response()->json(['status' => 'success', 'data' => $data, 'message'=> 'DATA'], 200);

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function viewOrder(Request $request)
    {
        $auth_user = JWTAuth::toUser($request->input('token'));
        $package = false;
        if($auth_user->hasRole('restaurant-owner-catering')){
          $package = true;
        }

        $now = Carbon::now()->toDateString();
        $noww = Carbon::now();
        $time = $noww->toTimeString();
        $label = '';
        $breakfast = $lunch = $dinner = false;
        if($time > "19:00:00"){
            $noww->addDay();
        }
        if($time > "19:00:00" || $time <= "07:00:00"){
            $time = "07:00:00";
            $label = 'bstatus';
            $breakfast = true;
        }
        elseif($time > "07:00:00" && $time <= "12:00:00"){
            $time = "12:00:00";
            $label = 'lstatus';
            $lunch = true;
        }
        elseif($time > "12:00:00" && $time <= "19:00:00"){
            $time = "19:00:00";
            $label = 'dstatus';
            $dinner = true;
        }
        try{
            $item_ids = $id = $request->input('order_id');
            $id = explode(',', $id);

            $restaurant = Restaurant::where('merchant_id', $auth_user->id)->select('id')->first();
            $restaurant_id = $restaurant->id;

            $items = OrderItem::whereIn('order_id', $id)->with('order')->whereHas('item', function($q) use($restaurant_id){
                $q->where('restaurant_id', $restaurant_id);
            })->get();
            $response = [];
            $dup = [];
            foreach($items as $item){
                if(!in_array($item->item_id, $dup)){
                  $dup[] = $item->item_id;
                }else{
                  $response[$item->item_id]['quantity'] += $item->quantity;
                  $response[$item->item_id]['id'] .= ','.$item->id;
                  continue;
                }

                $response[$item->item_id]['id'] = $item->id;
                $response[$item->item_id]['item_name'] = $item->item_name = $item->item->name;
                $response[$item->item_id]['quantity'] = $item->quantity;
                $response[$item->item_id]['item']['type'] = $item->item?$item->item->type:'';
                $response[$item->item_id]['item']['id'] = $item->item_id;
                $response[$item->item_id]['order']['delivery_date'] = @$item->order->delivery_date;

                $item->start_date = '';
                $item->end_date = '';
                if($package){
                  $subsc = Subscription::where('item_id', $item->item_id)->where('order_id', $item->order_id)->whereNotNull('delivery_date')->orderBy('delivery_date', 'asc')->first();
                  $item->start_date = @$subsc->delivery_date;
                  $subsc = Subscription::where('item_id', $item->item_id)->where('order_id', $item->order_id)->whereNotNull('delivery_date')->orderBy('delivery_date', 'desc')->first();
                  $item->end_date = @$subsc->delivery_date;
                  $sub = Subscription::where('item_id', $item->item_id)->where('order_id', $item->order_id)->where('delivery_date', $noww->toDateString())->first();
                  if($sub){
                    if($item->order->accepted){
                      if($sub->{$label}){
                        if($breakfast){
                          $item->restaurant_status_id = $sub->b_status;
                        }elseif($lunch){
                          $item->restaurant_status_id = $sub->l_status;
                        }else{
                          $item->restaurant_status_id = $sub->d_status;
                        }
                        $item->restaurant_status_name = $sub->{$label}->name;
                      }else{
                        $item->restaurant_status_id = 8;
                        $item->restaurant_status_name = 'In Progress';
                      }
                    }
                    else{
                      $item->restaurant_status_name = 'Order Accepted';
                    }
                  }else{
                    $item->restaurant_status_id = 8;
                    $item->restaurant_status_name = 'In Progress';
                  }
                }else{

                  if($item->order->accepted){
                    if($item->restaurant_status_id <= 9){
                      $item->restaurant_status_name = $item->restaurant_status->name;
                    }else{
                      if($item->agent_status)
                        $item->restaurant_status_name = $item->agent_status->name;
                      else
                        $item->restaurant_status_name = 'In Progress';
                    }

                  }
                  else{
                    $item->restaurant_status_name = 'Order Accepted';
                  }
                }
                $response[$item->item_id]['restaurant_status_id'] = $item->restaurant_status_id;
                $response[$item->item_id]['restaurant_status_name'] = $item->restaurant_status_name;
                $response[$item->item_id]['start_date'] = $item->start_date;
                $response[$item->item_id]['end_date'] = $item->end_date;

            }
            $items = array_values($response);
            return response()->json(['status' => 'success', 'data' => $items, 'message'=> 'SUCCESS'], 200);

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }

    }

    public function viewHistory(Request $request)
    {
        $auth_user = JWTAuth::toUser($request->input('token'));
        try{
            $restaurant = Restaurant::where('merchant_id', $auth_user->id)->select('id')->first();
            $restaurant_id = $restaurant->id;

            $orders = Order::whereHas('items', function($q) use($restaurant_id){
                $q->whereHas('item', function($qq) use($restaurant_id){
                        $qq->where('restaurant_id', $restaurant_id)->withTrashed();
                });
            })->whereIn('status_id', ['11','12','13'])->orderBy('created_at', 'desc')->get();

            if($auth_user->hasRole('restaurant-owner-single')){

              foreach($orders as $order){
                $address = "";
                if($order->dormitory_id){
                    $batch = Batch::where('dormitory_id', $order->dormitory_id)->whereDate('batch_date', $order->delivery_date)->first();
                    $address = @$order->dormitory->name;
                }else{
                    $batch = Batch::where('address','like', $order->address)->whereDate('batch_date', $order->delivery_date)->first();
                    $address = $order->address;
                }

                $batch_id = '';
                if($batch){
                  if($order->delivery_time == '07:00:00'){
                    $batch_id = $batch->batch_b;
                  }
                  elseif($order->delivery_time == '12:00:00'){
                    $batch_id = $batch->batch_l;
                  }
                  elseif($order->delivery_time == '19:00:00'){
                    $batch_id = $batch->batch_d;
                  }
                }
                // foreach($order->items as $item){
                //     if($item->item->restaurant_id == $restaurant_id){
                //         $count++;
                //     }
                // }
                $order->created_at = $order->created_at;//->format('M d Y H:i A');
                $order->item_count = $order->items->count();
                $order->status_name = $order->status->name;
                $order->batch_id = $batch_id;
                $order->address = $address;
              }
            }

            elseif($auth_user->hasRole('restaurant-owner-catering')){
              $now = Carbon::now();
              $time = $now->toTimeString();
              $pass = false;
              $label = '';
              $breakfast = $lunch = $dinner = false;
              if($time > "19:00:00"){
                  $pass = true;
              }
              if($time > "19:00:00" || $time <= "07:00:00"){
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

              $pack_orders = [];
              $days = [];

              $end_date = Carbon::now()->addDays('5')->toDateString();
              $subs = Subscription::where('delivery_date', '<=',$now->toDateString())->orderby('delivery_date', 'asc')->get();
              foreach($subs as $key => $sub){
                  $order = Order::where('id', $sub->order_id)->first();
                  $address = '';

                  if($order->dormitory_id){
                      $address = @$order->dormitory->name;
                  }else{
                      $address = $order->address;
                  }

                  if($sub->delivery_date == $now->toDateString()){
                    if($breakfast){
                        continue;
                    }
                    if($lunch){
                      if($sub->b_allowed){
                        $tt = "07:00:00";
                        $dt = Carbon::createFromFormat('Y-m-d H:i:s', $sub->delivery_date.' '.$tt);
                        $days[] = $pickup_time = $dt->format('d/m/Y h:i:A');
                        $pack_orders[] = [
                          'id' => $sub->order_id,
                          'item_count' => $order->items->count(),
                          'created_at' => $pickup_time,
                          'batch_id' => $sub->breakfast,
                          'address' => $address,
                          'status_id' => $sub->b_status,
                          'status_name' => @$sub->bstatus->name,
                        ];
                      }
                    }
                    if($dinner){
                      if($sub->b_allowed){
                        $tt = "07:00:00";
                        $dt = Carbon::createFromFormat('Y-m-d H:i:s', $sub->delivery_date.' '.$tt);
                        $days[] = $pickup_time = $dt->format('d/m/Y h:i:A');
                        $pack_orders[] = [
                          'id' => $sub->order_id,
                          'item_count' => $order->items->count(),
                          'created_at' => $pickup_time,
                          'batch_id' => $sub->breakfast,
                          'address' => $address,
                          'status_id' => $sub->b_status,
                          'status_name' => @$sub->bstatus->name,
                        ];
                      }
                      if($sub->l_allowed){
                        $tt = "12:00:00";
                        $dt = Carbon::createFromFormat('Y-m-d H:i:s', $sub->delivery_date.' '.$tt);
                        $days[] = $pickup_time = $dt->format('d/m/Y h:i:A');
                        $pack_orders[] = [
                          'id' => $sub->order_id,
                          'item_count' => $order->items->count(),
                          'created_at' => $pickup_time,
                          'batch_id' => $sub->lunch,
                          'address' => $address,
                          'status_id' => $sub->l_status,
                          'status_name' => @$sub->lstatus->name,
                        ];
                      }
                    }
                  }else{
                    if($sub->b_allowed){
                      $tt = "07:00:00";
                      $dt = Carbon::createFromFormat('Y-m-d H:i:s', $sub->delivery_date.' '.$tt);
                      $days[] = $pickup_time = $dt->format('d/m/Y h:i:A');
                      $pack_orders[] = [
                        'id' => $sub->order_id,
                        'item_count' => $order->items->count(),
                        'created_at' => $pickup_time,
                        'batch_id' => $sub->breakfast,
                        'address' => $address,
                        'status_id' => $sub->b_status,
                        'status_name' => @$sub->bstatus->name,
                      ];
                    }
                    if($sub->l_allowed){
                      $tt = "12:00:00";
                      $dt = Carbon::createFromFormat('Y-m-d H:i:s', $sub->delivery_date.' '.$tt);
                      $days[] = $pickup_time = $dt->format('d/m/Y h:i:A');
                      $pack_orders[] = [
                        'id' => $sub->order_id,
                        'item_count' => $order->items->count(),
                        'created_at' => $pickup_time,
                        'batch_id' => $sub->lunch,
                        'address' => $address,
                        'status_id' => $sub->l_status,
                        'status_name' => @$sub->lstatus->name,
                      ];
                    }
                    if($sub->d_allowed){
                      $tt = "19:00:00";
                      $dt = Carbon::createFromFormat('Y-m-d H:i:s', $sub->delivery_date.' '.$tt);
                      $days[] = $pickup_time = $dt->format('d/m/Y h:i:A');
                      $pack_orders[] = [
                        'id' => $sub->order_id,
                        'item_count' => $order->items->count(),
                        'created_at' => $pickup_time,
                        'batch_id' => $sub->dinner,
                        'address' => $address,
                        'status_id' => $sub->d_status,
                        'status_name' => @$sub->dstatus->name,
                      ];
                    }
                  }

              }
              $orders = array_values($pack_orders);
              // foreach($orders as $order){
              //   $address = "";
              //   if($order->dormitory_id){
              //       $address = @$order->dormitory->name;
              //   }else{
              //       $address = $order->address;
              //   }
              //   $order->created_at = $order->created_at;//->format('M d Y H:i A');
              //   $order->item_count = $order->items->count();
              //   $order->status_name = $order->status->name;
              //   $order->batch_id = null;
              //   $order->address = $address;
              // }
            }

            return response()->json(['status' => 'success','dd'=> $restaurant_id,  'data' => $orders, 'message'=> 'SUCCESS'], 200);

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }



    }

    public function packageSubscribed(Request $request)
    {
        try{
            $auth_user = JWTAuth::toUser($request->input('token'));
            $restaurant = Restaurant::where('merchant_id', $auth_user->id)->select('id')->first();
            $restaurant_id = $restaurant->id;

            $orders = OrderItem::whereHas('item', function($q) use($restaurant_id){
                $q->where('restaurant_id', $restaurant_id)
                ->where('type', 'package');
            })->get();
            $data = [];
            $dd = [];
            foreach($orders as $order){
                if(!in_array($order->item_id, $data)){
                    $data[] = $order->item_id;
                    $order->item_name = $order->item->name;
                    $dd[] = $order;
                }
            }
            $dd = array_values($dd);
            return response()->json(['status' => 'success', 'data' => $dd, 'message'=> 'SUCCESS'], 200);

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function packageSubscribers(Request $request)
    {
        try{
            $auth_user = JWTAuth::toUser($request->input('token'));
            $id = $request->input('item_id');

            $restaurant = Restaurant::where('merchant_id', $auth_user->id)->select('id')->first();
            $restaurant_id = $restaurant->id;

            $orders = Order::whereHas('items', function($q) use($id){
                $q->where('item_id', $id);
            })->whereNotIn('status_id', ['12'])->orderBy('created_at', 'desc')->orderBy('id', 'desc')->get();

            foreach($orders as $order){
                $order->user_name = $order->user->name;
                $order->subscription_start = $order->delivery_date;
                $subs = Subscription::where('order_id', $order->id)->orderBy('delivery_date', 'desc')->first();
                if($subs){
                  $order->subscription_end = $subs->delivery_date;
                }else{
                  $order->subscription_end = '';
                }
                $order->order_date = $order->created_at->format('Y-m-d');
            }
            return response()->json(['status' => 'success', 'data' => $orders, 'message'=> 'SUCCESS'], 200);

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }

    }

    public function packageSubscription(Request $request)
    {
        try{
            $id = $request->input('id');
            $item_id = $request->input('item_id');
            $from = $request->input('from');
            $to = $request->input('to');

            $auth_user = JWTAuth::toUser($request->input('token'));
            $orders = Order::find($id);

            // $restaurant = Restaurant::where('merchant_id', $orders->user_id)->select('id')->first();
            // $restaurant_id = $restaurant->id;
            $item = FoodMenu::find($item_id);

            // $order = OrderItem::findOrFail($id);
            // $date = Carbon::parse($orders->delivery_date)->format('M d Y');
            // $end_date = Carbon::parse($orders->delivery_date)->addDays(max($item->breakfast, $item->lunch, $item->dinner));
            // $end = new Carbon('last day of this month');
            // if($end < $end_date){
            //     $end_date = $end;
            // }

            $subs = Subscription::where('item_id', $item_id)->where('order_id', $id);
            if($from != '' && $to != ''){
                $subs->whereBetween('delivery_date', [$from, $to]);
            }

            $subs = $subs->orderBy('delivery_date', 'asc')->get()->toArray();

            $index = 0;
            $data = [];
            foreach($subs as $index => $sub){
                $subs[$index]['start_date'] = Carbon::parse($sub['delivery_date'])->format('M d Y');
            }
            // $index = 0;
            // $data = [];
            // while(strtotime($date) <= strtotime($end_date)) {
            //     $data[$index] = @$subs[$index];
            //     $data[$index]['start_date'] = $date;
            //     if(isset($data[$index]['breakfast']) && $data[$index]['breakfast'] != '')
            //         $data[$index]['breakfast'] = '';
            //     if(isset($data[$index]['lunch']) && $data[$index]['lunch'] != '')
            //         $data[$index]['lunch'] = '';
            //     if(isset($data[$index]['dinner']) && $data[$index]['dinner'] != '')
            //         $data[$index]['dinner'] = $data[$index]['dinner'];
            //     $date = date ("M d Y", strtotime("+1 day", strtotime($date)));
            //     $index++;
            // }

            return response()->json(['status' => 'success', 'data' => $subs, 'message'=> 'SUCCESS'], 200);

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function getMenu(Request $request)
    {
        try{
            $auth_user = JWTAuth::toUser($request->input('token'));
            $roles = $auth_user->getRoles();

            if(count($roles)){
                foreach($roles as $role){
                    $roles = $role;
                    break;
                }
            }else{
                $roles = "restaurant-owner-package";
            }
            $type = 'single';
            if($roles == "restaurant-owner-package"){
                $type = "package";
            }
            $restaurant = Restaurant::where('merchant_id', $auth_user->id)->select('id')->first();

            $menu = [];
            if($restaurant){
                $menu = FoodMenu::where('restaurant_id', $restaurant->id)->get();
                foreach($menu as $item){
                    if($item->description == ''){
                      $item->description = '';
                    }
                    if($item->image){
                        $img = explode(',', $item->image);
                        $ii =  url('customer/images/img_place.png');
                        foreach($img as $is){
                          if($is != ''){
                            $ii = $is;
                            break;
                          }
                        }
                        $item->image = url($ii);
                    }else{
                        $item->image = url('customer/images/img_place.png');
                    }
                    $item->price = $item->base_price;
                }
            }

            $courses = FoodCourse::pluck('name', 'id')->all();
            $category = FoodCategory::pluck('name', 'id')->all();

            return response()->json(['status' => 'success',  'data' => $menu, 'courses' => $courses, 'category' => $category, 'message'=> 'SUCCESS'], 200);

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }

    }

    public function viewProfile(Request $request)
    {
        try{
            $auth_user = JWTAuth::toUser($request->input('token'));
            $restaurant = Restaurant::where('merchant_id', $auth_user->id)->first();
            if($restaurant->image){
                $restaurant->prof_image = url($restaurant->image);
            }else{
                $restaurant->prof_image = url("merchant/images/img-profile.jpg");
            }

            $restaurant->email = $auth_user->email;
            if($restaurant->gst_no == NULL){
                $restaurant->gst_no = '';
            }

            if($auth_user->hasRole('restaurant-owner-single')){
                $restaurant->role = 'single';
            }else{
                $restaurant->role = 'package';
            }
            return response()->json(['status' => 'success', 'data' => $restaurant, 'message'=> 'SUCCESS'], 200);

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }

    }

    public function postProfile(Request $request)
    {
        try{
            $data_restra = $request->only('address', 'open_at', 'closes_at', 'phone_no');

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
                  $folder = "files/food";

                  $path = savePhoto($file, $folder, $type);
                  $data_restra['image'] = $path;
              } else {
                  return response()->json(['status' => 'error', 'data' => [], 'message'=> 'Did not match data URI with image data'], 200);
              }
            }

            $auth_user = JWTAuth::toUser($request->input('token'));
            $restaurant = Restaurant::where('merchant_id', $auth_user->id)->first();
            $restaurant->update($data_restra);

            return response()->json(['status' => 'success', 'data' => [], 'message'=> 'Profile updated successfully'], 200);

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }

    }

    public function updateItem(Request $request)
    {
        $auth_user = JWTAuth::toUser($request->input('token'));
        $package = false;
        if($auth_user->hasRole('restaurant-owner-catering')){
          $package = true;
        }

        $now = Carbon::now()->toDateString();
        $noww = Carbon::now();
        $time = $noww->toTimeString();
        $label = '';
        $breakfast = $lunch = $dinner = false;
        if($time > "19:00:00"){
            // $noww->addDay();
        }
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
        elseif($time > "12:00:00" && $time <= "19:00:00"){
            $time = "19:00:00";
            $label = 'd_status';
            $dinner = true;
        }
        try{
            $item_id = $request->input('item_id');
            $status_id = $request->input('status_id');
            $item_id = explode(',', $item_id);
            if($package){
                $items = OrderItem::whereIn('id', $item_id)->get();
                $exist = false;
                foreach($items as $item){
                  $exist = true;
                  $sub = Subscription::where('item_id', $item->item_id)->where('order_id', $item->order_id)
                  ->where('delivery_date', $noww->toDateString())->first();
                  if($sub){
                      $sub->update([$label => 9]);

                      $message = 'Order has been packed #'.$item->order_id;
                      $type = 'notifications';
                      $id = '';
                      $link = '';
                      $user = @$item->order->user;
                      \Log::debug("sending notification");
                      if($user && $user->fcm_token){
                        $ret = sendSingleLocal($user, $message, $type, $id, $link, 'user');
                        \Log::debug($ret);
                      }
                      $user = @$item->order->driver;
                      if($user && $user->fcm_token){
                        $ret = sendSingleLocal($user, $message, $type, $id, $link, 'driver');
                        \Log::debug($ret);
                      }
                  }
                }
                if($exist){
                    return response()->json(['status' => 'success', 'data' => [], 'message'=> 'SUCCESS'], 200);
                }else{
                    return response()->json(['status' => 'error', 'data' => $item, 'message'=> 'Subscription does not exist.'], 200);
                }
              }else{
                $items = OrderItem::whereIn('id', $item_id)->get();
                $exist = false;
                foreach($items as $item){
                  $exist = true;
                  $item->update(['restaurant_status_id' => 9]);

                  $message = 'Order has been packed #'.$item->order_id;
                  $type = 'notifications';
                  $id = '';
                  $link = '';
                  $user = @$item->order->user;
                  \Log::debug("sending notification");
                  if($user && $user->fcm_token){
                    $ret = sendSingleLocal($user, $message, $type, $id, $link, 'user');
                    \Log::debug($ret);
                  }
                  $user = @$item->order->driver;
                  if($user && $user->fcm_token){
                    $ret = sendSingleLocal($user, $message, $type, $id, $link, 'driver');
                    \Log::debug($ret);
                  }
                }
                if($exist){
                    return response()->json(['status' => 'success', 'data' => [], 'message'=> 'SUCCESS'], 200);
                }else{
                    return response()->json(['status' => 'error', 'data' => $item, 'message'=> 'Item does not exist.'], 200);
                }
            }
            return response()->json(['status' => 'error', 'data' => [], 'message'=> 'Item does not exist'], 200);

        }catch(Exception $e){
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function updateOrder(Request $request)
    {
        try{
            $auth_user = JWTAuth::toUser($request->input('token'));
            $item_id = $request->input('item_id');
            $status_id = $request->input('status_id');
            $package = false;
            $restaurant = Restaurant::where('merchant_id', $auth_user->id)->select('id')->first();
            if($restaurant)
                $restaurant_id = $restaurant->id;
            else{
                dd('Ask admin to add restaurant information to your account to proceed.');
            }
            if($auth_user->hasRole('restaurant-owner-catering')){
              $package = true;
            }

            if($status_id == ''){
                $status_id = 9;
            }
            if($package){
                $batch_id = $request->input('batch_id');
                $item_ids = explode(',', $item_id);

                foreach($item_ids as $order_id){
                  $items = Subscription::where('order_id', $order_id)->where(function($q) use($batch_id){
                      $q->where('breakfast', $batch_id)
                      ->orWhere('lunch', $batch_id)
                      ->orWhere('dinner', $batch_id);
                  })->get();
                  // ->whereHas('items', function($q) use($restaurant_id){
                  //     $q->whereHas('item', function($qq) use($restaurant_id){
                  //             $qq->where('restaurant_id', $restaurant_id);
                  //     });
                  // })
                  foreach($items as $item){
                    if($item){
                      if($item->breakfast == $batch_id){
                        $item->update(['b_status' => $status_id]);
                      }elseif($item->lunch == $batch_id){
                        $item->update(['l_status' => $status_id]);
                      }elseif($item->dinner == $batch_id){
                        $item->update(['d_status' => $status_id]);
                      }

                      $user = $item->user;
                      $message = 'Order status has been updated to packed';
                      $type = 'notifications';
                      $id = '';
                      $link = '';
                      if($user && $user->fcm_token){
                        $ret = sendSingleLocal($user, $message, $type, $id, $link, 'user');
                      }
                      $user = $item->driver;
                      if($user && $user->fcm_token){
                        $ret = sendSingleLocal($user, $message, $type, $id, $link, 'driver');
                      }
                      // no need to udpate restaurant status as its now updated in subscription table for package
                      // foreach($item->items as $it){
                      //   $it->update(['restaurant_status_id' => $status_id]);
                      // }
                    }
                  }

                }
                return response()->json(['status' => 'success', 'data' => [], 'message'=> 'SUCCESS'], 200);
                // else{
                //   return response()->json(['status' => 'error', 'data' => $item, 'message'=> 'Item does not exist'], 200);
                // }
            }
            else{
              $item_ids = explode(',', $item_id);
              foreach($item_ids as $order_id){
                $item = Order::where('id', $order_id)->first();
                if($item){
                  $item->update(['status_id' => $status_id]);
                  foreach($item->items as $it){
                    $it->update(['restaurant_status_id' => $status_id]);
                  }

                  $user = $item->user;
                  $message = 'Order status has been updated to packed';
                  $type = 'notifications';
                  $id = '';
                  $link = '';
                  if($user && $user->fcm_token){
                    $ret = sendSingleLocal($user, $message, $type, $id, $link, 'user');
                  }
                  $user = $item->driver;
                  if($user && $user->fcm_token){
                    $ret = sendSingleLocal($user, $message, $type, $id, $link, 'driver');
                  }
                }
                // else{
                //   return response()->json(['status' => 'error', 'data' => $item, 'message'=> 'Item does not exist'], 200);
                // }
              }
              return response()->json(['status' => 'success', 'data' => [], 'message'=> 'SUCCESS'], 200);
            }


        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }

    }

    public function acceptOrder(Request $request)
    {
        try{
            $auth_user = JWTAuth::toUser($request->input('token'));
            $item_id = $request->input('item_id');

            $item = Order::find($item_id);
            $end_date = '';
            if($item){
                if($item->type == 'package'){
                    $arr = [];
                    foreach($item->items as $tt){

                      $breakfast = $tt->item->breakfast;
                      $lunch = $tt->item->lunch;
                      $dinner = $tt->item->dinner;
                      $max = max($breakfast, $lunch, $dinner);

                      $actual_date = Carbon::parse($item->delivery_date);
                      $delivery_date = Carbon::parse($item->delivery_date);
                      $end_date = Carbon::parse($item->delivery_date)->addDays($max-1);

                      if($end_date->month > $delivery_date->month){
                        $end_date = $delivery_date->endOfMonth();
                      }

                      while($actual_date <= $end_date){
                        $date = $actual_date->toDateString();
                        $dd = [
                          'delivery_date' => $date,
                          'order_id'      => $item_id,
                          'item_id'       => $tt->item_id
                        ];
                        $subs = Subscription::where($dd)->first();
                        if(!$subs){
                          Subscription::create($dd);
                        }
                        $actual_date->addDay();
                      }
                    }
                }
                $item->update(['accepted' => '1', 'status_id' => 7]);
                return response()->json(['status' => 'success', 'data' => [], 'message'=> 'SUCCESS'], 200);
            }
            else{
                return response()->json(['status' => 'error', 'data' => $item, 'message'=> 'Item does not exist'], 200);
            }
        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function viewItem(Request $request)
    {
        try{
            $id = $request->input('id');
            $item = FoodMenu::findOrFail($id);
            if($item->image){
                $images = explode(',', $item->image);
                $i = [];
                foreach($images as $key => $img){
                    if($img != ''){
                        $i[] =$images[$key] = url($img);
                    }else{
                      unset($images[$key]);
                    }
                }
                $item->image = $i;//json_encode($images);
                $item->edit_image = $i;//$images;
            }else{
                // $item->image = [url('merchant/images/img-menu-01.jpg')];
                $item->edit_image = [url('customer/images/img_place.png')];
            }
            if($item->description == ''){
              $item->description = '';
            }
            $tags = [];
            $ids = [];
            foreach($item->tags as $tag){
                $tags[] = $tag->category->name;
                $ids[] = $tag->category_id;
            }

            $item->category = $tags;
            $item->category_id = $ids;
            $item->price = number_format($item->base_price,2);
            return response()->json(['status' => 'success', 'data' => $item, 'message'=> 'SUCCESS'], 200);

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }

    }

    public function deleteImage(Request $request)
    {
        try{
            $id = $request->input('id');
            $index = $request->input('index');
            $item = FoodMenu::findOrFail($id);
            if($item->image){
                $images = explode(',', $item->image);
                unset($images[$index]);

                $data['image'] = implode(',', $images);
                $item->update($data);

                return response()->json(['status' => 'success', 'data' => "Image removed successfully.", 'message'=> 'SUCCESS'], 200);
            }
            return response()->json(['status' => 'error', 'data' => 'No image to delete.', 'message'=> 'SUCCESS'], 200);

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function postItem(Request $request)
    {
        try{
            $auth_user = JWTAuth::toUser($request->input('token'));
            $restaurant = Restaurant::where('merchant_id', $auth_user->id)->select('id')->first();
            if($restaurant)
                $restaurant_id = $restaurant->id;
            else{
                dd('Ask admin to add restaurant information to your account to proceed.');
            }

            $data = $request->only('name', 'description', 'is_veg', 'is_halal', 'course_id', 'price');

            $data['base_price'] = $price = $data['price'];
            $restra = Restaurant::find($restaurant_id);
            if(!$restra){
              return response()->json(['status' => false, 'message' => 'Invalid restaurant.']);
            }
            $item = FoodMerchant::where('user_id', $restra->merchant_id)->first();

            $merchant = Merchant::find(12);
            $wlc_share_per = $merchant->merchant_share != ""?$merchant->merchant_share:8;
            $naanstap_share_per = $item->naanstap_share != ""?$item->naanstap_share:10;
            $flexm_per = $merchant->myma_transaction_charges != ""?$merchant->myma_transaction_charges:'2.5';

            $naanstap_share = number_format(($price*$naanstap_share_per)/100, 2);

            $selling_price = number_format((($price+$naanstap_share)/(1-($wlc_share_per/100)-($flexm_per/100))),2);
            $wlc_share = number_format(($selling_price*$wlc_share_per/100), 2);
            $flexm_share = number_format(($selling_price*$flexm_per)/100, 2);
            $data['price'] = $selling_price;

            $data['type'] = 'single';
            $files = $request->file('image');
            if(count($files)){
                $image = [];
                foreach($files as $fa){
                    $folder = "files/food";
                    $path = uploadPhoto($fa, $folder);
                    $image[] = $path;
                }
                if(count($image)){
                    $img = implode(',', $image);
                    $data['image'] = $img;
                }
            }
            // if($request->input('image')) {
            //   $files = $request->input('image');
            //   $files = explode('::',$files);
            //   $image = [];
            //   foreach($files as $file){
            //
            //       if(!$file){
            //           continue;
            //       }else{
            //           if (preg_match('/^data:image\/(\w+);base64,/', $file, $type)) {
            //               $file = substr($file, strpos($file, ',') + 1);
            //               $type = strtolower($type[1]); // jpg, png, gif
            //
            //               if (!in_array($type, [ 'jpg', 'jpeg', 'gif', 'png' ])) {
            //                   return response()->json(['status' => 'error', 'data' => [], 'message'=> 'Invalid image type supplied'], 200);
            //               }
            //
            //               $decode = base64_decode($file);
            //
            //               if ($decode === false) {
            //                   return response()->json(['status' => 'error', 'data' => [], 'message'=> 'Decode failed'], 200);
            //               }
            //               $folder = "files/food";
            //
            //               $path = savePhoto($file, $folder, $type);
            //               $image[] = $path;
            //           } else {
            //               $file = $request->file('image');
            //               $folder = "files/food";
            //
            //               $path = uploadPhoto($file, $folder);
            //               $image[] = $path;
            //
            //               // return response()->json(['status' => 'error', 'data' => [], 'message'=> 'Did not match data URI with image data'], 200);
            //           }
            //           if(count($image)){
            //               $data['image'] = implode(',', $image);
            //           }
            //       }
            //   }
            // }

            $auth_user = JWTAuth::toUser($request->input('token'));
            $restaurant = Restaurant::where('merchant_id', $auth_user->id)->select('id')->first();
            $data['restaurant_id'] = $restaurant->id;
            $food = FoodMenu::create($data);

            $tags = explode(',',$request->input('tags'));
            if(count($tags)){
                foreach($tags as $tag){
                    $cats = FoodCategory::where('id', $tag)->get();
                    if($cats->count()){
                        $cat_tag = $tag;
                    }else{
                        $cats = FoodCategory::create(['name' => ucfirst($tag), 'slug' => str_slug($tag)]);
                        $cat_tag = $cats->id;
                    }
                    FoodTag::create([
                        'food_id' => $food->id,
                        'category_id' => $cat_tag
                    ]);
                }
            }
            return response()->json(['status' => 'success', 'data' => [], 'message'=> 'Item added to menu.'], 200);

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function postItemEdit(Request $request)
    {
        try{
            $auth_user = JWTAuth::toUser($request->input('token'));
            $restaurant = Restaurant::where('merchant_id', $auth_user->id)->select('id')->first();
            if($restaurant)
                $restaurant_id = $restaurant->id;
            else{
                dd('Ask admin to add restaurant information to your account to proceed.');
            }

            $data = $request->only('name', 'description', 'is_veg', 'is_halal','price','course_id');

            $data['base_price'] = $price = $data['price'];
            $restra = Restaurant::find($restaurant_id);
            if(!$restra){
                return response()->json(['status' => false, 'message' => 'Invalid restaurant.']);
            }
            $item = FoodMerchant::where('user_id', $restra->merchant_id)->first();

            $merchant = Merchant::find(12);
            $wlc_share_per = $merchant->merchant_share != ""?$merchant->merchant_share:8;
            $naanstap_share_per = $item->naanstap_share != ""?$item->naanstap_share:10;
            $flexm_per = $merchant->myma_transaction_charges != ""?$merchant->myma_transaction_charges:'2.5';

            $naanstap_share = number_format(($price*$naanstap_share_per)/100, 2);

            $selling_price = number_format((($price+$naanstap_share)/(1-($wlc_share_per/100)-($flexm_per/100))),2);
            $wlc_share = number_format(($selling_price*$wlc_share_per/100), 2);
            $flexm_share = number_format(($selling_price*$flexm_per)/100, 2);
            $data['price'] = $selling_price;

            $id = $request->input('id');
            $item = FoodMenu::findOrFail($id);

            $files = $request->file('image');
            if(count($files)){
                $image = [];
                foreach($files as $fa){
                    $folder = "files/food";
                    $path = uploadPhoto($fa, $folder);
                    $image[] = $path;
                }
                if(count($image)){
                    $img = implode(',', $image);
                    if($item->image != ''){
                        $img = $item->image.','.$img;
                    }
                    $data['image'] = $img;
                }
            }
            // if($request->input('image')) {
            //   $files = $request->input('image');
            //   $files = explode('::',$files);
            //   $image = [];
            //   foreach($files as $file){
            //
            //       if(!$file){
            //           continue;
            //       }else{
            //           if (preg_match('/^data:image\/(\w+);base64,/', $file, $type)) {
            //               $file = substr($file, strpos($file, ',') + 1);
            //               $type = strtolower($type[1]); // jpg, png, gif
            //
            //               if (!in_array($type, [ 'jpg', 'jpeg', 'gif', 'png' ])) {
            //                   return response()->json(['status' => 'error', 'data' => [], 'message'=> 'Invalid image type supplied'], 200);
            //               }
            //
            //               $decode = base64_decode($file);
            //
            //               if ($decode === false) {
            //                   return response()->json(['status' => 'error', 'data' => [], 'message'=> 'Decode failed'], 200);
            //               }
            //               $folder = "files/food";
            //
            //               $path = savePhoto($file, $folder, $type);
            //               $image[] = $path;
            //           } else {
            //               $file = $request->file('image');
            //               $folder = "files/food";
            //
            //               $path = uploadPhoto($file, $folder);
            //               $image[] = $path;
            //
            //               // return response()->json(['status' => 'error', 'data' => [], 'message'=> 'Did not match data URI with image data'], 200);
            //           }
            //           if(count($image)){
            //               $data['image'] = implode(',', $image);
            //           }
            //       }
            //   }
            // }

            $auth_user = JWTAuth::toUser($request->input('token'));

            $item->update($data);

            FoodTag::where('food_id', $id)->delete();
            $tags = explode(',',$request->input('tags'));
            if(count($tags)){
                foreach($tags as $tag){
                    $cats = FoodCategory::where('id', $tag)->get();
                    if($cats->count()){
                        $cat_tag = $tag;
                    }else{
                        $cats = FoodCategory::create(['name' => ucfirst($tag), 'slug' => str_slug($tag)]);
                        $cat_tag = $cats->id;
                    }
                    FoodTag::create([
                        'food_id' => $id,
                        'category_id' => $cat_tag
                    ]);
                }
            }
            return response()->json(['status' => 'success', 'data' => [], 'message'=> 'Item updated.'], 200);

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function postPackage(Request $request)
    {
        try{
            $auth_user = JWTAuth::toUser($request->input('token'));
            $restaurant = Restaurant::where('merchant_id', $auth_user->id)->select('id')->first();
            if($restaurant)
                $restaurant_id = $restaurant->id;
            else{
                dd('Ask admin to add restaurant information to your account to proceed.');
            }
            $data = $request->only('name', 'description', 'is_veg', 'is_halal', 'breakfast', 'lunch', 'price', 'dinner');

            $data['base_price'] = $price = $data['price'];
            $restra = Restaurant::find($restaurant_id);
            if(!$restra){
              return response()->json(['status' => false, 'message' => 'Invalid restaurant.']);
            }
            $item = FoodMerchant::where('user_id', $restra->merchant_id)->first();

            $merchant = Merchant::find(13);
            $wlc_share_per = $merchant->merchant_share != ""?$merchant->merchant_share:8;
            $naanstap_share_per = $item->naanstap_share != ""?$item->naanstap_share:10;
            $flexm_per = $merchant->myma_transaction_charges != ""?$merchant->myma_transaction_charges:'2.5';

            $naanstap_share = number_format(($price*$naanstap_share_per)/100, 2);

            $selling_price = number_format((($price+$naanstap_share)/(1-($wlc_share_per/100)-($flexm_per/100))),2);
            $wlc_share = number_format(($selling_price*$wlc_share_per/100), 2);
            $flexm_share = number_format(($selling_price*$flexm_per)/100, 2);
            $data['price'] = $selling_price;

            $data['type'] = 'package';
            $files = $request->file('image');
            if(count($files)){
                $image = [];
                foreach($files as $fa){
                    $folder = "files/food";
                    $path = uploadPhoto($fa, $folder);
                    $image[] = $path;
                }
                if(count($image)){
                    $img = implode(',', $image);
                    $data['image'] = $img;
                }
            }
            // if($request->input('image')) {
            //   $fil = $request->input('image');
            //   $files = explode('::',$fil);
            //   $image = [];
            //   foreach($files as $file){
            //       if(!$file){
            //           continue;
            //       }else{
            //
            //           if (preg_match('/^data:image\/(\w+);base64,/', $file, $type)) {
            //               $file = substr($file, strpos($file, ',') + 1);
            //               $type = strtolower($type[1]); // jpg, png, gif
            //
            //               if (!in_array($type, [ 'jpg', 'jpeg', 'gif', 'png' ])) {
            //                   return response()->json(['status' => 'error', 'data' => [], 'message'=> 'Invalid image type supplied'], 200);
            //               }
            //
            //               $decode = base64_decode($file);
            //
            //               if ($decode === false) {
            //                   return response()->json(['status' => 'error', 'data' => [], 'message'=> 'Decode failed'], 200);
            //               }
            //               $folder = "files/food";
            //
            //               $path = savePhoto($file, $folder, $type);
            //               $image[] = $path;
            //
            //           } else {
            //               $file = $request->file('image');
            //               $folder = "files/food";
            //
            //               $path = uploadPhoto($file, $folder);
            //               $image[] = $path;
            //
            //               // return response()->json(['status' => 'error', 'data' => [], 'message'=> 'Did not match data URI with image data'], 200);
            //           }
            //           if(count($image)){
            //               $data['image'] = implode(',', $image);
            //           }
            //       }
            //   }
              // if (preg_match('/^data:image\/(\w+);base64,/', $file, $type)) {
              //     $file = substr($file, strpos($file, ',') + 1);
              //     $type = strtolower($type[1]); // jpg, png, gif
              //
              //     if (!in_array($type, [ 'jpg', 'jpeg', 'gif', 'png' ])) {
              //         return response()->json(['status' => 'error', 'data' => [], 'message'=> 'Invalid image type supplied'], 200);
              //         // throw new \Exception('invalid image type');
              //     }
              //
              //     $decode = base64_decode($file);
              //
              //     if ($decode === false) {
              //         return response()->json(['status' => 'error', 'data' => [], 'message'=> 'Decode failed'], 200);
              //         // throw new \Exception('base64_decode failed');
              //     }
              //     $folder = "files/food";
              //
              //     $path = savePhoto($file, $folder, $type);
              //     $data['image'] = $path;
              // } else {
              //     return response()->json(['status' => 'error', 'data' => [], 'message'=> 'Did not match data URI with image data'], 200);
              // }
            // }

            $auth_user = JWTAuth::toUser($request->input('token'));
            $restaurant = Restaurant::where('merchant_id', $auth_user->id)->select('id')->first();
            $data['restaurant_id'] = $restaurant->id;
            $food = FoodMenu::create($data);

            $tags = explode(',',$request->input('tags'));
            if(count($tags)){
                foreach($tags as $tag){
                    $cats = FoodCategory::where('id', $tag)->get();
                    if($cats->count()){
                        $cat_tag = $tag;
                    }else{
                        $cats = FoodCategory::create(['name' => ucfirst($tag), 'slug' => str_slug($tag)]);
                        $cat_tag = $cats->id;
                    }
                    FoodTag::create([
                        'food_id' => $food->id,
                        'category_id' => $cat_tag
                    ]);
                }
            }
            return response()->json(['status' => 'success', 'data' => [], 'message'=> 'Package created.'], 200);

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function editPackage(Request $request)
    {
        try{
            $id = $request->input('id');
            $item = FoodMenu::with('tags')->findOrFail($id);
            $tags = [];
            foreach($item->tags as $tag){
                $tags[] = $tag->category_id;
            }
            if($item->image){
                $images = explode(',', $item->image);
                foreach($images as $key =>  $img){
                    if($img){
                        $images[$key] = url($img);
                    }
                }
                $item->image = $images;
                $item->edit_image = $images;
            }else{
                // $item->image = [url('merchant/images/img-placeholder.png')];
                $item->edit_image = [url('customer/images/img_place.png')];
            }
            $item->category = $tags;
            $item->price = $item->base_price;

            return response()->json(['status' => 'success', 'data' => $item, 'message'=> 'SUCCESS'], 200);

        }catch(Exception $e){
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function updatePackage(Request $request)
    {

        try{
            $auth_user = JWTAuth::toUser($request->input('token'));
            $restaurant = Restaurant::where('merchant_id', $auth_user->id)->select('id')->first();
            if($restaurant)
                $restaurant_id = $restaurant->id;
            else{
                dd('Ask admin to add restaurant information to your account to proceed.');
            }
            $data = $request->only('name', 'description', 'is_veg', 'is_halal', 'breakfast', 'lunch', 'price','dinner');

            $data['base_price'] = $price = $data['price'];
            $restra = Restaurant::find($restaurant_id);
            if(!$restra){
              return response()->json(['status' => false, 'message' => 'Invalid restaurant.']);
            }
            $item = FoodMerchant::where('user_id', $restra->merchant_id)->first();

            $merchant = Merchant::find(13);
            $wlc_share_per = $merchant->merchant_share != ""?$merchant->merchant_share:8;
            $naanstap_share_per = $item->naanstap_share != ""?$item->naanstap_share:10;
            $flexm_per = $merchant->myma_transaction_charges != ""?$merchant->myma_transaction_charges:'2.5';

            $naanstap_share = number_format(($price*$naanstap_share_per)/100, 2);

            $selling_price = number_format((($price+$naanstap_share)/(1-($wlc_share_per/100)-($flexm_per/100))),2);
            $wlc_share = number_format(($selling_price*$wlc_share_per/100), 2);
            $flexm_share = number_format(($selling_price*$flexm_per)/100, 2);
            $data['price'] = $selling_price;

            $id = $request->input('id');
            $item = FoodMenu::findOrFail($id);
            $files = $request->file('image');
            if(count($files)){
                $image = [];
                foreach($files as $fa){
                    $folder = "files/food";
                    $path = uploadPhoto($fa, $folder);
                    $image[] = $path;
                }
                if(count($image)){
                    $img = implode(',', $image);
                    if($item->image != ''){
                        $img = $item->image.','.$img;
                    }
                    $data['image'] = $img;
                }
            }

            // if($request->input('image')) {
              // $files = $request->input('image');
              // $files = explode('::',$files);
              // $image = [];
              // foreach($files as $file){
              //
              //     if(!$file){
              //         continue;
              //     }else{
              //         if (preg_match('/^data:image\/(\w+);base64,/', $file, $type)) {
              //             $file = substr($file, strpos($file, ',') + 1);
              //             $type = strtolower($type[1]); // jpg, png, gif
              //
              //             if (!in_array($type, [ 'jpg', 'jpeg', 'gif', 'png' ])) {
              //                 return response()->json(['status' => 'error', 'data' => [], 'message'=> 'Invalid image type supplied'], 200);
              //             }
              //
              //             $decode = base64_decode($file);
              //
              //             if ($decode === false) {
              //                 return response()->json(['status' => 'error', 'data' => [], 'message'=> 'Decode failed'], 200);
              //             }
              //             $folder = "files/food";
              //
              //             $path = savePhoto($file, $folder, $type);
              //             $image[] = $path;
              //         } else {
              //             $file = $request->file('image');
              //             $folder = "files/food";
              //
              //             $path = uploadPhoto($file, $folder);
              //             $image[] = $path;
              //
              //             // return response()->json(['status' => 'error', 'data' => [], 'message'=> 'Did not match data URI with image data'], 200);
              //         }
              //         if(count($image)){
              //             $data['image'] = implode(',', $image);
              //         }
              //     }
              // }
            // }

            $auth_user = JWTAuth::toUser($request->input('token'));
            $item->update($data);

            FoodTag::where('food_id', $id)->delete();

            $tags = explode(',',$request->input('tags'));

            if(count($tags)){
                foreach($tags as $tag){
                    $cats = FoodCategory::where('id', $tag)->get();
                    if($cats->count()){
                        $cat_tag = $tag;
                    }else{
                        $cats = FoodCategory::create(['name' => ucfirst($tag), 'slug' => str_slug($tag)]);
                        $cat_tag = $cats->id;
                    }
                    FoodTag::create([
                        'food_id' => $id,
                        'category_id' => $cat_tag
                    ]);
                }
            }

            return response()->json(['status' => 'success', 'data' => [], 'message'=> 'Package updated.'], 200);

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }

    }

    public function getAccount(Request $request)
    {
        try{
            $auth_user = JWTAuth::toUser($request->input('token'));
            $restaurant = Restaurant::where('merchant_id', $auth_user->id)->select('id')->first();
            if($restaurant)
                $restaurant_id = $restaurant->id;
            else{
                dd('Ask admin to add restaurant information to your account to proceed.');
            }

            $status_ids = ['10','11','13'];
            $package = false;
            if($auth_user->hasRole('restaurant-owner-catering')){
              $package = true;
              $status_ids = ['7','8','10','11','13'];
            }
            $orders = Order::whereHas('items', function($q) use($restaurant_id){
                $q->whereHas('item', function($qq) use($restaurant_id){
                        $qq->where('restaurant_id', $restaurant_id);
                });
            })->whereIn('status_id', $status_ids)->orderBy('created_at', 'desc')->orderBy('id', 'desc')->get();

            $dd = [];
            foreach($orders as $order){
              // $trans = Transactions::where('type', 'like', '%food%')->where('ref_id', $order->id)->first();

              if($order->type == 'single'){
                if($order->dormitory_id){
                  $batch = Batch::where('dormitory_id', $order->dormitory_id)->whereDate('batch_date', $order->delivery_date)->first();
                }else{
                  $batch = Batch::where('address','like', $order->address)->whereDate('batch_date', $order->delivery_date)->first();
                }

                $batch_id = '';
                if($batch){
                  if($order->delivery_time == '07:00:00'){
                    $batch_id = $batch->batch_b;
                  }
                  elseif($order->delivery_time == '12:00:00'){
                    $batch_id = $batch->batch_l;
                  }
                  elseif($order->delivery_time == '19:00:00'){
                    $batch_id = $batch->batch_d;
                  }
                }
              }else{

                $batch_id = $order->id;
              //   if($batch){
              //     $batch_id = $batch->batch_b;
              //   }
              // }

                // $items = \DB::table(config("app.db_portal")'.order_items')
                // ->join(config("app.db_portal")'.orders', function ($join) use($batch_id){
                //     $join->on('orders.id', '=', 'order_items.order_id')
                //          ->where('orders.batch_id', $batch_id);
                // })
                // ->join(config("app.db_portal")'.food_menu', 'food_menu.id', '=', 'order_items.item_id')
                // ->select('order_items.*', 'food_menu.price')
                // ->get();
                // $total = 0;
                // foreach($items as $item){
                //     $total += $item->quantity*$item->price;
              }

              $order->batch_id = $batch_id;
              $order->sub_total = $order->total;//$total;
              //($trans != '' && $trans->food_share != '')?$trans->food_share:
              $dd[$batch_id]['batch_id'] = $batch_id;
              $dd[$batch_id]['sub_total'] = isset($dd[$batch_id]['sub_total'])?($dd[$batch_id]['sub_total']+$order->total):$order->total;

              $inv = FoodInvoices::find($batch_id);
              if($inv){
                $dd[$batch_id]['status'] = ucfirst($inv->status);
              }else{
                $dd[$batch_id]['status'] = 'Pending';
              }
            }

            $dd = array_values($dd);

            return response()->json(['status' => 'success', 'data' => $dd, 'orders' => $orders, 'message'=> 'SUCCESS'], 200);

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function getAccountDetail(Request $request)
    {
        try{
            $auth_user = JWTAuth::toUser($request->input('token'));
            $package = false;
            if($auth_user->hasRole('restaurant-owner-catering')){
              $package = true;
            }

            $restaurant = Restaurant::where('merchant_id', $auth_user->id)->select('id')->first();
            if($restaurant)
                $restaurant_id = $restaurant->id;
            else{
                dd('Ask admin to add restaurant information to your account to proceed.');
            }
            $batch_id = $request->input('batch_id');
            $items = [];
            if($package){
              // $batch_id contains order id for the package user type
              // $items = Order::find($batch_id);

              $items = \DB::table(config("app.db_portal").'.order_items')
              ->join(config("app.db_portal").'.food_menu', 'food_menu.id', '=', 'order_items.item_id')
              ->where('order_id', $batch_id)
              ->select('order_items.*', 'order_items.item_price as price','food_menu.name')
              ->get();

            }else{
              $status_ids = ['10','11','13'];
              $batch_b = Batch::where('batch_b', $batch_id)->first();
              $batch_l = Batch::where('batch_l', $batch_id)->first();
              $batch_d = Batch::where('batch_d', $batch_id)->first();

              if($batch_b){
                $items = \DB::table(config("app.db_portal").'.order_items')
                ->join(config("app.db_portal").'.orders', function ($join) use($batch_b){
                  $join->on('orders.id', '=', 'order_items.order_id')
                  ->whereDate('orders.delivery_date', $batch_b->batch_date);
                })
                ->join(config("app.db_portal").'.food_menu', 'food_menu.id', '=', 'order_items.item_id')
                ->where('orders.delivery_time', '07:00:00')
                ->where('orders.type', 'single')
                ->whereIn('orders.status_id', $status_ids)
                ->select('order_items.*', 'order_items.item_price as price','food_menu.name')
                ->get();
              }
              elseif($batch_l){
                $items = \DB::table(config("app.db_portal").'.order_items')
                ->join(config("app.db_portal").'.orders', function ($join) use($batch_l){
                  $join->on('orders.id', '=', 'order_items.order_id')
                  ->whereDate('orders.delivery_date', $batch_l->batch_date);
                })
                ->join(config("app.db_portal").'.food_menu', 'food_menu.id', '=', 'order_items.item_id')
                ->where('orders.delivery_time', '12:00:00')
                ->whereIn('orders.status_id', $status_ids)
                ->where('orders.type', 'single')
                ->select('order_items.*', 'order_items.item_price as price','food_menu.name')
                ->get();
              }
              elseif($batch_d){
                $items = \DB::table(config("app.db_portal").'.order_items')
                ->join(config("app.db_portal").'.orders', function ($join){
                  $join->on('orders.id', '=', 'order_items.order_id');
                })
                ->join(config("app.db_portal").'.food_menu', 'food_menu.id', '=', 'order_items.item_id')
                ->whereDate('orders.delivery_date', $batch_d->batch_date)
                ->where('orders.delivery_time', '19:00:00')
                ->whereIn('orders.status_id', $status_ids)
                ->where('orders.type', 'single')
                ->select('order_items.*', 'order_items.item_price as price','food_menu.name')
                ->get();
              }

            }

            return response()->json(['status' => 'success', 'data' => $items, 'message'=> 'SUCCESS'], 200);

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function getInvoiceDetail(Request $request)
    {
        try{
            $auth_user = JWTAuth::toUser($request->input('token'));
            $package = false;
            if($auth_user->hasRole('restaurant-owner-catering')){
              $package = true;
            }

            $batch_id = $request->input('batch_id');


            $restaurant = Restaurant::where('merchant_id', $auth_user->id)->select('id')->first();
            if($restaurant)
                $restaurant_id = $restaurant->id;
            else{
                dd('Ask admin to add restaurant information to your account to proceed.');
            }
            $items = [];

            if($package){
              $items = \DB::table(config("app.db_portal").'.order_items')
              ->join(config("app.db_portal").'.food_menu', 'food_menu.id', '=', 'order_items.item_id')
              ->where('order_id', $batch_id)
              ->select('order_items.*', 'order_items.item_price as price','food_menu.name')
              ->get();

            }else{
              $status_ids = ['10','11','13'];

              $batch_b = Batch::where('batch_b', $batch_id)->first();
              $batch_l = Batch::where('batch_l', $batch_id)->first();
              $batch_d = Batch::where('batch_d', $batch_id)->first();

              if($batch_b){
                $items = \DB::table(config("app.db_portal").'.order_items')
                ->join(config("app.db_portal").'.orders', function ($join) use($batch_b){
                  $join->on('orders.id', '=', 'order_items.order_id')
                  ->whereDate('orders.delivery_date', $batch_b->batch_date);
                })
                ->join(config("app.db_portal").'.food_menu', 'food_menu.id', '=', 'order_items.item_id')
                ->where('orders.delivery_time', '07:00:00')
                ->whereIn('orders.status_id', $status_ids)
                ->where('orders.type', 'single')
                ->select('order_items.*', 'order_items.item_price as price','food_menu.name')
                ->get();
              }
              elseif($batch_l){
                $items = \DB::table(config("app.db_portal").'.order_items')
                ->join(config("app.db_portal").'.orders', function ($join) use($batch_l){
                  $join->on('orders.id', '=', 'order_items.order_id')
                  ->whereDate('orders.delivery_date', $batch_l->batch_date);
                })
                ->join(config("app.db_portal").'.food_menu', 'food_menu.id', '=', 'order_items.item_id')
                ->where('orders.delivery_time', '12:00:00')
                ->whereIn('orders.status_id', $status_ids)
                ->where('orders.type', 'single')
                ->select('order_items.*', 'order_items.item_price as price','food_menu.name')
                ->get();
              }
              elseif($batch_d){
                $items = \DB::table(config("app.db_portal").'.order_items')
                ->join(config("app.db_portal").'.orders', function ($join) use($batch_d){
                  $join->on('orders.id', '=', 'order_items.order_id')
                  ->whereDate('orders.delivery_date', $batch_d->batch_date);
                })
                ->join(config("app.db_portal").'.food_menu', 'food_menu.id', '=', 'order_items.item_id')
                ->where('orders.delivery_time', '19:00:00')
                ->whereIn('orders.status_id', $status_ids)
                ->where('orders.type', 'single')
                ->select('order_items.*', 'order_items.item_price as price','food_menu.name')
                ->get();
              }

            }

            $invoice = FoodInvoices::where('batch_id', $batch_id)->first();
            $transaction_charge = 0;
            $myma_share = 0;
            $arr = [];
            $order_total = 0;
            foreach($items as $item){
                $trans = Transactions::where('type', 'like', '%food%')->where('ref_id', $item->order_id)->first();
                if(!in_array($item->order_id, $arr)){
                  $arr[] = $item->order_id;
                  $order = Order::find($item->order_id);
                  $flexm = 0;
                  if($order){
                    $flexm = $order->flexm;
                    $order_total += $order->total;
                  }
                  if($trans){
                    $myma_share += ($trans->transaction_amount - $trans->food_share);
                  }
                  $transaction_charge += $flexm;
                }
            }
            if($package){
              $share_per = getOption('food_package_share', 40);
            }else{
              $share_per = getOption('food_single_share', 40);
            }
            // $order_total -= $transaction_charge;
            // $myma_share = ($order_total*(100-$share_per))/100;

            if(!$invoice){
                $transaction_charge = 0;
                $total = 0;
                $arr = [];
                foreach($items as $item){
                    if(!in_array($item->order_id, $arr)){
                      $arr[] = $item->order_id;
                      $order = Order::find($item->order_id);
                      $flexm = 0;
                      if($order){
                        $flexm = $order->flexm;
                        $order_total += $order->total;
                      }
                      $transaction_charge += $flexm;
                    }
                    $total += $item->quantity*$item->price;
                }
                $data['batch_id'] = $batch_id;
                $data['status'] = 'pending';
                $data['user_id'] = $auth_user->id;
                // $data['user_id'] = 'merchant';
                $data['total'] = $total-$myma_share;
                $invoice = FoodInvoices::create($data);
            }

            $invoice->transaction_charge = $transaction_charge;
            $invoice->myma_share = number_format($myma_share,2);
            $invoice->invoice_date = Carbon::parse($invoice->created_at)->format('Y-m-d');

            $dd['items'] = $items;
            $dd['invoice'] = $invoice;

            return response()->json(['status' => 'success', 'data' => $dd, 'message'=> 'SUCCESS'], 200);

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function deleteItem(Request $request)
    {

      try{
          $id = $request->input('id');
          $item = FoodMenu::where('id', $id)->first();
          if($item){
            $item->delete();
            return response()->json(['status' => 'success', 'data' => 'Item Deleted.', 'message' => 'Item Deleted'], 200);
          }else{
            return response()->json(['status' => 'error', 'data' => 'Item does not exists.', 'message' => 'Item does not exists'], 200);
          }
      }catch(Exception $e){
          return response()->json(['status' => 'error', 'data'=> $e->getMessage(), 'message' => 'EXCEPTION_OCCURED'], $e->getStatusCode());
      }
    }

}
