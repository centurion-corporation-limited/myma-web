<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Subscription;
use App\Models\Trip;
use App\Models\TripPickup;
use App\Models\TripOrders;
use App\Models\FoodMenu;

class CreateTrip extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:trip';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create trip based on the subscriptions';

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
    //  \Log::debug("created trip");
      try{
        $now = Carbon::now();//->addDay();
        $subs = Subscription::where('delivery_date', $now->toDateString())->get();
        foreach($subs as $sub){
          if(!$sub->order){
            continue;
          }
          $user_id = $sub->order->driver_id;
          if($user_id == ''){
            continue;
          }
          $start_date = Carbon::parse($sub->order->delivery_date);
          $diff = $start_date->diffInDays($now)+1;

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
        addActivity('Cron error hile creating trip '.$e->getMessage(),1,[],[]);
      }
    }
}
