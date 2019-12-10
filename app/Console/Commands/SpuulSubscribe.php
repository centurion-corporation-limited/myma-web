<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\SpuulSubscription;
use App\Models\SpuulPlan;
use Activity;
use App\Models\Transactions;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;

class SpuulSubscribe extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spuul:subscribe';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To hit spuul api for the user\'s who have made payment for subscription.';

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
        \Log::debug("spuul subscribe");
        $start_date = Carbon::now()->toDateString();

        $subs = SpuulSubscription::whereDate('start_date', $start_date)->where('status', 'paid')->get();

        $client = new Client();
        $result = $client->post('https://api.spuul.com/oauth/token',[
            'form_params' => [
                'grant_type' => 'client_credentials',
                'client_id' => '1e9240872cfda7989634346aa89cd3bf05d6a0462756f58d79e445788cca8d66',
                'client_secret' => '6bdfaae72a9a41b551958c3ff0e1057a9ef25632f2f562049cba8b384dce41e3'
            ]
        ]);
        $code = $result->getStatusCode(); // 200
        $reason = $result->getReasonPhrase(); // OK
        if($code == "200" && $reason == "OK"){
            $body = $result->getBody();
            $content = json_decode($body->getContents());
            if(@$content->access_token){
              $spuul_token = $content->access_token;
            }else{
              Activity::log('Cron Spuul subscription error while getting token - '.json_encode($content), @$user->id);
              return false;
            }
        }else{
            Activity::log('Cron Spuul subscription error while getting token - '.json_encode($reason), @$user->id);
            return false;
        }
        foreach($subs as $sub){

          try{

            $d['email'] = $sub->email;
            $d['account_number'] = $sub->account_number;
            if($sub->type == 'monthly')
              $d['sku_code'] = 'interactive_sg_monthly_sgd';
            else
              $d['sku_code'] = 'interactive_sg_yearly_sgd';

            $client = new Client(['headers' => ['Accept' => 'application/vnd.spuul.v3', 'Authorization' => 'Bearer '.$spuul_token,
            'Cache-Control' => 'no-cache', 'content-type' => 'multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW']]);

            $result = $client->post('https://callbacks.spuul.com/sg_interactive/subscribe', [
              'form_params' => $d
            ]);

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
              $body = $result->getBody();
              $content = json_decode($body->getContents());
              $status = 'success';

              Activity::log('Spuul subscription request is successfull #'.$sub->id, @$user->id);
            }else{
              $content = $reason;
              $status = 'error';
            }

          }catch (BadResponseException $ex) {
              $response = $ex->getResponse();
              $jsonBody = json_decode((string) $response->getBody());
              \Log::debug(json_encode($jsonBody));
              $msg = '';
              if(@$jsonBody->message){
                $msg = @$jsonBody->message;
              }
              if(@$jsonBody->error_description){
                $msg = @$jsonBody->error_description;
              }
              if(@$jsonBody->subscription){
                foreach($jsonBody->subscription as $err){
                    $msg = @$jsonBody->subscription[0];
                    if($msg != ''){
                      break;
                    }
                }
              }
              $content = $msg;
              $status = 'error';
              Activity::log('Cron error while making spuul subscription request - '.$content.' Email - '.@$d['email']);
          }catch(GuzzleException $e){
              $content = $e->getMessage();
              Activity::log('Cron error while making spuul subscription request - '.$content.' Email - '.@$d['email']);
              $status = 'error';
          }catch(Exception $e){
              $content = $e->getMessage();
              $status = 'error';
              Activity::log('Cron error while making spuul subscription request - '.$content.' Email - '.@$d['email']);
          }

          $tt = Transactions::find($sub->transaction_id);
          $tt->update([
            'spuul_status' => $status,
            'spuul_request' => json_encode($d),
            'spuul_response' => json_encode($content)
          ]);
        }

      }catch(Exception $e){
        Activity::log('Cron error in spuul subscribe -'.$e->getMessage());
      }
    }
}
