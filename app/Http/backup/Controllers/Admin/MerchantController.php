<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Models\Account;
use App\Models\Merchant;
use App\Models\Terminal;
use App\Models\MerchantCode;
use App\Models\Dormitory;
use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;

use App\Http\Requests;
use App\Http\Requests\AddMerchantRequest;
use App\Http\Requests\EditMerchantRequest;

use App\Http\Controllers\Controller;
use Auth, Activity, JWTAuth;

class MerchantController extends Controller
{
    const BASE_URL = 'https://test-api.flexm.sg/';

    public function getList(Request $request)
    {
      $auth_user = Auth::user();
      $items = Merchant::where('type', 'instore')->where('active', 1);

      if ($name = $request->input('name')) {
          $name = strtolower($name);
          $items->whereRaw('lower(`merchant_name`) like ?', array("%{$name}%"));
      }

      $limit = 10;
      $items = $items->sortable(['id' => 'desc'])->paginate($limit);
      $paginate_data = $request->except('page');

      $names = Merchant::where('type', 'instore')->where('active', 1)->pluck('merchant_name');

      return view('admin.merchant.list', compact('items', 'auth_user', 'paginate_data', 'names'));
    }


    public function getAdd()
    {

        $auth_user = \Auth::user();
        $category_code = MerchantCode::pluck('title', 'code');
        $dorms = Dormitory::pluck('name');
        $dormitories[''] = 'Please add a location';
        foreach($dorms as $dorm){
          $dormitories[$dorm] = $dorm;
        }

        $users = User::whereHas('roles', function($q){
          $q->where('slug', 'spuul');
        })->pluck('name', 'id');

        return view('admin.merchant.add', compact('auth_user', 'languages', 'category_code', 'dormitories', 'users'));
    }

