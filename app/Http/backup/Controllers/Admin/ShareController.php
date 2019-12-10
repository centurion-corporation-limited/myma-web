<?php

namespace App\Http\Controllers\Admin;

use App\User, Activity;
use DB, Auth;
use App\Models\Share;
use App\Models\Account;
use App\Models\FoodMerchant;
use App\Models\Merchant;
use App\Models\MerchantCode;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\EditShareRequest;
use App\Http\Requests\FoodMerchantRequest;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\Option;

class ShareController extends Controller
{
    public function getCateringShare(Request $request)
    {
        $auth_user = \Auth::user();
        $item = Merchant::find(13);
        if($item){
          $acc = Account::where('merchant_id', 13)->where('merchant_type', 'flexm')->first();
          if($acc){
            $item->bank_name = $acc->bank_name;
            $item->account_number = $acc->account_number;
            $item->bank_address = $acc->bank_address;
            $item->bank_country = $acc->bank_country;
            $item->routing_code = $acc->routing_code;
            $item->swift_code = $acc->swift_code;
          }
        }

        $merchant_id = 13;
        return view('admin.share.catering', compact('auth_user', 'item', 'merchant_id'));
    }

    public function getNaanstapShare(Request $request)
    {
        $auth_user = \Auth::user();
        $item = Merchant::find(12);
        if($item){
          $acc = Account::where('merchant_id', 12)->where('merchant_type', 'flexm')->first();
          if($acc){
            $item->bank_name = $acc->bank_name;
            $item->account_number = $acc->account_number;
            $item->bank_address = $acc->bank_address;
            $item->bank_country = $acc->bank_country;
            $item->routing_code = $acc->routing_code;
            $item->swift_code = $acc->swift_code;
          }
        }
        $merchant_id = 12;
        return view('admin.share.naanstap', compact('auth_user', 'item', 'merchant_id'));
    }

    public function getSpuulShare(Request $request)
    {
        $auth_user = \Auth::user();
        $item = Merchant::find(2);
        if($item){
          $acc = Account::where('merchant_id', 2)->where('merchant_type', 'flexm')->first();
          if($acc){
            $item->bank_name = $acc->bank_name;
            $item->account_number = $acc->account_number;
            $item->bank_address = $acc->bank_address;
            $item->bank_country = $acc->bank_country;
            $item->routing_code = $acc->routing_code;
            $item->swift_code = $acc->swift_code;
          }
        }
        $merchant_id = 2;
        return view('admin.share.spuul', compact('auth_user', 'item', 'merchant_id'));
    }

    public function postShare($merchant_id, Request $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $merchant_id = decrypt($merchant_id);
        $item = Merchant::findOrFail($merchant_id);
        $data = $request->only('merchant_share', 'myma_transaction_share', 'revenue_model', 'product_type', 'merchant_address_1',
        'merchant_address_2', 'merchant_address_3', 'frequency');
        $item->update($data);
        //add bank account
        $bank_name = $request->input('bank_name');
        $acc_number = $request->input('account_number');
        $bank_address = $request->input('bank_address');
        $bank_country = $request->input('bank_country');
        $routing_code = $request->input('routing_code');
        $swift_code = $request->input('swift_code');
        // if($bank_name != '' || $acc_number != ''){
          $exist = Account::where('merchant_id', $merchant_id)->where('merchant_type', 'flexm')->first();
          if($exist){
            $exist->update([
              'bank_name' => $bank_name,
              'account_number' => $acc_number,
              'bank_address' => $bank_address,
              'bank_country' => $bank_country,
              'routing_code' => $routing_code,
              'swift_code' => $swift_code,
            ]);
          }else{
            Account::create([
              'bank_name' => $bank_name,
              'account_number' => $acc_number,
              'bank_address' => $bank_address,
              'bank_country' => $bank_country,
              'routing_code' => $routing_code,
              'swift_code' => $swift_code,
              'merchant_id' => $merchant_id,
              'merchant_type' => 'flexm'
            ]);
          }

        // }
        // $type = $request->type;
        Activity::log('Updated share settings - '.$item->merchant_name.' by '.$auth_user->name);

        return redirect()->back()->with([
            'flash_level'   => 'success',
            'flash_message' => 'Updated successfully.',
        ]);
    }

