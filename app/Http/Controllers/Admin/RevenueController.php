<?php

namespace App\Http\Controllers\Admin;

use App\User, App\Models\Activity, App\Models\Badwords;
use App\Models\Forum, DB;
use App\Models\Maintenance;
use App\Models\ProductType;
use App\Models\Singx;
use App\Models\Role;
use App\Models\Feedback;
use App\Models\LoginHistory;
use App\Models\Transactions;
use App\Models\Share;
use App\Models\Remittance;
use App\Models\RemittanceReport;
use App\Models\Merchant;
use App\Models\Advertisement;
use Illuminate\Http\Request;
use App\Models\Option;

use App\Http\Requests;
use App\Http\Requests\EditShareRequest;
use App\Http\Controllers\Controller;
use Carbon\Carbon, Excel;
use PHPExcel_Worksheet_Drawing;

class RevenueController extends Controller
{

  public function exportRevenueReport(Request $request)
  {
      $paymentsArray = [];

      $mid = $request->input('mid');
      if($mid == 'singx'){
        $trans = Singx::query();

        $from = $request->input('start');
        $to = $request->input('end');

        $to_date = Carbon::now()->toDateString();
        $from_date = '';

        if($from != '' && $to != ''){
            $from_date = $from = Carbon::parse($from)->toDateString();
            $to_date = $to = Carbon::parse($to)->toDateString();
            $trans->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to);
        }

        $item = $trans->selectRaw('*, count(id) as vendor_qty, sum(transaction_amount) as sum_transaction_amount,
        sum(myma_part) as sum_myma_part, sum(singx_part) as sum_singx_part')
        ->orderBy('created_at', 'asc')->first();

        $product_type = getOption('singx_product_type');
        $revenue_model = getOption('singx_revenue_model');
        $merchant_name = 'Singx';

        if($from == ''){
          $from_date = $item->created_at->format('Y-m-d');
        }
        $data = [];
        $data['vendor_code'] = 'Singx';
        $data['vendor_name'] = $merchant_name;
        $data['from_date'] = Carbon::parse($from_date)->format('d/m/Y');
        $data['to_date'] = Carbon::parse($to_date)->format('d/m/Y');
        $data['product_type'] = $product_type;
        $data['vendor_qty'] = $item->vendor_qty;
        $data['vendor_sale'] = number_format($item->sum_transaction_amount,4);//x
        $data['gross'] = number_format($item->sum_myma_part,4);//a
        $data['cost_sharing'] = 0.0000;//b
        $data['txn_fee'] = number_format($item->sum_myma_part,4);//c
        $data['cost_charged'] = number_format($item->sum_singx_part,4);//d
        $data['net'] = $data['gross']+$data['txn_fee']-$data['cost_sharing']-$data['cost_charged'];
        $data['net'] = number_format($data['net'],4);
        $data['gst'] = number_format(0,4);
        $data['payback_vendor'] = $data['vendor_sale']-$data['gross']-$data['txn_fee'];
        $data['payback_vendor'] = number_format($data['payback_vendor'],4);
        $data['revenue_model'] = $revenue_model;
        $items[] = (object)$data;


      }else{
        $from = $request->input('start');
        $to = $request->input('end');
        $items = $this->getReportItems($mid, $from, $to);

      }

      $vendor_sale = 0;
      $gross = 0;
      $cost_sharing = 0;
      $txn_fee = 0;
      $cost_charged = 0;
      $net = 0;
      $payback_vendor = 0;
      $gst = 0;
      if(count($items)){
          $paymentsArray[] = array('Vendor Code', 'Vendor Name', 'Txn date from', 'Txn date to', 'Product type',
          'Vendor QTY', 'Vendor Sale', 'Myma Comms share', 'Cost sharing charged', 'Myma Wallet Txn fee earned', 'Flexm cost',
          'Net revenue earned', 'GST', 'Payback to vendor', 'Revenue Model');
      }

      foreach($items as $item){
            $arr = [];

            $arr[] = $item->vendor_code;
            $arr[] = $item->vendor_name;
            $arr[] = $item->from_date;
            $arr[] = $item->to_date;
            $arr[] = $item->product_type;
            $arr[] = $item->vendor_qty;
            $arr[] = $item->vendor_sale;
            $arr[] = $item->gross;
            $arr[] = number_format($item->cost_sharing,4);
            $arr[] = $item->txn_fee;
            $arr[] = $item->cost_charged;
            $arr[] = $item->net;
            $arr[] = @$item->gst;
            $arr[] = $item->payback_vendor;
            $arr[] = $item->revenue_model;

            $paymentsArray[] = $arr;

            $vendor_sale += $item->vendor_sale;
            $gross += $item->gross;
            $cost_sharing += $item->cost_sharing;
            $txn_fee += $item->txn_fee;
            $cost_charged += $item->cost_charged;
            $net += $item->net;
            $gst += $item->gst;
            $payback_vendor += $item->payback_vendor;
      }

      $arr = [];

      $arr[] = 'Total';
      $arr[] = '';
      $arr[] = '';
      $arr[] = '';
      $arr[] = '';
      $arr[] = '';
      $arr[] = number_format($vendor_sale,4);
      $arr[] = number_format($gross,4);
      $arr[] = number_format($cost_sharing,4);
      $arr[] = number_format($txn_fee,4);
      $arr[] = number_format($cost_charged,4);
      $arr[] = number_format($net,4);
      $arr[] = number_format($gst,4);
      $arr[] = number_format($payback_vendor,4);
      $arr[] = '';

      $paymentsArray[] = $arr;
      Excel::create('RevenueReport', function($excel) use ($paymentsArray) {
        // Set the spreadsheet title, creator, and description
          $excel->setTitle('List');
          $excel->setCreator('Myma')->setCompany('Myma');
          // $excel->setDescription('payments file');

          // Build the spreadsheet, passing in the payments array
          $excel->sheet('sheet1', function($sheet) use ($paymentsArray) {
              $sheet->fromArray($paymentsArray, null, 'A1', false, false);
          });
      })->export('xls');
  }

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
          $from = $request->input('start');
          $to = $request->input('end');

