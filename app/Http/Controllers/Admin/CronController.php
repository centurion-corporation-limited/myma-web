<?php

namespace App\Http\Controllers\Admin;

use App\User, JWTAuth;
use App\Helper\RandomStringGenerator;
use App\Models\Advertisement;
use App\Models\Remittance;
use App\Models\RemittanceReport;
use App\Models\Wallet;
use App\Models\Adinvoices;
use App\Models\Sponsor;
use App\Models\FoodMenu;
use App\Models\FoodMerchant;
use Illuminate\Http\Request;
use App\Events\NotifyAdmin;

use App\Models\Merchant;
use App\Models\Terminal;
use App\Models\MerchantCode;
use App\Models\Payout;
use App\Models\Transactions;
//food
use App\Models\SpuulSubscription;
use App\Models\SpuulPlan;
use App\Models\Subscription;
use App\Models\Trip;
use App\Models\TripPickup;
use App\Models\TripOrders;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth, Activity;
use Carbon\Carbon;

class CronController extends Controller
{
    public function updateAdStatus(Request $request)
    {

      $items = Advertisement::orderBy('created_at', 'desc');
      $items = $items->get();
      $now = Carbon::now();

      foreach($items as $item){
          if($item->adv_type == 1){//impression
              if($item->impress){
                  if($item->plan->impressions >= $item->impress->impressions){
                      $item->status = 'running';
                  }else{
                      $item->status = 'completed';
                  }
              }
              else{
                  $item->status = 'running';
              }
          }else{
              $start = Carbon::parse($item->start);
              $end = Carbon::parse($item->end);
              if($start->lte($now) && $end->gte($now)){
                  $item->status = 'running';
              }elseif($end->lt($now)){
                  $item->status = 'completed';
              }else{
                  $item->status = 'upcoming';
              }
          }
          $item->save();
      }
    }

