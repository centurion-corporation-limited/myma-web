<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Notification;
use Carbon\Carbon;

class SendNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends scheduled notifications.';

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
      $now = Carbon::now()->toDateTimeString();
      $now_minute_ahead = Carbon::now()->addMinute()->toDateTimeString();

      $items = Notification::where('send_at', '>=', $now)->where('send_at', '<=', $now_minute_ahead)->get();
      
      foreach($items as $item){
        $user = $item->user;
        if($user->fcm_token){
          sendSingle($user, $item->message);
        }
      }
    }
}