          if($from != '' && $to != ''){
              $from = Carbon::parse($from);
              $to = Carbon::parse($to);
              $items->whereBetween('created_at', [$from, $to]);
          }

          if($input_mid = $request->input('mid')){
              $items->where('mid', $input_mid);
          }

          if($auth_user->hasRole('spuul')){
            $items->where('type', 'spuul');
          }
          if($auth_user->hasRole('training')){
            $mid = Merchant::where('user_id', $auth_user->id)->pluck('mid');
            $items->where('type', 'course');
            $items->whereIn('mid', $mid);
          }
          if($auth_user->hasRole('food-admin')){
            $items->where('type', 'food');
          }

        }elseif($type == 'instore'){
          $items = Transactions::orderBy('created_at', 'desc');
          $items->where('type', 'instore');

          if($id = $request->input('transaction_id')){
              $items->where('transaction_ref_no', 'like', "%{$id}%");
          }

        }else{
          $items = Remittance::orderBy('id', 'desc');
          if($id = $request->input('transaction_id')){
              $items->where('hash_id', 'like', "%{$id}%");
          }
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
                $paymentsArray[] = array('Type', 'Ref id', 'Phone no', 'Wallet user name', 'Transaction date', 'Transaction amount', 'Transaction currency',
                'Transaction ref no', 'Transaction status', 'Transaction code', 'MID', 'TID', 'Merchant name', 'Payment mode',
                'Flexm part', 'MYMA part', 'MYMA share', 'Merchant share');
            }
        }

        foreach($items as $item){

            $arr = [];
            if($type == 'remit'){
              $arr[] = $item->hash_id;
              $arr[] = $item->provider;
              $arr[] = $item->delivery_method;
              $arr[] = $item->receive_country;
              $arr[] = $item->status;
              $arr[] = $item->payout_agent;
              $arr[] = $item->customer_fx;
              $arr[] = $item->send_currency;
              $arr[] = $item->send_amount;
              $arr[] = $item->customer_fixed_fee;
              $arr[] = $item->total_transaction_amount;
              $arr[] = $item->receive_currency;
              $arr[] = $item->receive_amount;
              $arr[] = $item->crossrate;
              $arr[] = $item->provider_amount_fee_currency;
              $arr[] = $item->provider_amount_fee;
              $arr[] = $item->provider_exchange_rate;
              $arr[] = $item->send_amount_rails_currency;
              $arr[] = $item->send_amount_rails;
              $arr[] = $item->send_amount_before_fx;
              $arr[] = $item->send_amount_after_fx;
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

            }else{
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
              $arr[] = $item->myma_share;
              $arr[] = $item->other_share;
              // $arr[] = $item->status;
            }
            $paymentsArray[] = $arr;
        }

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

    public function getReport(Request $request)
    {
        $auth_user = \Auth::user();
        $items = [];
        $ad_manager = [];
        // User::whereHas('roles', function($q){
        //     $q->whereHas('permissions', function($qq){
        //         $qq->where('permissions.id',15)
        //         ->orWhere('permissions.id',16)
        //         ->orWhere('permissions.id',18);
        //     });
    		// 	})->orderBy('id', 'desc')->pluck('name','id');

        $mid = $request->input('mid');
        if($mid == 'singx'){
          $trans = Singx::query();

          $from = $request->input('start');
          $to = $request->input('end');

          $to_date = Carbon::now()->toDateString();
          $from_date = '';

          if($from != '' && $to != ''){
              $from_date = $from = Carbon::parse($from)->toDateString();
              $to_date = $to = Carbon::parse($to)->toDateString();
              $trans->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to);
          }

          $item = $trans->selectRaw('*, count(id) as vendor_qty, sum(transaction_amount) as sum_transaction_amount,
          sum(myma_part) as sum_myma_part, sum(singx_part) as sum_singx_part')
          ->orderBy('created_at', 'asc')->first();

          $product_type = getOption('singx_product_type');
          $revenue_model = getOption('singx_revenue_model');
          $merchant_name = 'Singx';

          if($from == '' && $item->created_at){
            $from_date = $item->created_at->format('Y-m-d');
          }
          $data = [];
          $data['type'] = 'singx';
          $data['vendor_code'] = 'Singx';
          $data['vendor_name'] = $merchant_name;
          $data['from_date'] = Carbon::parse($from_date)->format('d/m/Y');
          $data['to_date'] = Carbon::parse($to_date)->format('d/m/Y');
          $data['product_type'] = $product_type;
          $data['vendor_qty'] = $item->vendor_qty;
          $data['vendor_sale'] = number_format($item->sum_transaction_amount,4);//x
          $data['gross'] = number_format($item->sum_myma_part,4);//a
          $data['cost_sharing'] = 0.0000;//b
          $data['txn_fee'] = number_format($item->sum_myma_part,4);//c
          $data['cost_charged'] = number_format($item->sum_singx_part,4);//d
          $data['net'] = $data['gross']+$data['txn_fee']-$data['cost_sharing']-$data['cost_charged'];
          $data['net'] = number_format($data['net'],4);
          $data['gst'] = number_format(0,4);
          $data['payback_vendor'] = $data['vendor_sale']-$data['gross']-$data['txn_fee'];
          $data['payback_vendor'] = number_format($data['payback_vendor'],4);
          $data['revenue_model'] = $revenue_model;
          $items[] = (object)$data;


        }else{
          $from = $request->input('start');
          $to = $request->input('end');

          $items = $this->getReportItems($mid, $from, $to);

        }
        $user = \Auth::user();
        $merchants = Merchant::where('active', '1')->pluck('merchant_name', 'mid');

        $total['vendor_sale'] = 0;
        $total['gross'] = 0;
        $total['cost_sharing'] = 0;
        $total['txn_fee'] = 0;
        $total['cost_charged'] = 0;
        $total['net'] = 0;
        $total['gst'] = 0;
        $total['payback_vendor'] = 0;

        return view('admin.revenue.report', compact('items', 'user', 'merchants', 'ad_manager', 'total'));

    }

    public function getReportItems($mid, $from, $to){
      $trans = Transactions::query();
        $items= [];
      if($mid != ''){
        $merchant = Merchant::where('mid', $mid)->first();
        if(!$merchant){
          break;
        }
        $trans->where('mid', $mid);
      }

      $to_date = Carbon::now()->toDateString();
      $from_date = '';

      if($from != '' && $to != ''){
          $from_date = $from = Carbon::parse($from)->toDateString();
          $to_date = $to = Carbon::parse($to)->toDateString();
          $trans->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to);
      }

      $trans = $trans->selectRaw('id, mid, type, merchant_name, created_at, count(id) as vendor_qty, sum(transaction_amount) as sum_transaction_amount,
      sum(myma_share) as sum_myma_share, sum(myma_part) as sum_myma_part, sum(flexm_part) as sum_flexm_part, sum(gst) as sum_gst')
      ->groupBy('mid')->orderBy('created_at', 'asc')->get();

      foreach($trans as $item){
        $product_type = @$item->merchant->product_type;
        if($product_type == ''){
          if(@$item->merchant->user && @$item->merchant->user->hasRole('restaurant-owner-catering')){
            $product_type = 'Catering';
          }elseif(@$item->merchant->user && @$item->merchant->user->hasRole('restaurant-owner-single')){
            $product_type = 'Realtime Order';
          }
        }

        if($from == ''){
          $from_date = $item->created_at->format('Y-m-d');
        }
        $data = [];
        $data['type'] = $item->type;
        $data['vendor_code'] = $item->mid;
        $data['vendor_name'] = @$item->merchant_name;
        $data['from_date'] = Carbon::parse($from_date)->format('d/m/Y');
        $data['to_date'] = Carbon::parse($to_date)->format('d/m/Y');
        $data['product_type'] = $product_type;
        $data['vendor_qty'] = $item->vendor_qty;
        $data['vendor_sale'] = $item->sum_transaction_amount;//x
        if($item->type == 'instore'){
          $data['gross'] = 0;//a
        }else{
          $data['gross'] = $item->sum_myma_share;//a
        }

        $data['cost_sharing'] = 0.0000;//b
        $data['txn_fee'] = $item->sum_myma_part+$item->sum_flexm_part;//c
        $data['cost_charged'] = $item->sum_flexm_part;//d
        $data['net'] = $data['gross']+$data['txn_fee']-$data['cost_sharing']-$data['cost_charged'];
        $data['net'] = number_format($data['net'],4);
        $data['gst'] = number_format($item->sum_gst,4);
        $data['payback_vendor'] = $data['vendor_sale']-$data['gross']-$data['txn_fee']-$item->sum_gst;//added sum_gst to subtract - 7 feb-2019
        $data['payback_vendor'] = number_format($data['payback_vendor'],4);
        $data['revenue_model'] = @$item->merchant->revenue_model;

        $data['vendor_sale'] = number_format($data['vendor_sale'], 4);//x
        $data['gross'] = number_format($data['gross'],4);//a
        $data['txn_fee'] = number_format($data['txn_fee'],4);//c
        $data['cost_charged'] = number_format($data['cost_charged'],4);//c
            // $from_date = $item->created_at->toDateString();
            // break;
            //
        $items[] = (object)$data;
      }

      if($mid == ''){
        $items[] = $this->getSingxTxns($from, $to);
        // $items[] = $this->getAdTxns($from, $to, $ad_manager);
      }

      return $items;
    }

    public function viewTransaction($id, Request $request)
    {
        if(\Auth::guest()){
            return view('auth.login');
        }else{
            $id = decrypt($id);
            $item = Remittance::findOrFail($id);
            $wallet = RemittanceReport::where('hash_id', $item->hash_id)->first();

            $user = \Auth::user();
            return view('admin.transactions.view', compact('item', 'user', 'wallet'));
        }
    }

    private function getSingxTxns($from, $to){
      $trans = Singx::query();

      $to_date = Carbon::now()->toDateString();
      $from_date = '';

      if($from != '' && $to != ''){
          $from_date = $from = Carbon::parse($from)->toDateString();
          $to_date = $to = Carbon::parse($to)->toDateString();
          $trans->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to);
      }

      $item = $trans->selectRaw('*, count(id) as vendor_qty, sum(transaction_amount) as sum_transaction_amount,
      sum(myma_part) as sum_myma_part, sum(singx_part) as sum_singx_part')
      ->orderBy('created_at', 'asc')->first();

      $product_type = getOption('singx_product_type');
      $revenue_model = getOption('singx_revenue_model');
      $merchant_name = 'Singx';

      if($from == '' && $item->created_at != ''){
        $from_date = $item->created_at->format('Y-m-d');
      }
      $data = [];
      $data['type'] = 'singx';
      $data['vendor_code'] = 'Singx';
      $data['vendor_name'] = $merchant_name;
      $data['from_date'] = Carbon::parse($from_date)->format('d/m/Y');
      $data['to_date'] = Carbon::parse($to_date)->format('d/m/Y');
      $data['product_type'] = $product_type;
      $data['vendor_qty'] = $item->vendor_qty;
      $data['vendor_sale'] = $item->sum_transaction_amount;//x
      $data['gross'] = $item->sum_myma_part;//a
      $data['cost_sharing'] = 0.0000;//b
      $data['txn_fee'] = $item->sum_myma_part;//c
      $data['cost_charged'] = $item->sum_singx_part;//d
      $data['net'] = $data['gross']+$data['txn_fee']-$data['cost_sharing']-$data['cost_charged'];
      $data['net'] = number_format($data['net'],4);
      $data['gst'] = number_format(0,4);
      $data['payback_vendor'] = $data['vendor_sale']-$data['gross']-$data['txn_fee'];
      $data['payback_vendor'] = number_format($data['payback_vendor'],4);
      $data['revenue_model'] = $revenue_model;

      $data['vendor_sale'] = number_format($data['vendor_sale'],4);//x
      $data['gross'] = number_format($data['gross'],4);//a
      $data['txn_fee'] = number_format($data['txn_fee'],4);//c
      $data['cost_charged'] = number_format($data['cost_charged'],4);//c

      $item = (object)$data;

      return $item;
    }

    private function getAdTxns($from, $to, $ad_manager){

      foreach($ad_manager as $manager_id => $name){
        if($manager_id == 1){
          $ads = Advertisement::where('report_whom', $manager_id)->with('invoice')->get();
          foreach($ads as $ad){
            dd($ad);
          }
        }

      }
      $trans = Singx::query();

      $to_date = Carbon::now()->toDateString();
      $from_date = '';

      if($from != '' && $to != ''){
          $from_date = $from = Carbon::parse($from)->toDateString();
          $to_date = $to = Carbon::parse($to)->toDateString();
          $trans->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to);
      }

      $item = $trans->selectRaw('*, count(id) as vendor_qty, sum(transaction_amount) as sum_transaction_amount,
      sum(myma_share) as sum_myma_share, sum(myma_part) as sum_myma_part, sum(singx_part) as sum_singx_part')
      ->orderBy('created_at', 'asc')->first();

      $product_type = getOption('singx_product_type');
      $revenue_model = getOption('singx_revenue_model');
      $merchant_name = 'Singx';

      if($from == ''){
        $from_date = $item->created_at->format('Y-m-d');
      }
      $data = [];
      $data['vendor_code'] = 'Singx';
      $data['vendor_name'] = $merchant_name;
      $data['from_date'] = Carbon::parse($from_date)->format('d/m/Y');
      $data['to_date'] = Carbon::parse($to_date)->format('d/m/Y');
      $data['product_type'] = $product_type;
      $data['vendor_qty'] = $item->vendor_qty;
      $data['vendor_sale'] = number_format($item->sum_transaction_amount,4);//x
      $data['gross'] = number_format($item->sum_myma_share,4);//a
      $data['cost_sharing'] = 0.0000;//b
      $data['txn_fee'] = number_format($item->sum_myma_part,4);//c
      $data['cost_charged'] = number_format($item->sum_singx_part,4);//d
      $data['net'] = $data['gross']+$data['txn_fee']-$data['cost_sharing']-$data['cost_charged'];
      $data['net'] = number_format($data['net'],4);
      $data['payback_vendor'] = $data['vendor_sale']-$data['gross']-$data['txn_fee'];
      $data['payback_vendor'] = number_format($data['payback_vendor'],4);
      $data['revenue_model'] = $revenue_model;

      $item = (object)$data;

      return $item;
    }
}
