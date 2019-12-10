<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Models\ProductType;
use App\Models\Option;
use App\Models\Merchant;
use App\Models\FoodMerchant;
use App\Models\Terminal;
use App\Models\Payout;
use App\Models\Transactions;
use App\Models\Dormitory;
use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;

use App\Http\Requests;
use App\Http\Requests\SavePayoutRequest;
use App\Http\Controllers\Controller;
use Auth, Activity, Carbon\Carbon, Excel;
use PHPExcel_Worksheet_Drawing;
use Illuminate\Support\Facades\Crypt;

use Illuminate\Contracts\Encryption\DecryptException;


class PayoutController extends Controller
{

    public function exportTxns($items)
    {
        $paymentsArray = [];

        if(count($items)){
            $paymentsArray[] = array('Type', 'Ref id', 'Phone no', 'Wallet user name', 'Transaction date', 'Transaction amount', 'Transaction currency',
                'Transaction ref no', 'Transaction status', 'Transaction code', 'MID', 'TID', 'Merchant name', 'Payment mode',
                'FlexM Cost', 'MyMA Txn Cost', 'MyMA Comms Share', 'Merchant share');
        }

        $transaction_amount = $flexm_part = $myma_part = $myma_share = $other_share = 0;

        foreach($items as $item){
              $arr = [];
              $arr[] = $item->type;
              $arr[] = $item->ref_id;
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
              $arr[] = $item->flexm_part;
              $arr[] = $item->myma_part;
              if(strtolower($item->type) == 'instore'){
              $arr[] = number_format($item->myma_part,4);
              $myma_share += $item->myma_part;
                if($item->other_share == 0){
                  $arr[] = number_format( ($item->transaction_amount-$item->myma_part-$item->flexm_part),4);
                  $other_share += ($item->transaction_amount-$item->myma_part-$item->flexm_part);
                }
                else{
                  $arr[] = number_format($item->other_share,4);
                  $other_share += $item->other_share;
                }

              }
              else{
                $myma_share += $item->myma_share;
                $other_share += $item->other_share;
                $arr[] = number_format($item->myma_share,4);
                $arr[] = number_format($item->other_share,4);
              }
              // $arr[] = $item->status;

              $transaction_amount += $item->transaction_amount;
              $flexm_part += $item->flexm_part;
              $myma_part += $item->myma_part;

            $paymentsArray[] = $arr;
        }

        $arr = [];
        $arr[] = 'Total';
        $arr[] = '';
        $arr[] = '';
        $arr[] = '';
        $arr[] = '';
        $arr[] = '';
        $arr[] = '';
        $arr[] = '';
        $arr[] = '';
        $arr[] = '';
        $arr[] = '';
        $arr[] = '';
        $arr[] = '';
        $arr[] = '';
        $arr[] = '';
        $arr[] = '';
        $arr[] = '';
        $arr[] = '';

        $paymentsArray[] = $arr;

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

    public function exportPaymentReport(Request $request)
    {
        $paymentsArray = [];

        $merchant_id = $request->input('merchant_id');
        if($merchant_id == ''){
          $merchant_ids = Merchant::where('active', '1')->pluck('id');
          // $merchant_id  = Merchant::first()->mid;
        }else{
          // $merchant_id = decrypt($merchant_id);
          $merchant_ids = Merchant::where('mid', $merchant_id)->pluck('id');
        }
        $from = $request->input('start');
        $to = $request->input('end');

        $items = $this->getPaymentReportItems($merchant_ids, $from, $to);

        $show_checked = $request->input('show_checked');
        if($show_checked){
          $items->where('verified', '1');
        }

        $show_exported = $request->input('exported');
        if($show_exported){
          $items->where('exported', '1');
        }else{
          $items->where('exported', '0');
        }

        $show_non_checked = $request->input('show_non_checked');
        if($show_non_checked){
          $items->where('verified', '0');
        }

        $items = $items->get();

        if(count($items)){
            $paymentsArray[] = array('Vendor name', 'Payment due date','Vendor transacted date', 'Vendor product type',
            'Vendor sales quantity', 'Vendor Sales Amount', 'Wallet received amount', 'Myma Comms share',
            'Myma Wallet Txn fee earned', 'Flexm cost', 'GST', 'Net payable to vendor', 'Action' );
        }

        $gst = $amount = $wallet_rec_amt = $revenue_deducted = $txn_fee = $cost_charged = $net_payable = 0;
        foreach($items as $item){
              $arr = [];
              $arr[] = @$item->merchant->merchant_name;
              $item->payout_date = Carbon::parse($item->payout_date)->format('d/m/Y');
              $item->start_date = Carbon::parse($item->start_date)->format('d/m/Y');
              $arr[] = $item->payout_date;
              $arr[] = $item->start_date;
              $arr[] = @$item->merchant->product_type;
              $arr[] = $item->quantity;
              $arr[] = number_format($item->amount,4);
              $arr[] = number_format($item->wallet_received_amount,4);
              $arr[] = number_format($item->revenue_deducted,4);
              $arr[] = number_format($item->txn_fee,4);
              $arr[] = number_format($item->cost_charged,4);
              $arr[] = number_format($item->gst,4);
              $arr[] = number_format($item->net_payable,4);
              if($item->verified)
              $arr[] =  'checked';
              else
              $arr[] =  'not checked';

              $amount += $item->amount;
              $wallet_rec_amt += $item->wallet_received_amount;
              $revenue_deducted += $item->revenue_deducted;
              $txn_fee += $item->txn_fee;
              $cost_charged += $item->cost_charged;
              $gst += $item->gst;
              $net_payable += $item->net_payable;
              $paymentsArray[] = $arr;
        }

        $arr = [];
        $arr[] = 'Total';
        $arr[] = '';
        $arr[] = '';
        $arr[] = '';
        $arr[] = '';
        $arr[] = number_format($amount,4);
        $arr[] = number_format($wallet_rec_amt,4);
        $arr[] = number_format($revenue_deducted,4);
        $arr[] = number_format($txn_fee,4);
        $arr[] = number_format($cost_charged,4);
        $arr[] = number_format($gst,4);
        $arr[] = number_format($net_payable,4);

        $paymentsArray[] = $arr;

        Excel::create('PaymentReport', function($excel) use ($paymentsArray) {
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

    public function getUsers(Request $request)
    {
      $auth_user = Auth::user();
      $items = Merchant::where('active', '1')->pluck('merchant_name', 'id');
      if($auth_user->hasRole('food-admin')){
        $user_ids = User::whereHas('roles', function($q){
          $q->whereIn('slug', ['restaurant-owner-single', 'restaurant-owner-catering']);
        })->pluck('id');
        $merchants = FoodMerchant::whereIn('user_id', $user_ids)->select('user_id', 'id')->get();
        $items = [];
        foreach($merchants as $item){
          $items[encrypt($item->id)] = @$item->user->name;
        }

        return view('admin.payout.users', compact('items', 'auth_user'));
      }
      elseif(!$auth_user->hasRole('spuul')){
        foreach($items as $key => $item){
          $items[encrypt($key)] = $item;
          unset($items[$key]);
        }

        return view('admin.payout.users', compact('items', 'auth_user'));
      }else{
        $merchant = Merchant::where('user_id', $auth_user->id)->first();
        if($merchant){
          $merchant_id = $merchant->id;
          $merchant_id = encrypt($merchant_id);
          return redirect()->route('admin.payout.view', ['merchant_id' => $merchant_id]);
        }else{
          return redirect()->route('home');

          if($auth_user->hasRole('spuul')){
            $merchant_id = encrypt(2);
            return redirect()->route('admin.payout.view', ['merchant_id' => $merchant_id]);
          }else{
            return redirect()->route('/');
          }
        }
      }
    }

    public function getView(Request $request)
    {
        $auth_user = Auth::user();

        if ($merchant_id = $request->input('merchant_id')) {
            $now = Carbon::now()->toDateString();

            $merchant_id = decrypt($merchant_id);
            $merchant = Merchant::find($merchant_id);
            $payout = Payout::where('payout_for', 'wlc')->where('merchant_id', $merchant_id)->whereDate('payout_date', '>=', $now)->orderBy('payout_date', 'asc')->first();
            if($payout){
              $end_date = $payout->payout_date;
              $exist = 1;
            }else{
              $payout = Payout::where('payout_for', 'wlc')->where('merchant_id', $merchant_id)->orderBy('payout_date', 'desc')->first();
              if($payout){
                $end_date = $payout->payout_date;
                $exist = 1;
              }else{
                $exist = 0;
                $start_date = $merchant->start_date != ''?$merchant->start_date:$merchant->created_at;
                $start_date = Carbon::parse($start_date);

                $pay_insert = Payout::create([
                  'merchant_id' => $merchant_id,
                  'amount' => 0,
                  'start_date' => $start_date->toDateString(),
                  'status' => 'pending',
                  'payout_for'  => 'wlc'

                ]);
                $payout = Payout::find($pay_insert->id);
              }
            }

            if($merchant->frequency > 0){
              if($exist){
                  $start_date = Carbon::parse($end_date)->subDays($merchant->frequency);
              }
              $end_date = Carbon::parse($start_date)->addDays($merchant->frequency);
            }else{
              if($exist){
                  $start_date = Carbon::parse($end_date)->subDays(7);
              }
              $end_date = Carbon::parse($start_date)->addDays(7);
            }
            // elseif($merchant->frequency == '1_month'){
            //   if($exist){
            //       $start_date = Carbon::parse($end_date)->subMonth();
            //   }
            //   $end_date = Carbon::parse($start_date)->addMonth();
            // }
            // elseif($merchant->frequency == '2_month'){
            //   if($exist){
            //       $start_date = Carbon::parse($end_date)->subMonth();
            //   }
            //   $end_date = Carbon::parse($start_date)->addMonth();
            // }
            // elseif($merchant->frequency == '3_month'){
            //   if($exist){
            //       $start_date = Carbon::parse($end_date)->subMonth();
            //   }
            //   $end_date = Carbon::parse($start_date)->addMonth();
            // }

            if($exist == 0){
              $payout->update([
                'payout_date' => $end_date
              ]);
            }

            $start_date = $start_date;//->toDateString();
            //subtract a day as time period ended a day before
            $end_date = $end_date->subDay();
            $end_date = $end_date->toDateString();

            $total = Payout::where('payout_for', 'wlc')->where('merchant_id', $merchant->id)->where('status', 'paid')->sum('amount');
            $sum = Transactions::where('mid', $merchant->mid)->whereDate('created_at', '>=', $start_date)->whereDate('created_at', '<=', $end_date)->sum('transaction_amount');
            $sum1 = Transactions::where('mid', $merchant->mid)->whereDate('created_at', '>=', $start_date)->whereDate('created_at', '<=', $end_date)->sum('other_share');
            if($payout && $merchant->id != 13){
              $payout->update([
                'amount'  => $sum
              ]);
            }
            $data['total'] = $total;
            if($merchant->id == 13){
              $data['amount'] = $payout->net_payable;
            }else{
              $data['amount'] = $sum1;
            }
            $data['next_payout_date'] = Carbon::parse($payout->payout_date)->format('d/m/Y');
            $data['last_amount'] = 0;
            $today = false;
            if($data['next_payout_date'] == Carbon::now()->toDateString()){
              $today = true;
            }
            $data['last_payout_pending'] = false;
            $now = Carbon::now()->toDateString();
            $pending = Payout::where('payout_for', 'wlc')->where('merchant_id', $merchant_id)->where('status', 'pending')->count();
            $data['last_payout_list'] = [];
            $total = 0;
            if($pending > 1){
              $data['last_payout_pending'] = true;
              $ll = Payout::where('payout_for', 'wlc')->where('merchant_id', $merchant_id)->where('status', 'pending')->where('payout_date', '<', $now)->orderBy('id', 'desc');
              $ll = $ll->get();
              foreach($ll as $l){
                $l->payout_date = Carbon::parse($l->payout_date)->format('d/m/Y');
              }
              $data['last_payout_list'] = $ll;
              $data['last_amount'] = $ll->sum('net_payable');
              $total = Payout::where('payout_for', 'wlc')->where('merchant_id', $merchant_id)->where('status', 'pending')->where('payout_date', '<=', $now)->sum('net_payable');
            }

            $merchant_id = encrypt($merchant_id);
            $type = 'wlc';
            return view('admin.payout.view', compact('items', 'auth_user', 'data', 'today', 'total', 'merchant_id', 'type'));
        }else{
            return redirect()->route('admin.payout.users');
        }
    }

    public function getFoodView(Request $request)
    {
        $auth_user = Auth::user();

        if ($merchant_id = $request->input('merchant_id')) {
            $now = Carbon::now()->toDateString();

            $merchant_id = decrypt($merchant_id);

            $merchant = FoodMerchant::find($merchant_id);
            $payout = Payout::where('merchant_id', $merchant_id)->where('payout_for', 'food')->whereDate('payout_date', '>=', $now)->orderBy('payout_date', 'asc')->first();
            if($payout){
              $end_date = $payout->payout_date;
              $exist = 1;
            }else{

              $payout = Payout::where('merchant_id', $merchant_id)->where('payout_for', 'food')->orderBy('payout_date', 'desc')->first();
              if($payout){
                $end_date = $payout->payout_date;
                $exist = 1;
              }else{
                $exist = 0;
                $start_date = $merchant->start_date != ''?$merchant->start_date:$merchant->created_at;
                $start_date = Carbon::parse($start_date);

                $pay_insert = Payout::create([
                  'merchant_id' => $merchant_id,
                  'amount' => 0,
                  'start_date' => $start_date->toDateString(),
                  'status' => 'pending',
                  'payout_for'  => 'food'
                ]);
                $payout = Payout::find($pay_insert->id);
              }
            }

            if($merchant->frequency > 0){
              if($exist){
                  $start_date = Carbon::parse($end_date)->subDays($merchant->frequency);
              }
              $end_date = Carbon::parse($start_date)->addDays($merchant->frequency);
            }else{
              if($exist){
                  $start_date = Carbon::parse($end_date)->subDays(7);
              }
              $end_date = Carbon::parse($start_date)->addDays(7);
            }
            // elseif($merchant->frequency == '1_month'){
            //   if($exist){
            //       $start_date = Carbon::parse($end_date)->subMonth();
            //   }
            //   $end_date = Carbon::parse($start_date)->addMonth();
            // }
            // elseif($merchant->frequency == '2_month'){
            //   if($exist){
            //       $start_date = Carbon::parse($end_date)->subMonth();
            //   }
            //   $end_date = Carbon::parse($start_date)->addMonth();
            // }
            // elseif($merchant->frequency == '3_month'){
            //   if($exist){
            //       $start_date = Carbon::parse($end_date)->subMonth();
            //   }
            //   $end_date = Carbon::parse($start_date)->addMonth();
            // }

            if($exist == 0){
              $payout->update([
                'payout_date' => $end_date
              ]);
            }

            $start_date = $start_date;//->toDateString();
            //subtract a day as time period ended a day before
            $end_date = $end_date->subDay();
            $end_date = $end_date->toDateString();

            $total = Payout::where('merchant_id', $merchant->id)->where('payout_for', 'food')->where('status', 'paid')->sum('amount');
            $sum = Transactions::where('food_merchant_id', $merchant->id)->whereDate('created_at', '>=', $start_date)->whereDate('created_at', '<=', $end_date)->sum('food_share');
            if($payout){
              $payout->update([
                'amount'  => $sum
              ]);
            }
            $data['total'] = $total;
            $data['amount'] = $sum;
            $data['next_payout_date'] = $payout->payout_date;
            $data['last_amount'] = 0;

            $today = false;
            if($data['next_payout_date'] == Carbon::now()->toDateString()){
              $today = true;
            }
            $passed = false;
            if($data['next_payout_date'] < Carbon::now()->toDateString()){
              $passed = true;
              $data['last_amount'] = $data['amount'];
            }
            $data['last_payout_pending'] = false;
            $now = Carbon::now()->toDateString();
            $pending = Payout::where('payout_for', 'food')->where('merchant_id', $merchant_id)->where('status', 'pending')->count();
            $data['last_payout_list'] = [];
            $total = 0;
            if($pending > 1){
              $data['last_payout_pending'] = true;
              $ll = Payout::where('payout_for', 'food')->where('merchant_id', $merchant_id)->where('status', 'pending')->where('payout_date', '<', $now)->orderBy('id', 'desc');
              $data['last_payout_list'] = $ll->get();
              $data['last_amount'] = $ll->sum('amount');
              $total = Payout::where('payout_for', 'food')->where('merchant_id', $merchant_id)->where('status', 'pending')->where('payout_date', '<=', $now)->sum('amount');
            }
            // if($_SERVER['REMOTE_ADDR'] == '172.111.218.189'){
            //   $today = true;
            // }
            $merchant_id = encrypt($merchant_id);
            $type = 'food';
            return view('admin.payout.view', compact('items', 'auth_user', 'data', 'today', 'total', 'merchant_id', 'type', 'passed'));
        }else{
            return redirect()->route('admin.payout.users');
        }
    }

    public function getList(Request $request)
    {
        $auth_user = Auth::user();
        $export = $request->input('export');
        $statuses = Transactions::groupBy('transaction_status')->pluck('transaction_status');
        if ($payout_id = $request->input('payout_id')) {
          $payout_id = decrypt($payout_id);
          $payout = Payout::find($payout_id);
          if($payout->payout_for == 'food'){
            $food_merchant = FoodMerchant::find($payout->merchant_id);
            if($food_merchant){
              if($food_merchant->user->hasRole('restaurant-owner-catering')){
                $merchant_id = 13;
              }else{
                $merchant_id = 12;
              }
            }else{
              $merchant_id = $food_merchant->merchant_id;
            }
          }else{
            $merchant_id = $payout->merchant_id;
          }
          $start_date = $payout->start_date;
          $end_date = $payout->payout_date;
          $end_date = Carbon::parse($end_date)->subDay()->toDateString();
          $merchant = Merchant::find($merchant_id);

          $items = Transactions::where('mid', $merchant->mid)->whereDate('created_at', '=', $start_date);//->whereDate('created_at', '<=', $end_date);

          $data['myma_total'] = $items->sum('myma_share');
          $data['merchant_total'] = $items->sum('other_share');
          $data['flexm_total'] = $items->sum('flexm_part');

          if ($txn_id = $request->input('transaction_id')) {
            $items->where('transaction_ref_no', 'like', "%{$txn_id}%");
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

          if($export != 'true'){
            $limit = 10;
            $items = $items->sortable(['id' => 'desc'])->paginate($limit);
            $paginate_data = $request->except('page');

            return view('admin.payout.list', compact('items', 'auth_user', 'paginate_data', 'data' , 'statuses'));
          }else{
            $items = $items->get();
            $this->exportTxns($items);
          }
        }
        else{
          $merchant_id = $request->input('merchant_id');
          // $merchant_id = decrypt($merchant_id);
        }
        $now = Carbon::now()->toDateString();
        if ($merchant_id) {
            $merchant_id = decrypt($merchant_id);
            $merchant = Merchant::find($merchant_id);
            $payout = Payout::where('payout_for', 'wlc')->where('merchant_id', $merchant_id)->whereDate('payout_date', '=', $now)->orderBy('payout_date', 'asc')->first();
            if($payout){
              $end_date = $payout->payout_date;
              $exist = 1;
            }else{
              $payout = Payout::where('payout_for', 'wlc')->where('merchant_id', $merchant_id)->orderBy('payout_date', 'desc')->first();
              if($payout){
                $end_date = $payout->payout_date;
                $exist = 1;
              }else{
                $exist = 0;
                $start_date = $merchant->start_date != ''?$merchant->start_date:$merchant->created_at;
              }
            }

            if($merchant->frequency > 0){
              if($exist){
                  $start_date = Carbon::parse($end_date)->subDays($merchant->frequency);
              }
              $end_date = Carbon::parse($start_date)->addDays($merchant->frequency);
            }else{
              if($exist){
                  $start_date = Carbon::parse($end_date)->subDays(7);
              }
              $end_date = Carbon::parse($start_date)->addDays(7);
            }
            // elseif($merchant->frequency == '1_month'){
            //   if($exist){
            //       $start_date = Carbon::parse($end_date)->subMonth();
            //   }
            //   $end_date = Carbon::parse($start_date)->addMonth();
            // }
            // elseif($merchant->frequency == '2_month'){
            //   if($exist){
            //       $start_date = Carbon::parse($end_date)->subMonth();
            //   }
            //   $end_date = Carbon::parse($start_date)->addMonth();
            // }
            // elseif($merchant->frequency == '3_month'){
            //   if($exist){
            //       $start_date = Carbon::parse($end_date)->subMonth();
            //   }
            //   $end_date = Carbon::parse($start_date)->addMonth();
            // }

            //subtract a day as time period ended a day before
            $end_date = $end_date->subDay();
            $start_date = $start_date;//->toDateString();
            $end_date = $end_date;
            $type = $request->input('type');

            $items = Transactions::where('mid', $merchant->mid)->whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date);

            if($type == 'current'){
              $nn = Carbon::now();
              $payout = Payout::where('payout_for', 'wlc')->where('merchant_id', $merchant_id)->whereDate('payout_date', '>=', $nn->toDateString())->count();
              if($payout == 0){
                  $items->whereDate('created_at', '>=', $nn->toDateString());
              }
              if($payout > 1){
                $payout = Payout::where('payout_for', 'wlc')->where('merchant_id', $merchant_id)->whereDate('payout_date', '=', $nn->toDateString())->first();
                if($payout){
                  $st_date = Carbon::parse($payout->start_date);
                  $items->whereDate('created_at', '=', $st_date);
                }
              }


            }

            $data['myma_total'] = number_format(($items->sum('myma_share')+$items->sum('myma_part')),4);
            $data['merchant_total'] = $items->sum('other_share');
            $data['flexm_total'] = $items->sum('flexm_part');

            if ($txn_id = $request->input('transaction_id')) {
              $items->where('transaction_ref_no', 'like', "%{$txn_id}%");
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

            if($export != 'true'){
              $limit = 10;
              $items = $items->sortable(['id' => 'desc'])->paginate($limit);
              $paginate_data = $request->except('page');

              return view('admin.payout.list', compact('items', 'auth_user', 'paginate_data', 'data', 'statuses'));
            }else{
              $items = $items->get();
              $this->exportTxns($items);
            }
        }else{
            return redirect()->route('admin.payout.users');
        }
    }

    public function getFoodList(Request $request)
    {
        $auth_user = Auth::user();
        $export = $request->input('export');
        if ($payout_id = $request->input('payout_id')) {
          $payout_id = decrypt($payout_id);
          $payout = Payout::find($payout_id);
          $merchant_id = $payout->merchant_id;
          $start_date = $payout->start_date;
          $end_date = $payout->payout_date;
          $end_date = Carbon::parse($end_date)->subDay()->toDateString();
          $merchant = FoodMerchant::find($merchant_id);

          $items = Transactions::where('food_merchant_id', $merchant->id)->whereDate('created_at', '>=', $start_date)->whereDate('created_at', '<=', $end_date);

          $data['myma_total'] = $items->sum('naanstap_pay');
          $data['merchant_total'] = $items->sum('food_share');
          $data['flexm_total'] = $items->sum('flexm_part');

          if ($txn_id = $request->input('transaction_id')) {
            $items->where('transaction_ref_no', 'like', "%{$txn_id}%");
          }

          if($export != 'true'){
            $limit = 10;
            $items = $items->sortable(['id' => 'desc'])->paginate($limit);
            $paginate_data = $request->except('page');

            return view('admin.payout.food_list', compact('items', 'auth_user', 'paginate_data', 'data'));
          }else{
            $items = $items->get();
            $this->exportTxns($items);
          }
        }
        else{
          $merchant_id = $request->input('merchant_id');
          // $merchant_id = decrypt($merchant_id);
        }
        $now = Carbon::now()->toDateString();
        if ($merchant_id) {
            $merchant_id = decrypt($merchant_id);
            $merchant = FoodMerchant::find($merchant_id);
            $payout = Payout::where('payout_for', 'food')->where('merchant_id', $merchant_id)->whereDate('payout_date', '>=', $now)->orderBy('payout_date', 'asc')->first();
            if($payout){
              $end_date = $payout->payout_date;
              $exist = 1;
            }else{
              $payout = Payout::where('payout_for', 'food')->where('merchant_id', $merchant_id)->orderBy('payout_date', 'desc')->first();
              if($payout){
                $end_date = $payout->payout_date;
                $exist = 1;
              }else{
                $exist = 0;
                $start_date = $merchant->start_date != ''?$merchant->start_date:$merchant->created_at;
              }
            }

            if($merchant->frequency > 0){
              if($exist){
                  $start_date = Carbon::parse($end_date)->subDays($merchant->frequency);
              }
              $end_date = Carbon::parse($start_date)->addDays($merchant->frequency);
            }else{
              if($exist){
                  $start_date = Carbon::parse($end_date)->subDays(7);
              }
              $end_date = Carbon::parse($start_date)->addDays(7);
            }
            // elseif($merchant->frequency == '1_month'){
            //   if($exist){
            //       $start_date = Carbon::parse($end_date)->subMonth();
            //   }
            //   $end_date = Carbon::parse($start_date)->addMonth();
            // }
            // elseif($merchant->frequency == '2_month'){
            //   if($exist){
            //       $start_date = Carbon::parse($end_date)->subMonth();
            //   }
            //   $end_date = Carbon::parse($start_date)->addMonth();
            // }
            // elseif($merchant->frequency == '3_month'){
            //   if($exist){
            //       $start_date = Carbon::parse($end_date)->subMonth();
            //   }
            //   $end_date = Carbon::parse($start_date)->addMonth();
            // }

            //subtract a day as time period ended a day before
            $end_date = $end_date->subDay();
            $start_date = $start_date;//->toDateString();
            $end_date = $end_date;
            $type = $request->input('type');

            $items = Transactions::where('food_merchant_id', $merchant->id)->whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date);

            if($type == 'current'){
              $nn = Carbon::now();
              $payout = Payout::where('payout_for', 'food')->where('merchant_id', $merchant_id)->whereDate('payout_date', '>=', $nn->toDateString())->count();
              if($payout == 0){
                  $items->whereDate('created_at', '>=', $nn->toDateString());
              }
              if($payout > 1){
                $payout = Payout::where('payout_for', 'food')->where('merchant_id', $merchant_id)->whereDate('payout_date', '=', $nn->toDateString())->first();
                if($payout){
                  $st_date = Carbon::parse($payout->start_date);
                  $items->whereDate('created_at', '=', $st_date);
                }
              }


            }

            $data['myma_total'] = number_format(($items->sum('naanstap_pay')),4);
            $data['merchant_total'] = $items->sum('food_share');
            $data['flexm_total'] = $items->sum('flexm_part');

            if ($txn_id = $request->input('transaction_id')) {
              $items->where('transaction_ref_no', 'like', "%{$txn_id}%");
            }

            if($export != 'true'){
              $limit = 10;
              $items = $items->sortable(['id' => 'desc'])->paginate($limit);
              $paginate_data = $request->except('page');

              return view('admin.payout.food_list', compact('items', 'auth_user', 'paginate_data', 'data'));
            }else{
              $items = $items->get();
              $this->exportTxns($items);
            }
        }else{
            return redirect()->route('admin.payout.users');
        }
    }

    public function getPayoutList(Request $request)
    {
        $auth_user = Auth::user();

        if ($merchant_id = $request->input('merchant_id')) {
            $merchant_id = decrypt($merchant_id);
            $merchant = Merchant::find($merchant_id);
            $items = Payout::where('merchant_id', $merchant_id);

            if ($txn_id = $request->input('transaction_id')) {
              $items->where('transaction_id', 'like', "%{$txn_id}%");
            }

            $from = $request->input('start');
            $to = $request->input('end');

            if($from != '' && $to != ''){
                $from = Carbon::parse($from);
                $to = Carbon::parse($to);
                $items->whereDate('payout_date', '>=', $from)->whereDate('payout_date', '<=', $to);
            }

            if ($status = $request->input('type')) {
              $status = strtolower($status);
              if($status == 'pending'){
                $now = Carbon::now();
                $items->where('status', $status)->whereDate('payout_date', '<', $now->toDateString());
              }
              elseif($status == 'all'){
                $items->where('status', 'paid');
              }
            }

            $limit = 10;
            $items = $items->sortable(['id' => 'desc'])->paginate($limit);

            foreach($items as $item){
              $item->payout_date = Carbon::parse($item->payout_date)->format('d/m/Y');
            }
            $paginate_data = $request->except('page');

            return view('admin.payout.payout_list', compact('items', 'auth_user', 'paginate_data'));
        }else{
            return redirect()->route('admin.payout.users');
        }
    }

    public function getWlcPayout(Request $request)
    {
        $auth_user = Auth::user();

        $items = Payout::whereIn('merchant_id', [12,13]);

        if ($txn_id = $request->input('transaction_id')) {
              $items->where('transaction_id', 'like', "%{$txn_id}%");
        }

        $from = $request->input('start');
        $to = $request->input('end');

        if($from != '' && $to != ''){
                $from = Carbon::parse($from);
                $to = Carbon::parse($to);
                $items->whereDate('payout_date', '>=', $from)->whereDate('payout_date', '<=', $to);
        }

        if ($status = $request->input('type')) {
              $status = strtolower($status);
              if($status == 'pending'){
                $now = Carbon::now();
                $items->where('status', $status)->whereDate('payout_date', '<', $now->toDateString());
              }
              elseif($status == 'all'){
                $items->where('status', 'paid');
              }
        }

        $limit = 10;
        $items = $items->sortable(['id' => 'desc'])->paginate($limit);

        foreach($items as $item){
              $item->payout_date = Carbon::parse($item->payout_date)->format('d/m/Y');
        }
        $paginate_data = $request->except('page');

        return view('admin.payout.payout_list', compact('items', 'auth_user', 'paginate_data'));
    }

    public function getDownload(){

        $product_type = ProductType::pluck('name','code');
        $bank_charges = [
          'OUR' => ' Applicant pay all charges',
          'BEN' => 'Beneficiary pay all charge',
          'SHA' => 'Applicant pay DBS Bank charges, Beneficiary pay Agent Bank charges'
        ];
        return view('admin.revenue.download', compact('product_type', 'bank_charges'));
    }

    public function postDownload(Request $request)
    {
        $input = $request->all();

        $merchant_id = $request->input('merchant_id');

        if($merchant_id == ''){
          $merchant_ids = Merchant::where('active', '1')->pluck('id');
          // $merchant_id  = Merchant::first()->mid;
        }else{
          // $merchant_id = decrypt($merchant_id);
          $merchant_ids = Merchant::where('mid', $merchant_id)->pluck('id');
        }

        $from = $request->input('start');
        $to = $request->input('end');

        $items = $this->getPaymentReportItems($merchant_ids, $from, $to);
        $items = $items->where('verified', '1');
        $items = $items->where('exported', '0');
        $items = $items->orderBy('id', 'desc')->get();

        $date = Carbon::now()->format('dmY');

        Option::setOption('organization_id', $input['organization_id']);
        Option::setOption('organization_name', $input['organization_name']);
        Option::setOption('originating_account_no', $input['originating_account_no']);
        Option::setOption('payment_purpose', $input['payment_purpose']);

        //header
        $header = [
          'HEADER', $date, $input['organization_id'], $input['organization_name']
        ];

        //payment
        $count = $total = 0;
        $paymentsArray = [];
        $paymentsArray[] = $header;
        $d33 = '25';
        $arr_payment = ['SAL', 'SPE', 'SL2', 'SE2', 'MP', 'MPE', 'MP2', 'ME2', 'PSL', 'PS2', 'PMP', 'PP2'];
        if(in_array($input['product_type'], $arr_payment)){
          $d33 = '22';
        }elseif(in_array($input['product_type'], ['COL', 'SCE'])){
          $d33 = '30';
        }elseif($input['product_type'] == 'LVT'){
          $d33 = '20';
        }
        elseif($input['product_type'] == 'BPY'){
          $d33 = '21';
        }
        elseif($input['product_type'] == 'SGE'){
          $d33 = '23';
        }
        elseif($input['product_type'] == 'PVT'){
          $d33 = '24';
        }
        elseif($input['product_type'] == 'PPY'){
          $d33 = '25';
        }

        foreach($items as $item){
          $item->exported = '1';
          $item->save();

          $merchant = Merchant::where('id', $item->merchant_id)->first();

          if($merchant){
            $merchant_name = substr($merchant->merchant_name,0,34);
            $count++;
            $total += $item->net_payable;
            $paymentsArray[] = [
              'PAYMENT', $input['product_type'], $input['originating_account_no'],'SGD','','SGD','',$date,$input['bank_charges'],$input['originating_account_no'],
              $merchant_name,'',$merchant->merchant_address_1,$merchant->merchant_address_2,$merchant->merchant_address_3,@$merchant->account->account_number,
              '','','','',@$merchant->account->swift_code, @$merchant->account->bank_name, @$merchant->account->bank_address, @$merchant->account->bank_country,
              @$merchant->account->routing_code,'','0', number_format($item->net_payable, 2, '.', ''),'','','','',$d33,'','','','','','','','','',$input['payment_purpose'],'','','','','','','','','','','','','','',''
            ];

          }else{
            if(strtolower($item->vendor_code) == 'singx'){
              $count++;
              $total += $item->payback_vendor;

              $paymentsArray[] = [
                'PAYMENT', $input['product_type'], $input['originating_account_no'],'SGD','','SGD','',$date,$input['bank_charges'],$input['originating_account_no'],
                'SingX','', getOption('singx_address_1'),getOption('singx_address_2'),getOption('singx_address_3'),getOption('singx_account_number'),
                '','','','',getOption('singx_swift_code'), getOption('singx_bank_name'), getOption('singx_bank_address'), getOption('singx_bank_country'),
                getOption('singx_routing_code '),'','0',
                number_format($item->payback_vendor, 2, '.', ''),'','','','',$d33,'','','','','','','','','',$input['payment_purpose'],'','','','','','','','','','','','','','',''
              ];
            }
          }
        }
        
        $total = number_format($total, 2, '.', '');

        //trailer
        $paymentsArray[] = $trailer = [
          'TRAILER', $count, $total
        ];

        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=file.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        // Excel::create('file', function($excel) use ($paymentsArray) {
        //   // Set the spreadsheet title, creator, and description
        //     $excel->setTitle('DBS Report');
        //     $excel->setCreator('Myma')->setCompany('Myma');
        //     // $excel->setDescription('payments file');
        //
        //     // Build the spreadsheet, passing in the payments array
        //     $excel->sheet('sheet1', function($sheet) use ($paymentsArray) {
        //         $sheet->fromArray($paymentsArray, null, 'A1', false, false);
        //     });
        // })->export('csv');

        $callback = function() use ($paymentsArray, $header, $trailer)
        {
            $file = fopen('php://output', 'w');
            // fputcsv($file, $header);

            foreach($paymentsArray as $item) {
                fputcsv($file, $item);
            }
            // fputcsv($file, $trailer);
            fclose($file);
        };
        return \Response::stream($callback, 200, $headers);
    }

    public function getPaymentReportItems($merchant_id, $from ,$to){

      // $merchant = Merchant::where('mid', $merchant_id)->first();
      $items = Payout::where('amount', '!=', '0')->where('payout_for', 'wlc')->whereIn('merchant_id', $merchant_id);

      $to_date = Carbon::now()->toDateString();
      $from_date = '';

      if($from != '' && $to != ''){
          $from_date = $from = Carbon::parse($from)->toDateString();
          $to_date = $to = Carbon::parse($to)->toDateString();
          $items->whereDate('payout_date', '>=', $from)->whereDate('payout_date', '<=', $to);
      }

      return $items;
    }
    public function getPaymentReport(Request $request)
    {
        $auth_user = Auth::user();
        $merchant_id = $request->input('merchant_id');
        if($merchant_id == ''){
          $merchant_ids = Merchant::where('active', '1')->pluck('id');
          // $merchant_id  = Merchant::first()->mid;
        }else{
          // $merchant_id = decrypt($merchant_id);
          $merchant_ids = Merchant::where('mid', $merchant_id)->pluck('id');
        }
        $from = $request->input('start');
        $to = $request->input('end');

        $items = $this->getPaymentReportItems($merchant_ids, $from, $to);
        $items->where('exported', '0');
        $show_checked = $request->input('show_checked');
        if($show_checked){
          $items->where('verified', '1');
        }

        $show_non_checked = $request->input('show_non_checked');
        if($show_non_checked){
          $items->where('verified', '0');
        }
        $items->where('amount', '!=', 0);
        // $merchant = Merchant::where('mid', $merchant_id)->first();

        // $items = Payout::where('payout_for', 'wlc')->where('merchant_id', $merchant->id);
        //
        //
        // $to_date = Carbon::now()->toDateString();
        // $from_date = '';
        //
        // if($from != '' && $to != ''){
        //     $from_date = $from = Carbon::parse($from)->toDateString();
        //     $to_date = $to = Carbon::parse($to)->toDateString();
        //     $items->whereDate('payout_date', '>=', $from)->whereDate('payout_date', '<=', $to);
        // }
        $limit = 10;
        $items = $items->sortable(['id' => 'desc'])->paginate($limit);
        $paginate_data = $request->except('page');

        foreach($items as $item){
          $item->payout_date = Carbon::parse($item->payout_date)->format('d/m/Y');
          $item->start_date = Carbon::parse($item->start_date)->format('d/m/Y');
        }
        $merchants = Merchant::where('active', '1')->pluck('merchant_name', 'mid');
        // foreach($merchants as $key => $item){
        //     $merchants[encrypt($key)] = $item;
        //     unset($merchants[$key]);
        // }
        return view('admin.payout.report', compact('items', 'auth_user', 'paginate_data', 'merchants'));

    }

    public function getPaidPayment(Request $request)
    {
        $auth_user = Auth::user();
        $merchant_id = $request->input('merchant_id');
        if($merchant_id == ''){
          $merchant_ids = Merchant::where('active', '1')->pluck('id');
          // $merchant_id  = Merchant::first()->mid;
        }else{
          // $merchant_id = decrypt($merchant_id);
          $merchant_ids = Merchant::where('mid', $merchant_id)->pluck('id');
        }
        $from = $request->input('start');
        $to = $request->input('end');

        $items = $this->getPaymentReportItems($merchant_ids, $from, $to);
        $items->where('exported', '1');
        $show_checked = $request->input('show_checked');
        if($show_checked){
          $items->where('verified', '1');
        }

        $show_non_checked = $request->input('show_non_checked');
        if($show_non_checked){
          $items->where('verified', '0');
        }
        $items->where('amount', '!=', 0);

        $limit = 10;
        $items = $items->sortable(['id' => 'desc'])->paginate($limit);
        $paginate_data = $request->except('page');

        foreach($items as $item){
          $item->payout_date = Carbon::parse($item->payout_date)->format('d/m/Y');
          $item->start_date = Carbon::parse($item->start_date)->format('d/m/Y');
        }
        $merchants = Merchant::where('active', '1')->pluck('merchant_name', 'mid');

        return view('admin.payout.paid', compact('items', 'auth_user', 'paginate_data', 'merchants'));

    }

    public function verify(Request $request)
    {
        if ($item_id = $request->input('item_id')) {
            $item_id = decrypt($item_id);
            $payout = Payout::find($item_id);

            if($payout->verified){
                $payout->update(['verified' => '0']);
            }else{
                $payout->update(['verified' => '1']);
            }

            return response()->json(['status' => true]);
        }else{
            return response()->json(['status' => false, 'message' => 'Payout id is required']);
        }
    }


    public function savePayout(SavePayoutRequest $request)
    {
        $auth_user = \Auth::user();
        $merchant_id = $request->input('merchant_id');
        $type = strtolower($request->input('type'));
        $now = Carbon::now();
        // $merchant_id = decrypt($merchant_id);

    	  $payouts = Payout::where('payout_for', $type)->where('status', 'pending')->where('merchant_id', $merchant_id)->get();
        $data = $request->only('transaction_id', 'value_date', 'remarks');
        $data['status'] = 'paid';
        foreach($payouts as $out){
            $out->update($data);
        }

        return redirect()->route('admin.payout.view', ['merchant_id' => $merchant_id])->with([
          'flash_message' => 'Details Updated successfully',
          'flash_level'  => 'success'
        ]);
    }

    public function postEdit($id, Request $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $id = decrypt($id);
        $module = Merchant::findOrFail($id);
        $data = $request->only('merchant_name', 'merchant_category_code', 'dormitory_id', 'merchant_share', 'myma_transaction_share', 'payout_time');

        $module->update($data);
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
}
