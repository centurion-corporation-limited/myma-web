<?php

namespace App\Http\Controllers\Admin;

use App\User, App\Models\Activity, App\Models\Badwords, Auth;
use App\Models\Forum, DB;
use App\Models\Maintenance;
use App\Models\Role;
use App\Models\Order;
use App\Models\FoodMenu;
use App\Models\Feedback;
use App\Models\LoginHistory;
use App\Models\Transactions;
use App\Models\Share;
use App\Models\Remittance;
use App\Models\RemittanceReport;
use App\Models\Merchant;
use App\Models\FoodMerchant;
use App\Models\FlexmWallet;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\EditShareRequest;
use App\Http\Controllers\Controller;
use Carbon\Carbon, Excel;
use PHPExcel_Worksheet_Drawing;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class TransactionController extends Controller
{

    public function exportExcel(Request $request)
    {
        $auth_user = \Auth::user();

        $type = $request->input('type');

        if($type == 'inapp'){
          $items = Transactions::orderBy('created_at', 'desc');
          $items->where('type', '!=', 'instore');

          if($id = $request->input('transaction_id')){
              $items->where('transaction_ref_no', 'like', "%{$id}%");
          }

          if($input_mid = $request->input('mid')){
              $items->where('mid', $input_mid);
          }

          if($auth_user->hasRole('spuul')){
            $mid = Merchant::where('user_id', $auth_user->id)->pluck('mid');
            $items->where('type', 'spuul');
            $items->whereIn('mid', $mid);
          }
          if($auth_user->hasRole('training')){
            $mid = Merchant::where('user_id', $auth_user->id)->pluck('mid');
            $items->where('type', 'course');
            $items->whereIn('mid', $mid);
          }
          if($auth_user->hasRole('food-admin')){
            $items->where('type', 'food');
          }

        }
        elseif($type == 'instore'){
          $items = Transactions::orderBy('created_at', 'desc');
          $items->where('type', 'instore');

          if($id = $request->input('transaction_id')){
              $items->where('transaction_ref_no', 'like', "%{$id}%");
          }

          if($input_mid = $request->input('mid')){
              $items->where('mid', $input_mid);
          }
          
          if($auth_user->hasRole('spuul')){
            $mid = Merchant::where('user_id', $auth_user->id)->pluck('mid');
            $items->where('type', 'spuul');
            $items->whereIn('mid', $mid);
          }
          if($auth_user->hasRole('training')){
            $mid = Merchant::where('user_id', $auth_user->id)->pluck('mid');
            $items->where('type', 'course');
            $items->whereIn('mid', $mid);
          }
          if($auth_user->hasRole('food-admin')){
            $items->where('type', 'food');
          }

        }
        else{
          $items = Remittance::orderBy('id', 'desc');
          if($id = $request->input('transaction_id')){
              $items->where('hash_id', 'like', "%{$id}%");
          }

          if($status = $request->input('status')){
              $items->where('status', $status);
          }
        }

        if($email = $request->input('email')){
            $searchValue = strtolower($email);

            $user = User::all()->filter(function($record) use($searchValue) {
                        $email = $record->email;
                        try{
                            $email = Crypt::decrypt($email);
                        }catch(DecryptException $e){

                        }
                        if(($email) == $searchValue) {
                            return $record;
                        }
            })->pluck('id');
            $items->whereIn('user_id', $user);
        }
        $from = $request->input('start');
        $to = $request->input('end');

        if($from != '' && $to != ''){
            
            if(strpos($from, '/') !== false) {
                $from = Carbon::createFromFormat('d/m/Y', $from);
            }else{
                $from = Carbon::parse($from);
            }
            
            if(strpos($to, '/') !== false) {
                $to = Carbon::createFromFormat('d/m/Y', $to);
            }else{
                $to = Carbon::parse($to);
            }
            
            $items->whereDate('created_at', '>=', $from->toDateString())->whereDate('created_at', '<=', $to->toDateString());
        }

        $paymentsArray = [];
        $items = $items->get();

        if(count($items)){
            if($type == 'remit'){
                $paymentsArray[] = array('Hash id', 'Provider', 'Delivery method', 'Receive country', 'Status', 'Payout agent', 'Customer fx', 'Send currency', 'Send amount', 'Customer fixed fee',
                'Total transaction amount', 'Receive currency', 'Receive amount', 'Crossrate', 'Provider amount fee currency', 'Provider amount fee','Provider exchange rate',
                'Send amount rails currency', 'Send amount rails', 'Send amount before fx', 'Send amount after fx', 'Routing params', 'Ref id', 'Transaction code',
                'Sender first name','Sender middle name', 'Sender last name', 'Sender mobile number', 'Beneficiary first name','Beneficiary middle name','Beneficiary last name',
                'Date added','Date expiry','Status last updated');
            }else{
                if($auth_user->hasRole('admin')){
                    $paymentsArray[] = array('Type', 'Phone no', 'Wallet user name', 'Transaction date', 'Transaction amount', 'Transaction currency',
                    'Transaction ref no', 'Transaction status', 'Transaction code', 'MID', 'TID', 'Merchant name', 'Payment mode',
                    'FlexM cost', 'MyMA Wallet Txn fee earned', 'MyMA Comms Share', 'Merchant share', 'GST');    
                }else{
                    $paymentsArray[] = array('Type', 'Phone no', 'Wallet user name', 'Transaction date', 'Transaction amount', 'Transaction currency',
                    'Transaction ref no', 'Transaction status', 'Transaction code', 'MID', 'TID', 'Merchant name', 'Payment mode',
                    'MyMA Wallet Txn fee earned', 'MyMA Comms Share', 'Merchant share', 'GST');
                }
                
            }
        }

        $gst_share = $myma_part = $flexm_part = $myma_share = $merchant_share = 0;
        $send_amount = $total_transaction_amount = $receive_amount = 0;
        foreach($items as $item){

            $arr = [];
            if($type == 'remit'){
              $arr[] = $item->hash_id;
              $arr[] = $item->provider;
              $arr[] = $item->delivery_method;
              $arr[] = $item->receive_country;
              if($item->status == 'require')
                $arr[] = 'not confirmed';
              else
                $arr[] = $item->status;

              $arr[] = $item->payout_agent;
              $arr[] = number_format($item->customer_fx,4);
              $arr[] = $item->send_currency;
              $arr[] = number_format($item->send_amount,4);
              $arr[] = number_format($item->customer_fixed_fee,4);
              $arr[] = number_format($item->total_transaction_amount,4);
              $arr[] = $item->receive_currency;
              $arr[] = number_format($item->receive_amount,4);
              $arr[] = number_format($item->crossrate,4);
              $arr[] = $item->provider_amount_fee_currency;
              $arr[] = number_format($item->provider_amount_fee,4);
              $arr[] = number_format($item->provider_exchange_rate,4);
              $arr[] = $item->send_amount_rails_currency;
              $arr[] = $item->send_amount_rails;
              $arr[] = number_format($item->send_amount_before_fx,4);
              $arr[] = number_format($item->send_amount_after_fx,4);
              $arr[] = $item->routing_params;
              $arr[] = $item->ref_id;
              $arr[] = $item->transaction_code;
              $arr[] = $item->sender_first_name;
              $arr[] = $item->sender_middle_name;
              $arr[] = $item->sender_last_name;
              $arr[] = $item->sender_mobile_number;
              $arr[] = $item->ben_first_name;
              $arr[] = $item->ben_middle_name;
              $arr[] = $item->ben_last_name;
              $arr[] = $item->date_added;
              $arr[] = $item->date_expiry;
              $arr[] = $item->status_last_updated;

              $send_amount += $item->send_amount;
              $total_transaction_amount += $item->total_transaction_amount;
              $receive_amount += $item->receive_amount;
            }else{
              $arr[] = $item->type;
            //   $arr[] = $item->ref_id;
              $arr[] = $item->phone_no;
              $arr[] = $item->wallet_user_name;
              $arr[] = $item->transaction_date;
              $arr[] = $item->transaction_amount;
              $arr[] = $item->transaction_currency;
              $arr[] = $item->transaction_ref_no;
              $arr[] = $item->transaction_status;
              $arr[] = $item->transaction_code;
              $arr[] = $item->mid;
              $arr[] = $item->tid;
              $arr[] = $item->merchant_name;
              $arr[] = $item->payment_mode;
              if($auth_user->hasRole('admin')){
                $arr[] = number_format($item->flexm_part,4);
              }
              $arr[] = number_format($item->myma_part+$item->flexm_part,4);
              if(strtolower($item->type) == 'instore'){
              $arr[] = number_format(0,4);
                if($item->other_share == 0)
                $arr[] = number_format( ($item->transaction_amount-$item->myma_part-$item->flexm_part),4);
                else
                $arr[] = number_format($item->other_share,4);
              }
              else{
                $arr[] = number_format($item->myma_share,4);
                $arr[] = number_format($item->other_share,4);
              }
              $arr[] = number_format($item->gst,4);
              
              $flexm_part += $item->flexm_part;
              $myma_part += $item->myma_part+$item->flexm_part;
              $myma_share += $item->myma_share;
              $merchant_share += $item->other_share;
              $gst_share += $item->gst;
              $total_transaction_amount += $item->transaction_amount;
              // $arr[] = $item->status;
            }
            $paymentsArray[] = $arr;
        }

        $arr = [];
        if($type == 'remit'){
          $arr[] = 'Total';
          $arr[] = '';
          $arr[] = '';
          $arr[] = '';
          $arr[] = '';
          $arr[] = '';
          $arr[] = '';
          $arr[] = '';
          $arr[] = number_format($send_amount,4);
          $arr[] = '';
          $arr[] = number_format($total_transaction_amount,4);
          $arr[] = '';
          $arr[] = number_format($receive_amount,4);
          $arr[] = '';
          $arr[] = '';
          $arr[] = '';
          $arr[] = '';
          $arr[] = '';
          $arr[] = '';
          $arr[] = '';
          $arr[] = '';

        }else{
          $arr[] = 'Total';
        //   $arr[] = '';
          $arr[] = '';
          $arr[] = '';
          $arr[] = '';
          $arr[] = number_format($total_transaction_amount,4);
          $arr[] = '';
          $arr[] = '';
          $arr[] = '';
          $arr[] = '';
          $arr[] = '';
          $arr[] = '';
          $arr[] = '';
          $arr[] = '';
          if($auth_user->hasRole('admin')){
            $arr[] = number_format($flexm_part,4);
          }
          $arr[] = number_format($myma_part,4);
          $arr[] = number_format($myma_share,4);
          $arr[] = number_format($merchant_share,4);
          $arr[] = number_format($gst_share,4);
        }
        $paymentsArray[] = $arr;

        // dd($paymentsArray);

        Excel::create('Transactions', function($excel) use ($paymentsArray) {
          // Set the spreadsheet title, creator, and description
            $excel->setTitle('Transactions List');
            $excel->setCreator('Myma')->setCompany('Myma');
            // $excel->setDescription('payments file');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($paymentsArray) {
                $sheet->fromArray($paymentsArray, null, 'A1', false, false);
            });
        })->export('xls');
    }

    public function getUserList(Request $request)
    {
        $items = User::whereHas('roles', function($q){
                $q->whereHas('permissions', function($q){
                    $q->where('permissions.id',19)
                    ->orWhere('permissions.id',94);
                });
        })->orderBy('id');

        $limit = 10;
        $items = $items->paginate($limit);
        $paginate_data = $request->except('page');

        $user = \Auth::user();
        return view('admin.payout.list', compact('items', 'user', 'paginate_data'));
    }

    public function getInappTransactions(Request $request)
    {
        if(\Auth::guest()){
            return view('auth.login');
        }else{
            $auth_user = \Auth::user();
            $items = Transactions::query();

            if($auth_user->hasRole('spuul')){
              // $items->where('type', 'spuul');
              $mid = Merchant::where('user_id', $auth_user->id)->pluck('mid');
              $items->whereIn('mid', $mid);
            }else{
              $items->where('type', '!=', 'instore');
            }
            if($auth_user->hasRole('training')){
              $mid = Merchant::where('user_id', $auth_user->id)->pluck('mid');
              $items->where('type', 'course');
              $items->whereIn('mid', $mid);
            }
            if($auth_user->hasRole('food-admin')){
              $items->where('type', 'food');
            }



            if($id = $request->input('transaction_id')){
                $items->where('transaction_ref_no', 'like', "%{$id}%");
            }

            if($email = $request->input('email')){
                $searchValue = strtolower($email);

                $user = User::all()->filter(function($record) use($searchValue) {
                            $email = $record->email;
                            try{
                                $email = Crypt::decrypt($email);
                            }catch(DecryptException $e){

                            }
                            if(($email) == $searchValue) {
                                return $record;
                            }
                })->pluck('id');
                $items->whereIn('user_id', $user);
            }

            $from = $request->input('start');
            $to = $request->input('end');

            if($from != '' && $to != ''){

                if(strpos($from, '/') !== false) {
                  $from = explode('/',$from);
                  $from = Carbon::create($from[2],$from[1],$from[0]);
                }else{
                  $from = Carbon::parse($from);
                }

                if(strpos($to, '/') !== false) {
                  $to = explode('/',$to);
                  $to = Carbon::create($to[2],$to[1],$to[0]);
                }else{
                  $to = Carbon::parse($to);
                }

                $from = $from->toDateString();
                $to = $to->toDateString();
                $items->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to);
            }

            if($mid = $request->input('mid')){
                $items->where('mid', $mid);
            }
            $data['myma_total'] = $items->sum('myma_share')+$items->sum('myma_part');
            $data['merchant_total'] = $items->sum('other_share');
            $data['flexm_total'] = $items->sum('flexm_part');

            $limit = 10;
            $items = $items->sortable(['id' => 'desc'])->paginate($limit);
            $paginate_data = $request->except('page');
            $user = \Auth::user();
            $type = 'inapp';

            if($auth_user->can('view.transaction-list-instore')){
                $merchants = Merchant::where('type', 'inapp')->where('active', '1')->pluck('merchant_name', 'mid');
            }elseif($auth_user->hasRole('food-admin')){
                $user_ids = User::whereHas('roles', function($q){
                    $q->where('slug', 'restaurant-owner-single')->orWhere('slug', 'restaurant-owner-catering');
                })->pluck('id');

                $merchants = Merchant::whereIn('user_id',$user_ids)->pluck('merchant_name', 'mid');
            }else{
              $merchants = [];
            }


            return view('admin.dashboard.transactions', compact('items', 'user', 'paginate_data', 'type', 'data', 'merchants'));
        }
    }

    public function getInstoreTransactions(Request $request)
    {
        if(\Auth::guest()){
            return view('auth.login');
        }else{
            $auth_user = \Auth::user();
            $items = Transactions::query();

            if($auth_user->hasRole('spuul')){
              $mid = Merchant::where('user_id', $auth_user->id)->pluck('mid');
              $items->whereIn('mid', $mid);
            }else{
              $items->where('type', 'instore');
            }



            if($id = $request->input('transaction_id')){
                $items->where('transaction_ref_no', 'like', "%{$id}%");
            }

            if($email = $request->input('email')){
                $searchValue = strtolower($email);

                $user = User::all()->filter(function($record) use($searchValue) {
                            $email = $record->email;
                            try{
                                $email = Crypt::decrypt($email);
                            }catch(DecryptException $e){

                            }
                            if(($email) == $searchValue) {
                                return $record;
                            }
                })->pluck('id');
                $items->whereIn('user_id', $user);
            }

            $from = $request->input('start');
            $to = $request->input('end');

            if($from != '' && $to != ''){
              if(strpos($from, '/') !== false) {
                $from = explode('/',$from);
                $from = Carbon::create($from[2],$from[1],$from[0]);
              }else{
                $from = Carbon::parse($from);
              }

              if(strpos($to, '/') !== false) {
                $to = explode('/',$to);
                $to = Carbon::create($to[2],$to[1],$to[0]);
              }else{
                $to = Carbon::parse($to);
              }
              $from = $from->toDateString();
              $to = $to->toDateString();
                $items->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to);

            }

            if($mid = $request->input('mid')){
                $items->where('mid', $mid);
            }

            $data['myma_total'] = $items->sum('myma_share')+$items->sum('myma_part');
            $data['merchant_total'] = $items->sum('other_share');
            $data['flexm_total'] = $items->sum('flexm_part');

            $limit = 10;
            $items = $items->sortable(['id' => 'desc'])->paginate($limit);
            $paginate_data = $request->except('page');
            $user = \Auth::user();
            $type = 'instore';

            if($auth_user->can('view.transaction-list-instore')){
              if($auth_user->hasRole('admin|sub-admin')){
                  $merchants = Merchant::where('type', 'instore')->where('active', '1')->pluck('merchant_name', 'mid');
              }else{
                  $merchants = Merchant::where('user_id', $auth_user->id)->where('type', 'instore')->where('active', '1')->pluck('merchant_name', 'mid');
              }

            }else{
              $merchants = [];
            }

            return view('admin.dashboard.transactions', compact('items', 'user', 'paginate_data', 'type', 'data', 'merchants'));
        }
    }

    public function getRemitTransactions(Request $request)
    {
        if(\Auth::guest()){
            return view('auth.login');
        }else{
            $items = Remittance::query();
            $items1 = Remittance::where('status', 'success');
            $items2 = Remittance::where('status', 'require');

            if($id = $request->input('transaction_id')){
                $items->where('hash_id', 'like', "%{$id}%");
            }

            $from = $request->input('start');
            $to = $request->input('end');

            if($from != '' && $to != ''){
                $from = Carbon::parse($from);
                $to = Carbon::parse($to);
                $items->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to);
            }

            if($status = $request->input('status')){
                $items->where('status', $status);
            }

            if($email = $request->input('email')){
                $searchValue = strtolower($email);

                $user = User::all()->filter(function($record) use($searchValue) {
                            $email = $record->email;
                            try{
                                $email = Crypt::decrypt($email);
                            }catch(DecryptException $e){

                            }
                            if(($email) == $searchValue) {
                                return $record;
                            }
                })->pluck('id');
                $items->whereIn('user_id', $user);
            }

            $data['total'] = $items->sum('total_transaction_amount');
            $data['total_success'] = $items1->count();
            $data['total_not_confirmed'] = $items2->count();

            $limit = 10;
            $items = $items->sortable(['id' => 'desc'])->paginate($limit);
            $paginate_data = $request->except('page');
            $user = \Auth::user();
            $type = 'remit';

            $statuss = Remittance::groupBy('status')->pluck('status');
            $statuses = [];
            foreach($statuss as $status){
              if($status){
                if($status == 'require'){
                    $statuses[$status] = 'Not Confirmed';
                }else{
                    $statuses[$status] = ucfirst($status);
                }

              }
            }
            return view('admin.transactions.remit', compact('items', 'user', 'paginate_data', 'type', 'data', 'statuses'));
        }
    }
    
    public function getWalletTransactions(Request $request)
    {
        if(\Auth::guest()){
            return view('auth.login');
        }else{
            $items = FlexmWallet::query();
            $items1 = Remittance::where('status', 'success');
            $items2 = Remittance::where('status', 'require');

            if($id = $request->input('transaction_id')){
                $items->where('transaction_id', 'like', "%{$id}%");
            }

            $from = $request->input('start');
            $to = $request->input('end');

            if($from != '' && $to != ''){
                $from = Carbon::parse($from);
                $to = Carbon::parse($to);
                $items->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to);
            }

            if($status = $request->input('status')){
                $items->where('status', $status);
            }

            if($email = $request->input('email')){
                $searchValue = strtolower($email);

                $user = User::all()->filter(function($record) use($searchValue) {
                            $email = $record->email;
                            try{
                                $email = Crypt::decrypt($email);
                            }catch(DecryptException $e){

                            }
                            if(($email) == $searchValue) {
                                return $record;
                            }
                })->pluck('id');
                $items->whereIn('user_id', $user);
            }

            $limit = 10;
            $items = $items->sortable(['id' => 'desc'])->paginate($limit);
            $paginate_data = $request->except('page');
            $user = \Auth::user();

            return view('admin.transactions.wallet', compact('items', 'user', 'paginate_data', 'data'));
        }
    }
    
    public function viewTransaction($id, Request $request)
    {
            $id = decrypt($id);
            $item = Remittance::findOrFail($id);
            $wallet = RemittanceReport::where('hash_id', $item->hash_id)->first();

            $user = \Auth::user();
            return view('admin.transactions.view', compact('item', 'user', 'wallet'));
    }

    public function viewFoodTransactions($id, Request $request)
    {
          $id = decrypt($id);
          $item = Transactions::findOrFail($id);
          $user = \Auth::user();

          return view('admin.food.transaction.view', compact('item', 'user'));
    }

    public function editTransaction($id, Request $request)
    {
        if(\Auth::guest()){
            return view('auth.login');
        }else{
            $id = decrypt($id);
            $item = Transactions::findOrFail($id);
            if($item->status != 'paid'){
                $user = \Auth::user();
                return view('admin.dashboard.edit', compact('item', 'user'));
            }else{
                return redirect()->route('admin.transactions.logs')->with([
                    'flash_level'   => 'error',
                    'flash_message' => "This transaction already paid can't edit its details.",
                ]);
            }
        }
    }

    public function postTransaction($id, EditShareRequest $request)
    {
        if(\Auth::guest()){
            return view('auth.login');
        }else{
            $id = decrypt($id);
            $item = Transactions::findOrFail($id);
            if($item->status != 'paid'){
                $data = $request->only('myma_share','other_share','myma_part', 'flexm_part', 'status');
                $actual_total = $item->myma_part + $item->flexm_part;
                $updated_total = $data['myma_part']+$data['flexm_part'];
                if($actual_total < $updated_total){
                    return back()->withErrors('Transaction charges total cant be greater than original total. That is : '.$actual_total);
                }elseif($actual_total > $updated_total){
                    return back()->withErrors('Transaction charges total cant be less than original total. That is : '.$actual_total);
                }

                $total = $item->transaction_amount;
                $charges = $item->myma_part + $item->flexm_part;
                $total = $total - $charges;
                if($item->myma_share != '' && $item->flexm_share != ''){
                    $updated_share = $data['myma_share']+$data['other_share'];
                    $actual_share = $item->myma_share + $item->other_share;
                    if($actual_share < $updated_share){
                        return back()->withErrors('Share total cant be greater than original total. That is : '.$total);
                    }elseif($actual_share > $updated_share){
                        return back()->withErrors('Share total cant be less than original total. That is : '.$total);
                    }
                }else{

                    $updated_share = $data['myma_share']+$data['other_share'];
                    if($updated_share > $total || $updated_share < $total){
                        return back()->withErrors('Share total cant be greater than or less than original total. That is : '.$total);
                    }
                }

                $item->update($data);

                return redirect()->route('admin.transactions.logs')->with([
                    'flash_level'   => 'success',
                    'flash_message' => 'Details updated successfully.',
                ]);

            }else{
                return redirect()->route('admin.transactions.logs')->with([
                    'flash_level'   => 'error',
                    'flash_message' => "This transaction already paid can't update its details.",
                ]);
            }
        }
    }

    public function getFoodTransactions(Request $request)
    {
          $auth_user = \Auth::user();
          $items = Transactions::query();
          $items->where('type', '!=', 'instore');
          $items->where('type', 'food');

          $data['myma_total'] = $items->sum('myma_share');
          $data['merchant_total'] = $items->sum('other_share');
          $data['flexm_total'] = $items->sum('flexm_part');

          if($id = $request->input('transaction_id')){
              $items->where('transaction_ref_no', 'like', "%{$id}%");
          }
          $from = $request->input('start');
          $to = $request->input('end');

          if($from != '' && $to != ''){
              $from = Carbon::parse($from);
              $to = Carbon::parse($to);
              $items->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to);

          }

          if($mid = $request->input('mid')){
              $items->where('mid', $mid);
          }

          $limit = 10;
          $items = $items->sortable(['id' => 'desc'])->paginate($limit);
          $paginate_data = $request->except('page');
          $user = \Auth::user();
          $type = 'inapp';
          foreach($items as $item){
            $merchant_name = $item->merchant_name;
            $order = Order::find($item->ref_id);
            if($order){
                foreach($order->items as $order_item){
                  $item_id = $order_item->item_id;
    
                  $food = FoodMenu::find($item_id);
                  $merchant_name = @$food->restaurant->merchant->name;
                  break;
                }    
            }
            
            $item->merchant_name = $merchant_name;
          }

          $user_ids = User::whereHas('roles', function($q){
                $q->where('slug', 'restaurant-owner-single')->orWhere('slug', 'restaurant-owner-catering');
          })->where('blocked', '0')->pluck('id');

          $merchants = FoodMerchant::whereIn('user_id', $user_ids)->get();


          return view('admin.food.transaction.list', compact('items', 'user', 'paginate_data', 'type', 'data', 'merchants'));

    }

    public function getInvoice($id, Request $request)
    {
      $auth_user = Auth::user();
      $item = Transactions::findOrFail($id);
      if($item->invoice_id == ''){
        $item->invoice_id = 'MyMA-'.$item->merchant_name.'-'.$item->id;
        // $restaurant_name = str_replace(' ','', strtoupper($restaurant_name));
        // $invoice .= $restaurant_name.'-'.$order->id;
        // $order->invoice_id = $invoice;
      }
      $merchant = $item->merchant;
      return view('admin.transactions.print_invoice', compact('auth_user', 'item', 'merchant'));
    }
}