    public function addFlexmMerchant(Request $request)
    {
        $url = config('app.url').'api/v1/flexm/';
        $auth_user = User::whereHas('roles', function($q){
          $q->where('slug', 'admin');
        })->first();

        $user_ids = Merchant::whereNotNull('user_id')->pluck('user_id');

        $users = User::whereHas('roles', function($q){
          $q->where('slug', 'training');
        })->whereNotIn('id', $user_ids)->get();

        \Log::debug("start merchant");
        try{
          $client = new Client();
          foreach($users as $user){
              $data['merchant_name'] = $user->name;
              $data['merchant_category_code'] = '7399';
              $data['token'] = JWTAuth::fromUser($user);
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

                  $data['merchant_code'] = $cont->merchant_code;
                  $data['mid'] = $cont->mid;
                  $data['wallet_type_indicator'] = 'centurion';
                  $data['status'] = $cont->status;
                  $data['created_by'] = $auth_user->id;
                  $data['user_id'] = $user->id;
                  $data['type'] = 'inapp';
                  $exist = Merchant::create($data);

                  // $exist = Merchant::where('merchant_code', $data['merchant_code'])->first();
                  // if($exist){
                  //   $exist->update($data);
                  // }else{
                  // }
                  $dat['token'] = JWTAuth::fromUser($user);
                  $dat['merchant_code'] = $data['merchant_code'];
                  $dat['payment_mode'] = 1;
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
                      $terone['payment_mode'] = 1;
                      $terone['tid'] = $cont->tid;
                      $terone['status'] = $cont->status;
                      addActivity("Cron added a new flexm merchant - ".$user->name, $auth_user->id, $dat, $content);
                      Terminal::create($terone);
                      // $tero = Terminal::where($terone)->first();
                      // if($tero){
                      //   $tero->update($terone);
                      // }else{
                      // }
                    }
                    else{
                      addActivity("Cron error while adding a new flexm merchant terminal", $auth_user->id, $dat, $content);
                    }
                  }
                  else{
                    addActivity("Cron error while adding a new flexm merchant terminal reason code", $auth_user->id, $dat, ['code' => $code, 'reason' => $reason]);
                    //dd('Could not create terminal. Try again later');
                  }
                }else{
                  addActivity("Cron error while adding a new flexm merchant ", $auth_user->id, $data, $content);
                  //dd(@$content->message);
                }
              }else{
                addActivity("Cron error while adding a new flexm merchant reason code", $auth_user->id, $data, ['code'=> $code, 'reason' => $reason]);
                // dd('Could not create merchant. Try again later');
              }
          }
        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            addActivity("Cron error while adding a new flexm merchant ", @$auth_user->id, @$dat, @$jsonBody);
            //dd(@$jsonBody->errors);
        }catch(GuzzleException $e){
            addActivity("Cron error while adding a new flexm merchant ", @$auth_user->id, @$dat, @$e->getMessage());
            //dd($e->getMessage());
        }catch(Exception $e){
            addActivity("Cron error while adding a new flexm merchant ", @$auth_user->id, @$dat, @$e->getMessage());
            //dd($e->getMessage());
        }
    }

    //no need of this function
    public function addFoodMerchant(Request $request)
    {
        $url = config('app.url').'api/v1/flexm/';
        $auth_user = \Auth::user();

        $user_ids = Merchant::whereNotNull('user_id')->pluck('user_id');

        $users = User::whereHas('roles', function($q){
          $q->whereIn('slug', ['restaurant-owner-single', 'restaurant-owner-catering']);
        })->whereNotIn('id', $user_ids)->get();

        \Log::debug("start merchant");
        try{
          $client = new Client();
          foreach($users as $user){
              $data['merchant_name'] = $user->name;
              $data['merchant_category_code'] = '7399';

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

                  $data['merchant_code'] = $cont->merchant_code;
                  $data['mid'] = $cont->mid;
                  $data['wallet_type_indicator'] = 'centurion';
                  $data['status'] = $cont->status;
                  $data['created_by'] = $auth_user->id;
                  $data['user_id'] = $user->id;
                  $data['type'] = 'inapp';
                  $exist = Merchant::create($data);

                  // $exist = Merchant::where('merchant_code', $data['merchant_code'])->first();
                  // if($exist){
                  //   $exist->update($data);
                  // }else{
                  // }

                  $dat['merchant_code'] = $data['merchant_code'];
                  $dat['payment_mode'] = 1;
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
                      $terone['payment_mode'] = 1;
                      $terone['tid'] = $cont->tid;
                      $terone['status'] = $cont->status;

                      Terminal::create($terone);
                      // $tero = Terminal::where($terone)->first();
                      // if($tero){
                      //   $tero->update($terone);
                      // }else{
                      // }
                    }
                    else{
                      dd(@$content->message);
                    }
                  }
                  else{
                    dd('Could not create terminal. Try again later');
                  }
                }else{
                  dd(@$content->message);
                }
              }else{
                dd('Could not create merchant. Try again later');
              }
          }
          \Log::debug("stop merchant");
          dd('Merchant created');

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            dd(@$jsonBody->errors);
        }catch(GuzzleException $e){
            dd($e->getMessage());
        }catch(Exception $e){
            dd($e->getMessage());
        }
    }


    public function updateRemittanceDoc(Request $request)
    {
      try{
        $file = public_path('files/ftp/remittance_report.csv');
        $type = 'remittance';
        $header = [
            'hash_id', 'provider', 'delivery_method', 'receive_country', 'status', 'payout_agent', 'customer_fx', 'send_currency', 'send_amount', 'customer_fixed_fee',
            'total_transaction_amount', 'receive_currency', 'receive_amount', 'crossrate', 'provider_amount_fee_currency', 'provider_amount_fee','provider_exchange_rate',
            'send_amount_rails_currency', 'send_amount_rails', 'send_amount_before_fx', 'send_amount_after_fx', 'routing_params', 'ref_id', 'transaction_code',
            'sender_first_name','sender_middle_name', 'sender_last_name', 'sender_mobile_number', 'ben_first_name','ben_middle_name','ben_last_name',
            'date_added','date_expiry','status_last_updated'
        ];
        $customerArr = csvToArray($file, ',', $type, $header);
        for ($i = 0; $i < count($customerArr); $i ++)
        {
          RemittanceReport::create($customerArr[$i]);
        }
        dd("Successfully imported");
      }catch(Exception $e){
        dd($e->getMessage());
      }
    }

    public function updateWalletDoc(Request $request)
    {
      try{
        $file = public_path('files/ftp/wallet_report.csv');
        $type = 'wallet';
        $header = [
            'mobile', 'wallet_user_name', 'transaction_date', 'transaction_amount', 'transaction_currency',
            'transaction_ref_no', 'transaction_status', 'transaction_code', 'mid', 'tid', 'merchant_name', 'payment_mode',
        ];
        $customerArr = csvToArray($file, ',', $type, $header);
        for ($i = 0; $i < count($customerArr); $i ++)
        {
          Wallet::create($customerArr[$i]);
        }
        dd("Successfully imported");
      }catch(Exception $e){
        dd($e->getMessage());
      }
    }

    public function updateRemittanceVerify(Request $request)
    {
      try{
        $path = public_path('files/ftp/Remittance_Settlement_Report.xlsx');
        $type = 'wallet';
        // $path = $request->file('import_file')->getRealPath();
        $data = \Excel::load($path, function($reader) {})->get();

        if(!empty($data) && $data->count()){
            foreach ($data->toArray() as $key => $value) {
              $period = $value['transaction_paid_month'];
              $no_txns = $value['number_of_transactions'];
              $provider = strtolower($value['provider']);
              $amount = $value['send_amount_sgd'];
              $send_currency = $value['send_currency'];
              if($provider){
                $remit = Remittance::where('provider', $provider)->whereMonth('created_at', $period->month)
                ->where('send_currency', $send_currency)->get();
                $remit_txn = $remit->sum('total_transaction_amount');
                $remit_count = $remit->count();

                if($remit_txn != $amount || $remit_count != $no_txns){
                    // $datamessage = 'Remittance data </br> Count - '.$no_txns. '</br>Amount '.$amount;
                    // $message .='WLC data </br> Count - '.$remit_count.' Amount - '.$remit_txn;

                    //event(new NotifyAdmin($amount, $no_txns, $remit_txn, $remit_count));
                }else{

                }
              }
            }
        }
        $old_path = public_path('files/ftp/Remittance_Settlement_Report.xlsx');
        $new_path = public_path('files/uploaded/Remittance_Settlement_Report_'.mt_rand().time().'.xlsx');
        \File::move($old_path, $new_path);
        dd("Successfully imported");
      }catch(Exception $e){
        dd($e->getMessage());
      }
    }

    public function createPayoutOld(Request $request)
    {
      try{
        $merchants = Merchant::all();
        $now = Carbon::now();
        foreach($merchants as $merchant){
          $merchant_id = $merchant->id;

          //to update amount
          $payouts = Payout::where('merchant_id', $merchant_id)->where('status', 'pending')->orderBy('id', 'desc')->where('payout_date', '<=', $now->toDateString())->get();
          foreach($payouts as $pp){
              $start_date = Carbon::parse($pp->start_date);
              $end_date = Carbon::parse($pp->payout_date);
              $sum = Transactions::where('mid', $merchant->mid)->whereBetween('created_at', [$start_date, $end_date])->sum('other_share');

              $pp->update(['amount' => $sum]);
          }


          //to create a payout
          $payout = Payout::where('merchant_id', $merchant_id)->orderBy('id', 'desc')->where('payout_date', '>', $now->toDateString())->first();
          if(!$payout){
            $last_payout = Payout::where('merchant_id', $merchant_id)->orderBy('id', 'desc')->first();
            if($last_payout){
              $last_payout_date = Carbon::parse($last_payout->payout_date);
            }else{
              $last_payout_date = $merchant->start_date != ''?$merchant->start_date:$merchant->created_at;
            }

            if($merchant->frequency == 'weekly'){
              $payout_date = Carbon::parse($last_payout_date)->addDays(7);
            }
            elseif($merchant->frequency == '1_month'){
              $payout_date = Carbon::parse($last_payout_date)->addMonth();
            }
            elseif($merchant->frequency == '2_month'){
              $payout_date = Carbon::parse($last_payout_date)->addMonths(2);
            }
            elseif($merchant->frequency == '3_month'){
              $payout_date = Carbon::parse($last_payout_date)->addMonths(3);
            }

            $end_date = Carbon::parse($payout_date)->subDay();
            $sum = Transactions::where('mid', $merchant->mid)->whereBetween('created_at', [$last_payout_date, $end_date])->sum('other_share');

            Payout::create([
              'start_date'  => $last_payout_date,
              'payout_date' => $payout_date,
              'amount'  => $sum,
              'type'  => $merchant->frequency,
              'merchant_id' => $merchant_id,
              'status'  => 'pending'
            ]);

          }else{
            continue;
          }
        }

        dd('done');
      }catch(Exception $e){
        dd($e->getMessage());
      }

    }

    public function createPayout(Request $request)
    {
      try{
        $merchants = Merchant::all();
        $food_merchants = FoodMerchant::all();
        $now = "2018-10-05";
        $now = Carbon::parse($now);
        $end = Carbon::now();
        // $now = Carbon::now();

        while($now->diffInDays($end)){
            foreach($merchants as $merchant){
              $merchant_id = $merchant->id;

              //to create a payout
              $payout = Payout::where('payout_for', 'wlc')->where('merchant_id', $merchant_id)->whereDate('start_date', '=', $now)->first();

              if(!$payout){
                $last_payout = Payout::where('payout_for', 'wlc')->where('merchant_id', $merchant_id)->orderBy('id', 'desc')->first();
                $last_payout_date = $now;

                if($merchant->frequency == 'weekly'){
                  $payout_date = Carbon::parse($last_payout_date)->addDays(7);
                }
                elseif($merchant->frequency == '1_month'){
                  $payout_date = Carbon::parse($last_payout_date)->addMonth();
                }
                elseif($merchant->frequency == '2_month'){
                  $payout_date = Carbon::parse($last_payout_date)->addMonths(2);
                }
                elseif($merchant->frequency == '3_month'){
                  $payout_date = Carbon::parse($last_payout_date)->addMonths(3);
                }

                $end_date = Carbon::parse($payout_date)->subDay();
                $item = Transactions::where('mid', $merchant->mid)->whereDate('created_at', '=', $last_payout_date)->selectRaw('sum(transaction_amount) as sum_transaction_amount,
                count(*) as quantity, sum(myma_share) as sum_myma_share, sum(myma_part) as sum_myma_part, sum(flexm_part) as sum_flexm_part, sum(other_share) as sum_other_share')->groupBy('mid')->first();

                if($item){
                  $net = $item->sum_transaction_amount- $item->sum_myma_share - $item->sum_myma_part;//($item->sum_myma_share+$item->sum_myma_part-$item->sum_flexm_part);
                  Payout::create([
                    'start_date'  => $last_payout_date,
                    'payout_date' => $payout_date,
                    'amount'  => $item->sum_transaction_amount,
                    'type'  => $merchant->frequency,
                    'merchant_id' => $merchant_id,
                    'status'  => 'pending',
                    'quantity' => $item->quantity,
                    'wallet_received_amount' => '',
                    'revenue_deducted' => $item->sum_myma_share,
                    'txn_fee' => $item->sum_myma_part,
                    'cost_charged' => $item->sum_flexm_part,
                    'net_payable' => $net,
                    'payout_for'  => 'wlc'
                  ]);
                }
              }else{
                continue;
              }
            }

            foreach($food_merchants as $merchant){
              $merchant_id = $merchant->id;
              //to create a payout
              $payout = Payout::where('payout_for', 'food')->where('merchant_id', $merchant_id)->whereDate('start_date', '=', $now)->first();

              if(!$payout){
                $last_payout = Payout::where('payout_for', 'food')->where('merchant_id', $merchant_id)->orderBy('id', 'desc')->first();
                $last_payout_date = $now;

                if($merchant->frequency == 'weekly'){
                  $payout_date = Carbon::parse($last_payout_date)->addDays(7);
                }
                elseif($merchant->frequency == '1_month'){
                  $payout_date = Carbon::parse($last_payout_date)->addMonth();
                }
                elseif($merchant->frequency == '2_month'){
                  $payout_date = Carbon::parse($last_payout_date)->addMonths(2);
                }
                elseif($merchant->frequency == '3_month'){
                  $payout_date = Carbon::parse($last_payout_date)->addMonths(3);
                }

                $end_date = Carbon::parse($payout_date)->subDay();
                $item = Transactions::where('food_merchant_id', $merchant->id)->whereDate('created_at', '=', $last_payout_date)->selectRaw('sum(other_share) as sum_transaction_amount,
                count(*) as quantity, sum(myma_share) as sum_myma_share, sum(myma_part) as sum_myma_part, sum(flexm_part) as sum_flexm_part, sum(food_share) as sum_other_share')->groupBy('food_merchant_id')->first();

                if($item){
                  $net = $item->sum_transaction_amount- $item->sum_myma_share - $item->sum_myma_part;//($item->sum_myma_share+$item->sum_myma_part-$item->sum_flexm_part);
                  Payout::create([
                    'start_date'  => $last_payout_date,
                    'payout_date' => $payout_date,
                    'amount'  => $item->sum_transaction_amount,
                    'type'  => $merchant->frequency,
                    'merchant_id' => $merchant_id,
                    'status'  => 'pending',
                    'quantity' => $item->quantity,
                    'wallet_received_amount' => '',
                    'revenue_deducted' => $item->sum_myma_share,
                    'txn_fee' => $item->sum_myma_part,
                    'cost_charged' => $item->sum_flexm_part,
                    'net_payable' => $net,
                    'payout_for'  => 'food'
                  ]);
                }
              }else{
                continue;
              }
            }
            $now->addDay();
        }


        dd('done');
      }catch(Exception $e){
        dd($e->getMessage());
      }

    }

    public function createTrip(Request $request)
    {
      // echo md5('11110000000286'.'9200299');
      try{
        $now = Carbon::now();//->addDay();

        $time = $now->toTimeString();
        if($time > "19:00:00"){
          $now->addDay();
        }
        $subs = Subscription::where('delivery_date', $now->toDateString())->get();

        foreach($subs as $sub){
          $user_id = $sub->order->driver_id;
          if($user_id == ''){
            $user_id =49;
            //continue;
          }
          $start_date = Carbon::parse($sub->order->delivery_date);
          $diff = $start_date->diffInDays($now)+1;
          \Log::debug($sub->order_id.' - '.$diff);
          $food_item = FoodMenu::find($sub->item_id);

          if($food_item->breakfast && $food_item->breakfast > 1 && $food_item->breakfast >= $diff){

            $time = "07:00:00";
            $restaurant_id = $food_item->restaurant_id;

            $trip = Trip::where('trip_date', $now->toDateString())->where('trip_time', $time)->where('assigned_to', $user_id)->first();
            if(!$trip){
              $trip = Trip::create([
                'created_by' => $user_id,
                'status'  => 0,
                'price' => 0,
                'assigned_to' => $user_id,
                'trip_date' => $now->toDateString(),
                'trip_time' => $time
              ]);
            }

            $trip_pick = TripPickup::where([
                'trip_id'     => $trip->id,
                'pickup_id' => $restaurant_id,
            ])->first();

            if(!$trip_pick){
                $trip_pick = TripPickup::create([
                    'trip_id' => $trip->id,
                    'pickup_id' => $restaurant_id,
                ]);
            }

            $trip_order = TripOrders::where([
                'order_id' => $sub->order_id,
                'trip_pick_id' => $trip_pick->id,
            ])->first();

            if(!$trip_order){
                TripOrders::create([
                    'order_id' => $sub->order_id,
                    'trip_pick_id' => $trip_pick->id,
                ]);
            }
          }

          if($food_item->lunch && $food_item->lunch > 1 && $food_item->lunch >= $diff ){
            $time = "12:00:00";
            $restaurant_id = $food_item->restaurant_id;

            $trip = Trip::where('trip_date', $now->toDateString())->where('trip_time', $time)->where('assigned_to', $user_id)->first();
            if(!$trip){
              $trip = Trip::create([
                'created_by' => $user_id,
                'status'  => 0,
                'price' => 0,
                'assigned_to' => $user_id,
                'trip_date' => $now->toDateString(),
                'trip_time' => $time
              ]);
            }

            $trip_pick = TripPickup::where([
                'trip_id'     => $trip->id,
                'pickup_id' => $restaurant_id,
            ])->first();

            if(!$trip_pick){
                $trip_pick = TripPickup::create([
                    'trip_id' => $trip->id,
                    'pickup_id' => $restaurant_id,
                ]);
            }

            $trip_order = TripOrders::where([
                'order_id' => $sub->order_id,
                'trip_pick_id' => $trip_pick->id,
            ])->first();

            if(!$trip_order){
                TripOrders::create([
                    'order_id' => $sub->order_id,
                    'trip_pick_id' => $trip_pick->id,
                ]);
            }
          }

          if($food_item->dinner && $food_item->dinner > 1 && $food_item->dinner >= $diff){
            $time = "19:00:00";
            $restaurant_id = $food_item->restaurant_id;

            $trip = Trip::where('trip_date', $now->toDateString())->where('trip_time', $time)->where('assigned_to', $user_id)->first();
            if(!$trip){
              $trip = Trip::create([
                'created_by' => $user_id,
                'status'  => 0,
                'price' => 0,
                'assigned_to' => $user_id,
                'trip_date' => $now->toDateString(),
                'trip_time' => $time
              ]);
            }

            $trip_pick = TripPickup::where([
                'trip_id'     => $trip->id,
                'pickup_id' => $restaurant_id,
            ])->first();

            if(!$trip_pick){
                $trip_pick = TripPickup::create([
                    'trip_id' => $trip->id,
                    'pickup_id' => $restaurant_id,
                ]);
            }

            $trip_order = TripOrders::where([
                'order_id' => $sub->order_id,
                'trip_pick_id' => $trip_pick->id,
            ])->first();

            if(!$trip_order){
                TripOrders::create([
                    'order_id' => $sub->order_id,
                    'trip_pick_id' => $trip_pick->id,
                ]);
            }
          }

        }

      }catch(Exception $e){
        dd($e->getMessage());
      }

    }

    public function createToken(Request $request)
    {
      try{
        $users = User::whereHas('roles', function($q){
          $q->where('slug', 'app-user');
        })->whereNull('uid')->get();

        $generator = new RandomStringGenerator;
        $tokenLength = 32;


        foreach($users as $user){
          $token = $generator->generate($tokenLength);
          $flag = true;
          while($flag){
            $exist = User::where('uid', $token)->first();
            if($exist){
              $token = $generator->generate($tokenLength);
            }else{
              $flag = false;
            }
          }
          $user->uid = $token;
          $user->save();
        }


      }catch(Exception $e){
        dd($e->getMessage());
      }

    }


    public function checkSubscription(Request $request)
    {
      try{
        $start_date = Carbon::now();
        $end_date = Carbon::now()->addDays(3);

        $subs = SpuulSubscription::whereBetween('end_date', [$start_date, $end_date])->where('payment_done', '0')->get();
        foreach($subs as $sub){
            if($sub->user){
              $user_id = $sub->user_id;
              if($sub->plan_type == 'monthly'){
                  $plan = SpuulPlan::where('status', '1')->where('type', '1')->first();
              }else{
                  $plan = SpuulPlan::where('status', '1')->where('type', '2')->first();
              }

              if($plan){
                  $plan_id = $plan->id;
              }else{
                continue;
              }
              $text = $user_id.'_'.$plan_id.'_'.$sub->id;
              $text = encrypt($text);

              $link = route('frontend.spuul.payment', ['id' => $text]);
              sendSingle($sub->user, 'Your spuul subscription is expiring.Please make the payment or your subscription will be stopped', 'link', $link);
            }
        }

      }catch(Exception $e){
        dd($e->getMessage());
      }
    }

    public function subscribeToSpuul(Request $request)
    {
      try{
        $start_date = Carbon::now()->toDateString();

        $subs = SpuulSubscription::whereDate('start_date', $start_date)->where('status', 'paid')->get();

        $client = new Client();
        $result = $client->post('https://api.spuul.com/oauth/token',[
            'form_params' => [
                'grant_type' => 'client_credentials',
                'client_id' => '1e9240872cfda7989634346aa89cd3bf05d6a0462756f58d79e445788cca8d66',
                'client_secret' => '6bdfaae72a9a41b551958c3ff0e1057a9ef25632f2f562049cba8b384dce41e3'
            ]
        ]);
        $code = $result->getStatusCode(); // 200
        $reason = $result->getReasonPhrase(); // OK
        if($code == "200" && $reason == "OK"){
            $body = $result->getBody();
            $content = json_decode($body->getContents());
            if(@$content->access_token){
              $spuul_token = $content->access_token;
            }else{
              Activity::log('Cron Spuul subscription error while getting token - '.json_encode($content), @$user->id);
              dd($reason);
            }
        }else{
            Activity::log('Cron Spuul subscription error while getting token - '.json_encode($reason), @$user->id);
            dd($reason);
        }
        foreach($subs as $sub){

          try{

            $d['email'] = $sub->email;
            $d['account_number'] = $sub->account_number;
            if($sub->type == 'monthly')
              $d['sku_code'] = 'interactive_sg_monthly_sgd';
            else
              $d['sku_code'] = 'interactive_sg_yearly_sgd';

            $client = new Client(['headers' => ['Accept' => 'application/vnd.spuul.v3', 'Authorization' => 'Bearer '.$spuul_token,
            'Cache-Control' => 'no-cache', 'content-type' => 'multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW']]);

            $result = $client->post('https://callbacks.spuul.com/sg_interactive/subscribe', [
              'form_params' => $d
            ]);

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
              $body = $result->getBody();
              $content = json_decode($body->getContents());
              $status = 'success';

              Activity::log('Spuul subscription request is successfull #'.$sub->id, @$user->id);
            }else{
              $content = $reason;
              $status = 'error';
            }

          }catch (BadResponseException $ex) {
              $response = $ex->getResponse();
              $jsonBody = json_decode((string) $response->getBody());
              \Log::debug(json_encode($jsonBody));
              $msg = '';
              if(@$jsonBody->message){
                $msg = @$jsonBody->message;
              }
              if(@$jsonBody->error_description){
                $msg = @$jsonBody->error_description;
              }
              if(@$jsonBody->subscription){
                foreach($jsonBody->subscription as $err){
                    $msg = @$jsonBody->subscription[0];
                    if($msg != ''){
                      break;
                    }
                }
              }
              $content = $msg;
              $status = 'error';
              Activity::log('Cron error while making spuul subscription request - '.$content.' Email - '.@$d['email']);
          }catch(GuzzleException $e){
              $content = $e->getMessage();
              Activity::log('Cron error while making spuul subscription request - '.$content.' Email - '.@$d['email']);
              $status = 'error';
          }catch(Exception $e){
              $content = $e->getMessage();
              $status = 'error';
              Activity::log('Cron error while making spuul subscription request - '.$content.' Email - '.@$d['email']);
          }

          $tt = Transactions::find($sub->transaction_id);
          $tt->update([
            'spuul_status' => $status,
            'spuul_request' => json_encode($d),
            'spuul_response' => json_encode($content)
          ]);
        }

      }catch(Exception $e){
        dd($e->getMessage());
      }
    }
}
