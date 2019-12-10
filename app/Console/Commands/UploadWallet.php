<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Transactions;
use App\Models\Wallet;
use App\Events\NotifyAdmin;

class UploadWallet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upload:wallet';

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
          $file = public_path('files/ftp/wallet_report.csv');
          $result = \File::exists($file);
          if($result){
            $type = 'wallet';
            $header = [
                'mobile', 'wallet_user_name', 'transaction_date', 'transaction_amount', 'transaction_currency',
                'transaction_ref_no', 'transaction_status', 'transaction_code', 'mid', 'tid', 'merchant_name', 'payment_mode',
            ];
            $customerArr = csvToArray($file, ',', $type, $header);
            for ($i = 0; $i < count($customerArr); $i ++)
            {
              $dd = $customerArr[$i];
              $txn_date = @$dd['transaction_date'];
              if($txn_date != ''){
                //   try{
                    //   $txn_date = Carbon::createFromFormat('d-m-Y h:i', $txn_date);
                //   }catch(Exception $e){
                //     try{
                        $txn_date = Carbon::createFromFormat('d/m/Y H:i', $txn_date);          
                //     } catch(Exception $e){
                        $txn_date = Carbon::parse($txn_date);
                //     } 
                //   }
                $dd['transaction_date'] = $txn_date;
              }
              $wallet = Wallet::create($dd);
              $tran = Transactions::where('transaction_ref_no', $dd['transaction_ref_no'])->first();
              if($tran){
                  if($tran->transaction_amount != $dd['transaction_amount'] || $tran->mid != $dd['mid'] || $tran->tid != $dd['tid']){
                      $tran->update(['report_status' => 1]);
                  }
              }
            //   Wallet::create($customerArr[$i]);
            }

            $old_path = public_path('files/ftp/wallet_report.csv');
            $new_path = public_path('files/uploaded/wallet_report'.mt_rand().time().'.csv');
            \File::move($old_path, $new_path);
          }else{

          }
        }catch(Exception $e){
          addActivity('Cron error while checking wallet report '.$e->getMessage(),1,[],[]);
        }
    }
}
