<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Remittance;
use App\Events\NotifyAdmin;

class UploadSettlement extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upload:settlement';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify flexm records with the flexm records for remittance.';

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
        $path = public_path('files/ftp/Remittance_Settlement_Report.xlsx');
        $type = 'wallet';

        $result = \File::exists($path);
        if($result){
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

                      event(new NotifyAdmin($amount, $no_txns, $remit_txn, $remit_count));
                  }else{

                  }
                }
              }
              $old_path = public_path('files/ftp/Remittance_Settlement_Report.xlsx');
              $new_path = public_path('files/uploaded/Remittance_Settlement_Report_'.mt_rand().time().'.xlsx');
              \File::move($old_path, $new_path);
          }
        }

      }catch(Exception $e){
         addActivity('Cron error while checking remittance settlement report '.$e->getMessage(),1,[],[]);
      }
    }
}
