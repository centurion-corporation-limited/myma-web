<?php

namespace App\Http\Controllers\Api;

use App\User;
use Illuminate\Http\Request;
use App\Models\Singx;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\CookieJarInterface;
use GuzzleHttp\Cookie\SetCookie;
use GuzzleHttp\Cookie\FileCookieJar;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth, Activity, JWTAuth;

class SingxController extends Controller
{
    const BASE_URL = 'https://myhype.space/main/public/api/v1/singx/';
    const MAIN_URL = 'https://api.singx.co/singx/';
    public function signup(Request $request)
    {
        $url = self::MAIN_URL.'secure/CommonRestServiceImpl/signup';
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        \Log::debug("singx signup");
        \Log::debug(json_encode($request->all()));
        if($user->singx_account){
            \Log::debug("account was registered already #".$user->id);
            
          //return response()->json(['status' => 'success', 'data' => '', 'message'=> 'Request sent to singx'], 200);
        }
        if($user->profile){
          $phone = $user->profile->phone;
          $name = explode(' ', $user->name);
          $first_name = @$name[0];
          $middle_name = @$name[2]?@$name[1]:'';
          $last_name = @$name[2]?@$name[2]:@$name[1];

          $fin_no = $user->profile->fin_no;
          $zip_code = $user->profile->zip_code;
          $add_one = $user->profile->street_address;
          $add_two = 'Unit no '.$user->profile->unit_no;
            
          if($user->email == ''){
              return response()->json(['status' => 'error', 'data' => '', 'message'=> 'Update email address in your profile to proceed.'], 200);
          }
          if($phone == ''){
              return response()->json(['status' => 'error', 'data' => '', 'message'=> 'Update phone number in your profile to proceed.'], 200);
          }
          if($middle_name == '' && $last_name == ''){
              // return response()->json(['status' => 'error', 'data' => '', 'message'=> 'Update your full name in your profile to proceed.'], 200);
          }
          if($fin_no == ''){
              return response()->json(['status' => 'error', 'data' => '', 'message'=> 'Update fin no in your profile to proceed.'], 200);
          }
          if($add_one == ''){
              return response()->json(['status' => 'error', 'data' => '', 'message'=> 'Update address in your profile to proceed.'], 200);
          }
          if($add_two == ''){
              return response()->json(['status' => 'error', 'data' => '', 'message'=> 'Update address in your profile to proceed.'], 200);
          }
          if($zip_code == ''){
              return response()->json(['status' => 'error', 'data' => '', 'message'=> 'Update zip code in your profile to proceed.'], 200);
          }

        }else{
            return response()->json(['status' => 'error', 'data' => '', 'message'=> 'Update your profile to proceed.'], 200);
        }
        $data =[
          "loginId" => $user->email,
          "password"=> $user->uid,
          "mobileNumber" => "+65".$phone,
          "firstName" => $first_name,
          "middleName" => $last_name,
          "lastName" => $middle_name,
          "idNumber" => $fin_no,
          "entrySourceCode" => "MYMA",
          "postalCode" => $zip_code,
          "addressLine1" => $add_one,
          "addressLine2" => $add_two,
          'user_id' => $user_id,
        ];
        $dat = $data;
        unset($dat['password']);
        try{

            $client = new Client(['headers' => ['Content-type' => 'application/json']]);

            $result = $client->post(self::BASE_URL.'signup', [
                'form_params' => $data
            ]);
            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());

                if($content->status == 'error'){
                  \Log::debug(json_encode($content));
                  addActivity('Singx signup failed', @$user_id, @$dat, @$content, $url);

                  // Activity::log("Singx singup failed.". ' '.json_encode($content), $user->id);
                }else{
                  addActivity('Singx signup success', @$user_id, @$dat, @$content, $url);

                  // Activity::log("User registered successfully to singx.". ' '.$content->message, $user->id);
                }
                $uu = User::find($user->id);
                $uu->update(['singx_account' => '1']);
                return response()->json(['status' => 'success', 'data' => $content->data, 'message'=> 'Request sent to singx'], 200);
            }
            addActivity('Singx could not registered reason code', @$user_id, @$dat, ['code' => $code, 'reason' => $reason], $url);

            // Activity::log("User could not registered to singx.". ' '.$reason, $user->id);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            addActivity('Singx error while registration', @$user_id, @$dat, @$jsonBody, $url);

            // Activity::log("Exception occured while registering (singx).". ' '.json_encode(@$jsonBody->errors), $user->id);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
            addActivity('Singx error while registration', @$user_id, @$dat, $e->getMessage(), $url);

            // Activity::log("Exception occured while registering (singx).". ' '.$e->getMessage(), $user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            addActivity('Singx error while registration', @$user_id, @$dat, $e->getMessage(), $url);

