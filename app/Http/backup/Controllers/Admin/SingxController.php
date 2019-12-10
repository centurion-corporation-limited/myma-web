<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Models\Singx;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth, Excel;
use PHPExcel_Worksheet_Drawing;
use Carbon\Carbon;
class SingxController extends Controller
{

  public function exportExcel(Request $request)
  {
      $auth_user = \Auth::user();

        $items = Singx::orderBy('created_at', 'desc');

        if($id = $request->input('transaction_id')){
            $items->where('hash_id', 'like', "%{$id}%");
        }

        if($auth_user->hasRole('spuul')){
          $items->where('type', 'spuul');
        }

      $from = $request->input('start');
      $to = $request->input('end');

      if($from != '' && $to != ''){
          $from = Carbon::parse($from);
          $to = Carbon::parse($to);
          $items->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to);
      }

      $paymentsArray = [];
      $items = $items->get();

      if(count($items)){
          $paymentsArray[] = array('User', 'Txn id', 'Txn Amount', 'Txn Date', 'Singx Fee', 'Myma Part', 'Singx Part');
      }

      $myma_part = $singx_part = $singx_fee = $txn_amt = 0;

      foreach($items as $item){

          $arr = [];

            $arr[] = @$item->user->name;
            $arr[] = $item->transactionId;
            $arr[] = number_format($item->transaction_amount,4);
            $arr[] = $item->created_at;
            $arr[] = number_format($item->singx_fee,4);
            $arr[] = number_format($item->myma_part,4);
            $arr[] = number_format($item->flexm_part,4);

            $txn_amt += $item->transaction_amount;
            $myma_part += $item->myma_part;
            $singx_part += $item->flexm_part;
            $singx_fee += $item->singx_fee;

          $paymentsArray[] = $arr;
      }

      $arr = [];

        $arr[] = 'Total';
        $arr[] = '';
        $arr[] = number_format($txn_amt,4);
        $arr[] = '';
        $arr[] = number_format($singx_fee,4);
        $arr[] = number_format($myma_part,4);
        $arr[] = number_format($singx_part,4);

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

    public function getList(Request $request)
    {
      $auth_user = Auth::user();

      $items = User::where('singx_account','1');

      // if ($id = $request->input('id')) {
      //     $items->where('id', $id);
      // }

      $limit = 10;
      $items = $items->paginate($limit);
      $paginate_data = $request->except('page');

      return view('admin.singx.list', compact('items', 'auth_user', 'paginate_data'));
    }

    public function getRemittanceView($id, Request $request)
    {
    	  $auth_user = \Auth::user();
        $id = decrypt($id);
        $item = Singx::findOrFail($id);
        $wallet = '';
        return view('admin.singx.view', compact('item', 'wallet'));
    }

    public function getRemittance(Request $request)
    {
        $items = Singx::query();
        $items1 = Singx::query();
        // $items2 = Remittance::where('status', 'require');

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

        $data['total'] = $items1->sum('transaction_amount');
        $data['total_success'] = $items1->count();
        $data['total_not_confirmed'] = 0;//$items2->count();

        $limit = 10;
        $items = $items->sortable()->paginate($limit);
        $paginate_data = $request->except('page');
        $user = \Auth::user();
        $type = 'remit';

        return view('admin.singx.remittance', compact('items', 'user', 'paginate_data', 'type', 'data'));

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

}