    public function postAdd(AddMerchantRequest $request)
    {
        /** @var User $item */
        $url = config('app.url').'api/v1/flexm/';
        $auth_user = \Auth::user();
        $data = $request->only('merchant_name', 'merchant_category_code', 'location', 'merchant_share', 'myma_transaction_share',
        'frequency', 'start_date', 'revenue_model', 'v_cost_type', 'product_type', 'user_id', 'merchant_address_1', 'merchant_address_2', 'merchant_address_3');
        try{
          $tid = '';
          $token = JWTAuth::fromUser($auth_user);
          $data['token'] = $token;
          $data['type'] = 'instore';
          $client = new Client();
          $result = $client->post($url.'create/merchant',[
            'form_params' => $data
          ]);

          $code = $result->getStatusCode(); // 200
          $reason = $result->getReasonPhrase(); // OK

          if($code == "200" && $reason == "OK"){
            $body = $result->getBody();
            $content = json_decode($body->getContents());

            if($content->status == 'success'){
              $msg = $content->message;
              $cont = $content->data;
              $data['token'] = $token;
              $data['merchant_code'] = $cont->merchant_code;
              $data['mid'] = $cont->mid;
              $data['wallet_type_indicator'] = 'centurion';
              $data['status'] = $cont->status;
              $data['created_by'] = $auth_user->id;

              // \QrCode::format('png')->merge(public_path('/images/flexm.png'), .2, true)->errorCorrection('H')->margin(1)->size(400)->generate($data['mid'], '../public/files/merchant/'.$data['mid'].'.png');
              // $data['qr_code'] = 'files/merchant/'.$data['mid'].'.png';

              $exist = Merchant::where('merchant_code', $data['merchant_code'])->first();
              if($exist){
                $exist->update($data);
              }else{
                $exist = Merchant::create($data);
              }

              //add bank account
              $bank_name = $request->input('bank_name');
              $acc_number = $request->input('account_number');
              $bank_address = $request->input('bank_address');
              $bank_country = $request->input('bank_country');
              $routing_code = $request->input('routing_code');
              $swift_code = $request->input('swift_code');
              if($bank_name != '' && $acc_number != ''){
                Account::create([
                  'bank_name' => $bank_name,
                  'account_number' => $acc_number,
                  'bank_address' => $bank_address,
                  'bank_country' => $bank_country,
                  'routing_code' => $routing_code,
                  'swift_code' => $swift_code,
                  'merchant_id' => @$exist->id,
                  'merchant_type' => 'flexm'
                ]);
              }
              $term_exist = [];
              $dat['merchant_code'] = $data['merchant_code'];
              $dat['payment_mode'] = 2;
              $dat['token'] = $token;
              $result = $client->post($url.'create/terminal',[
                'form_params' => $dat
              ]);

              $code = $result->getStatusCode(); // 200
              $reason = $result->getReasonPhrase(); // OK
              if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());

                if($content->status == 'success'){
                  $msg = $content->message;
                  $cont = $content->data;
                  $terone['merchant_id'] = $exist['id'];
                  $terone['payment_mode'] = 2;
                  $tid = $terone['tid'] = $cont->tid;
                  $tero = Terminal::where($terone)->first();
                  $terone['location'] = $data['location'];
                  $terone['status'] = $cont->status;
                  if($tero){
                    $tero->update($terone);
                  }else{
                    Terminal::create($terone);
                  }
                }
                else{
                  if(strpos($content->message, 'terminal could be registered') !== false){
                    $dat = [];
                    $dat['token'] = $token;
                    $dat['merchant_code'] = $data['merchant_code'];
                    $result = $client->post($url.'merchant/info',[
                      'form_params' => $dat
                    ]);

                    $code = $result->getStatusCode(); // 200
                    $reason = $result->getReasonPhrase(); // OK
                    if($code == "200" && $reason == "OK"){
                      $body = $result->getBody();
                      $content = json_decode($body->getContents());

                      if($content->status == 'success'){
                          $msg = $content->message;
                          $cont = $content->data;
                          if(isset($cont->terminal)){
                            foreach($cont->terminal as $ter){
                              if($ter->payment_mode == 2){
                                continue;
                              }
                              $terone = [];
                              $terone['merchant_id'] = $exist['id'];
                              $terone['payment_mode'] = $ter->payment_mode;
                              $tid = $terone['tid'] = $ter->tid;
                              $term_exist[] = $ter->payment_mode;
                              $tero = Terminal::where($terone)->first();
                              $terone['location'] = $data['location'];
                              $terone['status'] = $ter->status;
                              if($tero){
                                $tero->update($terone);
                              }else{
                                Terminal::create($terone);
                              }
                            }
                          }
                      }else{
                          return back()->withInput()->withErrors([@$content->message]);
                      }
                    }else{
                        return back()->withInput()->withErrors(['Could not create terminal. Try again later']);
                    }
                    }else{
                    return back()->withInput()->withErrors([@$content->message]);
                  }
                }
              }
              else{
                  return back()->withInput()->withErrors(['Could not create terminal. Try again later']);
              }
            }else{
              return back()->withInput()->withErrors([@$content->message]);
            }
          }else{
            return back()->withInput()->withErrors(['Could not create merchant. Try again later']);
          }

          \QrCode::format('png')->merge(public_path('/images/flexm.png'), .2, true)->errorCorrection('H')->margin(1)->size(400)->generate($data['mid'].'_'.$tid, '../public/files/merchant/'.$data['mid'].'_'.$tid.'.png');
          $qr_code = 'files/merchant/'.$data['mid'].'_'.$tid.'.png';

          $merchant = Merchant::where('mid', $data['mid'])->first();
          if($merchant);
            $merchant->update(['qr_code' => $qr_code]);

          $terminal = Terminal::where('tid', $tid)->first();
          if($terminal)
            $terminal->update(['qr_code' => $qr_code]);

