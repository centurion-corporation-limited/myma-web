<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\RemittanceReport;
use App\Events\NotifyAdmin;

class UploadRemittance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upload:remittance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify remittance records with the flexm records for.';

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
          $file = public_path('files/ftp/remittance_report.csv');
          $result = \File::exists($file);
          if($result){
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

            $old_path = public_path('files/ftp/remittance_report.csv');
            $new_path = public_path('files/uploaded/remittance_report'.mt_rand().time().'.csv');
            \File::move($old_path, $new_path);
          }else{

          }
        }catch(Exception $e){
          addActivity('Cron error while checking remittance report '.$e->getMessage(),1,[],[]);
        }
    }
}
