<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\FoodCategory;
use App\Models\FoodCourse;
use App\Models\Status;
use App\Models\Batch;
use App\Models\Subscription;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth, Excel,  Carbon\Carbon;

class OrderController extends Controller
{
    public function getList(Request $request)
    {
      $auth_user = Auth::user();

      $items = Order::query();

      $type = $request->input('type');
      if ($type == 'today') {
          $now = Carbon::now();
          $items->whereDate('delivery_date', '>=', $now);
      }

      $status_id = $request->input('status_id');
      if ($status_id != '' && $status_id != 0) {
          $items->where('status_id', $status_id);
      }

      $flag = false;
      if($auth_user->hasRole('restaurant-owner-single|restaurant-owner-catering')){
          $flag = true;
          $restaurant_id = "";
          $restaurant = Restaurant::where('merchant_id', $auth_user->id)->first();
          if($restaurant){
            $restaurant_id = $restaurant->id;
          }
          $items->whereHas('items', function($q) use ($restaurant_id){
            $q->whereHas('item', function($qq) use ($restaurant_id){
              $qq->where('restaurant_id', $restaurant_id);
            });
          });
      }

      $statuses = Status::where('type', 'order')->pluck('name','id')->toArray();
      $statuses[0] = 'Select status';
      ksort($statuses);
      $limit = 50;
      $items = $items->sortable(['id' => 'desc'])->paginate($limit);
      $paginate_data = $request->except('page');

      return view('admin.food.orders.list', compact('items', 'auth_user', 'paginate_data', 'statuses', 'limit'));
    }


    public function getAdd()
    {
        $auth_user = \Auth::user();
        $users = User::whereHas('roles', function($q){
            $q->whereIn('slug', ['restaurant-owner-package', 'restaurant-owner-single'] );
        })->where('blocked', '0')->pluck('email','id');

        return view('admin.food.orders.add', compact('auth_user', 'users'));
    }

