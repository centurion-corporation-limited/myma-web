<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\SpuulSubscription;
use App\Models\SpuulPlan;

class SpuulCheckSubscription extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spuul:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To check spuul subscription and send notification if subscription is about to expire.';

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
        \Log::debug("spuul check");

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
              sendSingle($sub->user, 'Your spuul subscription is expiring. Please make the payment or your subscription will be stopped', 'link', $link);
            }
        }

      }catch(Exception $e){
        \Log::debug("Exception during spuul check cron");
        \Log::debug($e->getMessage());
      }
    }
}
