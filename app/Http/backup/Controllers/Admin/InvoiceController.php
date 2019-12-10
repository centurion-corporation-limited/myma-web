<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Models\Advertisement;
use App\Models\Plans;
use App\Models\Adinvoices;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth, Excel;
use PHPExcel_Worksheet_Drawing;

class InvoiceController extends Controller
{

    public function export(Request $request)
    {
        $auth_user = Auth::user();

        $user_ids = User::whereHas('roles', function($qq){
            $qq->where('slug', ['food-admin']);
        })->pluck('id');

        $items = Adinvoices::whereNotIn('user_id', $user_ids)->orderBy('created_at', 'desc');

        $user_ids = Adinvoices::whereNotIn('user_id', $user_ids)->pluck('user_id');

        $user_id = $request->input('user_id');
        if ($user_id != '0' && $user_id != '') {
            $items->where('user_id', $user_id);
        }

        $type = $request->input('type');
        if ($type != '0' && $type != '') {
            $items->where('type', $type);
        }

        $status = $request->input('status');
        if ($status != '0' && $status != '') {
            $items->where('status', $status);
        }

        $from = $request->input('from');
        $to = $request->input('to');
        if ($from != '' && $to != '') {
            $items->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to);
        }

        $items = $items->get();

        $paymentsArray = [];

        if(count($items)){
            $paymentsArray[] = array('S/No', 'User', 'Ad Type', 'Price', 'Status');
        }

        $price = 0;

        foreach($items as $key => $item){
              $arr = [];
              $arr[] = $key+1;
              $arr[] = @$item->user->name;
              $arr[] = $item->type;
              $arr[] = number_format($item->price,4);
              $arr[] = $item->status;

              $price += $item->price;

            $paymentsArray[] = $arr;
        }

        $arr = [];
        $arr[] = 'Total';
        $arr[] = '';
        $arr[] = '';
        $arr[] = number_format($price,4);
        $arr[] = '';

        $paymentsArray[] = $arr;

        Excel::create('AdInvoice', function($excel) use ($paymentsArray) {
          // Set the spreadsheet title, creator, and description
            $excel->setTitle('Invoice List');
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

      $user_ids = User::whereHas('roles', function($qq){
          $qq->where('slug', ['food-admin']);
      })->pluck('id');

      $items = Adinvoices::whereNotIn('user_id', $user_ids)->orderBy('created_at', 'desc');

      $user_ids = Adinvoices::whereNotIn('user_id', $user_ids)->pluck('user_id');

      $users[''] = 'Please select a user';

      $user_list = User::whereIn('id', $user_ids)->pluck('name', 'id');
      foreach($user_list as $id => $list){
        $users[$id] = $list;
      }

      $user_id = $request->input('user_id');
      if ($user_id != '0' && $user_id != '') {
          $items->where('user_id', $user_id);
      }

      $type = $request->input('type');
      if ($type != '0' && $type != '') {
          $items->where('type', $type);
      }

      $status = $request->input('status');
      if ($status != '0' && $status != '') {
          $items->where('status', $status);
      }

      $from = $request->input('from');
      $to = $request->input('to');
      if ($from != '' && $to != '') {
          $items->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to);
      }

      $limit = 10;
      $items = $items->paginate($limit);
      $paginate_data = $request->except('page');

      return view('admin.advertisement.list_invoices', compact('items', 'auth_user', 'paginate_data', 'users'));
    }


    // public function getAdd()
    // {
    //     $auth_user = \Auth::user();
    //     $vendors = User::whereHas('roles', function($q){
    // 				$q->where('slug', 'admin');
    // 			})->orderBy('id', 'desc')->pluck('name','id');
    //
    //     $impressions = Plans::where('type', 'impression')->get();
    //     $date = Plans::where('type', 'date')->get();
    //     return view('admin.advertisement.add', compact('auth_user', 'vendors', 'impressions', 'date'));
    // }
    //
    // public function postAdd(Request $request)
    // {
    //     /** @var User $item */
    //     $auth_user = \Auth::user();
    //     if($request->input('adv_type') == 1){
    //         $data = $request->only('title', 'type', 'plan_id', 'report_whom', 'description', 'adv_type');
    //         $data['slider_order'] = @$request->input('slider_order');
    //     }else{
    //         $data = $request->only('title', 'type', 'plan_id', 'start', 'end', 'report_whom', 'description', 'adv_type');
    //     }
    //
    //     $module = Advertisement::create($data);
    //
    //     return redirect()->route('admin.advertisement.list')->with([
    //         'flash_level'   => 'success',
    //         'flash_message' => 'Advertisement added successfully.',
    //     ]);
    //
    // }
    public function getEdit($id, Request $request)
    {
        $auth_user = \Auth::user();
        $id = decrypt($id);
        $vendors = User::whereHas('roles', function($q){
        				$q->where('slug', 'admin');
        			})->orderBy('id', 'desc')->pluck('name','id');

    	  $adinvoice = Adinvoices::findOrFail($id);

        $invoice = \App\Classes\Invoice::make()
             ->addItem('Advertisement '.$adinvoice->type. ' Plan', $adinvoice->price, 1, $adinvoice->id)
            ->number($id)
            ->tax(7)
            ->notes('')
            ->status($adinvoice->status)
            ->customer([
            'name' => @$adinvoice->user->name ,
            'id' => $adinvoice->user_id,
            'phone' => '+34 123 456 789',
            'location' => 'C / Unknown Street 1st',
            'zip' => '08241',
            'city' => 'Manresa',
            'country' => 'Singapore',
            ]);


        // $impressions = Plans::where('type', 'impression')->get();
        // $date = Plans::where('type', 'date')->get();

        return view('admin.advertisement.view_invoice', compact('invoice', 'vendors'));
    }

    public function postEdit($id, Request $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $id = decrypt($id);
        $module = Adinvoices::findOrFail($id);
        $data = $request->only('status');

        $module->update($data);

        return redirect()->route('admin.invoice.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Status updated successfully.',
        ]);

    }

    public function statusPaid($id)
    {
        $invoice = Adinvoices::findOrFail($id);
        $invoice->update(['status' => 'paid']);

        return redirect()->route('admin.invoice.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Invoice status updated',
        ]);
    }

    public function getDelete($id)
    {
        Adinvoices::destroy($id);

        return redirect()->route('admin.invoice.list')->with([
            'flash_level'   => 'danger',
            'flash_message' => 'Invoice Deleted',
        ]);
    }

    public function postDelete($id)
    {
        Adinvoices::delete($id);
        return redirect()->route('admin.invoice.list')->with([
          'flash_level'   => 'danger',
          'flash_message' => 'Invoice Deleted',
        ]);

    }
}