    public function postAdd(Request $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $data = $request->only('name', 'merchant_id', 'open_at', 'closes_at', 'gst_no', 'phone_no', 'address');
        $data['open_at'] = date('H:i:s', strtotime($data['open_at']));
        $data['closes_at'] = date('H:i:s', strtotime($data['closes_at']));

        $module = Order::create($data);
        // if($module){
        //     $user = User::findOrFail($data['manager_id']);
        //     $user->addPermission('view.maintenance-list');
        //
        // }

        return redirect()->route('admin.restaurant.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Restaurant added successfully.',
        ]);

    }

    public function getView($id, Request $request)
    {
        $id = decrypt($id);
		    $auth_user = \Auth::user();
		    $item = Order::findOrFail($id);
        $users = User::whereHas('roles', function($q){
            $q->whereIn('slug', ['restaurant-owner-package', 'restaurant-owner-single'] );
        })->where('blocked', '0')->pluck('email','id');

        foreach($item->items as $oitem){
          $item->merchant_rep = $oitem->name;
          $item->customer_rep = $oitem->deliver_name;
        }

        return view('admin.food.orders.view', compact('item', 'users', 'auth_user'));
    }

    public function getEdit($id, Request $request)
    {
        $id = decrypt($id);
    	  $auth_user = \Auth::user();
    	  $item = Order::findOrFail($id);
        // $status = Status::pluck('name', 'id');
        $users = User::whereHas('roles', function($q){
            $q->whereIn('slug', ['restaurant-owner-package', 'restaurant-owner-single'] );
        })->where('blocked', '0')->pluck('email','id');


        return view('admin.food.orders.edit', compact('item', 'users'));
    }

    public function postEdit($id, Request $request)
    {
        $id = decrypt($id);
        /** @var User $item */
        $auth_user = \Auth::user();
        $module = Order::findOrFail($id);

        $data = $request->only('name', 'merchant_id', 'open_at', 'closes_at', 'gst_no', 'phone_no', 'address');
        $data['open_at'] = date('H:i:s', strtotime($data['open_at']));
        $data['closes_at'] = date('H:i:s', strtotime($data['closes_at']));

        $up = $module->update($data);

        // if($up){
        //     $user = User::findOrFail($data['manager_id']);
        //     $user->addPermission('view.maintenance-list');
        // }

        return redirect()->route('admin.restaurant.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Restaurant details updated successfully.',
        ]);

    }

    public function getDelete($id)
    {
        $id = decrypt($id);
        Order::destroy($id);

        return redirect()->route('admin.orders.list')->with([
            'flash_level'   => 'danger',
            'flash_message' => 'Order Deleted',
        ]);
    }

    public function getInvoices(Request $request)
    {
      $auth_user = Auth::user();
      $from = request('start');
      $to = request('end');
      $items = [];
      if($from != '' && $to != ''){
        $from = explode('/', $from);
        $to = explode('/', $to);

        $from = Carbon::create($from[2], $from[1], $from[0])->toDateString();
        $to = Carbon::create($to[2],$to[1],$to[0])->toDateString();
        $items = Order::whereDate('created_at', '>=',$from)->whereDate('created_at', '<=', $to)->get();

        foreach($items as $order){
          if($order->invoice_id == ''){
            $invoice = 'NS-';
            $restaurant_name = '';
            foreach($order->items as $itemss){
              $restaurant_name = @$itemss->item->restaurant->name;
              if($restaurant_name != ''){
                break;
              }
            }
            $restaurant_name = str_replace(' ','', strtoupper($restaurant_name));
            $invoice .= $restaurant_name.'-'.$order->id;
            $order->invoice_id = $invoice;
          }
        }
        $name = 'invoices';
        $pdf = \PDF::loadView('admin.food.orders.print_invoice', compact('items'));
        return $pdf->download($name.'.pdf');

        // return view('admin.food.orders.print_invoice', compact('items'));
      }
      return view('admin.food.orders.invoices', compact('auth_user'));
    }

    public function getWlcInvoice($id, Request $request)
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
      $restaurant = [];

      foreach($order->items as $item){
          if($item->item && $item->item->restaurant){
              $restaurant = $item->item->restaurant;
              break;
          }
      }
      return view('admin.food.orders.wlc_invoice', compact('auth_user', 'order', 'restaurant'));
    }

    public function getMerchantInvoice($id, Request $request)
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
      $restaurant = [];

      foreach($order->items as $item){
          if($item->item && $item->item->restaurant){
              $restaurant = $item->item->restaurant;
              break;
          }
      }
      return view('admin.food.orders.merchant_invoice', compact('auth_user', 'order', 'restaurant'));
    }

    public function postInvoices(Request $request)
    {
      $auth_user = Auth::user();

      return view('admin.food.orders.invoices', compact('auth_user'));
    }

    public function getBatch(Request $request)
    {
      $auth_user = Auth::user();
      $batch_id = request('batch_id');
      $items =  [];
      if($batch_id != ''){
        $batch_id = strtoupper($batch_id);
        $sub_ids = Subscription::where('breakfast', $batch_id)->orWhere('lunch', $batch_id)->orWhere('dinner', $batch_id)->pluck('order_id');
        $items = Order::query();
        $items = $items->where('batch_id', $batch_id)->orWhereIn('id', $sub_ids)->get();

      }else{

      }

      // $limit = 10;
      // $items = $items->sortable()->paginate($limit);
      return view('admin.food.orders.batch', compact('auth_user','items'));
    }
    
    public function getWorker(Request $request)
    {
      $auth_user = Auth::user();

      $items = [];
      if($date = $request->input('date')){
        $date = Carbon::parse($date)->toDateString();
        $batches = Batch::where('batch_date', $date)->get();
        if($batches->count()){
            foreach($batches as $batch){
              $dd = [];
              if($batch->dormitory){
                $dd['address'] = $batch->dormitory->name;
              }else{
                $dd['address'] = $batch->address;
              }
              $dd['delivery_date'] = $batch->batch_date;
    
              $dd['batch_id'] = $batch->batch_b;
              $items[] = $dd;
              $dd['batch_id'] = $batch->batch_l;
              $items[] = $dd;
              $dd['batch_id'] = $batch->batch_d;
              $items[] = $dd;
            }
        }else{
            $addresses = [];
            $subs = Subscription::where('delivery_date', $date)->get();
            foreach($subs as $sub){
                $dd = [];
                if(@$sub->order->dormitory){
                    if(in_array($sub->order->dormitory_id, $addresses)){
                        continue;
                    }
                    $addresses[] = @$sub->order->dormitory_id;
                    $dd['address'] = @$sub->order->dormitory->name;
                }else{
                    if(in_array($sub->order->address, $addresses)){
                        continue;
                    }
                    $addresses[] = @$sub->order->address;
                    $dd['address'] = @$sub->order->address;
                }
                $dd['delivery_date'] = $sub->delivery_date;
    
                $dd['batch_id'] = '';
                $items[] = $dd;
                $dd['batch_id'] = '';
                $items[] = $dd;
                $dd['batch_id'] = '';
                $items[] = $dd;
            }
        }
      }


      return view('admin.food.orders.worker', compact('auth_user','items'));
    }

    public function exportWorker($batch_id, Request $request)
    {

        $orders = Order::where('batch_id', $batch_id)->get();
        $items = [];
        foreach($orders as $order){
          if($order->user){
              $dd = [];
              $dd['name'] = @$order->user->name;
              $dd['phone'] = @$order->phone_no;
              $dd['item'] = '';
              $dd['quantity'] = 0;
              foreach($order->items as $item){
                  if($dd['item'] != ''){
                      $dd['item'] .= ' ,';
                  }
                  $dd['item'] .= $item->item->name;
                  $dd['quantity'] += @$item->quantity;
              }
              
              $dd['address'] = @$order->dormitory?$order->dormitory->name:$order->address;
              $items[] = $dd;
          }
        }

        $subs = Subscription::where('breakfast', $batch_id)->orWhere('lunch', $batch_id)->orWhere('dinner', $batch_id)->get();
        foreach($subs as $sub){
          if($sub->order){
            if($sub->order->user){
              $dd = [];
              $dd['name'] = @$sub->order->user->name;
              $dd['phone'] = @$sub->order->phone_no;
              
              $dd['item'] = '';
              $dd['quantity'] = 0;
              foreach($sub->order->items as $item){
                  if($sub->item_id == $item->item_id){
                      if($dd['item'] != ''){
                          $dd['item'] .= ' ,';
                      }
                      $dd['item'] .= $item->item->name;
                      $dd['quantity'] += @$item->quantity;    
                  }else{
                      continue;
                  }
              }
              
              $dd['address'] = @$sub->order->dormitory?$sub->order->dormitory->name:$sub->order->address;
              $items[] = $dd;
            //   $items['name'] = @$sub->order->user->name;
            }
          }
        }
        $batch = Batch::where('batch_b', $batch_id)->orWhere('batch_l', $batch_id)->orWhere('batch_d', $batch_id)->first();
        $date = $batch->batch_date;
        $type = '';
        if($batch->batch_b == $batch_id){
          $type = 'Breakfast';
        }elseif($batch->batch_l == $batch_id){
          $type = 'Lunch';
        }else{
          $type = 'Dinner';
        }
        $paymentsArray = [];
        $paymentsArray[] = array('Date', $date);
        $paymentsArray[] = array('Type', $type);
        $paymentsArray[] = array('', '');

        $paymentsArray[] = array('SR.No','Name', 'Mobile No', 'Menu Type', 'Quantity', 'Address/Dorm','Remarks');

        $key = 1;
        foreach($items as $item){
            $arr = [];
            $arr[] = $key++;
            $arr[] = $item['name'];
            $arr[] = $item['phone'];
            $arr[] = $item['item'];
            $arr[] = $item['quantity'];
            $arr[] = $item['address'];
            $arr[] = '';
            $paymentsArray[] = $arr;
        }

        // dd($paymentsArray);

        Excel::create('Workers', function($excel) use ($paymentsArray) {
          // Set the spreadsheet title, creator, and description
            $excel->setTitle('Workers List');
            $excel->setCreator('Myma')->setCompany('Myma');
            // $excel->setDescription('payments file');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($paymentsArray) {
                $sheet->fromArray($paymentsArray, null, 'A1', false, false);
            });
        })->export('xls');
    }
}
