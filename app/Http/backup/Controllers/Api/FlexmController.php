<?php

namespace App\Http\Controllers\Api;

use App\User;
use Illuminate\Http\Request;
use App\Events\NotifyFlexmRegistration;

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
use Auth, JWTAuth, Activity;
use GuzzleHttp\Exception\BadResponseException;
use App\Models\SpuulPlan;
use App\Models\Merchant;
use App\Models\Course;
use App\Models\FlexmDoc;
use App\Models\Activity as LogActivity;
use Carbon\Carbon;

class FlexmController extends Controller
{
    // const BASE_URL = 'https://alpha.flexm.sg/api/';
    const BASE_URL = 'https://test-api.flexm.sg/';

    const KIWIRE = 'https://118.189.171.212:10100/';
    const MYMA_URL = 'https://myhype.space/main/public/api/v1/';

    public function kiwire(Request $request)
    {
        try{
          // "device_id":"d5977d81aabf4862","ip":"122.176.82.110"
            $client = new Client(['verify' => false]);

            $data = [];
            $result = $client->post(self::KIWIRE.'admin/agent/api_login.php', [
              'form_params' => [
                'code' => md5('apiuser|apiuser'),
                'cloud_id' => 'WEJP',
              ]
            ]);
            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                if($content->status == 'success'){
                  $result = $client->post(self::KIWIRE.'apiv2/login/online', [
                    'form_params' => [
                      'api_key' => '979e1ac9-c7bf-4e53-ac91-fd541fa86e08',
                      'cloud_id' => 'WEJP',
                      'token' => $content->token,
                      'nas-id' => 'WEJP-MK1',
                      'device' => $request->input('device_id'),//mac address of the device
                      'ip'  => $request->input('ip'),// ip address of the device
                      'user'  => 'apiuser'
                    ]
                  ]);
                  $code = $result->getStatusCode(); // 200
                  $reason = $result->getReasonPhrase(); // OK
                  if($code == "200" && $reason == "OK"){
                      $body = $result->getBody();
                      $content = json_decode($body->getContents());
                      return response()->json(['status' => 'success', 'data' => $content, 'message'=> @$content->result_message], 200);
                      if(@$content->is_success == 'true'){
                        return response()->json(['status' => 'success', 'data' => '', 'message'=> $content->result_message], 200);
                      }else{
                        if(isset($content->error)){
                          return response()->json(['status' => 'error', 'data' => '', 'message'=> $content->error], 200);
                        }else{
                          return response()->json(['status' => 'error', 'data' => '', 'message'=> @$content->result_message], 200);
                        }
                      }
                  }

                }else{
                  return response()->json(['status' => 'error', 'data' => '', 'message'=> 'Something went wrong'], 200);
                }
            }else{
                return response()->json(['status' => 'error', 'data' => '', 'message'=> 'Something went wrong'], 200);
            }

            return response()->json(['status' => 'error', 'data' => '', 'message'=> 'Something went wrong'], 200);

        }catch (BadResponseException $ex) {
              $response = $ex->getResponse();
              $jsonBody = json_decode((string) $response->getBody());
              return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
              return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
              return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    // public function kiwire(Request $request)
    // {
    //     try{
    //         $client = new Client();
    //         $data = [];
    //         $result = $client->post(self::MYMA_URL.'kiwire/login', [
    //           'form_params' => [
    //             'device' => $request->input('device'),
    //             'ip' => $request->input('ip'),
    //           ]
    //         ]);
    //         $code = $result->getStatusCode(); // 200
    //         $reason = $result->getReasonPhrase(); // OK
    //         if($code == "200" && $reason == "OK"){
    //             $body = $result->getBody();
    //             $content = json_decode($body->getContents());
    //
    //             return response()->json(['status' => $content->status, 'data' => $content->data, 'message'=> $content->message], 200);
    //
    //         }
    //         return response()->json(['status' => 'error', 'data' => '', 'message'=> 'Something went wrong'], 200);
    //
    //     }catch (BadResponseException $ex) {
    //         $response = $ex->getResponse();
    //         $jsonBody = json_decode((string) $response->getBody());
    //         return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
    //     }catch(GuzzleException $e){
    //         return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
    //     }catch(Exception $e){
    //         return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
    //     }
    // }

    public function options(Request $request)
    {
        try{

            $client = new Client();
            $data = [];
            $result = $client->get(self::BASE_URL.'user/enumerations/genders');
            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                $data[] = $content->data;
            }else{
                $data['genders'] = [];
            }

            $result = $client->get(self::BASE_URL.'user/enumerations/nationalities');
            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                $data[] = $content->data;
            }else{
                $data['nationalities'] = [];
            }

            $result = $client->get(self::BASE_URL.'user/enumerations/titles');
            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                $data[] = $content->data;
            }else{
                $data['titles'] = [];
            }
            return response()->json(['status' => 'success', 'data' => $data, 'message'=> $content->message], 200);

        }catch(Exception $e){
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function register(Request $request)
    {
      \Log::debug("Start Registration");
      \Log::debug($request->all());
        $date = Carbon::now()->format('Ymd');

        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $mobile_no = $request->input('mobile');

        $profile = @$user->profile;
        if($profile){
          if($profile->street_address == ""){
            return response()->json(['status' => 'error', 'data' => '', 'message'=> 'You need to update street address in your profile to proceed.'], 200);
          }elseif($profile->zip_code == ""){
            return response()->json(['status' => 'error', 'data' => '', 'message'=> 'You need to update zip_code in your profile to proceed.'], 200);
          }
        }

        try{
            $name = explode(' ', $request->input('full_name'));

            if(count($name) < 2){
              return response()->json(['status' => 'error', 'data' => '', 'message'=> 'Full Name must have at least one space. Or Full Name must have at least two words'], 200);
            }
            $data['preferred_name'] = $request->input('preferred_name');
            $data['full_name'] = $request->input('full_name');
            $data['mobile_country_code'] = $request->input('mobile_country_code');
            $data['mobile'] = $request->input('mobile');
            $data['password'] = $request->input('password');
            $data['password_confirmation'] = $request->input('password_confirmation');
            $data['id_type'] = $request->input('id_type');
            $data['id_type_number'] = $request->input('id_type_number');
            $data['birthday'] = $request->input('birthday');

            $data['device_signature'] = '{'.$request->input('device_signature')+$date.'}';
            $data['wallet_type_indicator'] = "centurion";
            // $data['address_1'] = $request->input('address_1');
            // $data['address_2'] = $request->input('address_2');
            // $data['city'] = $request->input('city');
            // $data['state'] = $request->input('state');
            // $data['country'] = $request->input('country');
            // $data['zipcode'] = $request->input('zipcode');

            // \Log::debug("input");
            // \Log::debug(json_encode($data));

            $client = new Client(['headers' => ['Content-type' => 'application/json']]);

            $result = $client->post(self::BASE_URL.'user/register', [
                'form_params' => $data,
            ]);

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK

            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();

                $content = json_decode($body->getContents());
                if($content->success){
                    $msg = $content->message;
                    $cont = $content->data;
                    \Log::debug('registration success');
                    \Log::debug(json_encode($cont));
                    $user->update(['flexm_account' => '1']);
                    addActivity('Flexm account registered successfully - '.$mobile_no, $user_id, $data, $content);

                    // Activity::log('Flexm account registered successfully #'.@$data['mobile'], @$user->id);
                    if($cont->token){
                        $flexm_token = $cont->token;
                        $data['id_date_expiry'] = @$user->profile->wp_expiry;
                        $profile_created = $this->createProfile($flexm_token, $data);
                        \Log::debug("Profile Created");
                        \Log::debug(json_encode($profile_created));

                        if($user->profile){
                          $dta = [];
                          $dta['address_1'] = '';
                          $dorm = @$user->profile->dormitory->name;
                          if($dorm){
                              $dta['address_1'] = $dorm;
                          }
                          if(@$user->profile->street_address){
                            $dta['address_1'] .= ' '.$user->profile->street_address;
                          }
                          if(@$user->profile->block){
                            $dta['address_1'] .= ' Block #'.$user->profile->block;
                          }
                          if(@$user->profile->sub_block){
                            $dta['address_1'] .= ' Sub-Block #'.$user->profile->sub_block;
                          }

                          $dta['address_2'] = '';
                          if(@$user->profile->unit_no){
                            $dta['address_2'] .= ' UN #'.$user->profile->unit_no;
                          }
                          if(@$user->profile->floor_no){
                            $dta['address_2'] .= ' FN #'.$user->profile->floor_no;
                          }
                          if(@$user->profile->room_no){
                            $dta['address_2'] .= ' RN #'.$user->profile->room_no;
                          }

                          $dta['city'] = 'singapore';
                          $dta['state'] = 'singapore';
                          $dta['country'] = 'Singapore';
                          $dta['zipcode'] = (@$user->profile->zip_code == '')?0:@$user->profile->zip_code;
                          $save_add = $this->saveAdd($flexm_token, $dta);
                           \Log::debug("Save Billing Address");
                           \Log::debug(json_encode($save_add));
                          $dta['address_type'] = 'residential';
                          $save_add = $this->saveAdd($flexm_token, $dta);
                          \Log::debug("Save Residential Address");
                          \Log::debug(json_encode($save_add));
                        }
                        $front_image = $request->input('front_of_id_image');
                        $back_image = $request->input('back_of_id_image');
                        $residential_image = $request->input('residential_image');
                        if($front_image != ''){
                          $d['phone_no'] = $data['mobile'];
                          $d['document'] = $front_image;
                          $d['verified'] = '0';
                          FlexmDoc::create($d);
                          $front_return = $this->uploadDocImage($flexm_token, $front_image);
                          \Log::debug("Front Image upload");
                          \Log::debug($front_image);
                          \Log::debug(json_encode($front_return));
                        }
                        if($back_image != ''){
                          $d['phone_no'] = $data['mobile'];
                          $d['document'] = $back_image;
                          $d['verified'] = '0';
                          FlexmDoc::create($d);
                          $front_return = $this->uploadDocImage($flexm_token, $back_image);
                          \Log::debug("Back Image upload");
                          \Log::debug($back_image);
                          \Log::debug(json_encode($front_return));
                        }
                        if($residential_image != ''){
                          $d['phone_no'] = $data['mobile'];
                          $d['document'] = $residential_image;
                          $d['verified'] = '0';
                          FlexmDoc::create($d);
                          $front_return = $this->uploadDocImage($flexm_token, $residential_image);
                          \Log::debug("Residential Image upload");
                          \Log::debug($residential_image);
                          \Log::debug(json_encode($front_return));
                        }

                    }
                    // $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);
                    // $result = $client->get(self::BASE_URL.'user/otp/request');
                    // $code = $result->getStatusCode(); // 200
                    // $reason = $result->getReasonPhrase(); // OK
                    event(new NotifyFlexmRegistration($user->id));

                    \Log::debug("complete register");
                    //$msg
                    return response()->json(['status' => 'success', 'data' => $cont, 'message'=> 'You have successfully signed up. Wait for approval.'], 200);
                }else{
                    \Log::debug("error else");
                    // Activity::log('Flexm error while registration -'.json_encode($content), @$user->id);
                    addActivity('Flexm error in registration - '.$mobile_no, $user_id, @$data, @$content);
                    \Log::debug(json_encode($content));
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            \Log::debug("error");
            \Log::debug(json_encode($reason));
            addActivity('Flexm error in registration because reason and code did not match - '.$mobile_no, $user_id, $data, ['code' => $code, 'reason' => $reason]);
            // Activity::log('Flexm error while registration -'.json_encode(@$reason), @$user->id);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());

            $msg = '';
            foreach(@$jsonBody->errors as $arr){
              $msg = is_array($arr)?$arr[0]:$arr;
              break;
            }
            if($msg == ''){
              $msg = @$jsonBody->message;
            }
            \Log::debug("registration error");
            \Log::debug(json_encode($jsonBody));
            if($msg != 'Mobile number is already registered.'){
                // Activity::log('Flexm error while registration -'.@$msg, @$user->id);
            }
            addActivity('Flexm error in registration - '.@$mobile_no, @$user_id, @$data, @$jsonBody);

            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> $msg], 200);
        }catch(GuzzleException $e){
            addActivity('Flexm error in registration - '.@$mobile_no, @$user_id, @$data, @$e->getMessage());

            // Activity::log('Flexm error while registration -'.$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            addActivity('Flexm error in registration - '.@$mobile_no, @$user_id, @$data, @$e->getMessage());

            // Activity::log('Flexm error while registration -'.$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }


    public function login(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        try{

            $data['mobile_country_code'] = $request->input('mobile_country_code');
            $data['mobile'] = $request->input('mobile');
            $data['password'] = trim($request->input('password'));
            $data['user_type'] = 2;//web app user
            $data['device_signature'] = "{$request->input('device_signature')}";//$request->input('device_signature');

            $type = $request->input('type');
            $id = $request->input('id');

            $user_id = $user->id;
            $mobile_no = $data['mobile'];

            $other_share_per = 0.75;
            if($type == 'course' && $id != ''){
              $item = Course::find($id);
              if($item){
                if($item->type == 'free' || ($item->type == 'paid' && $item->fee == 0)){
                  return response()->json(['status' => 'error', 'data' => '', 'message'=> 'No need to make payment for free course.'], 200);
                }else{
                  $amount = $item->fee;
                }
              }else{
                return response()->json(['status' => 'error', 'data' => '', 'message'=> 'Course does not exist.'], 200);
                //course does not exist
              }
              $vendor_id = $item->vendor_id;
              $merchant = Merchant::where('user_id', $vendor_id)->first();
              if($merchant){
                $other_share_per = $merchant->myma_transaction_share;
              }

            }
            elseif($type == 'spuul' && $id != ''){
              //merchant created already as it will be one only
              $plan = SpuulPlan::find($id);
              if($plan){
                $amount = $plan->price;
              }else{
                return response()->json(['status' => 'error', 'data' => '', 'message'=> 'Plan does not exist.'], 200);
              }
              $vendor_id = 2;
              $merchant = Merchant::find($vendor_id);
              if($merchant){
                $other_share_per = $merchant->myma_transaction_share;
              }
            }

            $client = new Client(['headers' => ['Content-type' => 'application/json']]);

            $result = $client->post(self::BASE_URL.'auth/login', [
                'json' => $data,
            ]);

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                if($content->success){
                    $user->update(['flexm_account' => '1']);
                    $msg = $content->message;
                    $cont = $content->data;
                    $flexm_token = $cont->token;

                    if($flexm_token && strtolower(@$cont->user->profile->document_approval_status) != 'approved'){
                      $res = $this->checkVerified($flexm_token, $mobile_no, $user_id);
                      $type = 2;
                      if(@$cont->user->profile->id_type == 'epfin'){
                        $type = 3;
                      }
                      if($res){
                        $res = json_decode($res);
                        if($res->status == 'error' && $res->message == 'You are not authorized to access this page.'){
                          addActivity('Flexm User\'s cant login as his phone number is not verified -'.$mobile_no, $user_id, $data, $content);
                          return response()->json(['status' => "error", 'data' => 'verify_number', 'message'=> 'Phone number is not verified.', 'flexm_token' => $flexm_token, 'type' => $type], 200);
                        }
                      }
                    }
                    if(strtolower(@$cont->user->profile->document_approval_status) == 'pending'){
                      $type = 2;
                      if(@$cont->user->profile->id_type == 'epfin'){
                        $type = 3;
                      }
                      addActivity('Flexm user can\'t login because have not uploaded documents - '.$mobile_no, $user_id, $data, $content);
                      return response()->json(['status' => "error", 'data' => 'upload_doc', 'message'=> "You can't login as have not uploaded document.", 'flexm_token' => $flexm_token, 'type' => $type], 200);
                    }
                    if(strtolower(@$cont->user->profile->document_approval_status) == 'submitted'){
                      addActivity('Flexm user can\'t login as his account is not approved - '.$mobile_no, $user_id, $data, $content);

                      return response()->json(['status' => "error", 'data' => '', 'message'=> "You can't login as your account is not approved yet.", 'flexm_token' => $flexm_token], 200);
                    }
                    $share = getoption('flexm_charges_app', '0.75');
                    $cont->flexm_charges = $share+$other_share_per;
                    // Activity::log('Flexm user logged in successfully - '.@$data['mobile'], @$user->id);
                    addActivity('Flexm user logged in successfully - '.$mobile_no, $user_id, $data, $content);

                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    // Activity::log('Flexm error in login - '.json_encode($content), @$user->id);
                    addActivity('Flexm error in login - '.$mobile_no, $user_id, $data, $content);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            addActivity('Flexm error in login because reason and code did not match - '.$mobile_no, $user_id, $data, ['code' => $code, 'reason' => $reason]);
            // Activity::log('Flexm error in login - '.@$reason, @$user->id);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            addActivity('Flexm error in login - '.@$mobile_no, @$user_id, @$data, $jsonBody);
            // Activity::log('Flexm error in login - '.json_encode($jsonBody), @$user->id);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
            addActivity('Flexm error in login - '.@$mobile_no, @$user_id, @$data, $e->getMessage());
            // Activity::log('Flexm error in login - '.@$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            addActivity('Flexm error in login - '.@$mobile_no, @$user_id, @$data, $e->getMessage());
            // Activity::log('Flexm error in login - '.@$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function forgotPassword(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $mobile_no = $data['mobile'] = $request->input('mobile');
        $user_id = $user->id;
        try{
            $data['mobile_country_code'] = $request->input('mobile_country_code');


            $client = new Client(['headers' => ['Content-type' => 'application/json']]);

            $result = $client->post(self::BASE_URL.'password/forgot', [
                'form_params' => $data,
            ]);

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());

                if($content->success){
                    $msg = $content->message;
                    $cont = $content->data;
                    addActivity('Flexm user initiated forgot password request - '.@$mobile_no, @$user_id, @$data, $content);
                    // Activity::log('Flexm user initiated forgot password request - '.@$data['mobile'], @$user->id);
                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    addActivity('Flexm error in forgot password - '.@$mobile_no, @$user_id, @$data, $content);
                    // Activity::log('Flexm error in forgot password - '.json_encode($content), @$user->id);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            addActivity('Flexm error reason code - '.@$mobile_no, @$user_id, @$data, ['code' => $code, 'reason' => $reason]);
            // Activity::log('Flexm error in forgot password - '.@$reason, @$user->id);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            addActivity('Flexm error while forgot password - '.@$mobile_no, @$user_id, @$data, $jsonBody);
            // Activity::log('Flexm error while forgot password - '.json_encode($jsonBody), @$user->id);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
            addActivity('Flexm error while forgot password - '.@$mobile_no, @$user_id, @$data, $e->getMessage());
            // Activity::log('Flexm error while forgot password - '.@$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            addActivity('Flexm error while forgot password - '.@$mobile_no, @$user_id, @$data, $e->getMessage());
            // Activity::log('Flexm error while forgot password - '.@$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function resetPassword(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $mobile_no = $request->input('mobile');

        try{
            // $flexm_token = $request->input('flexm_token');

            $data['mobile_country_code'] = $request->input('mobile_country_code');
            $data['mobile'] = $request->input('mobile');
            $data['password'] = $request->input('password');
            $data['password_confirmation'] = $request->input('password_confirmation');
            $data['otp_number'] = $request->input('otp_number');
            $data['token'] = $request->input('reset_token');
            $data['reset_password_token'] = $request->input('reset_password_token');

            $client = new Client(['headers' => ['Content-type' => 'application/json']]);

            $result = $client->patch(self::BASE_URL.'password/reset', [
                'form_params' => $data,
            ]);
            $data = $request->except('password', 'password_confirmation');

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                if($content->success){
                    $msg = $content->message;
                    $cont = $content->data;
                    addActivity('Flexm user successfully reset password - '.@$mobile_no, @$user_id, @$data, $content);
                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    addActivity('Flexm error reset password - '.@$mobile_no, @$user_id, @$data, $content);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            addActivity('Flexm error reset password reason code - '.@$mobile_no, @$user_id, @$data, ['code' => $code, 'reason' => $reason]);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            // Activity::log('Flexm error while reset password - '.json_encode($jsonBody), @$user->id);
            addActivity('Flexm error reset password - '.@$mobile_no, @$user_id, @$data, $jsonBody);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
            // Activity::log('Flexm error while reset password - '.$e->getMessage(), @$user->id);
            addActivity('Flexm error reset password - '.@$mobile_no, @$user_id, @$data, $e->getMessage());
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            addActivity('Flexm error reset password - '.@$mobile_no, @$user_id, @$data, $e->getMessage());
            // Activity::log('Flexm error while reset password - '.$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function changePassword(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        try{
            $flexm_token = $request->input('flexm_token');

            $data['current_password'] = $request->input('current_password');
            $data['password'] = $request->input('password');
            $data['password_confirmation'] = $request->input('password_confirmation');

            $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);

            $result = $client->patch(self::BASE_URL.'user/change/password', [
                'form_params' => $data,
            ]);

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                if($content->success){
                    $msg = $content->message;
                    $cont = $content->data;
                    addActivity('Flexm user changed password successfully.', @$user_id, [], $content);

                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    addActivity('Flexm error change password.', @$user_id, [], $content);

                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            addActivity('Flexm error change password reason code.', @$user_id, [], ['code' => $code, 'reason' => $reason]);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            addActivity('Flexm error change password.', @$user_id, [], $jsonBody);
            // Activity::log('Flexm error while changin password - '.json_encode($jsonBody), @$user->id);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
            addActivity('Flexm error change password.', @$user_id, [], $e->getMessage());
            // Activity::log('Flexm error while changin password - '.$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            addActivity('Flexm error change password.', @$user_id, [], $e->getMessage());
            // Activity::log('Flexm error while changin password - '.$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function getProfile(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        try{
            $flexm_token = $request->input('flexm_token');

            $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);
            $result = $client->get(self::BASE_URL.'user/profile?debug=false');

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                if($content->success){
                    $msg = $content->message;
                    $cont = $content->data;
                    if(@$cont->profile->id_date_expiry != ''){
                      $cont->profile->id_date_expiry = Carbon::parse($cont->profile->id_date_expiry)->format('Y/m/d');
                    }
                    addActivity('Flexm user accessed profile.', @$user_id, [], $content);
                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    addActivity('Flexm error when user accessed profile.', @$user_id, [], $content);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            addActivity('Flexm error when user accessed profile reason code.', @$user_id, [], ['code' => $code, 'reason' => $reason]);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            addActivity('Flexm error while fetching profile.', @$user_id, [], $jsonBody);
            // Activity::log('Flexm error while fetching profile - '.json_encode($jsonBody), @$user->id);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
            addActivity('Flexm error while fetching profile.', @$user_id, [], $e->getMessage());
            // Activity::log('Flexm error while fetching profile - '.$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            addActivity('Flexm error while fetching profile.', @$user_id, [], $e->getMessage());
            // Activity::log('Flexm error while fetching profile - '.$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function checkVerified($flexm_token, $mobile_no, $user_id)
    {

        try{
            $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);

            $result = $client->get(self::BASE_URL.'user/profile?debug=false');

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());

                if($content->success){
                    $msg = $content->message;
                    $cont = $content->data;

                    addActivity('Flexm - user is having access to profile - '.$mobile_no, $user_id, [], $content);
                    return json_encode(['status' => "success", 'data' => $cont, 'message'=> $msg]);
                }else{
                    addActivity('Flexm - user is not allowed access to profile need to verify phone no - '.$mobile_no, $user_id, [], $content);
                    return json_encode(['status' => 'error','data' => $content, 'message'=> $content->message]);
                }
            }
            addActivity('Flexm - either reason or code idd not match need checking Reason - '.$reason.' Code -'.$code.' - '.$mobile_no, $user_id, [], $content);
            return json_encode(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED']);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            addActivity('Flexm - error while checking if user is allowed access to profile - '.$mobile_no, $user_id, [], $jsonBody);
            return json_encode(['status' => 'error', 'data' => $jsonBody->errors, 'message'=> $jsonBody->message]);
        }catch(GuzzleException $e){
            addActivity('Flexm - exception while checking if user is allowed access to profile - '.$mobile_no, $user_id, [], $e->getMessage());
            return json_encode(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED']);
        }catch(Exception $e){
            addActivity('Flexm - exception while checking if user is allowed access to profile - '.$mobile_no, $user_id, [], $e->getMessage());
            return json_encode(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED']);
        }
    }

    public function updateProfile(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        try{
            $flexm_token = $request->input('flexm_token');

            $data['id_type'] = $request->input('id_type');
            $data['id_type_number'] = $request->input('id_type_number');
            $data['country_of_issue'] = 'Singapore';//$request->input('country_of_issue');
            $data['birthday'] = $request->input('birthday');
            $expiry_date = @$user->profile->wp_expiry;
            $data['id_date_expiry'] = $expiry_date;

            if($request->input('nationality') != '')
                $data['nationality'] = $request->input('nationality');

            if($data['birthday'] != ''){
              $data['birthday'] = Carbon::parse($data['birthday'])->format('Y/m/d');
            }
            if($request->input('gender') != '')
                $data['gender'] = $request->input('gender');
            if($request->input('title') != '')
                $data['title'] = $request->input('title');

            $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);

            $result = $client->put(self::BASE_URL.'user/profile', [
                'form_params' => $data,
            ]);

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                if($content->success){
                    $msg = $content->message;
                    $cont = $content->data;
                    addActivity('Flexm updated profile.', @$user_id, $data, $content);
                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    addActivity('Flexm updated profile.', @$user_id, $data, $content);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            addActivity('Flexm error while updating profile reason code.', @$user_id, $data, ['code' => $code, 'reason' => $reason]);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            addActivity('Flexm error while updating profile.', @$user_id, @$data, $jsonBody);
            // Activity::log('Flexm error while updating profile - '.json_encode($jsonBody), @$user->id);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
            // Activity::log('Flexm error while updating profile - '.$e->getMessage(), @$user->id);
            addActivity('Flexm error while updating profile.', @$user_id, @$data, $e->getMessage());
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            addActivity('Flexm error while updating profile.', @$user_id, @$data, $e->getMessage());
            // Activity::log('Flexm error while updating profile - '.$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function uploadPhoto(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        try{
            $flexm_token = $request->input('flexm_token');

            $data['image'] = $request->input('image');

            $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);

            $result = $client->patch(self::BASE_URL.'user/profile/upload',[
                'form_params' => $data,
            ]);

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                if($content->success){
                    $msg = $content->message;
                    $cont = $content->data;
                    addActivity('Flexm uploaded profile photo.', @$user_id, $data, $content);
                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    addActivity('Flexm error while uploading profile photo.', @$user_id, $data, $content);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            addActivity('Flexm error while uploading profile photo reason code', @$user_id, $data, ['code' => $code, 'reason' => $reason]);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            addActivity('Flexm error while uploading profile photo.', @$user_id, @$data, $jsonBody);
            // Activity::log('Flexm error while uploading profile picture - '.json_encode($jsonBody), @$user->id);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
            addActivity('Flexm error while uploading profile photo.', @$user_id, @$data, $e->getMessage());
            // Activity::log('Flexm error while uploading profile picture - '.$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            addActivity('Flexm error while uploading profile photo.', @$user_id, @$data, $e->getMessage());
            // Activity::log('Flexm error while uploading profile picture - '.$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function updateMobileNumber(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        try{
            $flexm_token = $request->input('flexm_token');

            $data['mobile_country_code'] = $request->input('mobile_country_code');
            $data['mobile'] = $request->input('mobile');

            $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);

            $result = $client->patch(self::BASE_URL.'user/change/mobile/',[
                'form_params' => $data,
            ]);

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                if($content->success){
                    $msg = $content->message;
                    $cont = $content->data;
                    addActivity('Flexm update mobile no request sent otp.', @$user_id, $data, $content);
                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    addActivity('Flexm error in update mobile no request.', @$user_id, $data, $content);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            addActivity('Flexm error while update mobile no request reason code.', @$user_id, $data, ['code' => $code, 'reason' => $reason]);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());

            $msg = '';
            foreach(@$jsonBody->errors as $arr){
              $msg = is_array($arr)?$arr[0]:$arr;
              break;
            }
            if($msg == ''){
              $msg = @$jsonBody->message;
            }
            addActivity('Flexm error while updating mobile number.', @$user_id, @$data, $jsonBody);
            // Activity::log('Flexm error while updating mobile number - '.json_encode($jsonBody), @$user->id);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$msg], 200);
        }catch(GuzzleException $e){
            addActivity('Flexm error while updating mobile number.', @$user_id, @$data, $e->getMessage());
            // Activity::log('Flexm error while updating mobile number - '.$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            addActivity('Flexm error while updating mobile number.', @$user_id, @$data, $e->getMessage());
            // Activity::log('Flexm error while updating mobile number - '.$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function updateAddress(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        try{

            $flexm_token = $request->input('flexm_token');
            $address_type = $request->input('address_type') != ''?$request->input('address_type'):'billing';

            $data['address_1'] = $request->input('address_1');
            $data['address_2'] = $request->input('address_2');
            $data['city'] = $request->input('city');
            $data['state'] = $request->input('state');
            $data['country'] = $request->input('country');
            $data['zipcode'] = $request->input('zipcode');

            $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);

            $result = $client->put(self::BASE_URL.'user/addresses/'.$address_type,[
                'form_params' => $data,
            ]);

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                if($content->success){
                    $msg = $content->message;
                    $cont = $content->data;
                    addActivity('Flexm updated address.', @$user_id, @$data, $content);
                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    addActivity('Flexm error while updating address.', @$user_id, @$data, $content);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            addActivity('Flexm error while updating address reason code.', @$user_id, @$data, ['code' => $code, 'reason' => $reason]);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            addActivity('Flexm error while updating address.', @$user_id, @$data, $jsonBody);
            // Activity::log('Flexm error while updating address - '.json_encode($jsonBody), @$user->id);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
            addActivity('Flexm error while updating address.', @$user_id, @$data, $e->getMessage());
            // Activity::log('Flexm error while updating address - '.$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            // Activity::log('Flexm error while updating address - '.$e->getMessage(), @$user->id);
            addActivity('Flexm error while updating address.', @$user_id, @$data, $e->getMessage());
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function getReferralCode(Request $request)
    {
        try{
            $flexm_token = $request->input('flexm_token');

            $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);

            $result = $client->get(self::BASE_URL.'user/referral/code');

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                if($content->success){
                    $msg = $content->message;
                    $cont = $content->data;
                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            return response()->json(['status' => 'error', 'data' => $jsonBody->errors, 'message'=> $jsonBody->message], 200);
        }catch(GuzzleException $e){
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function generateOTP(Request $request)
    {

        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        try{
            $flexm_token = $request->input('flexm_token');
            $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);

            $result = $client->get(self::BASE_URL.'user/otp/request');

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                if($content->success){
                    $msg = $content->message;
                    $cont = $content->data;
                    addActivity('Flexm generated otp.', @$user_id, [], $content);
                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    addActivity('Flexm error while generating otp', @$user_id, @$data, $content);
                    // Activity::log('Flexm error while generating otp - '.json_encode($content), @$user->id);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            addActivity('Flexm error while generating otp reason code', @$user_id, @$data, ['code' => $code, 'reason' => $reason]);
            // Activity::log('Flexm error while generating otp - '.json_encode($reason), @$user->id);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            addActivity('Flexm error while generating otp', @$user_id, [], $jsonBody);
            // Activity::log('Flexm error while generating otp - '.json_encode($jsonBody), @$user->id);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
            addActivity('Flexm error while generating otp', @$user_id, @$data, $e->getMessage());
            // Activity::log('Flexm error while generating otp - '.$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            // Activity::log('Flexm error while generating otp - '.$e->getMessage(), @$user->id);
            addActivity('Flexm error while generating otp', @$user_id, @$data, $e->getMessage());
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function verifyOTP(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        try{

            $flexm_token = $request->input('flexm_token');
            // $profile = $this->checkVerified($flexm_token);
            $mobile_no = $request->input('mobile_no');

            // if($profile){
            //   $profile = json_decode($profile);
            //   if(isset($profile['data']['mobile']) && $profile['data']['mobile'] != ''){
            //     $mobile_no = $profile['data']['mobile'];
            //   }
            //   // if($mobile_no){
            //   //   $docs = FlexmDoc::where('phone_no', $mobile_no)->get();
            //   //   foreach($docs as $doc){
            //   //     return "yeah";
            //   //     $doc->update(['verified' => '1']);
            //   //   }
            //   // }
            // }
            //
            // return $mobile_no;
            $data['otp_number'] = $request->input('otp_number');
            $data['token'] = $request->input('otp_token');

            $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);

            $result = $client->patch(self::BASE_URL.'user/otp/verify',[
                'form_params' => $data,
            ]);

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                if($content->success){
                    $msg = $content->message;
                    $cont = $content->data;

                    if($mobile_no != ''){
                      $docs = FlexmDoc::where('phone_no', $mobile_no)->get();
                      foreach($docs as $doc){
                        $front_return = $this->uploadDocImage($flexm_token, $doc->document);
                        \Log::debug("ON otp Image upload");
                        \Log::debug(json_encode($front_return));
                        $parsed = json_decode($front_return);
                        if(@$parsed['status'] == 'success'){
                          $doc->delete();
                        }
                      }
                    }
                    addActivity('Flexm otp verified', @$user_id, @$data, $content);
                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    addActivity('Flexm error while verifying otp', @$user_id, @$data, $content);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            addActivity('Flexm error while verifying otp', @$user_id, @$data, ['code' => $code, 'reason' => $reason]);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            addActivity('Flexm error while verifying otp', @$user_id, @$data, $jsonBody);
            // Activity::log('Flexm error while verifying otp - '.json_encode($jsonBody), @$user->id);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
            addActivity('Flexm error while verifying otp', @$user_id, @$data, $e->getMessage());
            // Activity::log('Flexm error while verifying otp - '.$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            // Activity::log('Flexm error while verifying otp - '.$e->getMessage(), @$user->id);
            addActivity('Flexm error while verifying otp', @$user_id, @$data, $e->getMessage());
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function createProfile($flexm_token, $input, $user_id = '')
    {
        try{
            $data['id_type'] = $input['id_type'];
            $data['id_type_number'] = $input['id_type_number'];
            $data['id_date_expiry'] = $input['id_date_expiry'];
            $data['country_of_issue'] = (isset($input['country_of_issue']) && $input['country_of_issue'] != '')?$input['country_of_issue']:'Singapore';
            $data['nationality'] = (isset($input['nationality']) && $input['nationality'] != '') ?$input['nationality']:'Singaporean';
            $data['birthday'] = $input['birthday'];
            $data['gender'] = (isset($input['gender']) && $input['gender'] != '')?ucfirst($input['gender']):'Male';
            $data['title'] = (isset($input['title']) && $input['title'] != '')?$input['title']:'Mr';

            $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);

            $result = $client->put(self::BASE_URL.'user/profile', [
                'form_params' => $data,
            ]);

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                if($content->success){
                    $msg = $content->message;
                    $cont = $content->data;
                    addActivity('Flexm created profile', @$user_id, @$data, $content);
                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    addActivity('Flexm error while creating profile', @$user_id, @$data, $content);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            addActivity('Flexm error while creating profile reason code', @$user_id, @$data, ['code' => $code, 'reason' => $reason]);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            addActivity('Flexm error while creating profile', @$user_id, @$data, $jsonBody);
            return response()->json(['status' => 'error', 'data' => $jsonBody->errors, 'message'=> $jsonBody->message], 200);
        }catch(GuzzleException $e){
            addActivity('Flexm error while creating profile', @$user_id, @$data, $e->getMessage());
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            addActivity('Flexm error while creating profile', @$user_id, @$data, $e->getMessage());
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function saveAdd($flexm_token, $input, $user_id = '')
    {
        try{
            $address_type = @$input['address_type'] != ''?@$input['address_type']:'billing';

            $data['address_1'] = $input['address_1'];
            $data['address_2'] = $input['address_2'] != ''?$input['address_2']:$input['address_1'];
            $data['city'] = $input['city'];
            $data['state'] = $input['state'];
            $data['country'] = ucfirst($input['country']);
            $data['zipcode'] = $input['zipcode'];

            $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);

            $result = $client->put(self::BASE_URL.'user/addresses/'.$address_type,[
                'form_params' => $data,
            ]);

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                if($content->success){
                    $msg = $content->message;
                    $cont = $content->data;
                    addActivity('Flexm added address', @$user_id, @$data, $content);
                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    addActivity('Flexm error while adding address', @$user_id, @$data, $content);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            addActivity('Flexm error while adding address reason code', @$user_id, @$data, ['code' => $code, 'reason' => $reason]);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            addActivity('Flexm error while adding address', @$user_id, @$data, $jsonBody);
            return response()->json(['status' => 'error', 'data' => $jsonBody->errors, 'message'=> $jsonBody->message], 200);
        }catch(GuzzleException $e){
            addActivity('Flexm error while adding address', @$user_id, @$data, $e->getMessage());
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            addActivity('Flexm error while adding address', @$user_id, @$data, $e->getMessage());
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function uploadDocImage($flexm_token, $image, $user_id = '')
    {
        try{
            $data['image'] = $image;
            $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);

            $result = $client->post(self::BASE_URL.'user/documents',[
                'form_params' => $data,
            ]);

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                if($content->success){
                    $msg = $content->message;
                    $cont = $content->data;
                    addActivity('Flexm uploaded doc.', @$user_id, @$data, $content);
                    return $this->submitDocImage($flexm_token);
                    // return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    addActivity('Flexm error while uploading doc.', @$user_id, @$data, $content);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            addActivity('Flexm error while uploading doc reason code.', @$user_id, @$data, ['code' => $code, 'reason' => $reason]);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            addActivity('Flexm error while uploading doc.', @$user_id, @$data, $jsonBody);
            return response()->json(['status' => 'error', 'data' => $jsonBody->errors, 'message'=> $jsonBody->message], 200);
        }catch(GuzzleException $e){
            addActivity('Flexm error while uploading doc.', @$user_id, @$data, $e->getMessage());
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            addActivity('Flexm error while uploading doc.', @$user_id, @$data, $e->getMessage());
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function submitDocImage($flexm_token, $user_id = '')
    {
        try{
            $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);

            $result = $client->patch(self::BASE_URL.'user/documents/submit');

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                if($content->success){
                    $msg = $content->message;
                    $cont = $content->data;
                    addActivity('Flexm submitted doc.', @$user_id, @$data, $content);
                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    addActivity('Flexm error while submitting doc.', @$user_id, @$data, $content);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            addActivity('Flexm error while submitting doc reason code.', @$user_id, @$data, ['code' => $code, 'reason' => $reason]);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            addActivity('Flexm error while submitting doc.', @$user_id, @$data, $jsonBody);

            return response()->json(['status' => 'error', 'data' => $jsonBody->errors, 'message'=> $jsonBody->message], 200);
        }catch(GuzzleException $e){
            addActivity('Flexm error while submitting doc.', @$user_id, @$data, $e->getMessage());

            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            addActivity('Flexm error while submitting doc.', @$user_id, @$data, $e->getMessage());

            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function uploadDoc(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        try{
            $flexm_token = $request->input('flexm_token');

            $data['image'] = $request->input('image');

            $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);

            $result = $client->post(self::BASE_URL.'user/documents',[
                'form_params' => $data,
            ]);

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                if($content->success){
                    $msg = $content->message;
                    $cont = $content->data;
                    addActivity('Flexm uploaded doc', @$user_id, @$data, $content);
                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    addActivity('Flexm error while uploading doc', @$user_id, @$data, $content);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            addActivity('Flexm error while uploading doc reason code', @$user_id, @$data, ['code'=> $code, 'reason' => $reason]);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            // Activity::log('Flexm error while uploading doc - '.json_encode($jsonBody), @$user->id);
            addActivity('Flexm error while uploading doc', @$user_id, @$data, $jsonBody);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
            // Activity::log('Flexm error while uploading doc - '.$e->getMessage(), @$user->id);
            addActivity('Flexm error while uploading doc', @$user_id, @$data, $e->getMessage());
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            // Activity::log('Flexm error while uploading doc - '.$e->getMessage(), @$user->id);
            addActivity('Flexm error while uploading doc', @$user_id, @$data, $e->getMessage());
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function submitDoc(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        try{
            $flexm_token = $request->input('flexm_token');

            $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);

            $result = $client->patch(self::BASE_URL.'user/documents/submit');

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                if($content->success){
                    $msg = $content->message;
                    $cont = $content->data;
                    addActivity('Flexm submitted doc', @$user_id, @$data, $content);
                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    addActivity('Flexm error while submitting doc', @$user_id, @$data, $content);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            addActivity('Flexm error while submitting doc', @$user_id, @$data, ['code' => $code, 'reason' => $reason]);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            addActivity('Flexm error while submitting doc', @$user_id, @$data, $jsonBody);
            // Activity::log('Flexm error while submitting doc - '.json_encode($jsonBody), @$user->id);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
            addActivity('Flexm error while submitting doc', @$user_id, @$data, $e->getMessage());
            // Activity::log('Flexm error while submitting doc - '.$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            addActivity('Flexm error while submitting doc', @$user_id, @$data, $e->getMessage());
            // Activity::log('Flexm error while submitting doc - '.$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function logout(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        try{
            $flexm_token = $request->input('flexm_token');

            $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);

            $result = $client->delete(self::BASE_URL.'auth/logout');

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                if($content->success){
                    $msg = $content->message;
                    $cont = $content->data;
                    addActivity('Flexm logged out', @$user_id, @$data, $content);
                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    addActivity('Flexm error while logging out', @$user_id, @$data, $content);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            addActivity('Flexm error while logging out reason code', @$user_id, @$data, ['code' => $code, 'reason' => $reason]);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            // Activity::log('Flexm error while logout - '.json_encode($jsonBody), @$user->id);
            addActivity('Flexm error while logging out', @$user_id, @$data, $jsonBody);

            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
            // Activity::log('Flexm error while logout - '.$e->getMessage(), @$user->id);
            addActivity('Flexm error while logging out', @$user_id, @$data, $e->getMessage());

            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            // Activity::log('Flexm error while logout - '.$e->getMessage(), @$user->id);
            addActivity('Flexm error while logging out', @$user_id, @$data, $e->getMessage());

            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

}
