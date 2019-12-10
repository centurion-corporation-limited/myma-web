<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Subscription;
use App\Models\Restaurant;
use App\Models\FoodCategory;
use App\Models\FoodCourse;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth, Carbon\Carbon;

class SubscriptionsController extends Controller
{
    public function getList(Request $request)
    {
      $auth_user = Auth::user();

      $items = Order::where('type', 'package');

      // if ($id = $request->input('id')) {
      //     $items->where('id', $id);
      // }

      $limit = 50;
      $items = $items->sortable(['id' => 'desc'])->paginate($limit);
      $paginate_data = $request->except('page');

      return view('admin.food.subscriptions.list', compact('items', 'auth_user', 'paginate_data', 'limit'));
    }


    public function getAdd()
    {
        $auth_user = \Auth::user();
        $users = User::whereHas('roles', function($q){
            $q->whereIn('slug', ['restaurant-owner-package', 'restaurant-owner-single'] );
        })->where('blocked', '0')->pluck('email','id');

        return view('admin.food.subscriptions.add', compact('auth_user', 'users'));
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

        return view('admin.food.subscriptions.view', compact('item', 'users'));
    }

    public function getSubscriptionView($id, Request $request)
    {
        $order = OrderItem::findOrFail($id);
        $date = Carbon::parse($order->order->delivery_date)->format('M d, Y');
        $end_date = Carbon::parse($order->order->delivery_date)->addDays(max($order->item->breakfast, $order->item->lunch, $order->item->dinner)-1);
        $end = new Carbon('last day of this month');
        if($end < $end_date){
            $end_date = $end;
        }
        $subs = Subscription::where('item_id', $order->item_id)->where('order_id', $order->order->id)->get()->toArray();

        return view('admin.food.subscriptions.detail', compact('order', 'date', 'end_date', 'subs'));
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

        return view('admin.food.subscriptions.edit', compact('item', 'users'));
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

}