          return redirect()->route('admin.merchant.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Merchant added successfully.',
          ]);
        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());

            return back()->withInput()->withErrors(@$jsonBody->errors);
            // return response()->json(['status' => 'error', 'data' => $jsonBody->errors, 'message'=> $jsonBody->message], 200);
        }catch(GuzzleException $e){
            return back()->withInput()->withErrors($e->getMessage());
        }catch(Exception $e){
            return back()->withInput()->withErrors($e->getMessage());
        }
    }
    public function getEdit($id, Request $request)
    {
        $auth_user = \Auth::user();
        $id = decrypt($id);
    	  $item = Merchant::findOrFail($id);
        // $data['mid'] = $item->mid;
        // foreach($item->terminals as $key => $ter){
        //   $tid = $ter->tid;
        //   \QrCode::format('png')->merge(public_path('/images/flexm.png'), .2, true)->errorCorrection('H')->margin(1)->size(400)->generate($data['mid'].'_'.$tid, '../public/files/merchant/'.$data['mid'].'_'.$tid.'.png');
        //   $qr_code = 'files/merchant/'.$data['mid'].'_'.$tid.'.png';
        //
        //   if($key == 0){
        //       $item->update(['qr_code' => $qr_code]);
        //   }
        //   $ter->update(['qr_code' => $qr_code]);
        // }



        // \QrCode::format('png')->merge(public_path('/images/flexm.png'), .2, true)->errorCorrection('H')->margin(1)->size(400)->generate($item->mid, '../public/files/merchant/'.$item->mid.'.png');
        $category_code = MerchantCode::pluck('title', 'code');
        $dorms = Dormitory::pluck('name');
        $dormitories[''] = 'Please add a location';
        foreach($dorms as $dorm){
          $dormitories[$dorm] = $dorm;
        }
        $account = Account::where('merchant_id', $id)->where('merchant_type', 'flexm')->first();
        if($item){
          $item->merchant_share = $item->merchant_share == 0?'':$item->merchant_share;
          $item->myma_transaction_share = $item->myma_transaction_share == 0?'':$item->myma_transaction_share;

          if($account){
            $item->bank_name = $account->bank_name;
            $item->account_number = $account->account_number;
            $item->bank_address = $account->bank_address;
            $item->bank_country = $account->bank_country;
            $item->routing_code = $account->routing_code;
            $item->swift_code = $account->swift_code;
          }else{
            $item->bank_name = '';
            $item->account_number = '';
            $item->bank_address = '';
            $item->bank_country = '';
            $item->routing_code = '';
            $item->swift_code = '';
          }
        }

        $users = User::whereHas('roles', function($q){
          $q->where('slug', 'spuul');
        })->pluck('name', 'id');

        return view('admin.merchant.edit', compact('item', 'languages', 'category_code', 'dormitories', 'users'));
    }

    public function postEdit($id, EditMerchantRequest $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $id = decrypt($id);
        $module = Merchant::findOrFail($id);
        $data = $request->only('user_id', 'location', 'merchant_share', 'myma_transaction_share', 'frequency', 'revenue_model', 'v_cost_type',
        'product_type', 'merchant_address_1', 'merchant_address_2', 'merchant_address_3');

        $module->update($data);

        //add bank account
        $bank_name = $request->input('bank_name');
        $acc_number = $request->input('account_number');
        $bank_address = $request->input('bank_address');
        $bank_country = $request->input('bank_country');
        $routing_code = $request->input('routing_code');
        $swift_code = $request->input('swift_code');
        if($bank_name != '' && $acc_number != ''){
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

        }
        // Activity::log('Mom category updated #'.@$id. ' by '.$auth_user->name);

        return redirect()->route('admin.merchant.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Merchant details updated successfully.',
        ]);

    }

    public function getDelete($id)
    {
        Merchant::destroy($id);

        $auth_user = Auth::user();
        Activity::log('Deleted merchant by '.$auth_user->name);

        return redirect()->route('admin.merchant.list')->with([
            'flash_level'   => 'danger',
            'flash_message' => 'Merchant Deleted',
        ]);
    }

    public function postDelete($id)
    {
        Merchant::delete($id);
        Activity::log('Deleted merchant by '.$auth_user->name);
        return redirect()->route('admin.merchant.list')->with([
          'flash_level'   => 'danger',
          'flash_message' => 'Merchant Deleted',
        ]);

    }

    public function getQRView($id, Request $request)
    {
        $id = decrypt($id);
    	  $item = Merchant::findOrFail($id);
        if($item){
            return view('admin.merchant.qrcode', compact('item'));
        }
        abort('404');
    }
}
