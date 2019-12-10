<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Advertisement;
use Carbon\Carbon;

class UpdateAds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:ads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update ads status';

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
// \Log::debug("updated ads");
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
}
