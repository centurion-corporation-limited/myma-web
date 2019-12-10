<?php

namespace App\Listeners;

use App\Events\UpdatePrice;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;
use App\Models\FoodMerchant;
use App\Models\Merchant;
use App\Models\FoodMenu;
use App\Models\Restaurant;
use Mail;

class PriceListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UpdatedPrice  $event
     * @return void
     */
    public function handle(UpdatePrice $event)
    {
        $id = $event->id;
        $foodmerchant = FoodMerchant::find($id);

        $merchant = Merchant::find(12);

        $gst = 7;
        if($foodmerchant && $merchant){
          $restaurant =Restaurant::where('merchant_id', $foodmerchant->user_id)->first();
          if($restaurant){
            $items = FoodMenu::where('restaurant_id', $restaurant->id)->get();

            $wlc_share_per = $merchant->merchant_share != ""?$merchant->merchant_share:8;
            $naanstap_share_per = $foodmerchant->naanstap_share != ""?$foodmerchant->naanstap_share:10;
            $flexm_per = $merchant->myma_transaction_charges != ""?$merchant->myma_transaction_charges:'2.5';

            foreach($items as $item){
              $price = $item->base_price;
              $naanstap_share = (($price*$naanstap_share_per)/100);
              $selling_price = ((($price+$naanstap_share)/(1-($wlc_share_per/100)-($flexm_per/100)-($gst/100*($wlc_share_per/100)))));
              // $wlc_share = (($selling_price*$wlc_share_per/100));
              // $flexm_share = (($selling_price*$flexm_per)/100);

              $item->price = $selling_price;
              $item->update();
            }
          }
        }
    }
}