            // Activity::log("Exception occured while registering (singx).". ' '.$e->getMessage(), $user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function checkStatus(Request $request)
    {
        $url = self::MAIN_URL.'secure/Authentication/authenticate';
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $data = [];
        try{

            \Log::debug("status check");
            \Log::debug(json_encode($request->all()));
            $username = $user->email;
            $password = $user->uid;

            if($username == 'test2@yopmail.com'){
            //   $username = 'qasingx@gmail.com';
            //   $password = 'Singx1234';
               $username = 'naveenkumar@singx.co';
               $password = 'lDHb99KxJnix9X6oVokw3q44Cnm4JxfZ';
            //   // \Log::debug($username);
            }
            $client = new Client(['headers' => ['Content-type' => 'application/json']]);

            $result = $client->post(self::BASE_URL.'login', [
                'form_params' => [
                    'user_id' => $user_id,
                    'username' => $username,
                    'password' => $password,
                ]
            ]);
            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                if(@$content->status == 'error'){
                    $msg = $content->message;
                    
                    addActivity('Singx error '.$msg, @$user_id,  ['username' => @$username], @$content, $url);
                    return response()->json(['status' => 'error', 'data' => $content->data, 'message'=> $content->message], 200);
                }
                \Log::debug(json_encode($content));
                $clientt = new Client(['headers' => ['Content-type' => 'application/json']]);

                $resultt = $clientt->post(self::BASE_URL.'verify', [
                    'form_params' => [
                        'user_id' => $user_id,
                    ]
                ]);
                $codet = $resultt->getStatusCode(); // 200
                $reasont = $resultt->getReasonPhrase(); // OK
                if($codet == "200" && $reasont == "OK"){
                    $bodyt = $resultt->getBody();
                    $contentt = json_decode($bodyt->getContents());
                    if(@$contentt->status == 'error'){
                      $text = '';
                      $msg = $contentt->message;
                      if($contentt->data && @$contentt->data->response){
                          if(@$contentt->data->response->diagnosis)
                          {
                            $msg = @$contentt->data->response->diagnosis;      
                            $text .= 'Diagnosis - '.$msg;
                          }
                          if(@$contentt->data->response->solution){
                              $solution = $contentt->data->response->solution;
                              $text .= ' Solution - '.$solution;
                          }
                        
                      }
                      
                      addActivity('Singx error '.$text, @$user_id,  ['username' => @$username], @$contentt, $url);
                      return response()->json(['status' => 'error', 'data' => $contentt->data, 'message'=> $contentt->message], 200);
                    }else{
                        \Log::debug(json_encode($contentt));
                        foreach(@$contentt->data as $d){
                          if(isset($d->profileStatus) && @$d->profileStatus == 10){
                            addActivity('Singx status check success', @$user_id,  ['username' => @$username], @$contentt, $url);
    
                            return response()->json(['status' => $contentt->status, 'data' => $contentt->data, 'message'=> $contentt->message], 200);
                          }
                          elseif(isset($d->profileStatus) && $d->profileStatus < 10){
                            addActivity('Singx waiting for approval', @$user_id,  ['username' => @$username], @$contentt, $url);
    
                            return response()->json(['status' => 'waiting', 'data' => $contentt->data, 'message'=> $contentt->message], 200);
                          }elseif($user->singx_account){
                            addActivity('Singx waiting for approval', @$user_id,  ['username' => @$username], @$contentt, $url);
    
                            return response()->json(['status' => 'error', 'data' => $contentt->data, 'message'=> $contentt->message], 200);
                          }
                        }
                    }
                }
            }else{
              addActivity('Singx error while status check reason code', @$user_id,  ['username' => @$username], ['code' => $code, 'reason' => $reason], $url);

              return response()->json(['status' => $content->status, 'data' => $content->data, 'message'=> $content->message], 200);
            }

            if($user->singx_account == 1){
              addActivity('Singx waiting for approval reason code', @$user_id,  ['username' => @$username], ['code' => $code, 'reason' => $reason], $url);

              return response()->json(['status' => 'error', 'data' => '', 'message'=> 'success'], 200);
            }
            return response()->json(['status' => 'error', 'data' => $content, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            addActivity('Singx error while status check', @$user_id,  ['username' => @$username], @$jsonBody, $url);

            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
            addActivity('Singx error while status check', @$user_id,  ['username' => @$username], @$e->getMessage(), $url);

            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            addActivity('Singx error while status check', @$user_id,  ['username' => @$username], @$e->getMessage(), $url);

            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function login(Request $request)
    {
        $url = self::MAIN_URL.'secure/Authentication/authenticate';
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $data = [];
        try{
            \Log::debug("login");
            \Log::debug(json_encode($request->all()));

            // $username = 'qasingx@gmail.com';
            // $password = 'Singx1234';
            // if($username != 'qasingx@gmail.com'){
              $username = $user->email;
              $password = $user->uid;

            // }
            if($username == 'test2@yopmail.com'){
            //   $username = 'qasingx@gmail.com';
            //   $password = 'Singx1234';
               $username = 'naveenkumar@singx.co';
               $password = 'lDHb99KxJnix9X6oVokw3q44Cnm4JxfZ';
              // \Log::debug($username);
            }
            // return response()->json(['status' => "success", 'data' => [
            //     'first_name' => ''
            // ], 'message'=> 'SUCCESS'], 200);
            $long = $request->input('longitude');
            $lat = $request->input('latitude');

            $client = new Client(['headers' => ['Content-type' => 'application/json']]);

            $result = $client->post(self::BASE_URL.'login', [
                'form_params' => [
                    'user_id'  => $user_id,
                    'username' => $username,
                    'password' => $password,
                ]
            ]);
            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                // Activity::log("User logged in successfully to singx.", $user->id);
                addActivity('Singx login', @$user_id,  ['username' => @$username], @$content, $url);
                $customer_data = $this->getCustomerId($user_id);
                if(@$customer_data['status'] == 'success'){
                  \Log::debug(json_encode($customer_data));
                  $customer_id = @$customer_data['data']->contactId;
                  $contact_id = @$customer_data['data']->customerId;
                  @$content->data->customer_id = $customer_id;
                  @$content->data->contact_id = $contact_id;
                  \Log::debug("Login response");
                  \Log::debug(json_encode($content->data));
                  return response()->json(['status' => $content->status, 'data' => $content->data, 'message'=> $content->message], 200);
                }else{
                  return response()->json(['status' => 'error', 'data' => $content->data, 'message'=> 'Counld not fetch customer ID.'], 200);
                }

            }
            addActivity('Singx error while login reason code', @$user_id,  ['username' => @$username], ['code' => $code, 'reason' => $reason], $url);
            // Activity::log("User could not login to singx. - ".$reason, $user->id);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            addActivity('Singx error while login', @$user_id,  ['username' => @$username], @$jsonBody, $url);
            // Activity::log("Exception occured while logging in (singx). - ".json_encode($jsonBody), $user->id);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
            addActivity('Singx error while login', @$user_id,  ['username' => @$username], @$e->getMessage(), $url);
            // Activity::log("Exception occured while logging in (singx). - ".$e->getMessage(), $user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            addActivity('Singx error while login', @$user_id,  ['username' => @$username], @$e->getMessage(), $url);
            // Activity::log("Exception occured while logging in (singx). - ".$e->getMessage(), $user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }

    }
    
    public function getCountryList()
    {
        try{
            $data = [
              [
                'name' => 'Singapore',
                'id' => '59C3BBD2-5D26-4A47-8FC1-2EFA628049CE'
              ]
          ];

            return response()->json(['status' => 'success', 'data' => $data, 'message'=> 'SUCCESS']);

        }catch(Exception $e){
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED']);
        }
    }
    
    public function getAccountType()
    {
        try{
            $data = [
              [
                'name' => 'Current',
                'code' => 'current'
              ],
            [
              'name' => 'Savings',
              'code' => 'savings'
            ],
            [
              'name' => 'NRE',
              'code' => 'nre'
            ],
            [
              'name' => 'NRO',
              'code' => 'nro'
            ]

          ];

            return response()->json(['status' => 'success', 'data' => $data, 'message'=> 'SUCCESS']);

        }catch(Exception $e){
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED']);
        }
    }

    public function getCustomerId($user_id)
    {
        $url = self::MAIN_URL.'secure/CustomerType/findById';
        try{
            $client = new Client(['headers' => ['Content-type' => 'application/json']]);

            $result = $client->post(self::BASE_URL.'getCustomerId', [
                'form_params' => [
                    'user_id' => $user_id
                ],
            ]);
            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                if($content->status = 'success'){
                  addActivity('Singx fetched user details', @$user_id,  @$data, $content, $url);

                }else{
                  addActivity('Singx error while fetching user details', @$user_id,  @$data, $content, $url);
                }
                return ['status' => @$content->status, 'data' => @$content->data, 'message'=> @$content->message];
            }
            addActivity('Singx error while fetching user details reason code', @$user_id,  @$data, ['code' => $code, 'reason' => $reason], $url);
            return ['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'];

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            addActivity('Singx error while fetching user details', @$user_id,  @$data, $jsonBody, $url);
            return ['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message];
        }catch(GuzzleException $e){
            addActivity('Singx error while fetching user details', @$user_id,  @$data, $e->getMessage(), $url);
            return ['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'];
        }catch(Exception $e){
            addActivity('Singx error while fetching user details', @$user_id,  @$data, $e->getMessage(), $url);
            return ['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'];
        }
    }

    public function postLogin(Request $request)
    {
        $url = self::MAIN_URL.'secure/SingxContacts/findById';
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        
        $id = $request->input('id');
        $data = ['id' => $id];
        try{
            \Log::debug("post login");
            \Log::debug(json_encode($request->all()));

            $client = new Client(['headers' => ['Content-type' => 'application/json']]);

            $result = $client->post(self::BASE_URL.'post/login', [
                'form_params' => [
                    'id' => $id,
                    'user_id' => $user_id
                ],
                // 'cookies' => $cookieJar
            ]);

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();

                $content = json_decode($body->getContents());
                if($content->status = "success"){
                    if(isset($content->data->response->success) && $content->data->response->success == true){
                    // $msg = $content->response->message;
                    // $cont = $content->response->data;
                    // Activity::log("User session created (singx).", $user->id);
                        addActivity('Singx post logged is success', @$user_id,  ['id' => $id], $content, $url);
                    }else{
                        addActivity('Singx error while post logged api', @$user_id,  ['id' => $id], $content, $url);
                    }
                }else{
                    addActivity('Singx error while post logged api', @$user_id,  ['id' => $id], $content, $url);

                //     $cont = $content->response->message;
                //     return response()->json(['status' => 'error','data' => $content, 'message'=> 'EXCEPTION_OCCURED'], 200);
                }
                return response()->json(['status' => $content->status, 'data' => $content->data, 'message'=> $content->message], 200);

            }
            // Activity::log("Exception occured while requesting session creation (singx).", $user->id);
            addActivity('Singx error while post login by id reason code', @$user_id,  @$data, ['code' => $code, 'reason' => $reason], $url);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch(Exception $e){
            addActivity('Singx error while post login by id api', @$user_id,  @$data, @$e->getMessage(), $url);
            // Activity::log("Exception occured while requesting session creation (singx).", $user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function forgotPassword(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $data = [];
        $data['email'] = $email = $request->input('email');

        try{
            \Log::debug("forgot password");
            \Log::debug(json_encode($request->all()));

            // $cookieFile = storage_path('jar.txt');
            // $cookieJar = new FileCookieJar($cookieFile,true);
            //
            // $cookieJar->clear();
            // if($email == 'qasingx@gmail.com'){
            //     return response()->json(['status' => 'success', 'data' => '', 'message'=> 'An email with reset link has been sent.'], 200);
            // }else{
            //     return response()->json(['status' => 'error', 'data' => '', 'message'=> 'This email is not valid'], 200);
            // }
            $client = new Client(['headers' => ['Content-type' => 'application/json']]);

            $result = $client->post(self::BASE_URL.'forgotPassword', [
                'json' => [
                    'email' => $email
                ]
                // ,
                // 'cookies' => $cookieJar
            ]);
            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                // Activity::log("Forgot password request created (singx).", $user->id);
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                if($content->status == 'success'){
                  addActivity('Singx successfull forgot password request', @$user_id,  @$data, @$content, $url);
                }else{
                  addActivity('Singx error while requesting forgot password', @$user_id,  @$data, @$content, $url);
                }
                return response()->json(['status' => $content->status, 'data' => $content->data, 'message'=> 'SENT_OTP'], 200);
            }
            // Activity::log("Forgot password request could not create (singx).", $user->id);
            addActivity('Singx error while requesting forgot password reason code', @$user_id,  @$data, ['code' => $code, 'reason' => $reason], $url);

            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
        catch(Exception $e){
            addActivity('Singx error while requesting forgot password', @$user_id,  @$data, @$e->getMessage(), $url);
            // Activity::log("Exception occured while requesting forgot password (singx).", $user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function listSender(Request $request)
    {
        $url = self::MAIN_URL.'secure/CustomerBankAccount/getCustomerAccDetails';
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $data = [];
        
        $path = storage_path($user_id.'.txt');
        $pathe = str_replace('wlc', 'main', $path);
        $cookie_content = '';
        if(\File::exists($pathe)){
          $cookie_content = \File::get($pathe);
          if($cookie_content){
              $cookie_content = json_decode($cookie_content);
          }
        }
        
        $data['cookie'] = $cookie_content;
        try{
            \Log::debug("list sender");
            \Log::debug(json_encode($request->all()));
            
            // $cookieFile = storage_path('jar.txt');
            // $cookieJar = new FileCookieJar($cookieFile);

            $client = new Client(['headers' => ['Content-type' => 'application/json']]);

            $result = $client->post(self::BASE_URL.'sender/list', [
                'form_params' => [
                    'user_id' => $user_id
                ],
            ]);

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                // Activity::log("Accessed sender list (singx).", $user->id);
                $content = json_decode($body->getContents());

                // return response()->json(['status' => $content->status, 'data' => $content->data, 'message'=> $content->message], 200);
                if($content->status == 'success'){
                    foreach($content->data as $dd){
                      $dd->firstName = $dd->firstName.' '.$dd->lastName;
                    }
                    addActivity('Singx successfull sender account list request', @$user_id,  @$data, @$content, $url);

                    return response()->json(['status' => $content->status, 'data' => $content->data, 'message'=> $content->message], 200);
                }else{
                    addActivity('Singx error while fetchign sender account list', @$user_id,  @$data, @$content, $url);
                    // $cont = $content->response->message;
                    return response()->json(['status' => $content->status, 'data' => $content->data, 'message'=> $content->message], 200);
                }
            }
            // Activity::log("Sender list could not fetch (singx).", $user->id);
            addActivity('Singx error while fetchign sender account list reason code', @$user_id,  @$data, ['code' => $code, 'reason' => $reason], $url);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch(Exception $e){
            addActivity('Singx error while fetching sender account list', @$user_id,  @$data, @$e->getMessage(), $url);
            // Activity::log("Exception occured while fetching sender list (singx).", $user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function addSender(Request $request)
    {
        $url = self::MAIN_URL.'secure/CustomerBankAccount/save';
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $data = [];
        
        $path = storage_path($user_id.'.txt');
        $pathe = str_replace('wlc', 'main', $path);
        $cookie_content = '';
        if(\File::exists($pathe)){
          $cookie_content = \File::get($pathe);
          if($cookie_content){
              $cookie_content = json_decode($cookie_content);
          }
        }
        
        $data['cookie'] = $cookie_content;
        
        $firstName = $request->input('firstName');
        $name = explode(' ', $firstName);
        $middleName = $request->input('middleName');
        $lastName = $request->input('lastName');
        // \Log::debug($name);
        // return response()->json(['status' => 'error', 'data' => '', 'message'=> 'Enter full name.'], 200);
        if($lastName == '' && count($name) == 1){
            return response()->json(['status' => 'error', 'data' => '', 'message'=> 'Enter full name.'], 200);
        }

        if($lastName == '' && count($name) == 2){
            $firstName = $name[0];
            $lastName = $name[1];
        }
        if($lastName == '' && count($name) == 3){
            $firstName = $name[0];
            $middleName = $name[1];
            $lastName = $name[2];
        }
        
        $data['firstName'] = $firstName;
        $data['middleName'] = $middleName;
        $data['lastName'] = $lastName;
        $data['accountNumber'] = $accountNumber = $request->input('accountNumber');
        if(strlen($accountNumber) < 7 || strlen($accountNumber) > 30){
          return response()->json(['status' => 'error', 'data' => '', 'message'=> 'Account number must be between 7 to 30 digits.'], 200);
        }
        $data['jointAccHolderName'] = $jointAccHolderName = $request->input('jointAccHolderName');
        $data['customerId'] = $customerId = $request->input('customerId');
        $data['wireTransferModeId'] = $wireTransferModeId = $request->input('wireTransferModeId');
        $data['bankId'] = $bankId = $request->input('bankId');
        $data['branchId'] = $branchId = $request->input('branchId');
        $data['countryId'] = $countryId = $request->input('countryId');
        
        try{
            \Log::debug("add sender");
            \Log::debug(json_encode($request->all()));

            // $cookieFile = storage_path('jar.txt');
            // $cookieJar = new FileCookieJar($cookieFile);

            $client = new Client(['headers' => ['Content-type' => 'application/json']]);

            // $bankId = '7D3C84F5-3304-4A8C-B4CF-8AD2EAB9D9FB';
            // return response()->json(['status' => 'success', 'data' => '', 'message'=> 'SUCCESS'], 200);
            $result = $client->post(self::BASE_URL.'sender/add', [
                'form_params' => [
                    'firstName' => $firstName,
                    'middleName' => $middleName,
                    'lastName' => $lastName,
                    'accountNumber' => $accountNumber,
                    'jointAccHolderName' => $jointAccHolderName,
                    'customerId' => $customerId,
                    'wireTransferModeId' => $wireTransferModeId,
                    'bankId' => $bankId,
                    'branchId' => $branchId,
                    'countryId' => $countryId,
                    'user_id' => $user_id,
                ],
                // 'cookies' => $cookieJar
            ]);

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();

                $content = json_decode($body->getContents());
                \Log::debug(json_encode($content));
                // return response()->json(['status' => "success", 'data' => [], 'message'=> $content->message], 200);

                if($content->status == 'success'){
                    addActivity('Singx new sender has been added', @$user_id,  @$data, @$content, $url);
                    // Activity::log("Added a new sender (singx).", $user->id);
                }else{
                    addActivity('Singx error while adding a new sender', @$user_id,  @$data, @$content, $url);
                    // Activity::log("Exception occured while requesting branch listing (singx) -".json_encode($content->data), $user->id);
                }
                return response()->json(['status' => $content->status, 'data' => $content->data, 'message'=> $content->message], 200);


                // if($content->response->success){
                //     $cont = $content->response->data;
                //     return response()->json(['status' => 'success','data' => $cont, 'message'=> 'DATA'], 200);
                // }else{
                //     $cont = $content->response->message;
                //     return response()->json(['status' => 'error','data' => $content, 'message'=> 'EXCEPTION_OCCURED'], 200);
                // }
            }
            // Activity::log("Sender could not add due to an exception (singx).", $user->id);
            addActivity('Singx error while adding a new sender reason code', @$user_id,  @$data, ['code' => $code, 'reason' => $reason], $url);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            // Activity::log("Exception occured while requesting branch listing (singx).", $user->id);
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            // \Log::debug("add sender error");
            // \Log::debug(json_encode($jsonBody));
            addActivity('Singx error while adding sender', @$user_id,  @$data, @$jsonBody, $url);

            return response()->json(['status' => 'error', 'data' => $jsonBody->errors, 'message'=> $jsonBody->message], 200);
        }catch(GuzzleException $e){
            // Activity::log("Sender could not add due to an exception (singx).", $user->id);
            addActivity('Singx error while adding sender', @$user_id,  @$data, @$e->getMessage(), $url);

            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
        catch(Exception $e){
            // Activity::log("Sender could not add due to an exception (singx).", $user->id);
            addActivity('Singx error while adding sender', @$user_id,  @$data, @$e->getMessage(), $url);

            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }

    }

    public function getView(Request $request)
    {
        try{
            \Log::debug("get view");
            \Log::debug(json_encode($request->all()));
            // $cookieFile = storage_path('jar.txt');
            // $cookieJar = new FileCookieJar($cookieFile);

            $client = new Client(['headers' => ['Content-type' => 'application/json']]);

            $result = $client->post(self::BASE_URL.'sender/view', [
                // 'cookies' => $cookieJar
            ]);

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();

                $content = json_decode($body->getContents());
                return response()->json(['status' => $content->status, 'data' => $content->data, 'message'=> $content->message], 200);

                // if($content->response->success){
                //     $cont = $content->response->data;
                //     return response()->json(['status' => 'success','data' => $cont, 'message'=> 'DATA'], 200);
                // }else{
                //     $cont = $content->response->message;
                //     return response()->json(['status' => 'error','data' => $content, 'message'=> 'EXCEPTION_OCCURED'], 200);
                // }
            }
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch(GuzzleException $e){

            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
        catch(Exception $e){
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }

    }

    public function listReceiver(Request $request)
    {
        $url = self::MAIN_URL.'secure/ReceiverAccountDetailServiceWS/getReceiverAccList';
        $user = JWTAuth::toUser($request->input('token'));
        $user_id =$user->id;
        $data = [];
        
        $path = storage_path($user_id.'.txt');
        $pathe = str_replace('wlc', 'main', $path);
        $cookie_content = '';
        if(\File::exists($pathe)){
          $cookie_content = \File::get($pathe);
          if($cookie_content){
              $cookie_content = json_decode($cookie_content);
          }
        }
        
        $data['cookie'] = $cookie_content;
        try{
            \Log::debug("list receiver");
            \Log::debug(json_encode($request->all()));

            // $cookieFile = storage_path('jar.txt');
            // $cookieJar = new FileCookieJar($cookieFile);

            $client = new Client(['headers' => ['Content-type' => 'application/json']]);

            $result = $client->post(self::BASE_URL.'receiver/list', [
                'form_params' => [
                    'user_id' => $user_id
                ],
            ]);

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if( in_array($code, ['200', '201']) && in_array($reason, ['OK', 'Created']) ) {
                $body = $result->getBody();
                // Activity::log("Accessed receiver list (singx).", $user->id);
                $content = json_decode($body->getContents());
                if($content->status == 'success'){
                    addActivity('Singx fetched receiver account list', @$user_id,  @$data, @$content, $url);
                }else{
                    addActivity('Singx error while fetching receiver account list', @$user_id,  @$data, @$content, $url);
                }
                return response()->json(['status' => $content->status, 'data' => $content->data, 'message'=> $content->message], 200);
            }
            addActivity('Singx error while requesting receiveer account listing reason code', @$user_id,  @$data, ['code' => $code, 'reason' => $reason], $url);
            // Activity::log("Exception occured while accessing receiver account list (singx).", $user->id);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch(Exception $e){
            addActivity('Singx error while requesting receiveer account listing', @$user_id,  @$data, @$e->getMessage(), $url);
            // Activity::log("Exception occured while accessing receiver account list (singx).", $user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }

    }

    public function viewReceiver(Request $request)
    {
        try{
            // $cookieFile = storage_path('jar.txt');
            // $cookieJar = new FileCookieJar($cookieFile);

            $countryId = $request->input('countryId');
            $client = new Client(['headers' => ['Content-type' => 'application/json']]);

            $result = $client->post(self::BASE_URL.'receiver/view', [
                'form_params' => [
                    'countryId' => $countryId,
                ],
                // 'cookies' => $cookieJar
            ]);

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();

                $content = json_decode($body->getContents());
                return response()->json(['status' => $content->status, 'data' => $content->data, 'message'=> $content->message], 200);

                // if($content->response->success){
                //     $cont = $content->response->data;
                //     return response()->json(['status' => 'success','data' => $cont, 'message'=> 'DATA'], 200);
                // }else{
                //     $cont = $content->response->message;
                //     return response()->json(['status' => 'error','data' => $content, 'message'=> 'EXCEPTION_OCCURED'], 200);
                // }
            }
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch(Exception $e){
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function addReceiver(Request $request)
    {
        $url = self::MAIN_URL.'secure/RecceiverAccount/save';
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $data = [];
        $path = storage_path($user_id.'.txt');
        $pathe = str_replace('wlc', 'main', $path);
        $cookie_content = '';
        if(\File::exists($pathe)){
          $cookie_content = \File::get($pathe);
          if($cookie_content){
              $cookie_content = json_decode($cookie_content);
          }
        }
        
        $data['cookie'] = $cookie_content;
        
        $data['receiverName'] = $receiverName = $request->input('receiverName');
        $data['accountNumber'] = $accountNumber = $request->input('accountNumber');
        if(strlen($accountNumber) < 7 || strlen($accountNumber) > 30){
          return response()->json(['status' => 'error', 'data' => '', 'message'=> 'Account number must be between 7 to 30 digits.'], 200);
        }
        $data['accounttype'] = $accountType = $request->input('accounttype');
        $data['residanceAddr'] = $residenceAddress = $request->input('residanceAddr');
        $data['countryId'] = $countryId = $request->input('countryId');
        $data['bankId'] = $bankId = $request->input('bankId');
        $data['branchCode'] = $branchCode = $request->input('branchCode');
        $data['customerId'] = $customerId = $request->input('customerId');
        $data['wireTransferModeId'] = $wireTransferModeId = $request->input('wireTransferModeId');
        $data['oneTimePassword'] = $otp = $request->input('oneTimePassword');
        $data['branchId'] = $branchId = $request->input('branchId');

        try{
            \Log::debug("add receiver");
            \Log::debug(json_encode($request->all()));

            // $cookieFile = storage_path('jar.txt');
            // $cookieJar = new FileCookieJar($cookieFile);

            $client = new Client(['headers' => ['Content-type' => 'application/json']]);

            $result = $client->post(self::BASE_URL.'receiver/add', [
                'form_params' => [
                    'receiverName' => $receiverName,
                    'accountNumber' => $accountNumber,
                    'accounttype' => $accountType,
                    'residanceAddr' => $residenceAddress,
                    'countryId' => $countryId,
                    'bankId' => $bankId,
                    'branchCode' => $branchCode,
                    'customerId' => $customerId,
                    'wireTransferModeId' => $wireTransferModeId,
                    'oneTimePassword' => $otp,
                    'branchId' => $branchId,
                    'user_id' => $user_id,
                ],
                // 'cookies' => $cookieJar
            ]);

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();

                $content = json_decode($body->getContents());
                if($content->status == 'success'){
                    addActivity('Singx successfully added receiver account', @$user_id,  @$data, @$content, $url);
                }else{
                    addActivity('Singx error while adding a new receiver account', @$user_id,  @$data, @$content, $url);
                }
                // Activity::log("Added a new receiver account (singx).", $user->id);
                return response()->json(['status' => $content->status, 'data' => $content->data, 'message'=> $content->message], 200);

            }
            addActivity('Singx error while creating new receiver account reason code', @$user_id,  @$data, ['code' => $code, 'reason' => $reason], $url);
            // Activity::log("Exception occured while adding a new receiver account (singx).", $user->id);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch(GuzzleException $e){
            addActivity('Singx error while creating new receiver account', @$user_id,  @$data, @$e->getMessage(), $url);
            // Activity::log("Exception occured while adding a new receiver account (singx).", $user->id);

            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            addActivity('Singx error while creating new receiver account', @$user_id,  @$data, @$e->getMessage(), $url);
            // Activity::log("Exception occured while adding a new receiver account (singx).", $user->id);

            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function getReceiverById(Request $request)
    {
        $url = self::MAIN_URL.'secure/RecceiverAccount/findById';
        try{
            \Log::debug("get receiver");
            \Log::debug(json_encode($request->all()));
            $user = JWTAuth::toUser($request->input('token'));

            // $cookieFile = storage_path('jar.txt');
            // $cookieJar = new FileCookieJar($cookieFile);

            $id = $request->input('id');
            $client = new Client(['headers' => ['Content-type' => 'application/json']]);

            $result = $client->post(self::BASE_URL.'receiver/findById', [
                'form_params' => [
                    'id' => $id,
                ],
                // 'cookies' => $cookieJar
            ]);

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                Activity::log("Accessed receiver account details (singx).", $user->id);

                $content = json_decode($body->getContents());
                return response()->json(['status' => $content->status, 'data' => $content->data, 'message'=> $content->message], 200);

                // if($content->response->success){
                //     $cont = $content->response->data;
                //     return response()->json(['status' => 'success','data' => $cont, 'message'=> 'DATA'], 200);
                // }else{
                //     $cont = $content->response->message;
                //     return response()->json(['status' => 'error','data' => $content, 'message'=> 'EXCEPTION_OCCURED'], 200);
                // }
            }
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch(Exception $e){
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }

    }

    public function updateReceiverAccount(Request $request)
    {
        try{
            $user = JWTAuth::toUser($request->input('token'));

            $receiverName = $request->input('receiverName');
            $accountNumber = $request->input('accountNumber');
            $accountType = $request->input('accountType');
            $residenceAddress = $request->input('residenceAddress');
            $countryId = $request->input('countryId');
            $bankId = $request->input('bankId');
            $branchCode = $request->input('branchCode');
            $customerId = $request->input('customerId');
            $wireTransferModeId = $request->input('wireTransferModeId');
            $otp = $request->input('otp');
            $branchId = $request->input('branchId');

            $client = new Client();
            $skip = 0;

            $result = $client->post(self::BASE_URL.'receiver/update', [
                'form_params' => [
                    'receiverName' => $receiverName,
                    'accountNumber' => $accountNumber,
                    'accountType' => $accountType,
                    'residenceAddress' => $residenceAddress,
                    'countryId' => $countryId,
                    'bankId' => $bankId,
                    'branchCode' => $branchCode,
                    'customerId' => $customerId,
                    'wireTransferModeId' => $wireTransferModeId,
                    'otp' => $otp,
                    'branchId' => $branchId,
                ]
            ]);
            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());

                return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
            }else{
                return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);

                echo "exception occured";
                exit;
            }

        }catch(Exception $e){
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }

    }

    public function generateOTP(Request $request)
    {
        $url = self::MAIN_URL.'secure/TransactionInfo/getOtpReceiver';
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $data = [];
        $path = storage_path($user_id.'.txt');
        $pathe = str_replace('wlc', 'main', $path);
        $cookie_content = '';
        if(\File::exists($pathe)){
          $cookie_content = \File::get($pathe);
          if($cookie_content){
              $cookie_content = json_decode($cookie_content);
          }
        }
        
        $data['cookie'] = $cookie_content;
        try{
            \Log::debug("generate otp");
            \Log::debug(json_encode($request->all()));
            $client = new Client();
            // return response()->json(['status' => 'success', 'data' => '', 'message'=> 'DATA'], 200);

            $result = $client->post(self::BASE_URL.'transaction/otp', [
                'form_params' => [
                    'user_id' => $user_id
                ],
            ]);
            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                if($content->status == 'success'){
                    addActivity('Singx created otp request', @$user_id,  @$data, @$content, $url);
                }else{
                    addActivity('Singx error while requesting otp', @$user_id,  @$data, @$content, $url);
                }
                return response()->json(['status' => $content->status, 'data' => $content->data, 'message'=> $content->message], 200);

            }
            addActivity('Singx error while requesting otp generation reason code', @$user_id,  @$data, ['code' => $code, 'reason' => $reason], $url);
            // Activity::log("Exception occured while creating OTP request (singx).", $user->id);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch(Exception $e){
            addActivity('Singx error while requesting otp generation', @$user_id,  @$data, @$e->getMessage(), $url);
            // Activity::log("Exception occured while creating OTP request (singx).", $user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }

    }

    public function getExchange(Request $request)
    {
        
        \Log::debug("get exchange");
        \Log::debug(json_encode($request->all()));
        // try {
        //     $hostname = isset($_SERVER['argv'][1])? $_SERVER['argv'][1]: 'apidev1.singx.co';
        //     $port = isset($_SERVER['argv'][2])? $_SERVER['argv'][2]: 8444;
        //
        //     printf(' ? Attempting to connect to: %s:%u', $hostname, $port);
        //
        //     if(@fsockopen($hostname, $port, $code, $message, 5) === false) {
        //         throw new \Exception($message, $code);
        //     }
        //
        //     printf("\r:) Successfully connected to: %s:%u", $hostname, $port);
        // }
        // catch(\Exception $e) {
        //     printf("\nError: %u - %s", $e->getCode(), $e->getMessage());
        //     exit(1);
        // }
        // exit;

        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $data = [];
        $path = storage_path($user_id.'.txt');
        $pathe = str_replace('wlc', 'main', $path);
        $cookie_content = '';
        if(\File::exists($pathe)){
          $cookie_content = \File::get($pathe);
          if($cookie_content){
              $cookie_content = json_decode($cookie_content);
          }
        }
        
        $data['cookie'] = $cookie_content;
        $data['fromId'] = $fromId = $request->input('fromId');
        $data['toId'] = $toId = $request->input('toId');
        $data['amount'] = $amount = $request->input('amount');
        $data['receiveAmt'] = $receiveAmt = $request->input('receiveAmt');
        $data['fromCountryId'] = $fromCountryId = $request->input('fromCountryId');
        $data['toCountryId'] = $toCountryId = $request->input('toCountryId');
        $data['feeType'] = $feeType = $request->input('feeType');
        $data['label'] = $label = $request->input('label');
        $data['customerId'] = $customerId = $request->input('customerId');
    
        if($customerId != ''){
            $url = self::MAIN_URL.'secure/ExchangeRateWS/getExchangeRates';
        }else{
            $url = self::MAIN_URL.'secure/CommonRestServiceImpl/getExchangeRates';
        }
        try{

            $client = new Client(['headers' => ['Content-type' => 'application/json']]);
            if($toId == 'TAKA'){
              $toId = 'BDT';
              $toCountryId = '043A9F21-71C8-40E6-A95F-72DA2EC84885';
            }
            $result = $client->post(self::BASE_URL.'exchange/rate', [
                'form_params' => [
                    'fromId' => $fromId,
                    'toId' => $toId,
                    'amount' => $amount,
                    'receiveAmt' => $receiveAmt,
                    'fromCountryId' => $fromCountryId,
                    'toCountryId' => $toCountryId,
                    'feeType' => $feeType,
                    'label' => $label,
                    'customerId' => $customerId,
                    'user_id' => $user_id,
                ]
            ]);
            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                if($content->status == 'success'){
                    addActivity('Singx fetched exchange rate', @$user_id,  @$data, @$content, $url);
                }else{
                    addActivity('Singx error while requesting exchange rate', @$user_id,  @$data, @$content, $url);
                }
                return response()->json(['status' => $content->status, 'data' => $content->data, 'message'=> $content->message], 200);

            }
            addActivity('Singx error while exchange rate request reason code', @$user_id,  @$data, ['code' => $code, 'reason' => $reason], $url);
            // Activity::log("Exception occured while exchange rate request (singx).", $user->id);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch(Exception $e){
            addActivity('Singx error while exchange rate request', @$user_id,  @$data, @$e->getMessage(), $url);
            // Activity::log("Exception occured while exchange rate request (singx).", $user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }

    }

    public function searchIfsc(Request $request)
    {
        $url = self::MAIN_URL.'secure/BankBranch/findByIFSCCode';
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $data = [];
        $path = storage_path($user_id.'.txt');
        $pathe = str_replace('wlc', 'main', $path);
        $cookie_content = '';
        if(\File::exists($pathe)){
          $cookie_content = \File::get($pathe);
          if($cookie_content){
              $cookie_content = json_decode($cookie_content);
          }
        }
        
        $data['cookie'] = $cookie_content;
        $data['countryid'] = $country_id = $request->input('countryid');
        $data['code'] = $ifsc = $request->input('code');

        try{
            \Log::debug("search ifsc");
            \Log::debug(json_encode($request->all()));

            // $cookieFile = storage_path('jar.txt');
            // $cookieJar = new FileCookieJar($cookieFile);

            $client = new Client(['headers' => ['Content-type' => 'application/json']]);

            $result = $client->post(self::BASE_URL.'search/ifsc', [
                'form_params' => [
                    'countryid' => $country_id,
                    'code' => $ifsc,
                    'user_id' => $user_id,
                ],
                // 'cookies' => $cookieJar
            ]);
            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                if($content->status == 'success'){
                    addActivity('Singx successfull search using ifsc ', @$user_id,  @$data, @$content, $url);
                }else{
                    addActivity('Singx error while searching using ifsc', @$user_id,  @$data, @$content, $url);
                }
                return response()->json(['status' => $content->status, 'data' => $content->data, 'message'=> $content->message], 200);

            }
            addActivity('Singx error while searching using ifsc reson code', @$user_id,  @$data, ['code' => $code, 'reason' => $reason], $url);
            // Activity::log("Excepton occured while searching using ifsc (singx).", $user->id);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch(Exception $e){
            addActivity('Singx error while searching using ifsc', @$user_id,  @$data, @$e->getMessage(), $url);
            // Activity::log("Excepton occured while searching using ifsc (singx).", $user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function getListByCountryNWired(Request $request)
    {
        $url = self::MAIN_URL.'secure/Bank/findByCountryIdWiredTransferId';
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $data = [];
        
        $path = storage_path($user_id.'.txt');
        $pathe = str_replace('wlc', 'main', $path);
        $cookie_content = '';
        if(\File::exists($pathe)){
          $cookie_content = \File::get($pathe);
          if($cookie_content){
              $cookie_content = json_decode($cookie_content);
          }
        }
        
        $data['cookie'] = $cookie_content;
        
        $data['countryId'] = $countryId = $request->input('countryId');
        $data['wiredId'] = $wiredId = $request->input('wiredId');

        try{
            \Log::debug("get list by country n wired");
            \Log::debug(json_encode($request->all()));

            // $cookieFile = storage_path('jar.txt');
            // $cookieJar = new FileCookieJar($cookieFile);

            $client = new Client(['headers' => ['Content-type' => 'application/json']]);

            $result = $client->post(self::BASE_URL.'bank/list', [
                'form_params' => [
                    'countryId' => $countryId,
                    'wiredId' => $wiredId,
                    'user_id' => $user_id,
                ],
                // 'cookies' => $cookieJar
            ]);

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();

                $content = json_decode($body->getContents());
                if($content->status == 'success'){
                    addActivity('Singx fetched bank listing by country and wired', @$user_id,  @$data, @$content, $url);
                }else{
                    addActivity('Singx error while requesting bank listing by country and wired', @$user_id,  @$data, @$content, $url);
                }
                return response()->json(['status' => $content->status, 'data' => $content->data, 'message'=> $content->message], 200);

            }
            addActivity('Singx error while requesting bank listing by country and wired reason code', @$user_id,  @$data, ['code' => $code, 'reason' => $reason], $url);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch(Exception $e){
            addActivity('Singx error while requesting bank listing by country and wired', @$user_id,  @$data, @$e->getMessage(), $url);

            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }

    }

    public function getListByCountry(Request $request)
    {
        $url = self::MAIN_URL.'secure/Bank/findByCountryId';
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $data = [];
        
        $path = storage_path($user_id.'.txt');
        $pathe = str_replace('wlc', 'main', $path);
        $cookie_content = '';
        if(\File::exists($pathe)){
          $cookie_content = \File::get($pathe);
          if($cookie_content){
              $cookie_content = json_decode($cookie_content);
          }
        }
        
        $data['cookie'] = $cookie_content;
        
        $data['countryId'] = $countryId = $request->input('countryId');
        try{
            \Log::debug("get list by country");
            \Log::debug(json_encode($request->all()));

            // $cookieFile = storage_path('jar.txt');
            // $cookieJar = new FileCookieJar($cookieFile);

            $client = new Client(['headers' => ['Content-type' => 'application/json']]);

            $result = $client->post(self::BASE_URL.'bank/findByCountryId', [
                'form_params' => [
                    'countryId' => $countryId,
                    'user_id' => $user_id,
                ],
                // 'cookies' => $cookieJar
            ]);

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                if($content->status == 'success'){
                    addActivity('Singx fetched bank listing by country', @$user_id,  @$data, @$content, $url);
                }else{
                    addActivity('Singx error while requesting bank listing by country', @$user_id,  @$data, @$content, $url);
                }
                return response()->json(['status' => $content->status, 'data' => $content->data, 'message'=> $content->message], 200);

            }
            // Activity::log("Exception occured while requesting bank list by country (singx).", $user->id);
            addActivity('Singx error while requesting bank listing by country reason code', @$user_id,  @$data, ['code' => $code, 'reason' => $reason], $url);

            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch(Exception $e){
            addActivity('Singx error while requesting bank listing by country', @$user_id,  @$data, @$e->getMessage(), $url);

            // Activity::log("Exception occured while requesting bank list by country (singx).", $user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }

    }

    public function getBranchListByBank(Request $request)
    {
        $url = self::MAIN_URL.'secure/BankBranch/findByBankId';
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $data = [];
        
        $path = storage_path($user_id.'.txt');
        $pathe = str_replace('wlc', 'main', $path);
        $cookie_content = '';
        if(\File::exists($pathe)){
          $cookie_content = \File::get($pathe);
          if($cookie_content){
              $cookie_content = json_decode($cookie_content);
          }
        }
        
        $data['cookie'] = $cookie_content;
        $data['bankId'] = $bankId = $request->input('bankId');

        try{
            \Log::debug("get branch by bank");
            \Log::debug(json_encode($request->all()));

            // $cookieFile = storage_path('jar.txt');
            // $cookieJar = new FileCookieJar($cookieFile);

            $client = new Client(['headers' => ['Content-type' => 'application/json']]);

            $result = $client->post(self::BASE_URL.'list/branch', [
                'form_params' => [
                    'bankId' => $bankId,
                    'user_id' => $user_id,
                ],
                // 'cookies' => $cookieJar
            ]);

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                if($content->status == 'success'){
                    addActivity('Singx fetched branch listing', @$user_id,  @$data, @$content, $url);
                }else{
                    addActivity('Singx error while requesting branch listing', @$user_id,  @$data, @$content, $url);
                }
                return response()->json(['status' => $content->status, 'data' => $content->data, 'message'=> $content->message], 200);

            }
            // Activity::log("Exception occured while requesting branch listing (singx).", $user->id);
            addActivity('Singx error while requesting branch listing reason code', @$user_id,  @$data, ['code' => $code, 'reason' => $reason], $url);

            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            // Activity::log("Exception occured while requesting branch listing (singx).", $user->id);
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            addActivity('Singx error while requesting branch listing', @$user_id,  @$data, @$jsonBody, $url);

            return response()->json(['status' => 'error', 'data' => $jsonBody->errors, 'message'=> $jsonBody->message], 200);
        }catch(GuzzleException $e){
            // Activity::log("Exception occured while requesting branch listing (singx).", $user->id);
            addActivity('Singx error while requesting branch listing', @$user_id,  @$data, @$e->getMessage(), $url);

            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            // Activity::log("Exception occured while requesting branch listing (singx).", $user->id);
            addActivity('Singx error while requesting branch listing', @$user_id,  @$data, @$e->getMessage(), $url);

            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }

    }

    public function listPurpose(Request $request)
    {
        $url = self::MAIN_URL.'secure/TransferPurpose/findAll';
        $user = JWTAuth::toUser($request->input('token'));
        $user_id =$user->id;
        $data = [];
        
        $path = storage_path($user_id.'.txt');
        $pathe = str_replace('wlc', 'main', $path);
        $cookie_content = '';
        if(\File::exists($pathe)){
          $cookie_content = \File::get($pathe);
          if($cookie_content){
              $cookie_content = json_decode($cookie_content);
          }
        }
        
        $data['cookie'] = $cookie_content;
        try{
            \Log::debug("list purpose");
            \Log::debug(json_encode($request->all()));
            // $cookieFile = storage_path('jar.txt');
            // $cookieJar = new FileCookieJar($cookieFile);

            $client = new Client(['headers' => ['Content-type' => 'application/json']]);

            $result = $client->post(self::BASE_URL.'transaction/purpose', [
                'form_params' => [
                    'user_id' => $user_id
                ],
            ]);

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                if($content->status == 'success'){
                    addActivity('Singx fetched purpose list', @$user_id,  @$data, @$content, $url);
                }else{
                    addActivity('Singx error while requesting purpose listing', @$user_id,  @$data, @$content, $url);
                }
                return response()->json(['status' => $content->status, 'data' => $content->data, 'message'=> $content->message], 200);

                // if($content->response->success){
                //     $cont = $content->response->data;
                //     return response()->json(['status' => 'success','data' => $cont, 'message'=> 'DATA'], 200);
                // }else{
                //     $cont = $content->response->message;
                //     return response()->json(['status' => 'error','data' => $content, 'message'=> 'EXCEPTION_OCCURED'], 200);
                // }
            }
            // Activity::log("Exception occured while requesting puspose listing (singx).", $user->id);
            addActivity('Singx error while requesting purpose listing reason code', @$user_id,  @$data, ['code' => $code, 'reason' => $reason], $url);

            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch(Exception $e){
            addActivity('Singx error while requesting purpose listing', @$user_id,  @$data, @$e->getMessage(), $url);

            // Activity::log("Exception occured while requesting puspose listing (singx).", $user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }

    }
    
    public function listRelationship(Request $request)
    {
        $url = self::MAIN_URL.'secure/Relationship/findAll';
        $user = JWTAuth::toUser($request->input('token'));
        $user_id =$user->id;
        $data = [];
        
        $path = storage_path($user_id.'.txt');
        $pathe = str_replace('wlc', 'main', $path);
        $cookie_content = '';
        if(\File::exists($pathe)){
          $cookie_content = \File::get($pathe);
          if($cookie_content){
              $cookie_content = json_decode($cookie_content);
          }
        }
        
        $data['cookie'] = $cookie_content;
        try{
            // \Log::debug("list relationship");
            // \Log::debug(json_encode($request->all()));
            // $cookieFile = storage_path('jar.txt');
            // $cookieJar = new FileCookieJar($cookieFile);

            $client = new Client(['headers' => ['Content-type' => 'application/json']]);

            $result = $client->post(self::BASE_URL.'transaction/relationship/list', [
                'form_params' => [
                    'user_id' => $user_id
                ],
            ]);

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                if($content->status == 'success'){
                  if(false && @$content->data->response->success == false){
                    $msg = @$content->data->response->message;
                    $diagnosis = @$content->data->response->diagnosis;
                    $solution = @$content->data->response->solution;
                    addActivity('Singx error while fetching relationship listing #Message - '.$msg.' Diagnosis - '.$diagnosis.' Solution - '.$solution, @$user_id,  @$data, $content, $url);
                    return response()->json(['status' => 'error', 'data' => $msg, 'message'=> $msg], 200);

                  }else{
                    addActivity('Singx fetched relationship list', @$user_id,  @$data, @$content, $url);
                  }
                }else{
                    $msg = @$content->data->response->message;
                    $diagnosis = @$content->data->response->diagnosis;
                    $solution = @$content->data->response->solution;
                    addActivity('Singx error while fetching relationship listing #Message - '.$msg.' Diagnosis - '.$diagnosis.' Solution - '.$solution, @$user_id,  @$data, @$content, $url);
                }
                return response()->json(['status' => $content->status, 'data' => $content->data, 'message'=> $content->message], 200);
            }
            addActivity('Singx error while requesting relationship listing reason code', @$user_id,  @$data, ['code' => $code, 'reason' => $reason], $url);

            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch(Exception $e){
            addActivity('Singx error while requesting relationship listing', @$user_id,  @$data, @$e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }

    }
    
    public function generateOtpTxn(Request $request)
    {
        $url = self::MAIN_URL.'secure/TransactionInfo/getOtp';
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $data = [];
        $path = storage_path($user_id.'.txt');
        $pathe = str_replace('wlc', 'main', $path);
        $cookie_content = '';
        if(\File::exists($pathe)){
          $cookie_content = \File::get($pathe);
          if($cookie_content){
              $cookie_content = json_decode($cookie_content);
          }
        }
        
        $data['cookie'] = $cookie_content;
        try{
            \Log::debug("generate txn otp");
            \Log::debug(json_encode($request->all()));

            // return response()->json(['status' => 'success', 'data' => '', 'message'=> 'DATA'], 200);

            // $cookieFile = storage_path('jar.txt');
            // $cookieJar = new FileCookieJar($cookieFile);

            $client = new Client(['headers' => ['Content-type' => 'application/json']]);

            $result = $client->post(self::BASE_URL.'transaction/otp', [
                'form_params' => [
                    'user_id' => $user_id
                ],
            ]);

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                // Activity::log("Requested transaction otp (singx).", $user->id);
                $content = json_decode($body->getContents());
                if($content->status == 'success'){
                    addActivity('Singx requested transaction otp', @$user_id,  @$data, @$content, $url);
                }else{
                    addActivity('Singx error while requesting transaction otp', @$user_id,  @$data, @$content, $url);
                }
                return response()->json(['status' => $content->status, 'data' => $content->data, 'message'=> $content->message], 200);

            }
            addActivity('Singx error while requesting transaction otp reason code', @$user_id,  @$data, ['code' => $code, 'reason' => $reason], $url);
            // Activity::log("Exception occured while requesting transaction otp (singx).", $user->id);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch(Exception $e){
            addActivity('Singx error while requesting otp', @$user_id,  @$data, @$e->getMessage(), $url);

            // Activity::log("Exception occured while requesting transaction otp (singx).", $user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }

    }

    public function udpateEnquiry(Request $request)
    {
        $url = self::MAIN_URL.'secure/TransferEnquiry/save';
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $data = [];
        
        $path = storage_path($user_id.'.txt');
        $pathe = str_replace('wlc', 'main', $path);
        $cookie_content = '';
        if(\File::exists($pathe)){
          $cookie_content = \File::get($pathe);
          if($cookie_content){
              $cookie_content = json_decode($cookie_content);
          }
        }
        
        $data['cookie'] = $cookie_content;
        $data['sendAmount'] = $sendAmount = $request->input('sendAmount');
        $data['receivedAmount'] = $receivedAmount = $request->input('receivedAmount');
        $data['feeType'] = $feeType = $request->input('feeType');
        $data['exchangeRate'] = $exchangeRate = $request->input('exchangeRate');
        $data['singxFee'] = $singxFee = $request->input('singxFee');
        $data['totalPayable'] = $totalPayable = $request->input('totalPayable');
        $data['fromCurrencyCode'] = $fromCurrencyCode = $request->input('fromCurrencyCode');
        $data['toCurrencyCode'] = $toCurrencyCode = $request->input('toCurrencyCode');
        $data['fromCountryId'] = $fromCountryId = $request->input('fromCountryId');
        $data['toCountryId'] = $toCountryId = $request->input('toCountryId');
        $data['corridorId'] = $corridorId = $request->input('corridorId');
        $data['oneTimePassword'] = $oneTimePassword = $request->input('oneTimePassword');
        $data['validityPeriod'] = $validityPeriod = $request->input('validityPeriod');
        $data['savings'] = $savings = $request->input('savings');
        $data['enquiryDT'] = $enquiryDT = $request->input('enquiryDT');
        $data['contactId'] = $contactId = $request->input('contactId');
        $data['customerId'] = $customerId = $request->input('customerId');

        try{
            // $cookieFile = storage_path('jar.txt');
            // $cookieJar = new FileCookieJar($cookieFile);
            $client = new Client(['headers' => ['Content-type' => 'application/json']]);

            \Log::debug('Enquiry request');
            \Log::debug(json_encode($request->all()));
            $result = $client->post(self::BASE_URL.'update/enquiry', [
                'form_params' => [
                    'sendAmount' => $sendAmount,
                    'receivedAmount' => $receivedAmount,
                    'feeType' => $feeType,
                    'exchangeRate' => $exchangeRate,
                    'singxFee' => $singxFee,
                    'totalPayable' => $totalPayable,
                    'fromCurrencyCode' => $fromCurrencyCode,
                    'toCurrencyCode' => $toCurrencyCode,
                    'fromCountryId' => $fromCountryId,
                    'toCountryId' => $toCountryId,
                    'corridorId' => $corridorId,
                    'oneTimePassword' => $oneTimePassword,
                    'validityPeriod' => $validityPeriod,
                    'savings' => $savings,
                    'enquiryDT' => $enquiryDT,
                    'contactId' => $contactId,
                    'customerId' => $customerId,
                    'user_id'   => $user_id
                ]
                // 'cookies' => $cookieJar
            ]);

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if(in_array($code, ['200', '201']) && in_array($reason, ['OK', 'Created']) ){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                if($content->status == 'success'){
                    addActivity('Singx requested to update enquiry', @$user_id,  @$data, @$content, $url);
                }else{
                    addActivity('Singx error while updating enquiry', @$user_id,  @$data, @$content, $url);
                }
                return response()->json(['status' => $content->status, 'data' => $content->data, 'message'=> $content->message], 200);

            }
            addActivity('Singx error while updating enquiry reason code', @$user_id,  @$data, ['code' => $code, 'reason' => $reason], $url);
            // Activity::log("Exception occured while updating enquiry (singx).", $user->id);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            // Activity::log("Exception occured while updating enquiry (singx).", $user->id);
            addActivity('Singx error while updating enquiry', @$user_id,  @$data, @$jsonBody, $url);

            return response()->json(['status' => 'error', 'data' => $jsonBody->errors, 'message'=> $jsonBody->message], 200);
        }catch(GuzzleException $e){
            // Activity::log("Exception occured while updating enquiry (singx).", $user->id);
            addActivity('Singx error while updating enquiry', @$user_id,  @$data, @$e->getMessage(), $url);

            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            // Activity::log("Exception occured while updating enquiry (singx).", $user->id);
            addActivity('Singx error while updating enquiry', @$user_id,  @$data, @$e->getMessage(), $url);

            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function updateReceiver(Request $request)
    {
        $url = self::MAIN_URL.'secure/FundReceiver/save';
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $data = [];
        
        $path = storage_path($user_id.'.txt');
        $pathe = str_replace('wlc', 'main', $path);
        $cookie_content = '';
        if(\File::exists($pathe)){
          $cookie_content = \File::get($pathe);
          if($cookie_content){
              $cookie_content = json_decode($cookie_content);
          }
        }
        
        $data['cookie'] = $cookie_content;
        $data['enquiryId'] = $enquiryId = $request->input('enquiryId');
        $data['receiverId'] = $receiverId = $request->input('receiverId');
        $data['transferPurposeId'] = $transferPurposeId = $request->input('transferPurposeId');
        $data['transferRemark'] = $transferRemark = $request->input('transferRemark');

        try{
            \Log::debug("update reciver");
            \Log::debug(json_encode($request->all()));

            // $cookieFile = storage_path('jar.txt');
            // $cookieJar = new FileCookieJar($cookieFile);

            $client = new Client(['headers' => ['Content-type' => 'application/json']]);

            $result = $client->post(self::BASE_URL.'transaction/receiver', [
                'form_params' => [
                    'enquiryId' => $enquiryId,
                    'receiverId' => $receiverId,
                    'transferPurposeId' => $transferPurposeId,
                    'transferRemark' => $transferRemark,
                    'user_id'   => $user_id,
                ],
                // 'cookies' => $cookieJar
            ]);

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                if($content->status == 'success'){
                    addActivity('Singx updated receiver table successfully', @$user_id,  @$data, @$content, $url);
                }else{
                    addActivity('Singx error while updating receiver table', @$user_id,  @$data, @$content, $url);
                }
                return response()->json(['status' => $content->status, 'data' => $content->data, 'message'=> $content->message], 200);

            }
            // Activity::log("Exception occured while updating receiver (singx).", $user->id);
            addActivity('Singx error while updating receiver table reason code', @$user_id,  @$data, ['code' => $code, 'reason' => $reason], $url);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            // Activity::log("Exception occured while updating receiver (singx).", $user->id);
            addActivity('Singx error while updating receiver table', @$user_id,  @$data, @$jsonBody(), $url);
            return response()->json(['status' => 'error', 'data' => $jsonBody->errors, 'message'=> $jsonBody->message], 200);
        }catch(GuzzleException $e){
            // Activity::log("Exception occured while updating receiver (singx).", $user->id);
            addActivity('Singx error while updating receiver table', @$user_id,  @$data, @$e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            // Activity::log("Exception occured while updating receiver (singx).", $user->id);
            addActivity('Singx error while updating receiver table', @$user_id,  @$data, @$e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function createTransaction(Request $request)
    {
        $url = self::MAIN_URL.'secure/TransactionInfo/save';
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $data = [];
        
        $path = storage_path($user_id.'.txt');
        $pathe = str_replace('wlc', 'main', $path);
        $cookie_content = '';
        if(\File::exists($pathe)){
          $cookie_content = \File::get($pathe);
          if($cookie_content){
              $cookie_content = json_decode($cookie_content);
          }
        }
        
        $data['cookie'] = $cookie_content;
        $data['transactionDT'] = $transactionDT = $request->input('transactionDT');
        $data['enquiryId'] = $enquiryId = $request->input('enquiryId');
        $data['receiverId'] = $receiverId = $request->input('receiverId');
        $data['receiverBankId'] = $receiverBankId = $request->input('receiverBankId');
        $data['receiverCountryId'] = $receiverCountryId = $request->input('receiverCountryId');
        $data['accountNumber'] = $accountNumber = $request->input('accountNumber');
        $data['accountMapId'] = $accountMapId = $request->input('accountMapId');
        $data['activityId'] = $activityId = $request->input('activityId');
        $data['userTxnId'] = $userTxnId = $request->input('userTxnId');
        $data['stageId'] = $stageId = $request->input('stageId');
        $data['statusId'] = $statusId = $request->input('statusId');
        $data['receiverRelationship'] = $receiverRelationship = $request->input('receiverRelationship');

        $data['sendAmount'] = $sent_amount = $request->input('sendAmount');
        $data['singxFee'] = $singx_fee = $request->input('singxFee');
        $data['exchangeRate'] = $exchange_rate = $request->input('exchangeRate');
        $data['receivedAmount'] = $received_amount = $request->input('receivedAmount');

        try{
            \Log::debug("create txn");
            \Log::debug(json_encode($request->all()));
            // $cookieFile = storage_path('jar.txt');
            // $cookieJar = new FileCookieJar($cookieFile);

            $client = new Client(['headers' => ['Content-type' => 'application/json']]);

            $result = $client->post(self::BASE_URL.'transaction/create', [
                'form_params' => [
                    'transactionDT' => $transactionDT,
                    'enquiryId' => $enquiryId,
                    'receiverId' => $receiverId,
                    'receiverBankId' => $receiverBankId,
                    'receiverCountryId' => $receiverCountryId,
                    'accountNumber' => $accountNumber,
                    'accountMapId' => $accountMapId,
                    'activityId' => $activityId,
                    'userTxnId' => $userTxnId,
                    'stageId' => $stageId,
                    'statusId' => $statusId,
                    'receiverRelationship' => $receiverRelationship,
                    'user_id'   => $user_id,
                ],
                // 'cookies' => $cookieJar
            ]);

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                // Activity::log("Created a successfull transaction (singx).", $user->id);
                $content = json_decode($body->getContents());
                if($content->status == 'success' && (!isset($content->data->response) && @$content->data->response->success != false)){
                    addActivity('Singx remitted successfully', @$user_id,  @$data, @$content, $url);
                    $x = (array)$content->data;
                    $x['user_id'] = $user->id;
                    $x['transaction_amount'] = $sent_amount+$singx_fee;
                    $x['sent_amount'] = $sent_amount;
                    $x['singx_fee'] = $singx_fee;
                    $x['received_amount'] = $received_amount;
                    $x['exchange_rate'] = $exchange_rate;
                    $share_percent = getOption('singx_charges', 0);//myma share
                    $gst = getOption('gst_tax', 7);//myma share
                    $myma_part = ($singx_fee*$share_percent)/100;
                    $gst_tax = ($myma_part*$gst/100);

                    $singx_part = $singx_fee-$myma_part;
                    $x['myma_part'] = $myma_part;
                    $x['singx_part'] = $singx_part-$gst_tax;
                    $x['gst_tax'] = $gst_tax;
                    $x['response'] = json_encode($content->data);
                    Singx::create($x);

                }else{
                    addActivity('Singx error while creating transaction', @$user_id,  @$data, @$content, $url);
                }
                \Log::debug('Create txn response');
                \Log::debug(json_encode($content));

                return response()->json(['status' => $content->status, 'data' => $content->data, 'message'=> $content->message], 200);

            }
            addActivity('Singx error while creating transaction reason code', @$user_id,  @$data, ['code' => $code, 'reason' => $reason], $url);
            // Activity::log("Exception occured while creating transaction (singx).", $user->id);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            // Activity::log("Exception occured while creating transaction (singx).", $user->id);
            addActivity('Singx error while creating transaction', @$user_id,  @$data, @$jsonBody, $url);
            return response()->json(['status' => 'error', 'data' => $jsonBody->errors, 'message'=> $jsonBody->message], 200);
        }catch(GuzzleException $e){
            // Activity::log("Exception occured while creating transaction (singx).", $user->id);
            addActivity('Singx error while creating transaction', @$user_id,  @$data, @$e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            // Activity::log("Exception occured while creating transaction (singx).", $user->id);
            addActivity('Singx error while creating transaction', @$user_id,  @$data, @$e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function listTransaction(Request $request)
    {
        $url = self::MAIN_URL.'secure/queryExecutor/getData';
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $dat = $data = [];
        
        $path = storage_path($user_id.'.txt');
        $pathe = str_replace('wlc', 'main', $path);
        $cookie_content = '';
        if(\File::exists($pathe)){
          $cookie_content = \File::get($pathe);
          if($cookie_content){
              $cookie_content = json_decode($cookie_content);
          }
        }
        
        $data['cookie'] = $cookie_content;
        $data['customerId'] = $dat['customerId'] = $request->customerId;
        $data['limit'] = $dat['limit'] = $request->limit;
        $data['page_no'] = $dat['page_no'] = $request->page_no;
        $data['user_id'] = $dat['user_id'] = $user_id;

        try{
            \Log::debug("list transaction");
            \Log::debug(json_encode($request->all()));


            // $cookieFile = storage_path('jar.txt');
            // $cookieJar = new FileCookieJar($cookieFile);
            $client = new Client(['headers' => ['Content-type' => 'application/json']]);

            $result = $client->post(self::BASE_URL.'transaction/list', [
                'form_params' => $dat
            ]);

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                // Activity::log("Accessed transaction listing (singx).", $user->id);
                $content = json_decode($body->getContents());
                if($content->status == "success"){
                  addActivity('Singx fetched transaction listing', @$user_id,  @$data, @$content, $url);
                }else{
                  addActivity('Singx error while listing transaction', @$user_id,  @$data, @$content, $url);
                }

                return response()->json(['status' => $content->status, 'data' => $content->data, 'total' => $content->total, 'message'=> $content->message], 200);

            }
            addActivity('Singx error while listing transaction reason code', @$user_id,  @$data, ["code" => $code, "reason" => $reason], $url);
            // Activity::log("Exception occured while accessing transaction listing (singx).", $user->id);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            addActivity('Singx error while listing transaction', @$user_id,  @$data, @$jsonBody, $url);
            // Activity::log("Exception occured while accessing transaction listing (singx).", $user->id);
            return response()->json(['status' => 'error', 'data' => $jsonBody->errors, 'message'=> $jsonBody->message], 200);
        }catch(GuzzleException $e){
            addActivity('Singx error while listing transaction', @$user_id,  @$data, @$e->getMessage(), $url);
            // Activity::log("Exception occured while accessing transaction listing (singx).", $user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            addActivity('Singx error while listing transaction', @$user_id,  @$data, @$e->getMessage(), $url);
            // Activity::log("Exception occured while accessing transaction listing (singx).", $user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

}
