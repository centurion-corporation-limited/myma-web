<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Models\Coupon;
use App\Models\FoodMenu;
// use App\Models\FoodCategory;
// use App\Models\FoodPackage;
// use App\Models\FoodTag;
use App\Models\Restaurant;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\AddCouponRequest;
use App\Http\Requests\EditCouponRequest;
use App\Http\Controllers\Controller;
use Auth, Activity;
use Carbon\Carbon;


class CouponController extends Controller
{
    public function getList(Request $request)
    {
      $auth_user = Auth::user();

      $items = Coupon::query();

      if ($id = $request->input('id')) {
          $items->where('id', $id);
      }

      if ($type = $request->input('type')) {
          $now = Carbon::now();
          $items->whereDate('expiry', '>=', $now);
      }

      $limit = 10;
      $items = $items->sortable(['id' => 'desc'])->paginate($limit);
      $paginate_data = $request->except('page');

      $now = Carbon::now();
      foreach($items as $item){
        $end = Carbon::parse($item->expiry);
        if($now->diffInDays($end, false) < 0){
          $item->status = 'Expired';
        }else{
          $item->status = 'Active';
        }
      }

      return view('admin.food.coupon.list', compact('items', 'auth_user', 'paginate_data'));
    }


    public function getAdd()
    {
        $auth_user = \Auth::user();
        $restra = Restaurant::pluck('name','id');
        $types = ['direct' => 'Direct', 'percent' => 'Percent'];
        $restra_type = ['single' => 'Food Outlet', 'package' => 'Catering'];

        $merchants = User::whereHas('roles', function($q){
            $q->whereIn('slug', ['restaurant-owner-single','restaurant-owner-catering'] );
        })->where('blocked', '0')->pluck('name', 'id');

        return view('admin.food.coupon.add', compact('auth_user', 'restra', 'types', 'restra_type', 'merchants'));
    }

    public function postAdd(AddCouponRequest $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();

        $data = $request->only('code', 'type', 'value', 'expiry', 'merchant_id', 'price', 'item_ids');
        if(is_array($data['item_ids'])){
          $data['item_ids'] = implode(',', $data['item_ids']);
        }
        if($data['expiry'] != ''){
            $start = explode('/',$data['expiry']);
            $start = Carbon::create($start[2],$start[1],$start[0]);
            $data['expiry'] = $start->toDateTimeString();
        }

        $data['created_by'] = $auth_user->id;
        $coupon = Coupon::create($data);

        Activity::log('Added new Coupon #'.@$coupon->id. ' by '.$auth_user->name);

        return redirect()->route('admin.coupon.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Coupon added successfully.',
        ]);

    }

    public function getView($id, Request $request)
    {
		    $auth_user = \Auth::user();
        $id = decrypt($id);
		    $item = Coupon::findOrFail($id);
        $item->expiry = date('d/m/Y', strtotime($item->expiry));

        return view('admin.food.coupon.view', compact('item', 'auth_user'));
    }

    public function getEdit($id, Request $request)
    {
    	  $auth_user = \Auth::user();
        $id = decrypt($id);
    	  $item = Coupon::findOrFail($id);
        $item->expiry = date('d/m/Y', strtotime($item->expiry));
        $types = ['direct' => 'Direct', 'percent' => 'Percent'];
        $restra_type = ['single' => 'Food Outlet', 'package' => 'Catering'];

        $merchants = User::whereHas('roles', function($q){
            $q->whereIn('slug', ['restaurant-owner-single','restaurant-owner-catering'] );
        })->where('blocked', '0')->pluck('name', 'id');

        $restra = Restaurant::where('merchant_id', $item->merchant_id)->first();
        if($restra){
            $items = FoodMenu::where('published', '1')->where('restaurant_id', $restra->id)->get();
        }

        if($item){
          $item->item_ids = explode(',', $item->item_ids);
        }

        return view('admin.food.coupon.edit', compact('item', 'auth_user', 'types', 'restra_type', 'merchants', 'items'));
    }

    public function postEdit($id, EditCouponRequest $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $id = decrypt($id);
        $coupon = Coupon::findOrFail($id);

        $data = $request->only('code', 'type', 'value', 'expiry', 'merchant_id', 'price', 'item_ids');
        
        if(is_array($data['item_ids'])){
          $data['item_ids'] = implode(',', $data['item_ids']);
        }
        if($data['expiry'] != ''){
            $start = explode('/',$data['expiry']);
            $start = Carbon::create($start[2],$start[1],$start[0]);
            $data['expiry'] = $start->toDateString();
        }


        $up = $coupon->update($data);

        Activity::log('Updated coupon #'.$id. ' by '.$auth_user->name);

        return redirect()->route('admin.coupon.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Coupon updated successfully.',
        ]);

    }

    public function getDelete($id)
    {
        Coupon::destroy($id);
        $auth_user = Auth::user();
        Activity::log('Deleted coupon #'.$id. ' by '.$auth_user->name);

        return redirect()->route('admin.coupon.list')->with([
            'flash_level'   => 'danger',
            'flash_message' => 'Coupon Deleted',
        ]);
    }

}