    public function getSingxShare(Request $request)
    {
        $auth_user = \Auth::user();
        return view('admin.share.singx', compact('auth_user'));
    }

    public function getFlexmShare(Request $request)
    {
        $auth_user = \Auth::user();
        return view('admin.share.flexm', compact('auth_user'));
    }

    public function postFlexmShare(Request $request)
    {

        $auth_user = Auth::user();
        $merchant_type = $request->merchant_type;

        $flag = false;
        foreach ($request->input('options') as $key => $value) {
            $flag = true;
            if (is_array($value)) {
                $value = serialize($value);
            }
            Option::setOption($key, $value);
        }

        if($flag && $merchant_type != 'singx')
          Activity::log('Updated flexm share '.$auth_user->name);

        return redirect()->back()->with([
            'flash_level'   => 'success',
            'flash_message' => 'Updated successfully.',
        ]);
    }

    public function getCourseShare(Request $request)
    {
      $auth_user = \Auth::user();

      // $items = User::whereHas('roles', function($q){
      //     $q->whereHas('permissions', function($q){
      //         $q->where('permissions.id',19);
      //     });
      // })->orderBy('id');

      $user_ids = User::whereHas('roles', function($q){
        $q->where('slug', 'training');
      })->pluck('id');

      $items = Merchant::whereIn('user_id', $user_ids)->orderBy('id');

      // if ($id = $request->input('id')) {
      //     $items->where('id', $id);
      // }

      $limit = 10;
      $items = $items->sortable(['id' => 'desc'])->paginate($limit);
      $paginate_data = $request->except('page');

      return view('admin.share.list', compact('items', 'auth_user','paginate_data'));
    }

    public function getCourseShareEdit($id, Request $request)
    {
        $auth_user = \Auth::user();
        $id = decrypt($id);
        $category_code = MerchantCode::pluck('title', 'code');
        $item = Merchant::findOrFail($id);
        if($item){
          if($item->product_type == ''){
            if(@$item->user->hasRole('restaurant-owner-catering')){
              $item->product_type = 'Catering';
            }elseif(@$item->user->hasRole('restaurant-owner-single')){
              $item->product_type = 'Realtime Order';
            }
          }
          $item->merchant_share = $item->merchant_share == 0?'':$item->merchant_share;
          $item->myma_transaction_share = $item->myma_transaction_share == 0?'':$item->myma_transaction_share;

          $acc = Account::where('merchant_id', $id)->where('merchant_type', 'flexm')->first();
          if($acc){
            $item->bank_name = $acc->bank_name;
            $item->account_number = $acc->account_number;
            $item->bank_address = $acc->bank_address;
            $item->bank_country = $acc->bank_country;
            $item->routing_code = $acc->routing_code;
            $item->swift_code = $acc->swift_code;
          }
        }
        return view('admin.share.edit', compact('item', 'auth_user', 'category_code'));
    }

