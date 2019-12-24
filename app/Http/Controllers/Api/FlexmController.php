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

use App\Models\UserProfile;
use App\Models\RedeemUser;

class FlexmController extends Controller
{
    // const BASE_URL = 'https://alpha.flexm.sg/api/';
    const BASE_URL = 'https://wallet.flexm.sg/api/';//'https://test-api.flexm.sg/';

    const KIWIRE = 'https://118.189.171.212:10100/';
    const MYMA_URL = 'https://wlc.myma.app/main/public/api/v1/';

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
              return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
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
    //         return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
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
                if($content->data && $content->data->nationalities){
                  foreach($content->data->nationalities as $d){
                    $d->description = $d->code;
                  }
                }
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
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
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
        $is_cron = $request->input('cron');
        
        $profile = @$user->profile;
        
        if($profile){
          if($profile->street_address == "" && $profile->dormitory_id == ""){
            return response()->json(['status' => 'error', 'data' => 'You need to update street address in your profile to proceed.', 'message'=> 'You need to update street address in your profile to proceed.'], 200);
          }elseif($profile->zip_code == ""){
            //return response()->json(['status' => 'error', 'data' => 'You need to update zip_code in your profile to proceed.', 'message'=> 'You need to update zip_code in your profile to proceed.'], 200);
          }elseif($profile->wp_front == ""){
            //return response()->json(['status' => 'error', 'data' => 'You need to upload work permit photos under your profile.', 'message'=> 'You need to upload work permit photos under your profile.'], 200);
          }elseif($profile->wp_back == ""){
            //return response()->json(['status' => 'error', 'data' => 'You need to upload work permit photos under your profile.', 'message'=> 'You need to upload work permit photos under your profile.'], 200);
          }
          $mobile_no = $profile->phone;
          
          $front_image =  static_file($profile->wp_front);//public_path($profile->wp_front);
          FlexmDoc::where('phone_no', $mobile_no)->where('verified', '0')->delete();
          $image = http_get_contents($front_image);//file_get_contents($front_image);
          if ($image !== false){
                $front_image = 'data:image/jpeg;base64,'.base64_encode($image);
                $d['phone_no'] = $mobile_no;
                $d['document'] = $front_image;
                $d['verified'] = '0';
                FlexmDoc::create($d);
          }
    
          $back_image =  static_file($profile->wp_back);
          $image = http_get_contents($back_image);//file_get_contents($back_image);
          if ($image !== false){
                $back_image = 'data:image/jpeg;base64,'.base64_encode($image);
                $d['phone_no'] = $mobile_no;
                $d['document'] = $back_image;
                $d['verified'] = '0';
                FlexmDoc::create($d);
          }
        //   if($_SERVER['REMOTE_ADDR'] == '172.111.251.43'){
        //       return response()->json(['status' => 'error', 'data' => '', 'message'=> 'Front image check'], 200);
        //   }
        //   try{
        //   $front_image = base64_encode(file_get_contents(url($profile->wp_front)));
        //   $back_image = base64_encode(file_get_contents(url($profile->wp_back)));
        //   }catch(Exception $e){
        //       $front_image = "";
        //       $back_image = "";
        //   }
        //   if($front_image != ''){
        //       $d['phone_no'] = $mobile_no;
        //       $d['document'] = 'data:image/jpeg;base64,'.$front_image;
        //       $d['verified'] = '0';
        //       $d['type'] = "front";
        //       $doc = FlexmDoc::where('type', 'front')->where('phone_no', $mobile_no)->first();
        //       if($doc){
        //           $doc->document = $d['document'];
        //           $doc->save();
        //       }else{
        //         FlexmDoc::create($d);
        //       }

        //   }
        //   if($back_image != ''){
        //       $d['phone_no'] = $mobile_no;
        //       $d['document'] = 'data:image/jpeg;base64,'.$back_image;
        //       $d['verified'] = '0';
        //       $d['type'] = "back";
        //       $doc = FlexmDoc::where('type', 'back')->where('phone_no', $mobile_no)->first();
        //       if($doc){
        //           $doc->document = $d['document'];
        //           $doc->save();
        //       }else{
        //         FlexmDoc::create($d);
        //       }

        //   }

        }else{
            return response()->json(['status' => 'error', 'data' => 'You need to update your profile to proceed.', 'message'=> 'You need to update your profile to proceed.'], 200);
        }
        $url = self::BASE_URL.'user/register';
        try{
            $name = explode(' ', $user->name);
            $pref_name = $user->name;
            if(count($name) < 2){
                $pref_name .= ' FNU';
              //return response()->json(['status' => 'error', 'data' => 'You need to update your full name in profile to proceed.', 'message'=> 'You need to update your full name in profile to proceed.'], 200);
            }
            $data['preferred_name'] = $pref_name;
            $data['full_name'] = $pref_name;
            $data['mobile_country_code'] = '65';
            $data['mobile'] = $mobile_no;
            $data['password'] = $request->input('password');
            $data['password_confirmation'] = $request->input('password_confirmation');
            $data['id_type'] = 'wp';
            $data['id_type_number'] = $profile->fin_no;
            $data['birthday'] = Carbon::parse($profile->dob)->format('Y/m/d');

            $data['device_signature'] = '{'.$request->input('device_signature').''.$date.'}';
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

            $result = $client->post($url, [
                'form_params' => $data,
            ]);

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            unset($data['password']);
            unset($data['password_confirmation']);
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();

                $content = json_decode($body->getContents());
                if($content->success){
                    $msg = $content->message;
                    $cont = $content->data;
                    \Log::debug('registration success');
                    \Log::debug(json_encode($cont));

                    addActivity('Flexm account registered successfully - '.$mobile_no, $user_id, $data, $content, $url);

                    // Activity::log('Flexm account registered successfully #'.@$data['mobile'], @$user->id);
                    if($cont->token){
                        
                        if(strtolower(@$cont->user->profile->document_approval_status) == 'pending'){
                          $user->update(['flexm_status' => 'Need to upload documents.']);
                        }
                        if(strtolower(@$cont->user->profile->document_approval_status) == 'submitted'){
                          $user->update(['flexm_status' => 'Document submitted waiting for approval.']);
                        }
                        if(strtolower(@$cont->user->profile->document_approval_status) != 'approved'){
                          $user->update(['flexm_status' => 'Mobile number verification is pending.']);
                        }else{
                          $user->update(['flexm_status' => 'Approved']);
                        }
                        
                        $flexm_token = $cont->token;
                        $data['nationality'] = $request->input('nationality');
                        $data['id_date_expiry'] = '';
                        if(@$user->profile->wp_expiry != ''){
                            if(@$user->profile->wp_expiry == '0000-00-00'){
                                $data['id_date_expiry'] = '';
                            }else{
                                $data['id_date_expiry'] = @$user->profile->wp_expiry;        
                            }
                        }
                        
                        $profile_created = $this->createProfile($flexm_token, $data);
                        \Log::debug("Profile Created");
                        \Log::debug(json_encode($profile_created));

                        if($user->profile){
                          $dta = [];
                          $dta['address_1'] = '';
                          $dta['address_2'] = '';
                          $dorm = @$user->profile->dormitory->address_1;
                          $zip_code = (@$user->profile->zip_code == '')?0:@$user->profile->zip_code;
                          if($dorm){
                            $dta['address_1'] = $dorm;
                            $dta['address_2'] = @$user->profile->dormitory->address_2;     
                            //   if($zip_code == 0){
                            $zip_code = @$user->profile->dormitory->zip_code;
                            //   }
                          }elseif(@$user->profile->street_address){
                            $dta['address_1'] = $user->profile->street_address;
                            if(@$user->profile->block){
                                $dta['address_1'] .= ' BLK #'.$user->profile->block;
                            }
                            if(@$user->profile->sub_block){
                                $dta['address_1'] .= ' S-BLK #'.$user->profile->sub_block;
                            }
                            if(@$user->profile->unit_no){
                                $dta['address_2'] .= ' UN #'.$user->profile->unit_no;
                            }
                            if(@$user->profile->floor_no){
                                $dta['address_2'] .= ' FN #'.$user->profile->floor_no;
                            }
                            if(@$user->profile->room_no){
                                $dta['address_2'] .= ' RN #'.$user->profile->room_no;
                            }
                          }
                         
                          $dta['city'] = 'Singapore';
                          $dta['state'] = 'Singapore';
                          $dta['country'] = 'Singapore';
                          $dta['zipcode'] = $zip_code; 
                          $save_add = $this->saveAdd($flexm_token, $dta);
                           \Log::debug("Save Billing Address");
                           \Log::debug(json_encode($save_add));
                          $dta['address_type'] = 'residential';
                          $save_add = $this->saveAdd($flexm_token, $dta);
                          \Log::debug("Save Residential Address");
                          \Log::debug(json_encode($save_add));
                        }
                        //
                        // $front_image = $request->input('front_of_id_image');
                        // $back_image = $request->input('back_of_id_image');
                        // //$residential_image = $request->input('residential_image');
                        // if($front_image != ''){
                        //   $d['phone_no'] = $data['mobile'];
                        //   $d['document'] = $front_image;
                        //   $d['verified'] = '0';
                        //   FlexmDoc::create($d);
                        //   $front_return = $this->uploadDocImage($flexm_token, $front_image);
                        //   \Log::debug("Front Image upload");
                        //   \Log::debug($front_image);
                        //   \Log::debug(json_encode($front_return));
                        // }
                        // if($back_image != ''){
                        //   $d['phone_no'] = $data['mobile'];
                        //   $d['document'] = $back_image;
                        //   $d['verified'] = '0';
                        //   FlexmDoc::create($d);
                        //   $front_return = $this->uploadDocImage($flexm_token, $back_image);
                        //   \Log::debug("Back Image upload");
                        //   \Log::debug($back_image);
                        //   \Log::debug(json_encode($front_return));
                        // }
                        // if($residential_image != ''){
                        //   $d['phone_no'] = $data['mobile'];
                        //   $d['document'] = $residential_image;
                        //   $d['verified'] = '0';
                        //   FlexmDoc::create($d);
                        //   $front_return = $this->uploadDocImage($flexm_token, $residential_image);
                        //   \Log::debug("Residential Image upload");
                        //   \Log::debug($residential_image);
                        //   \Log::debug(json_encode($front_return));
                        // }

                    }
                    // $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);
                    // $result = $client->get(self::BASE_URL.'user/otp/request');
                    // $code = $result->getStatusCode(); // 200
                    // $reason = $result->getReasonPhrase(); // OK
                    
                    //event to send email on registration
                    event(new NotifyFlexmRegistration($user->id));
                    if($is_cron == ''){
                      $user->update(['flexm_direct' => '1']);

                      if(strlen($mobile_no) == 8){
                        $mobile_no = '+65'.$mobile_no;
                      }
                      $message = "Dear MyMA app Customer, your Wallet account has been successfully created.Top-up your MyMA Wallet money$ easily using ATM or iBanking.
                      You can login and start using MyMA Wallet - Home menu to send Remittance$ to overseas, make Physical Merchants QR payment at Westlite Dormitory 
                      merchant shops and our app partners: Spuul movies and Naanstap food catering!";
                      
                      sendSMS($mobile_no, $message);
                    }
                    $user->update(['flexm_account' => '1']);
                    \Log::debug("complete register");
                    return response()->json(['status' => 'success', 'data' => $cont, 'message'=> 'You have successfully signed up. Wait for approval.'], 200);
                }else{
                    \Log::debug("error else");
                    $user->update(['flexm_error' => '1', 'flexm_error_text' => @$content]);
                    // Activity::log('Flexm error while registration -'.json_encode($content), @$user->id);
                    addActivity('Flexm error in registration - '.$mobile_no, $user_id, @$data, @$content, $url);
                    \Log::debug(json_encode($content));
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            \Log::debug("error");
            \Log::debug(json_encode($reason));
            addActivity('Flexm error in registration because reason and code did not match - '.$mobile_no, $user_id, $data, ['code' => $code, 'reason' => $reason], $url);
            // Activity::log('Flexm error while registration -'.json_encode(@$reason), @$user->id);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());

            $msg = '';
            if(isset($jsonBody->errors)){
                foreach(@$jsonBody->errors as $arr){
                  $msg = is_array($arr)?@$arr[0]:$arr;
                  break;
                }    
            } 
            if($msg == ''){
              $msg = @$jsonBody->message;
            }
            \Log::debug("registration error");
            \Log::debug(json_encode($jsonBody));
            if($msg != 'Mobile number is already registered.'){
                // Activity::log('Flexm error while registration -'.@$msg, @$user->id);
            }
            $user->update(['flexm_error' => '1', 'flexm_error_text' => @json_encode($jsonBody)]);
            addActivity('Flexm error in registration - '.@$mobile_no, @$user_id, @$data, @$jsonBody, $url);

            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> $msg, 'full'=> $jsonBody ], 200);
        }catch(GuzzleException $e){
            addActivity('Flexm error in registration - '.@$mobile_no, @$user_id, @$data, @$e->getMessage(), $url);
            $user->update(['flexm_error' => '1', 'flexm_error_text' => @$e->getMessage()]);
            // Activity::log('Flexm error while registration -'.$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED', 'full' => $e->getMessage()], 200);
        }catch(Exception $e){
            addActivity('Flexm error in registration - '.@$mobile_no, @$user_id, @$data, @$e->getMessage(), $url);
            $user->update(['flexm_error' => '1', 'flexm_error_text' => @$e->getMessage()]);
            // Activity::log('Flexm error while registration -'.$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED', 'full' => $e->getMessage()], 200);
        }
    }


    public function login(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $data['mobile_country_code'] = $request->input('mobile_country_code');
        $data['mobile'] = $request->input('mobile');
        $data['password'] = trim($request->input('password'));
        $data['user_type'] = 2;//web app user
        $data['device_signature'] = "{$request->input('device_signature')}";//$request->input('device_signature');
        $user_id = $user->id;
        $url = self::BASE_URL.'auth/login';
        try{
            $type = $request->input('type');
            $id = $request->input('id');
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

            $result = $client->post($url, [
                'json' => $data,
            ]);
            unset($data['password']);
            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                if($content->success){
                    $user_profile = UserProfile::where('user_id', $user->id)->first();  

                    $content->data->user->isTouchRequested= false;
                    $content->data->user->isTouchRedeemed= false;
                    $content->data->user->isStarhubRequested = false;
                    $content->data->user->isMastercardRequested = false;
                    $content->data->user->isSpuulRequested = false;
                    $content->data->user->isLocalFIN = false;
                    $content->data->user->isTouchActive = true;

                    // $redeem_user_touch = RedeemUser::where('name',$user->name)->where('type','TOUCH')->where('mobile',$user_profile->phone)->where('status','credit_successful')->count();    
                    // if($redeem_user_touch > 0) $content->data->user->isTouchRedeemed= true;

                    
                    //$redeem_user_touch = RedeemUser::where('name',$user->name)->where('type','TOUCH')->where('mobile',$user_profile->phone)->count();    
                    $redeem_user_touch = RedeemUser::where('mobile',$mobile_no)->count();    
                    if($redeem_user_touch > 0) {
                        $content->data->user->isTouchRequested= true; 
                        $content->data->user->isTouchRedeemed= true;
                    }

                    $redeem_user_starhub = RedeemUser::where('name',$user->name)->where('type','starhub')->where('mobile',$user_profile->phone)->count();    
                    if($redeem_user_starhub > 0) $content->data->user->isStarhubRequested = true;

                    $redeem_user_mastercard = RedeemUser::where('name',$user->name)->where('type','mastercard')->where('mobile',$user_profile->phone)->count();    
                    if($redeem_user_mastercard > 0) $content->data->user->isMastercardRequested = true;

                    $redeem_user_spuul = RedeemUser::where('name',$user->name)->where('type','spuul')->where('mobile',$user_profile->phone)->count();    
                    if($redeem_user_spuul > 0) $content->data->user->isSpuulRequested= true;

                    if (substr($user_profile->fin_no, 0, 1) === 'S')
                        $content->data->user->isLocalFIN = true;                    

                    $content->data->user->currentTime = date('Y-m-d h:i:s');

                    if($user->profile->phone == $data['mobile']){
                        $user->update(['flexm_account' => '1']);
                    }

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
                          $user->update(['flexm_status' => 'Mobile number verification is pending']);
                          addActivity('Flexm User\'s cant login as his phone number is not verified -'.$mobile_no, $user_id, $data, $content, $url);
                          return response()->json(['status' => "error", 'data' => 'verify_number', 'message'=> 'Phone number is not verified.', 'flexm_token' => $flexm_token, 'type' => $type], 200);
                        }
                      }
                    }
                    /*if(strtolower(@$cont->user->profile->document_approval_status) == 'pending'){
                      $user->update(['flexm_status' => 'Need to upload documents.']);
                      $type = 2;
                      if(@$cont->user->profile->id_type == 'epfin'){
                        $type = 3;
                      }
                      addActivity('Flexm user can\'t login because have not uploaded documents - '.$mobile_no, $user_id, $data, $content, $url);
                      return response()->json(['status' => "error", 'data' => 'upload_doc', 'message'=> "You can't login as have not uploaded document.", 'flexm_token' => $flexm_token, 'type' => $type], 200);
                    }*/
                    // if(strtolower(@$cont->user->profile->document_approval_status) == 'submitted'){
                    //     $user->update(['flexm_status' => 'Document submitted waiting for approval.']);
                    //   addActivity('Flexm user can\'t login as his account is not approved - '.$mobile_no, $user_id, $data, $content, $url);

                    //   return response()->json(['status' => "error", 'data' => '', 'message'=> "You can't login as your account is not approved yet.", 'flexm_token' => $flexm_token], 200);
                    // }
                    $user->update(['flexm_status' => 'Approved']);
                    $share = getoption('flexm_charges_app', '0.75');
                    $cont->flexm_charges = $share+$other_share_per;
                    $cont->ih_base_url = 'flexm-ih.mmvpay.com';
                    // Activity::log('Flexm user logged in successfully - '.@$data['mobile'], @$user->id);
                    addActivity('Flexm user logged in successfully - '.$mobile_no, $user_id, $data, $content, $url);

                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    // Activity::log('Flexm error in login - '.json_encode($content), @$user->id);
                    addActivity('Flexm error in login - '.$mobile_no, $user_id, $data, $content, $url);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            addActivity('Flexm error in login because reason and code did not match - '.$mobile_no, $user_id, $data, ['code' => $code, 'reason' => $reason], $url);
            // Activity::log('Flexm error in login - '.@$reason, @$user->id);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            addActivity('Flexm error in login - '.@$mobile_no, @$user_id, @$data, $jsonBody, $url);
            // Activity::log('Flexm error in login - '.json_encode($jsonBody), @$user->id);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
            addActivity('Flexm error in login - '.@$mobile_no, @$user_id, @$data, $e->getMessage(), $url);
            // Activity::log('Flexm error in login - '.@$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            addActivity('Flexm error in login - '.@$mobile_no, @$user_id, @$data, $e->getMessage(), $url);
            // Activity::log('Flexm error in login - '.@$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function forgotPassword(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $mobile_no = $data['mobile'] = $request->input('mobile');
        $user_id = $user->id;
        $data['mobile_country_code'] = $request->input('mobile_country_code');

        $url = self::BASE_URL.'password/forgot';
        try{
            $client = new Client(['headers' => ['Content-type' => 'application/json']]);

            $result = $client->post($url, [
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
                    addActivity('Flexm user initiated forgot password request - '.@$mobile_no, @$user_id, @$data, $content, $url);
                    // Activity::log('Flexm user initiated forgot password request - '.@$data['mobile'], @$user->id);
                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    addActivity('Flexm error in forgot password - '.@$mobile_no, @$user_id, @$data, $content, $url);
                    // Activity::log('Flexm error in forgot password - '.json_encode($content), @$user->id);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            addActivity('Flexm error reason code - '.@$mobile_no, @$user_id, @$data, ['code' => $code, 'reason' => $reason], $url);
            // Activity::log('Flexm error in forgot password - '.@$reason, @$user->id);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            addActivity('Flexm error while forgot password - '.@$mobile_no, @$user_id, @$data, $jsonBody, $url);
            // Activity::log('Flexm error while forgot password - '.json_encode($jsonBody), @$user->id);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
            addActivity('Flexm error while forgot password - '.@$mobile_no, @$user_id, @$data, $e->getMessage(), $url);
            // Activity::log('Flexm error while forgot password - '.@$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            addActivity('Flexm error while forgot password - '.@$mobile_no, @$user_id, @$data, $e->getMessage(), $url);
            // Activity::log('Flexm error while forgot password - '.@$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function resetPassword(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $mobile_no = $request->input('mobile');
        $data['mobile_country_code'] = $request->input('mobile_country_code');
        $data['mobile'] = $request->input('mobile');
        $data['password'] = $request->input('password');
        $data['password_confirmation'] = $request->input('password_confirmation');
        $data['otp_number'] = $request->input('otp_number');
        $data['token'] = $request->input('reset_token');
        $data['reset_password_token'] = $request->input('reset_password_token');
        $url = self::BASE_URL.'password/reset';
        try{
            // $flexm_token = $request->input('flexm_token');


            $client = new Client(['headers' => ['Content-type' => 'application/json']]);

            $result = $client->patch($url, [
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
                    addActivity('Flexm user successfully reset password - '.@$mobile_no, @$user_id, @$data, $content, $url);
                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    addActivity('Flexm error reset password - '.@$mobile_no, @$user_id, @$data, $content, $url);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            addActivity('Flexm error reset password reason code - '.@$mobile_no, @$user_id, @$data, ['code' => $code, 'reason' => $reason], $url);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            // Activity::log('Flexm error while reset password - '.json_encode($jsonBody), @$user->id);
            addActivity('Flexm error reset password - '.@$mobile_no, @$user_id, @$data, $jsonBody, $url);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
            // Activity::log('Flexm error while reset password - '.$e->getMessage(), @$user->id);
            addActivity('Flexm error reset password - '.@$mobile_no, @$user_id, @$data, $e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            addActivity('Flexm error reset password - '.@$mobile_no, @$user_id, @$data, $e->getMessage(), $url);
            // Activity::log('Flexm error while reset password - '.$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function changePassword(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $flexm_token = $request->input('flexm_token');

        $data['current_password'] = $request->input('current_password');
        $data['password'] = $request->input('password');
        $data['password_confirmation'] = $request->input('password_confirmation');
        $url = self::BASE_URL.'user/change/password';
        try{

            $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);

            $result = $client->patch($url, [
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
                    addActivity('Flexm user changed password successfully.', @$user_id, [], $content, $url);

                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    addActivity('Flexm error change password.', @$user_id, [], $content, $url);

                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            addActivity('Flexm error change password reason code.', @$user_id, [], ['code' => $code, 'reason' => $reason], $url);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            addActivity('Flexm error change password.', @$user_id, [], $jsonBody, $url);
            // Activity::log('Flexm error while changin password - '.json_encode($jsonBody), @$user->id);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
            addActivity('Flexm error change password.', @$user_id, [], $e->getMessage(), $url);
            // Activity::log('Flexm error while changin password - '.$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            addActivity('Flexm error change password.', @$user_id, [], $e->getMessage(), $url);
            // Activity::log('Flexm error while changin password - '.$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function getProfile(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $url = self::BASE_URL.'user/profile?debug=false';
        try{
            $flexm_token = $request->input('flexm_token');

            $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);
            $result = $client->get($url);

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
                    addActivity('Flexm user accessed profile.', @$user_id, [], $content, $url);
                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    addActivity('Flexm error when user accessed profile.', @$user_id, [], $content, $url);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            addActivity('Flexm error when user accessed profile reason code.', @$user_id, [], ['code' => $code, 'reason' => $reason], $url);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            addActivity('Flexm error while fetching profile.', @$user_id, [], $jsonBody, $url);
            // Activity::log('Flexm error while fetching profile - '.json_encode($jsonBody), @$user->id);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
            addActivity('Flexm error while fetching profile.', @$user_id, [], $e->getMessage(), $url);
            // Activity::log('Flexm error while fetching profile - '.$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            addActivity('Flexm error while fetching profile.', @$user_id, [], $e->getMessage(), $url);
            // Activity::log('Flexm error while fetching profile - '.$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function checkVerified($flexm_token, $mobile_no, $user_id)
    {
        $url = self::BASE_URL.'user/profile?debug=false';
        try{
            $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);

            $result = $client->get($url);

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());

                if($content->success){
                    $msg = $content->message;
                    $cont = $content->data;

                    addActivity('Flexm - user is having access to profile - '.$mobile_no, $user_id, [], $content, $url);
                    return json_encode(['status' => "success", 'data' => $cont, 'message'=> $msg]);
                }else{
                    addActivity('Flexm - user is not allowed access to profile need to verify phone no - '.$mobile_no, $user_id, [], $content, $url);
                    return json_encode(['status' => 'error','data' => $content, 'message'=> $content->message]);
                }
            }
            addActivity('Flexm - either reason or code idd not match need checking Reason - '.$reason.' Code -'.$code.' - '.$mobile_no, $user_id, [], $content, $url);
            return json_encode(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED']);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            addActivity('Flexm - error while checking if user is allowed access to profile - '.$mobile_no, $user_id, [], $jsonBody, $url);
            return json_encode(['status' => 'error', 'data' => $jsonBody->errors, 'message'=> $jsonBody->message]);
        }catch(GuzzleException $e){
            addActivity('Flexm - exception while checking if user is allowed access to profile - '.$mobile_no, $user_id, [], $e->getMessage(), $url);
            return json_encode(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED']);
        }catch(Exception $e){
            addActivity('Flexm - exception while checking if user is allowed access to profile - '.$mobile_no, $user_id, [], $e->getMessage(), $url);
            return json_encode(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED']);
        }
    }

    public function updateProfile(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;

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

        $url = self::BASE_URL.'user/profile';
        try{

            $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);

            $result = $client->put($url, [
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
                    addActivity('Flexm updated profile.', @$user_id, $data, $content, $url);
                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    addActivity('Flexm updated profile.', @$user_id, $data, $content, $url);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            addActivity('Flexm error while updating profile reason code.', @$user_id, $data, ['code' => $code, 'reason' => $reason], $url);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            addActivity('Flexm error while updating profile.', @$user_id, @$data, $jsonBody, $url);
            // Activity::log('Flexm error while updating profile - '.json_encode($jsonBody), @$user->id);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
            // Activity::log('Flexm error while updating profile - '.$e->getMessage(), @$user->id);
            addActivity('Flexm error while updating profile.', @$user_id, @$data, $e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            addActivity('Flexm error while updating profile.', @$user_id, @$data, $e->getMessage(), $url);
            // Activity::log('Flexm error while updating profile - '.$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function uploadPhoto(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $data['image'] = $request->input('image');

        $url = self::BASE_URL.'user/profile/upload';
        try{
            $flexm_token = $request->input('flexm_token');
            $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);

            $result = $client->patch($url,[
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
                    addActivity('Flexm uploaded profile photo.', @$user_id, $data, $content, $url);
                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    addActivity('Flexm error while uploading profile photo.', @$user_id, $data, $content, $url);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            addActivity('Flexm error while uploading profile photo reason code', @$user_id, $data, ['code' => $code, 'reason' => $reason], $url);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            addActivity('Flexm error while uploading profile photo.', @$user_id, @$data, $jsonBody, $url);
            // Activity::log('Flexm error while uploading profile picture - '.json_encode($jsonBody), @$user->id);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
            addActivity('Flexm error while uploading profile photo.', @$user_id, @$data, $e->getMessage(), $url);
            // Activity::log('Flexm error while uploading profile picture - '.$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            addActivity('Flexm error while uploading profile photo.', @$user_id, @$data, $e->getMessage(), $url);
            // Activity::log('Flexm error while uploading profile picture - '.$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function updateMobileNumber(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $flexm_token = $request->input('flexm_token');

        $data['mobile_country_code'] = $request->input('mobile_country_code');
        $data['mobile'] = $request->input('mobile');
        $url = self::BASE_URL.'user/change/mobile/';
        try{

            $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);

            $result = $client->patch($url,[
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
                    addActivity('Flexm update mobile no request sent otp.', @$user_id, $data, $content, $url);
                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    addActivity('Flexm error in update mobile no request.', @$user_id, $data, $content, $url);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            addActivity('Flexm error while update mobile no request reason code.', @$user_id, $data, ['code' => $code, 'reason' => $reason], $url);
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
            addActivity('Flexm error while updating mobile number.', @$user_id, @$data, $jsonBody, $url);
            // Activity::log('Flexm error while updating mobile number - '.json_encode($jsonBody), @$user->id);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$msg], 200);
        }catch(GuzzleException $e){
            addActivity('Flexm error while updating mobile number.', @$user_id, @$data, $e->getMessage(), $url);
            // Activity::log('Flexm error while updating mobile number - '.$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            addActivity('Flexm error while updating mobile number.', @$user_id, @$data, $e->getMessage(), $url);
            // Activity::log('Flexm error while updating mobile number - '.$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function updateAddress(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;

        $flexm_token = $request->input('flexm_token');
        $address_type = $request->input('address_type') != ''?$request->input('address_type'):'billing';

        $data['address_1'] = $request->input('address_1');
        $data['address_2'] = $request->input('address_2');
        $data['city'] = $request->input('city');
        $data['state'] = $request->input('state');
        $data['country'] = $request->input('country');
        $data['zipcode'] = $request->input('zipcode');
        $url = self::BASE_URL.'user/addresses/'.$address_type;
        try{

            $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);

            $result = $client->put($url,[
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
                    addActivity('Flexm updated address.', @$user_id, @$data, $content, $url);
                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    addActivity('Flexm error while updating address.', @$user_id, @$data, $content, $url);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            addActivity('Flexm error while updating address reason code.', @$user_id, @$data, ['code' => $code, 'reason' => $reason], $url);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            addActivity('Flexm error while updating address.', @$user_id, @$data, $jsonBody, $url);
            // Activity::log('Flexm error while updating address - '.json_encode($jsonBody), @$user->id);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
            addActivity('Flexm error while updating address.', @$user_id, @$data, $e->getMessage(), $url);
            // Activity::log('Flexm error while updating address - '.$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            // Activity::log('Flexm error while updating address - '.$e->getMessage(), @$user->id);
            addActivity('Flexm error while updating address.', @$user_id, @$data, $e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
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
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function generateOTP(Request $request)
    {

        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $url = self::BASE_URL.'user/otp/request';
        try{
            $flexm_token = $request->input('flexm_token');
            $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);

            $result = $client->get($url);

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                if($content->success){
                    $msg = $content->message;
                    $cont = $content->data;
                    addActivity('Flexm generated otp.', @$user_id, [], $content, $url);
                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    addActivity('Flexm error while generating otp', @$user_id, @$data, $content, $url);
                    // Activity::log('Flexm error while generating otp - '.json_encode($content), @$user->id);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            addActivity('Flexm error while generating otp reason code', @$user_id, @$data, ['code' => $code, 'reason' => $reason], $url);
            // Activity::log('Flexm error while generating otp - '.json_encode($reason), @$user->id);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            addActivity('Flexm error while generating otp', @$user_id, [], $jsonBody, $url);
            // Activity::log('Flexm error while generating otp - '.json_encode($jsonBody), @$user->id);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
            addActivity('Flexm error while generating otp', @$user_id, @$data, $e->getMessage(), $url);
            // Activity::log('Flexm error while generating otp - '.$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            // Activity::log('Flexm error while generating otp - '.$e->getMessage(), @$user->id);
            addActivity('Flexm error while generating otp', @$user_id, @$data, $e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function verifyOTP(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        
        $flexm_token = $request->input('flexm_token');
        // $profile = $this->checkVerified($flexm_token);
        $mobile_no = $request->input('mobile_no');
        $mobile_no = @$user->profile->phone;
        
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

        $url = self::BASE_URL.'user/otp/verify';
        try{

            $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);

            $result = $client->patch($url,[
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
                    addActivity('Flexm otp verified', @$user_id, @$data, $content, $url);
                    
                    if($mobile_no != ''){
                      $docs = FlexmDoc::where('phone_no', $mobile_no)->get();
                      foreach($docs as $doc){
                        $front_return = $this->uploadDocImage($flexm_token, $doc->document);
                        \Log::debug("ON otp Image upload");
                        \Log::debug(json_encode($front_return));
                        $parsed = json_decode($front_return);
                        if(@$parsed['status'] == 'success'){
                            $doc->verified = '1';
                            $doc->save();
                        //   $doc->delete();
                        }
                      }
                    }
                    
                    // if($mobile_no != ''){
                    //   $docs = FlexmDoc::where('phone_no', $mobile_no)->get();
                    //   foreach($docs as $doc){
                    //     $front_return = $this->uploadDocImage($flexm_token, $doc->document);
                    //     \Log::debug("ON otp Image upload");
                    //     \Log::debug(json_encode($front_return));
                    //     $parsed = json_decode($front_return);
                    //     if(@$parsed['status'] == 'success'){
                    //         $doc->verified = '1';
                    //         $doc->save();
                    //     //   $doc->delete();
                    //     }
                    //   }
                    // }
                    
                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    addActivity('Flexm error while verifying otp', @$user_id, @$data, $content, $url);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            addActivity('Flexm error while verifying otp', @$user_id, @$data, ['code' => $code, 'reason' => $reason], $url);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            addActivity('Flexm error while verifying otp', @$user_id, @$data, $jsonBody, $url);
            // Activity::log('Flexm error while verifying otp - '.json_encode($jsonBody), @$user->id);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
            addActivity('Flexm error while verifying otp', @$user_id, @$data, $e->getMessage(), $url);
            // Activity::log('Flexm error while verifying otp - '.$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            // Activity::log('Flexm error while verifying otp - '.$e->getMessage(), @$user->id);
            addActivity('Flexm error while verifying otp', @$user_id, @$data, $e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function createProfile($flexm_token, $input, $user_id = '')
    {

        $data['id_type'] = $input['id_type'];
        $data['id_type_number'] = $input['id_type_number'];
        $data['id_date_expiry'] = $input['id_date_expiry'];
        $data['country_of_issue'] = (isset($input['country_of_issue']) && $input['country_of_issue'] != '')?$input['country_of_issue']:'Singapore';
        $data['nationality'] = (isset($input['nationality']) && $input['nationality'] != '') ?$input['nationality']:'Singaporean';
        $data['birthday'] = $input['birthday'];
        $data['gender'] = (isset($input['gender']) && $input['gender'] != '')?ucfirst($input['gender']):'Male';
        $data['title'] = (isset($input['title']) && $input['title'] != '')?$input['title']:'Mr';

        $url = self::BASE_URL.'user/profile';
        try{
            $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);

            $result = $client->put($url, [
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
                    addActivity('Flexm created profile', @$user_id, @$data, $content, $url);
                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    addActivity('Flexm error while creating profile', @$user_id, @$data, $content, $url);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            addActivity('Flexm error while creating profile reason code', @$user_id, @$data, ['code' => $code, 'reason' => $reason], $url);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            addActivity('Flexm error while creating profile', @$user_id, @$data, $jsonBody, $url);
            return response()->json(['status' => 'error', 'data' => $jsonBody->errors, 'message'=> $jsonBody->message], 200);
        }catch(GuzzleException $e){
            addActivity('Flexm error while creating profile', @$user_id, @$data, $e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            addActivity('Flexm error while creating profile', @$user_id, @$data, $e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function saveAdd($flexm_token, $input, $user_id = '')
    {
        $address_type = @$input['address_type'] != ''?@$input['address_type']:'billing';

        $data['address_1'] = $input['address_1'];
        $data['address_2'] = $input['address_2'] != ''?$input['address_2']:$input['address_1'];
        $data['address_1'] = substr($data['address_1'],0,34);
        $data['address_2'] = substr($data['address_2'],0,34); 
        $data['city'] = $input['city'];
        $data['state'] = $input['state'];
        $data['country'] = ucfirst($input['country']);
        $data['zipcode'] = $input['zipcode'];

        $url = self::BASE_URL.'user/addresses/'.$address_type;
        try{

            $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);

            $result = $client->put($url,[
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
                    addActivity('Flexm added address', @$user_id, @$data, $content, $url);
                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    addActivity('Flexm error while adding address', @$user_id, @$data, $content, $url);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            addActivity('Flexm error while adding address reason code', @$user_id, @$data, ['code' => $code, 'reason' => $reason], $url);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            addActivity('Flexm error while adding address', @$user_id, @$data, $jsonBody, $url);
            return response()->json(['status' => 'error', 'data' => $jsonBody->errors, 'message'=> $jsonBody->message], 200);
        }catch(GuzzleException $e){
            addActivity('Flexm error while adding address', @$user_id, @$data, $e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            addActivity('Flexm error while adding address', @$user_id, @$data, $e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function uploadDocImage($flexm_token, $image, $user_id = '')
    {
        $data['image'] = $image;
        $url  = self::BASE_URL.'user/documents';
        try{
            $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);

            $result = $client->post($url,[
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
                    addActivity('Flexm uploaded doc.', @$user_id, @$data, $content, $url);
                    return $this->submitDocImage($flexm_token);
                    // return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    addActivity('Flexm error while uploading doc.', @$user_id, @$data, $content, $url);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            addActivity('Flexm error while uploading doc reason code.', @$user_id, @$data, ['code' => $code, 'reason' => $reason], $url);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            addActivity('Flexm error while uploading doc.', @$user_id, @$data, $jsonBody, $url);
            return response()->json(['status' => 'error', 'data' => $jsonBody->errors, 'message'=> $jsonBody->message], 200);
        }catch(GuzzleException $e){
            addActivity('Flexm error while uploading doc.', @$user_id, @$data, $e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            addActivity('Flexm error while uploading doc.', @$user_id, @$data, $e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function submitDocImage($flexm_token, $user_id = '')
    {
        $url  = self::BASE_URL.'user/documents/submit';
        try{
            $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);

            $result = $client->patch($url);

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                if($content->success){
                    $msg = $content->message;
                    $cont = $content->data;
                    addActivity('Flexm submitted doc.', @$user_id, @$data, $content, $url);
                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    addActivity('Flexm error while submitting doc.', @$user_id, @$data, $content, $url);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            addActivity('Flexm error while submitting doc reason code.', @$user_id, @$data, ['code' => $code, 'reason' => $reason], $url);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            addActivity('Flexm error while submitting doc.', @$user_id, @$data, $jsonBody, $url);

            return response()->json(['status' => 'error', 'data' => $jsonBody->errors, 'message'=> $jsonBody->message], 200);
        }catch(GuzzleException $e){
            addActivity('Flexm error while submitting doc.', @$user_id, @$data, $e->getMessage(), $url);

            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            addActivity('Flexm error while submitting doc.', @$user_id, @$data, $e->getMessage(), $url);

            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function uploadDoc(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $flexm_token = $request->input('flexm_token');

        $data['image'] = $request->input('image');

        $url = self::BASE_URL.'user/documents';
        try{
            $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);

            $result = $client->post($url,[
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
                    addActivity('Flexm uploaded doc', @$user_id, @$data, $content, $url);
                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    addActivity('Flexm error while uploading doc', @$user_id, @$data, $content, $url);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            addActivity('Flexm error while uploading doc reason code', @$user_id, @$data, ['code'=> $code, 'reason' => $reason], $url);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            // Activity::log('Flexm error while uploading doc - '.json_encode($jsonBody), @$user->id);
            addActivity('Flexm error while uploading doc', @$user_id, @$data, $jsonBody, $url);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
            // Activity::log('Flexm error while uploading doc - '.$e->getMessage(), @$user->id);
            addActivity('Flexm error while uploading doc', @$user_id, @$data, $e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            // Activity::log('Flexm error while uploading doc - '.$e->getMessage(), @$user->id);
            addActivity('Flexm error while uploading doc', @$user_id, @$data, $e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function submitDoc(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $url = self::BASE_URL.'user/documents/submit';
        try{
            $flexm_token = $request->input('flexm_token');

            $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);

            $result = $client->patch($url);

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                if($content->success){
                    $msg = $content->message;
                    $cont = $content->data;
                    addActivity('Flexm submitted doc', @$user_id, @$data, $content, $url);
                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    addActivity('Flexm error while submitting doc', @$user_id, @$data, $content, $url);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            addActivity('Flexm error while submitting doc', @$user_id, @$data, ['code' => $code, 'reason' => $reason], $url);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            addActivity('Flexm error while submitting doc', @$user_id, @$data, $jsonBody, $url);
            // Activity::log('Flexm error while submitting doc - '.json_encode($jsonBody), @$user->id);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
            addActivity('Flexm error while submitting doc', @$user_id, @$data, $e->getMessage(), $url);
            // Activity::log('Flexm error while submitting doc - '.$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            addActivity('Flexm error while submitting doc', @$user_id, @$data, $e->getMessage(), $url);
            // Activity::log('Flexm error while submitting doc - '.$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function logout(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $url = self::BASE_URL.'auth/logout';
        try{
            $flexm_token = $request->input('flexm_token');

            $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);

            $result = $client->delete($url);

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                if($content->success){
                    $msg = $content->message;
                    $cont = $content->data;
                    addActivity('Flexm logged out', @$user_id, @$data, $content, $url);
                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    addActivity('Flexm error while logging out', @$user_id, @$data, $content, $url);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            addActivity('Flexm error while logging out reason code', @$user_id, @$data, ['code' => $code, 'reason' => $reason], $url);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            // Activity::log('Flexm error while logout - '.json_encode($jsonBody), @$user->id);
            addActivity('Flexm error while logging out', @$user_id, @$data, $jsonBody, $url);

            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
            // Activity::log('Flexm error while logout - '.$e->getMessage(), @$user->id);
            addActivity('Flexm error while logging out', @$user_id, @$data, $e->getMessage(), $url);

            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            // Activity::log('Flexm error while logout - '.$e->getMessage(), @$user->id);
            addActivity('Flexm error while logging out', @$user_id, @$data, $e->getMessage(), $url);

            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

}
