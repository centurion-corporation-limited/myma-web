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
use App\Models\RedeemUser;
use App\Models\Dormitory;

use Illuminate\Http\Request;
use App\Models\Option;

use App\Http\Requests;
use App\Http\Requests\EditShareRequest;
use App\Http\Controllers\Controller;
use Carbon\Carbon, Excel;
use PHPExcel_Worksheet_Drawing;


//use App\User;

class RedeemController extends Controller
{
  public function getRedeemReport(Request $request)
    {
        $auth_user = \Auth::user();
        $items = [];
        $items = $this->getReportItems($request,true);
        $paginate_data = $request->except('page');

        $dorm = Dormitory::pluck('name' ,'id')->toArray();
        $dorm[0] = 'Select Dormitory';
        ksort($dorm);
        array_push($dorm, 'Others Address');

        return view('admin.redeem.report', compact('items','paginate_data',  'dorm'));

    }

    public function getReportItems(Request $request, $is_paginate){
      $type = $request->input('type');
      $status = $request->input('status');
      $from = $request->input('start');
      $to = $request->input('end');
      $sort = $request->input('sort');
      $order = $request->input('order');
      $phone = $request->input('phone');
      $fin_no = $request->input('fin_no');
      $credited_start = $request->input('credited_start');
      $credited_end = $request->input('credited_end');

      $items= [];

      $redeemUserQuery = RedeemUser::query();
      if($type != ''){
        $redeemUserQuery->where('type', $type);
      }

      if ($status  != '') {
        $redeemUserQuery->where('status', $status);
      }

      if ($phone  != '') {
        $redeemUserQuery->where('mobile', 'like', "%{$phone}%");
      }

      if ($fin_no  != '') {
        $redeemUserQuery->where('fin_no', 'like', "%{$fin_no}%");
      }


      $to_date = Carbon::now()->toDateString();
      $from_date = '';

      if($from != '' && $to != ''){
          $from_date = $from = Carbon::parse($from)->toDateString();
          $to_date = $to = Carbon::parse($to)->toDateString();
          $redeemUserQuery->whereDate('click_date', '>=', $from)->whereDate('click_date', '<=', $to);
      }

      $credited_to_date = Carbon::now()->toDateString();
      $credited_from_date = '';

      if($credited_start != '' && $credited_end != ''){
          $credited_from_date = $credited_start = Carbon::parse($credited_start)->toDateString();
          $credited_to_date = $credited_end = Carbon::parse($credited_end)->toDateString();
          $redeemUserQuery->whereDate('wallet_credited_at', '>=', $credited_start)->whereDate('wallet_credited_at', '<=', $credited_end);
      }

      $dorm = $request->input('dormitory_id');
      if ($dorm != '' && $dorm != 0 && $dorm != 6 ) {
          $redeemUserQuery->whereHas('profile', function ($q) use ($dorm) {
              $q->where('dormitory_id', $dorm);
          });
      }else if($dorm == 6){
        $redeemUserQuery->whereHas('profile', function ($q) use ($dorm) {
            $q->where('dormitory_id', 0)->orWhereNull('dormitory_id');
        });

      }

      if ($sort) {
        $sort_by = $sort;
      }else{
        $sort_by = 'click_date';
      }

      if ($order) {
        $order_by = $order;
      }else{
        $order_by = 'DESC';
      }
      //echo $sort_by ."----".$order_by;exit;
      
      $redeemUsers = $redeemUserQuery->select('id','name','mobile','user_id','type', 'fin_no', 
                                              'transaction_id' , 'click_redeem' , 'click_date' , 'wallet_credited_at' , 'credit_amount' , 
                                              'status','created_at','updated_at')->orderBy($sort_by, $order_by)->get();
      
      if($is_paginate){ 
        $limit = 10;
        $redeemUsers = $redeemUserQuery->paginate($limit);
      }
      return $redeemUsers;
    }

    public function exportRedeemReport(Request $request)
    { 
      $touch_recordsArray = [];
      $touch_records = $this->getReportItems($request,false);

      if(count($touch_records)){
        $paymentsArray[] = array('ID', 'User ID', 'Name', 'Mobile', 'TOUCH-CoH', 'FIN/ID no#', 'Transaction Reference ID' , 'Click Redeem ?' , 
                                  'Redeem Clicked Date' , 'eWallet Credited $10 Date/Time' , 'Credited Amount' ,'Transaction Status' ); //, 'Updated At'
      }

      foreach($touch_records as $touch_record){
        $arr = [];
        $arr[] = $touch_record->id;
        $arr[] = $touch_record->user_id;
        $arr[] = $touch_record->name;
        $arr[] = $touch_record->mobile;
        $arr[] = $touch_record->type;
        $arr[] = $touch_record->fin_no;
        $arr[] = $touch_record->transaction_id;
        $arr[] = $touch_record->click_redeem;
        $arr[] = $touch_record->click_date;
        $arr[] = $touch_record->wallet_credited_at ? date('d-m-Y H:i:s', strtotime($touch_record->wallet_credited_at)): '';
        $arr[] = $touch_record->credit_amount;
        $arr[] = ($touch_record->status =='redeem_successful') ? 'Redeem Successful' :'Credit Successful';
        $paymentsArray[] = $arr;
      }

      $arr = [];

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

      Excel::create('MyMA $10 Redeem & Credited List', function($excel) use ($paymentsArray) {
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

}
