<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\FoodMerchant;
use App\Models\Transactions;

use App\Models\Merchant;

use App\Models\Payout;

class CreatePayout extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:payout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create payouts';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
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

                if($merchant->frequency > 0){
                  $payout_date = Carbon::parse($last_payout_date)->addDays($merchant->frequency);
                }else{
                  $payout_date = Carbon::parse($last_payout_date)->addDays(7);
                }
                // elseif($merchant->frequency == '1_month'){
                //   $payout_date = Carbon::parse($last_payout_date)->addMonth();
                // }
                // elseif($merchant->frequency == '2_month'){
                //   $payout_date = Carbon::parse($last_payout_date)->addMonths(2);
                // }
                // elseif($merchant->frequency == '3_month'){
                //   $payout_date = Carbon::parse($last_payout_date)->addMonths(3);
                // }

                $end_date = Carbon::parse($payout_date)->subDay();
                $item = Transactions::where('mid', $merchant->mid)->whereDate('created_at', '=', $last_payout_date)->selectRaw('sum(transaction_amount) as sum_transaction_amount,
                count(*) as quantity, sum(myma_share) as sum_myma_share, sum(myma_part) as sum_myma_part, sum(flexm_part) as sum_flexm_part, sum(other_share) as sum_other_share
                , sum(gst) as sum_gst, type')->groupBy('mid')->first();

                if($item){
                  if($item->type == 'instore'){
                      $net = $item->sum_transaction_amount- $item->sum_myma_part - $item->sum_flexm_part - $item->sum_gst;//($item->sum_myma_share+$item->sum_myma_part-$item->sum_flexm_part);
                      $revenue_deducted = 0;
                  }else{
                      $net = $item->sum_transaction_amount- $item->sum_myma_share - $item->sum_myma_part - $item->sum_gst-$item->sum_flexm_part;//($item->sum_myma_share+$item->sum_myma_part-$item->sum_flexm_part);
                      $revenue_deducted = $item->sum_myma_share;
                  }

                  Payout::create([
                    'start_date'  => $last_payout_date,
                    'payout_date' => $payout_date,
                    'amount'  => $item->sum_transaction_amount,
                    'type'  => $merchant->frequency,
                    'merchant_id' => $merchant_id,
                    'status'  => 'pending',
                    'quantity' => $item->quantity,
                    'wallet_received_amount' => '',
                    'revenue_deducted' => $revenue_deducted,
                    'txn_fee' => $item->sum_myma_part+$item->sum_flexm_part,
                    'cost_charged' => $item->sum_flexm_part,
                    'gst' => $item->sum_gst,
                    'net_payable' => $net,
                    'payout_for'  => 'wlc'
                  ]);
                }
              }else{
                continue;
              }
            }

            foreach($food_merchants as $merchant){
              // if($merchant->user->hasRole('restaurant-owner-catering')){
              //   $merchant_id = '';
              // }else{
              //   $merchant_id = '';
              // }
              $merchant_id = $merchant->id;
              //to create a payout
              $payout = Payout::where('payout_for', 'food')->where('merchant_id', $merchant_id)->whereDate('start_date', '=', $now)->first();

              if(!$payout){
                $last_payout = Payout::where('payout_for', 'food')->where('merchant_id', $merchant_id)->orderBy('id', 'desc')->first();
                $last_payout_date = $now;

                if($merchant->frequency > 0){
                  $payout_date = Carbon::parse($last_payout_date)->addDays($merchant->frequency);
                }else{
                  $payout_date = Carbon::parse($last_payout_date)->addDays(7);
                }
                // elseif($merchant->frequency == '1_month'){
                //   $payout_date = Carbon::parse($last_payout_date)->addMonth();
                // }
                // elseif($merchant->frequency == '2_month'){
                //   $payout_date = Carbon::parse($last_payout_date)->addMonths(2);
                // }
                // elseif($merchant->frequency == '3_month'){
                //   $payout_date = Carbon::parse($last_payout_date)->addMonths(3);
                // }

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

      }catch(Exception $e){
        \Log::debug($e->getMessage());
      }
    }
}
