<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use App\Models\Terminal;
use App\Models\Merchant;
use App\User, JWTAuth;

class AddFlexmMerchant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flexm:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a flexm merchant for course user';

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
      $url = config('app.url').'api/v1/flexm/';
      $auth_user = User::whereHas('roles', function($q){
        $q->where('slug', 'admin');
      })->first();

      $user_ids = Merchant::whereNotNull('user_id')->pluck('user_id');

      $users = User::whereHas('roles', function($q){
        $q->where('slug', 'training');
      })->whereNotIn('id', $user_ids)->get();

      try{
        $client = new Client();
        foreach($users as $user){
            $data['merchant_name'] = $user->name;
            $data['merchant_category_code'] = '7399';
            $data['token'] = JWTAuth::fromUser($user);
            $result = $client->post($url.'create/merchant',[
              'form_params' => $data
            ]);

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK

            if($code == "200" && $reason == "OK"){
              $body = $result->getBody();
              $content = json_decode($body->getContents());

              if($content->status == 'success'){
                $msg = $content->message;
                $cont = $content->data;

                $data['merchant_code'] = $cont->merchant_code;
                $data['mid'] = $cont->mid;
                $data['wallet_type_indicator'] = 'centurion';
                $data['status'] = $cont->status;
                $data['created_by'] = $auth_user->id;
                $data['user_id'] = $user->id;
                $data['type'] = 'inapp';
                $exist = Merchant::create($data);

                // $exist = Merchant::where('merchant_code', $data['merchant_code'])->first();
                // if($exist){
                //   $exist->update($data);
                // }else{
                // }
                $dat['token'] = JWTAuth::fromUser($user);
                $dat['merchant_code'] = $data['merchant_code'];
                $dat['payment_mode'] = 1;
                $result = $client->post($url.'create/terminal',[
                  'form_params' => $dat
                ]);

                $code = $result->getStatusCode(); // 200
                $reason = $result->getReasonPhrase(); // OK
                if($code == "200" && $reason == "OK"){
                  $body = $result->getBody();
                  $content = json_decode($body->getContents());

                  if($content->status == 'success'){
                    $msg = $content->message;
                    $cont = $content->data;
                    $terone['merchant_id'] = $exist['id'];
                    $terone['payment_mode'] = 1;
                    $terone['tid'] = $cont->tid;
                    $terone['status'] = $cont->status;
                    addActivity("Cron added a new flexm merchant - ".$user->name, $auth_user->id, $dat, $content);
                    Terminal::create($terone);
                    // $tero = Terminal::where($terone)->first();
                    // if($tero){
                    //   $tero->update($terone);
                    // }else{
                    // }
                  }
                  else{
                    addActivity("Cron error while adding a new flexm merchant terminal", $auth_user->id, $dat, $content);
                  }
                }
                else{
                  addActivity("Cron error while adding a new flexm merchant terminal reason code", $auth_user->id, $dat, ['code' => $code, 'reason' => $reason]);
                  //dd('Could not create terminal. Try again later');
                }
              }else{
                addActivity("Cron error while adding a new flexm merchant ", $auth_user->id, $data, $content);
                //dd(@$content->message);
              }
            }else{
              addActivity("Cron error while adding a new flexm merchant reason code", $auth_user->id, $data, ['code'=> $code, 'reason' => $reason]);
              // dd('Could not create merchant. Try again later');
            }
        }
      }catch (BadResponseException $ex) {
          $response = $ex->getResponse();
          $jsonBody = json_decode((string) $response->getBody());
          addActivity("Cron error while adding a new flexm merchant ", @$auth_user->id, @$dat, @$jsonBody);
          //dd(@$jsonBody->errors);
      }catch(GuzzleException $e){
          addActivity("Cron error while adding a new flexm merchant ", @$auth_user->id, @$dat, @$e->getMessage());
          //dd($e->getMessage());
      }catch(Exception $e){
          addActivity("Cron error while adding a new flexm merchant ", @$auth_user->id, @$dat, @$e->getMessage());
          //dd($e->getMessage());
      }
    }
}
