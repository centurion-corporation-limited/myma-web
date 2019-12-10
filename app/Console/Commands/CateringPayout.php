<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\FoodMerchant;
use App\Models\Transactions;

use App\Models\Merchant;
use App\Models\Order;

use App\Models\PayoutPackage;
use App\Models\Payout;

class CateringPayout extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'catering:payout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create catering payouts';

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

        $merchant = Merchant::find(13);
        $now = Carbon::now();
        $end = Carbon::now();
        $start_of_month = Carbon::now()->startOfMonth();
        $end_of_month = $end->endOfMonth();

        $merchant_id = $merchant->id;

        //to create a payout
        $payout = Payout::where('payout_for', 'wlc')->where('merchant_id', $merchant_id)->whereDate('start_date', '=', $start_of_month)->first();
        $last_payout_date = $now->startOfMonth()->subDay();

        $orders = Order::where('type', 'package')->where('created_at', '>=', $start_of_month)->where('created_at', '<=', $end_of_month)->get();
        $package_1 = 0;
        $package_2 = 0;
        $package_3 = 0;
        $package_4 = 0;
        $package = 0;
        foreach($orders as $order){
          foreach($order->items as $order_item){
              $item = $order_item->item;
              if(!$item){
                continue;
              }
              $quantity = $order_item->quantity;
              $days = 0;
              if($item->breakfast){
                $days = $item->breakfast;
              }
              elseif($item->lunch){
                $days = $item->lunch;
              }
              elseif($item->dinner){
                $days = $item->dinner;
              }
              if($days >= 1 && $days <=7 ){
                $i = 1*$quantity;
                $package_1 += $i;
              }
              if($days >= 8 && $days <=14 ){
                $i = 1*$quantity;
                $package_2 += $i;
              }
              if($days >= 15 && $days <=22 ){
                $i = 1*$quantity;
                $package_3 += $i;
              }
              if($days >= 23){
                $i = 1*$quantity;
                $package_4 += $i;
              }
          }
        }
        if($package_1){
          $package += $package_1/4;
        }
        if($package_2){
          $package += $package_2/2;
        }
        if($package_3){
          $package += $package_3*3/4;
        }
        if($package_4){
          $package += $package_4;
        }

        $out_of_limit = false;
        $amount = $merchant->merchant_share+$merchant->naanstap_share;
        if($package > $merchant->subscription_limit){
          $out_of_limit = true;
        }

        $item = Transactions::where('mid', $merchant->mid)->whereDate('created_at', '>', $last_payout_date)->selectRaw('sum(transaction_amount) as sum_transaction_amount,
              count(*) as quantity, sum(myma_share) as sum_myma_share, sum(myma_part) as sum_myma_part, sum(flexm_part) as sum_flexm_part, sum(other_share) as sum_other_share, type')->groupBy('mid')->first();

        $net = 0;
        $revenue_deducted = 0;

        if($item){
            $net = $item->sum_transaction_amount- $item->sum_myma_share - $item->sum_myma_part;
            $revenue_deducted = $item->sum_myma_share;
        }

        if(!$payout){
                Payout::create([
                    'start_date'  => $start_of_month,
                    'payout_date' => $end_of_month,
                    'amount'  => $item->sum_transaction_amount,
                    'type'  => $merchant->frequency,
                    'merchant_id' => $merchant_id,
                    'status'  => 'pending',
                    'quantity' => $item->quantity,
                    'wallet_received_amount' => '',
                    'revenue_deducted' => $revenue_deducted,
                    'txn_fee' => $item->sum_myma_part,
                    'cost_charged' => $item->sum_flexm_part,
                    'net_payable' => $net,
                    'payout_for'  => 'wlc'
                ]);
        }
        else{
          if($out_of_limit){
            $final_amount = $item->sum_transaction_amount - ($package*$amount);
            $total_share = $package*$amount;
            $naanstap_share = $total_share/4;
            $wlc_share = $total_share-$naanstap_share;

            $pack = PayoutPackage::where('payout_id', $payout->id)->first();
            if(!$pack){
              PayoutPackage::create([
                'payout_id' => $payout->id,
                'package_1' => $package_1,
                'package_2' => $package_2,
                'package_3' => $package_3,
                'package_4' => $package_4,
                // 'package'   => $package,
                'total_amt' => $item->sum_transaction_amount,
                'iso_share' => $final_amount,
                'wlc_share' => $naanstap_share,
                'naanstap_share' => $wlc_share,
              ]);
            }else{
              $pack->update([
                'package_1' => $package_1,
                'package_2' => $package_2,
                'package_3' => $package_3,
                'package_4' => $package_4,
                // 'package'   => $package,
                'total_amt' => $item->sum_transaction_amount,
                'iso_share' => $final_amount,
                'wlc_share' => $naanstap_share,
                'naanstap_share' => $wlc_share,
              ]);
            }


          }else{
            $final_amount = $item->sum_transaction_amount;
          }
          $payout->update([
                    'amount'  => $final_amount,
                    'quantity' => $item->quantity,
                    'revenue_deducted' => $revenue_deducted,
                    'txn_fee' => $item->sum_myma_part,
                    'cost_charged' => $item->sum_flexm_part,
                    'net_payable' => $net,
          ]);
        }

      }catch(Exception $e){
        \Log::debug($e->getMessage());
      }
    }
}