    public function updateCourseShare($id, Request $request)
    {
        $auth_user = \Auth::user();
        $id = decrypt($id);

        $item = Merchant::findOrFail($id);
        $data = $request->only('merchant_share', 'myma_transaction_share', 'product_type', 'merchant_address_1', 'merchant_address_2',
          'merchant_address_3', 'frequency');
        if($data['product_type'] == ''){
          if(@$item->user->hasRole('restaurant-owner-catering')){
            $data['product_type'] = 'Catering';
          }elseif(@$item->user->hasRole('restaurant-owner-single')){
            $data['product_type'] = 'Realtime Order';
          }
        }
        $item->update($data);
        $bank_name = $request->input('bank_name');
        $acc_number = $request->input('account_number');
        $bank_address = $request->input('bank_address');
        $bank_country = $request->input('bank_country');
        $routing_code = $request->input('routing_code');
        $swift_code = $request->input('swift_code');
        // if($bank_name != '' || $acc_number != ''){
          $exist = Account::where('merchant_id', $id)->where('merchant_type', 'flexm')->first();
          if($exist){
            $exist->update([
              'bank_name' => $bank_name,
              'account_number' => $acc_number,
              'bank_address' => $bank_address,
              'bank_country' => $bank_country,
              'routing_code' => $routing_code,
              'swift_code' => $swift_code,
            ]);
          }else{
            Account::create([
              'bank_name' => $bank_name,
              'account_number' => $acc_number,
              'bank_address' => $bank_address,
              'bank_country' => $bank_country,
              'routing_code' => $routing_code,
              'swift_code' => $swift_code,
              'merchant_id' => $id,
              'merchant_type' => 'flexm'
            ]);
          }

        // }
        $user = User::find($item->user_id);
        if($user->hasRole('training')){
          return redirect()->route('admin.share.courses')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Updated successfully.',
          ]);

        }else{
          return redirect()->route('admin.share.food')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Updated successfully.',
          ]);
        }
    }

    public function getFoodShare(Request $request)
    {
      $auth_user = \Auth::user();

      $user_ids = User::whereHas('roles', function($q){
        $q->whereIn('slug', ['restaurant-owner-single', 'restaurant-owner-catering']);
      })->pluck('id');

      foreach($user_ids as $id){
        $merchant = FoodMerchant::where('user_id', $id)->first();
        if(!$merchant){
          FoodMerchant::create(['user_id' => $id]);
        }
      }
      $items = FoodMerchant::query();

      // if ($id = $request->input('id')) {
      //     $items->where('id', $id);
      // }

      $limit = 10;
      $items = $items->sortable(['id' => 'desc'])->paginate($limit);
      $paginate_data = $request->except('page');

      return view('admin.food.merchant.list', compact('items', 'auth_user','paginate_data'));
    }

    public function getFoodShareEdit($id, Request $request)
    {
        $auth_user = \Auth::user();
        $id = decrypt($id);
        $item = FoodMerchant::findOrFail($id);
        if($item){
          $catering = false;
          if(@$item->user->hasRole('restaurant-owner-catering')){
            $catering = true;
          }
          $acc = Account::where('merchant_id', $id)->where('merchant_type', 'food')->first();
          if($acc){
            $item->bank_name = $acc->bank_name;
            $item->account_number = $acc->account_number;
            $item->bank_address = $acc->bank_address;
            $item->bank_country = $acc->bank_country;
            $item->routing_code = $acc->routing_code;
            $item->swift_code = $acc->swift_code;
          }
        }
        return view('admin.food.merchant.edit', compact('item', 'auth_user', 'catering'));
    }

    public function updateFoodShare($id, FoodMerchantRequest $request)
    {
        $auth_user = \Auth::user();
        $id = decrypt($id);

        $item = FoodMerchant::findOrFail($id);
        $data = $request->only('naanstap_share', 'sub_limit', 'per_sub_price', 'frequency', 'start_date');
        if($data['start_date'] == ''){
          unset($data['start_date']);
        }
        $item->update($data);
        $bank_name = $request->input('bank_name');
        $acc_number = $request->input('account_number');
        $bank_address = $request->input('bank_address');
        $bank_country = $request->input('bank_country');
        $routing_code = $request->input('routing_code');
        $swift_code = $request->input('swift_code');
        // if($bank_name != '' || $acc_number != ''){
          $exist = Account::where('merchant_id', $id)->where('merchant_type', 'food')->first();
          if($exist){
            $exist->update([
              'bank_name' => $bank_name,
              'account_number' => $acc_number,
              'bank_address' => $bank_address,
              'bank_country' => $bank_country,
              'routing_code' => $routing_code,
              'swift_code' => $swift_code,
            ]);
          }else{
            Account::create([
              'bank_name' => $bank_name,
              'account_number' => $acc_number,
              'bank_address' => $bank_address,
              'bank_country' => $bank_country,
              'routing_code' => $routing_code,
              'swift_code' => $swift_code,
              'merchant_id' => $id,
              'merchant_type' => 'food'
            ]);
          }

        // }
        $user = User::find($item->user_id);
        return redirect()->route('admin.share.food')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Updated successfully.',
        ]);

    }

}
