<?php

namespace App\Http\Controllers\Api;

use App\Events\SendBrowserNotification;

use App\User, Activity;
use Illuminate\Http\Request;

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
use Auth,JWTAuth;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Message\Request as GRequest;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use App\Models\FlexmHtml;
use App\Models\Merchant;
use App\Models\Terminal;
use App\Models\Course;
use App\Models\SpuulPlan;
use App\Models\SpuulSubscription;
use App\Models\Transactions;
use App\Models\Remittance;
use App\Models\CourseJoined;
use App\Models\FlexmWallet;
use App\Models\Activity as LogActivity;
use Carbon\Carbon;

class FlexmTopupController extends Controller
{
    // const BASE_URL = 'https://alpha.flexm.sg/api/';
    const BASE_URL = 'https://wallet.flexm.sg/api/';//'https://test-api.flexm.sg/';

    public function addActivity($text, $user_id, $data, $response, $url = ""){
      $ip_address = request()->ip();

      LogActivity::create([
        'text' => $text,
        'user_id' => ($user_id == '' ? null : $user_id),
        'ip_address' => $ip_address,
        'request' => json_encode($data),
        'response' => json_encode($response),
        'url'   => $url
      ]);
    }

    public function listCardInfo(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $data['card_id'] = $request->input('card_id');
        $flexm_token = $request->input('flexm_token');
        $card_id = $request->input('card_id');
        $url = self::BASE_URL.'user/wallets/cards/detail/'.$card_id;
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
                    $this->addActivity('Flexm list card info.', @$user_id, @$data, $content, $url);
                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    $this->addActivity('Flexm error list card info.', @$user_id, @$data, $content, $url);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            $this->addActivity('Flexm error list card info reason code.', @$user_id, @$data, ['code' => $code, 'reason' => $reason], $url);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            $this->addActivity('Flexm error while list card info.', @$user_id, @$data, $jsonBody, $url);
            // Activity::log('Flexm error while list card info - '.json_encode($jsonBody), @$user->id);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
            $this->addActivity('Flexm error while list card info.', @$user_id, @$data, $e->getMessage(), $url);
            // Activity::log('Flexm error while list card info - '.$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            $this->addActivity('Flexm error while list card info.', @$user_id, @$data, $e->getMessage(), $url);
            // Activity::log('Flexm error while list card info - '.$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function listSavedCardInfo(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $data['provider_hash'] = $request->input('provider_hash');
        $flexm_token = $request->input('flexm_token');
        $provider_hash = $request->input('provider_hash');

        $url = self::BASE_URL.'user/wallets/cards/token/'.$provider_hash;

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
                    $this->addActivity('Flexm fetched saved card list', @$user_id, @$data, $content, $url);
                    // Activity::log('Flexm fetched saved card list', @$user->id);
                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    $this->addActivity('Flexm error while fetching saved card list.', @$user_id, @$data, $content, $url);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            $this->addActivity('Flexm error while fetching saved card list reason code.', @$user_id, @$data, ['code' => $code, 'reason' => $reason], $url);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            $this->addActivity('Flexm error while fetching saved card list.', @$user_id, @$data, $jsonBody, $url);
            // Activity::log('Flexm error while fetching saved card list - '.json_encode($jsonBody), @$user->id);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
            // Activity::log('Flexm error while fetching saved card list - '.$e->getMessage(), @$user->id);
            $this->addActivity('Flexm error while fetching saved card list.', @$user_id, @$data, $e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            // Activity::log('Flexm error while fetching saved card list - '.$e->getMessage(), @$user->id);
            $this->addActivity('Flexm error while fetching saved card list.', @$user_id, @$data, $e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function listProvider(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));

        try{
            $flexm_token = $request->input('flexm_token');

            // $dd[] = [
            //   "id": "bc74af05ddda293430ea9e8928ffc8e1",
            //   "name": "Easypay2",
            //   "product_code": "flexmpo",
            //   "provider_hash": "86441e0baa1cd2da72b098feaccd15f3",
            //   "provider_type": "online",
            //   "provider_currency": "S$",
            //   "provider_mode": "Cards (Debit & Credit)",
            //   "date_created": "2018-09-04 17:08:19",
            //   "topup_min_amount": "10.000",
            //   "fee_details": {
            //       "fee_flat": "0.000",
            //       "fee_percentage": "2.50",
            //       "tax_flat": "0.000",
            //       "tax_percentage": "0.00"
            //   },
            //   "description": "Easypay by Wirecard",
            //   "image": {
            //       "source": "https://vcard-assets.s3.amazonaws.com/payments/easypay2.png"
            //   }
            // ];

            $dd = [
              [
                  "name" => "ATM",
                  "mode" => "online",
                  "provider_name"=> "ATM",
                  "provider_hash"=> "316643082e790943fe77e1eacea40cff",
                  "fees_percent"=> 0,
                  "fee_details"=> [
                      "fee_flat"=> "0.000",
                      "fee_percentage"=> 0,
                      "tax_flat"=> "0.000",
                      "tax_percentage"=> "0.00"
                  ],
                  "topup_duration" =>  "Instant",
                  "topup_min_amount" => "10.00",
                  "maximum_amt" => "999.00",
                  "rates" =>  [10, 20, 50, 100],
                  "status" =>  "active",
                  "has_guide" =>  false,
                  "display_name" =>  "DBS / POSB ATM",
                  "logo" =>  url('images/atm.png')
              ],
              [
                  "name" => "PayNow",
                  "mode" => "online",
                  "provider_name"=> "PayNow",
                  "provider_hash"=> "316643082e790943fe77e1eacea40cff",
                  "fees_percent"=> 0,
                  "fee_details"=> [
                      "fee_flat"=> "0.000",
                      "fee_percentage"=> 0,
                      "tax_flat"=> "0.000",
                      "tax_percentage"=> "0.00"
                  ],
                  "topup_duration" =>  "Instant",
                  "topup_min_amount" => "10.00",
                  "maximum_amt" => "999.00",
                  "rates" =>  [10, 20, 50, 100],
                  "status" =>  "active",
                  "has_guide" =>  false,
                  "display_name" =>  "PayNow",
                  "logo" =>  url('images/paynow.png')
              ],
              [
                  "name" =>  "DBS/POSB & OCBC",
                  "mode" =>  "offline",
                  "provider_name" =>  "dbs",
                  "provider_hash" =>  "N/A",
                  "fees_percent" =>  0,
                  "fee_details" =>  [
                      "fee_flat" =>  "0.000",
                      "fee_percentage" =>  "0.00",
                      "tax_flat" =>  "0.000",
                      "tax_percentage" =>  "0.00"
                  ],
                  "topup_duration" =>  "2-3 days",
                  "topup_min_amount" =>  "10.00",
                  "maximum_amt" =>  "999.99",
                  "rates" =>  [10, 20, 50, 100],
                  "status" =>  "active",
                  "has_guide" =>  true,
                  "display_name" =>  "DBS/POSB IBANKING/MOBILE APP",
                  "logo" =>  url('images/dbs.png')//"https://s3-ap-southeast-1.amazonaws.com/flexm/mobile/payment-providers/assets/dbs-logo.png"
              ],
              [
                      "name" =>  "Enets",
                      "mode" =>  "online",
                      "provider_name" =>  "Enets",
                      "provider_hash" =>  "69148e31855f8204468c48a96efab164",
                      "fees_percent" =>  2.8,
                      "fee_details" =>  [
                          "fee_flat" =>  "0.000",
                          "fee_percentage" =>  2.8,
                          "tax_flat" =>  "0.000",
                          "tax_percentage" =>  "0.00"
                      ],
                      "topup_duration" =>  "Instant",
                      "topup_min_amount" => "10.00",
                      "maximum_amt" => "999.99",
                      "rates" =>  [10, 20, 50, 100],
                      "status" =>  "active",
                      "has_guide" =>  false,
                      "display_name" =>  "eNETS",
                      "logo" =>  "https://s3-ap-southeast-1.amazonaws.com/flexm/mobile/payment-providers/assets/enets-logo.png"
              ],
              [
                  "name" => "DBS/POSB Cash Deposit Machine (CDM)",
                  "mode" =>  "offline",
                  "provider_name" =>  "cdm",
                  "provider_hash" =>  "N/A",
                  "fees_percent" =>  0,
                  "fee_details" =>  [
                      "fee_flat" =>  "0.000",
                      "fee_percentage" =>  "0.00",
                      "tax_flat" =>  "0.000",
                      "tax_percentage" =>  "0.00"
                  ],
                  "topup_duration" =>  "2-3 days",
                  "topup_min_amount" => "10.00",
                  "maximum_amt" => "999.99",
                  "rates" =>  [10, 20, 50, 100],
                  "status" =>  "active",
                  "has_guide" =>  true,
                  "display_name" =>  "DBS/POSB Cash Deposit Machine (CDM)",
                  "logo" =>  "https://s3-ap-southeast-1.amazonaws.com/flexm/mobile/payment-providers/assets/cdm-logo.png"
              ],
              [
                          "name" => "MasterCardDirectPayment",
                          "mode" => "online",
                          "provider_name"=> "MasterCardDirectPayment",
                          "provider_hash"=> "316643082e790943fe77e1eacea40cff",
                          "fees_percent"=> 2.5,
                          "fee_details"=> [
                              "fee_flat"=> "0.000",
                              "fee_percentage"=> 2.50,
                              "tax_flat"=> "0.000",
                              "tax_percentage"=> "0.00"
                          ],
                          "topup_duration" =>  "Instant",
                          "topup_min_amount" => "10.00",
                          "maximum_amt" => "999.00",
                          "rates" =>  [10, 20, 50, 100],
                          "status" =>  "active",
                          "has_guide" =>  false,
                          "display_name" =>  "VISA / MASTERCARD",
                          "logo" =>  url('images/visa_new.png')//"https://s3-ap-southeast-1.amazonaws.com/flexm/mobile/payment-providers/assets/visa-mastercard.png"
                ],
                [
                      "name" =>  "FlexM Stores",
                      "mode" =>  "offline",
                      "provider_name" =>  "flexm",
                      "provider_hash" =>  "N/A",
                      "fees_percent" =>  0,
                      "fee_details" =>  [
                          "fee_flat" =>  "0.000",
                          "fee_percentage" =>  "0.00",
                          "tax_flat" =>  "0.000",
                          "tax_percentage" =>  "0.00"
                      ],
                      "topup_duration" =>  "2-3 days",
                      "topup_min_amount" => "10.00",
                      "maximum_amt" => "999.99",
                      "rates" =>  [10, 20, 50, 100],
                      "status" =>  "active",
                      "has_guide" =>  false,
                      "display_name" =>  "FlexM Stores",
                      "logo" =>  "https://s3-ap-southeast-1.amazonaws.com/flexm/mobile/payment-providers/assets/flexm-logo.png"
                  ]
                ];

            return response()->json(['status' => "success", 'data' => $dd, 'message'=> ''], 200);

            // $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);
            //
            // $result = $client->get(self::BASE_URL.'user/wallets/funds/providers');
            //
            // $code = $result->getStatusCode(); // 200
            // $reason = $result->getReasonPhrase(); // OK
            // if($code == "200" && $reason == "OK"){
            //     $body = $result->getBody();
            //     $content = json_decode($body->getContents());
            //     if($content->success){
            //         $msg = $content->message;
            //         $cont = $content->data;
            //         return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
            //     }else{
            //         return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
            //     }
            // }
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

    public function createProvider(Request $request)
    {

        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $url = self::BASE_URL.'user/wallets/funds/providers';

        $flexm_token = $request->input('flexm_token');
        $data['amount'] = $request->input('amount');
        if($data['amount'] < 1 || $data['amount'] > 999){
          return response()->json(['status' => 'error', 'data' => [], 'message' => 'Amount should be between the min-max range.']);
        }
        $data['expiry_month'] = $request->input('expiry_month');
        $data['expiry_year'] = $request->input('expiry_year');
        $data['mobile'] = $request->input('mobile');
        $data['mobile_country_code'] = $request->input('mobile_country_code');
        $data['pan'] = $request->input('pan');
        $data['provider_name'] = $request->input('provider_name');
        $data['save_card'] = $request->input('save_card');
        $data['security_code'] = $request->input('security_code');

        try{

            $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);

            $result = $client->post($url, [
                'form_params' => $data
            ]);

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                if($content->success){
                    $msg = $content->message;
                    $cont = $content->data;
                    $this->addActivity('Flexm topup created.', @$user_id, @$data, $content, $url);
                    if(isset($cont->payment->html_content)){

                        $url = route('html', $user->id);
                        $html = $cont->payment->html_content;
                        FlexmHtml::create([
                          'user_id' => $user->id,
                          'html'  => $html
                        ]);
                        $cont->payment->html_link = $url;
                    }

                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    $this->addActivity('Flexm error while creating provider.', @$user_id, @$data, $content, $url);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            $this->addActivity('Flexm error while creating provider reason code.', @$user_id, @$data, ['code' => $code, 'reason' => $reason], $url);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            // \Log::debug(json_encode($jsonBody));
            $msg = $jsonBody->message;
            if($msg == ''){
              $msg = $jsonBody->ih_response_body;
            }
            $this->addActivity('Flexm error while creating provider.', @$user_id, @$data, $jsonBody, $url);
            // Activity::log('Flexm error while creating provider - '.json_encode($jsonBody), @$user->id);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$msg], 200);
        }catch(GuzzleException $e){
            $this->addActivity('Flexm error while creating provider.', @$user_id, @$data, $e->getMessage(), $url);
            // Activity::log('Flexm error while creating provider - '.$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            $this->addActivity('Flexm error while creating provider.', @$user_id, @$data, $e->getMessage(), $url);

            // Activity::log('Flexm error while creating provider - '.$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function getTxnsHistory(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $flexm_token = $request->input('flexm_token');
        $page_no = $request->input('page_no');
        if($page_no == '' || $page_no < 2){
          $page_no = 1;
        }

        $url = self::BASE_URL.'user/wallets/transactions?page='.$page_no;
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
                    
                if(isset($cont->transactions))
                {
                    foreach($cont->transactions as $cc){
                      if(@$cc->details->merchant){
                        $cc->details->merchant = $cc->details->merchant->merchant_name;
                      }
                      if($cc->type == 'Load'){
                        $cc->type = 'Unload';
                        $cc->description = 'Money transferred from wallet to card.';
                      }
                      elseif($cc->type == 'Unload'){
                        $cc->type = 'Load';
                        $cc->description = 'Money transferred from card to wallet.';
                      }
                      elseif($cc->type == 'Money Transfer Debit'){
                        $txns = Transactions::where('transaction_ref_no', $cc->id)->first();
                        if($txns){
                          if($txns->type == 'spuul'){
                            $cc->description = 'Spuul subscription payment.';
                          }

                          if($txns->type == 'course'){
                            $cc->description = 'Bought a course.';
                          }

                          if($txns->type == 'food'){
                            $cc->description = 'Ordered a food item.';
                          }

                          if($txns->type == 'instore'){
                            $cc->description = 'Instore Payment.';
                          }
                        }
                        if($cc->description == 'remittances'){
                          $status_id = $stat = '';
                          if(isset($cc->details) && isset($cc->details->status_update_history)){
                            $statuses = $cc->details->status_update_history;

                            foreach($statuses as $status){
                              $status_id = $status->new_status;
                            }
                            switch($status_id){
                              case '001':
                                $stat = 'Processing';
                                break;
                              case '002':
                                $stat = 'Transfer pending';
                                break;
                              case '003':
                                $stat = 'Transfer failed';
                                break;
                              case '004':
                                $stat = 'Refund failed';
                                break;
                              case '005':
                                $stat = 'Refund succeeded';
                                break;
                              case '006':
                                $stat = 'Available';
                                break;
                              case '007':
                                $stat = 'Paid';
                                break;
                              case '008':
                                $stat = 'Cancelled';
                                break;
                              case '009':
                                $stat = 'Cancelled by user';
                                break;
                              case '010':
                                $stat = 'Rejected';
                                break;
                              case '011':
                                $stat = 'No response';
                                break;
                              case '012':
                                $stat = 'Gateway timeout';
                                break;
                              case '013':
                                $stat = '';
                                break;
                              case '014':
                                $stat = 'Cancel in progress';
                                break;
                              case '015':
                                $stat = 'Expired without refund';
                                break;
                              case '016':
                                $stat = 'Manual refund needed';
                                break;
                              case '017':
                                $stat = 'Expired and refunded';
                                break;

                              default:
                                $stat = '';
                            }
                          }
                          $cc->remittance_status = $stat;
                          $cc->pickup_id = 'QWE123';

                        }
                      }
                    }
                }
                    $this->addActivity('Flexm fetched txn history.', @$user_id, @$data, $content, $url);
                    // Activity::log('Flexm fetched txn history', @$user->id);
                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    $this->addActivity('Flexm error while fetching txn history.', @$user_id, @$data, $content, $url);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            $this->addActivity('Flexm error while fetching txn history.', @$user_id, @$data, $jsonBody, $url);
            // Activity::log('Flexm error while fetching txn history - '.json_encode($jsonBody), @$user->id);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
            $this->addActivity('Flexm error while fetching txn history.', @$user_id, @$data, $e->getMessage(), $url);
            // Activity::log('Flexm error while fetching txn history - '.$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            // Activity::log('Flexm error while fetching txn history - '.$e->getMessage(), @$user->id);
            $this->addActivity('Flexm error while fetching txn history.', @$user_id, @$data, $e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function paymentRecordByID(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $data['ref_id'] = $request->input('ref_id');

        $flexm_token = $request->input('flexm_token');
        $ref_id = $request->input('ref_id');

        $url = self::BASE_URL.'user/payment/status/'.$ref_id;
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
                    $this->addActivity('Flexm fetched txn record by id.', @$user_id, @$data, $content, $url);
                    // Activity::log('Flexm fetched txn record by id', @$user->id);
                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    $this->addActivity('Flexm error while fetching payment record.', @$user_id, @$data, $content, $url);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            $this->addActivity('Flexm error while fetching payment record.', @$user_id, @$data, $jsonBody, $url);
            // Activity::log('Flexm error while fetching payment record - '.json_encode($jsonBody), @$user->id);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
            // Activity::log('Flexm error while fetching payment record - '.$e->getMessage(), @$user->id);
            $this->addActivity('Flexm error while fetching payment record.', @$user_id, @$data, $e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            // Activity::log('Flexm error while fetching payment record - '.$e->getMessage(), @$user->id);
            $this->addActivity('Flexm error while fetching payment record.', @$user_id, @$data, $e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function cardType(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $data['type'] = 'centurion';
        $url = self::BASE_URL.'user/cards/types/centurion';
        try{
            $flexm_token = $request->input('flexm_token');

            $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);

            $result = $client->get($url);

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                \Log::debug(json_encode($content));
                if($content->success){
                    $msg = $content->message;
                    $cont = $content->data;
                    foreach($cont->types as $cc){
                      if($cc->code == 'flexmcemccard'){
                        $cc->tnc = 'MyMA Virtual Card is issued by MatchMove Pay Pte Ltd, an authorised issuer of Mastercard Prepaid Cards in Singapore. MyMA app is a co-brand partner of MatchMove Pay Pte Ltd under a commercial agreement between MyMA app and MatchMove Pay Pte Ltd';
                      }else{
                        $cc->tnc = 'MyMA PhysicalCard is issued by MatchMove Pay Pte Ltd, an authorised issuer of Mastercard Prepaid Cards in Singapore. MyMA app is a co-brand partner of MatchMove Pay Pte Ltd under a commercial agreement between MyMA app and MatchMove Pay Pte Ltd';
                      }
                      $cc->image = [];
                      $cc->image['small'] = $cc->image_small;
                      $cc->image['medium'] = $cc->image_medium;
                      $cc->image['large'] = $cc->image_large;
                      if(isset($cc->custom_description))
                        $cc->description = $cc->custom_description;
                    }
                    // Activity::log('Flexm fetched fetched card type', @$user->id);
                    $this->addActivity('Flexm fetched card type.', @$user_id, @$data, $content, $url);
                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    $this->addActivity('Flexm error while fetching card type.', @$user_id, @$data, $content, $url);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            $this->addActivity('Flexm error while fetching card type reason code.', @$user_id, @$data, ['code' => $code, 'reason' => $reason], $url);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            $this->addActivity('Flexm error while fetching card type.', @$user_id, @$data, $jsonBody, $url);
            // Activity::log('Flexm error while fetching card type - '.json_encode($jsonBody), @$user->id);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
            // Activity::log('Flexm error while fetching card type - '.$e->getMessage(), @$user->id);
            $this->addActivity('Flexm error while fetching card type.', @$user_id, @$data, $e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            $this->addActivity('Flexm error while fetching card type.', @$user_id, @$data, $e->getMessage(), $url);
            // Activity::log('Flexm error while fetching card type - '.$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function createCard(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $flexm_token = $request->input('flexm_token');
        $data['type'] = $request->input('type');
        // if($data['type'] == 'Virtual Card'){
        //   $data['type'] = 'flexmmccard';
        // }else{
        //   $data['type'] = 'flexmpcard';
        // }
        \Log::debug('add card');
        \Log::debug($request->all());
        
        // $cont = '{"id":"4ea6077c199d55d9488567f40c5c3910","number":"KCDCl2cmBjDCvsO2XVTCrRTCgzHCuQ==","holder":{"name":"Benny Neo"},"funds":{"available":{"currency":"S$","amount":"0.00"},"withholding":{"currency":"S$","amount":"0.00"}},"type":{"type":"flexmcemcpcard","name":"MyMA Physical Mastercard","description":"MyMA Physical Card"},"date":{"expiry":"2024-01","issued":"2019-03-25","closed":null},"image":{"small":"https:\/\/vcard-assets.s3.amazonaws.com\/sg\/products\/flexmcemcpcard\/card-small.png","medium":"https:\/\/vcard-assets.s3.amazonaws.com\/sg\/products\/flexmcemcpcard\/card-medium.png","large":"https:\/\/vcard-assets.s3.amazonaws.com\/sg\/products\/flexmcemcpcard\/card-large.png"},"status":{"is_active":false,"text":"pending activation"},"links":[{"rel":"securities.tokens","href":"https:\/\/flexm-ih.mmvpay.com\/api\/v1\/users\/wallets\/cards\/4ea6077c199d55d9488567f40c5c3910\/securities\/tokens","method":"GET"},{"rel":"cards.activation","href":"https:\/\/flexm-ih.mmvpay.com\/api\/v1\/users\/wallets\/cards\/4ea6077c199d55d9488567f40c5c3910","method":"PUT"}],"name":null,"activation":{"status":"pending","token":"e5c79313e941cb7732acd34f04fb8e91"}}';
        // $cont = json_decode($cont);
        // $id = $toke = '';
        // if(isset($cont->activation)){
        //     $toke = $cont->activation->token;
        // }
        // if(isset($cont->id)){
        //     $id = $cont->id;
        // }
        // return response()->json(['status' => "success", 'data' => $cont, 'message'=> 'Success', 'id' => $id, 'token' => $toke], 200);
        
        if($request->input('assoc_number') != ''){
            $data['assoc_number'] = $request->input('assoc_number');
        }
        $url = self::BASE_URL.'user/wallets/cards';
        try{
            $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);

            $result = $client->post($url, [
                'json' => $data
            ]);

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                \Log::debug('response');
                \Log::debug(json_encode($content));
                if($content->success){
                    $msg = $content->message;
                    $cont = $content->data;
                    if(isset($cont->activation)){
                        $cont->token = $cont->activation->token;
                    }
                    $this->addActivity('Flexm added new card.', @$user_id, @$data, $content, $url);
                    // Activity::log('Flexm created new card', @$user->id);
                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    $this->addActivity('Flexm error while adding new card.', @$user_id, @$data, $content, $url);

                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            $this->addActivity('Flexm error while adding new card reason code.', @$user_id, @$data, ['code' => $code, 'reason' => $reason], $url);

            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            $this->addActivity('Flexm error while adding new card.', @$user_id, @$data, $jsonBody, $url);

            // Activity::log('Flexm error while creating card - '.json_encode($jsonBody), @$user->id);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message?@$jsonBody->message:@$jsonBody->ih_response_body], 200);
        }catch(GuzzleException $e){
          $this->addActivity('Flexm error while adding new card.', @$user_id, @$data, $e->getMessage(), $url);

            // Activity::log('Flexm error while creating card - '.$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
          $this->addActivity('Flexm error while adding new card.', @$user_id, @$data, $e->getMessage(), $url);

            // Activity::log('Flexm error while creating card - '.$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }
    
    public function cardOTP(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        // \Log::debug(" card verify otp");
        // return response()->json(['status' => "success", 'data' => '', 'message'=> ''], 200);
        $type = 'flexmcemcpcard';
        if($request->input('type') == 'Physical Card'){
            $type = 'flexmcemcpcard';
        }
        $flexm_token = $request->input('flexm_token');
        $data['type'] = $type;
        $data['id'] = $request->input('id');
        $data['token'] = $request->input('otp_token');
        $data['otp_number'] = $request->input('otp_number');

        $url = self::BASE_URL.'user/wallets/cards/activate';
        try{
            $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);

            $result = $client->put($url, [
                'json' => $data
            ]);

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                if($content->success){
                    $msg = $content->message;
                    $cont = $content->data;
                    $this->addActivity('Flexm new card otp verified.', @$user_id, @$data, $content, $url);
                    // Activity::log('Flexm created new card', @$user->id);
                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    $this->addActivity('Flexm error while verifying new card otp.', @$user_id, @$data, $content, $url);

                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            $this->addActivity('Flexm error while verifying new card otp.', @$user_id, @$data, ['code' => $code, 'reason' => $reason], $url);

            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            $this->addActivity('Flexm error while verifying new card otp.', @$user_id, @$data, $jsonBody, $url);

            // Activity::log('Flexm error while creating card - '.json_encode($jsonBody), @$user->id);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message?@$jsonBody->message:@$jsonBody->ih_response_body], 200);
        }catch(GuzzleException $e){
          $this->addActivity('Flexm error while verifying new card otp.', @$user_id, @$data, $e->getMessage(), $url);

            // Activity::log('Flexm error while creating card - '.$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
          $this->addActivity('Flexm error while verifying new card otp.', @$user_id, @$data, $e->getMessage(), $url);

            // Activity::log('Flexm error while creating card - '.$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }
    
    public function getBalance(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $url = self::BASE_URL.'user/wallets';
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
                    $this->addActivity('Flexm fetched card listing.', @$user_id, @$data, $content, $url);
                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    $this->addActivity('Flexm error while fetching card listing.', @$user_id, @$data, $content, $url);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            $this->addActivity('Flexm error while fetching card listing reason code.', @$user_id, @$data, ['code' => $code, 'reason' => $reason], $url);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            $this->addActivity('Flexm error while fetching card listing.', @$user_id, @$data, $jsonBody, $url);
            // Activity::log('Flexm error while fetching card list - '.json_encode($jsonBody), @$user->id);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
            // Activity::log('Flexm error while fetching card list - '.$e->getMessage(), @$user->id);
            $this->addActivity('Flexm error while fetching card listing.', @$user_id, @$data, $e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            // Activity::log('Flexm error while fetching card list - '.$e->getMessage(), @$user->id);
            $this->addActivity('Flexm error while fetching card listing.', @$user_id, @$data, $e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function listCard(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $url = self::BASE_URL.'user/wallets';
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
                    $card_funds = 0;
                    $flag = false;
                    $this->addActivity('Flexm fetched card listing.', @$user_id, @$data, $content, $url);

                    foreach($cont->cards as $single){
                      if($single->status != 'locked'){
                        $card_id = $single->id;

                        $cclient = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);
                        $uurl = self::BASE_URL.'user/wallets/cards/detail/'.$card_id;
                        $rresult = $cclient->get($uurl);

                        $ccode = $rresult->getStatusCode(); // 200
                        $rreason = $rresult->getReasonPhrase(); // OK
                        if($ccode == "200" && $rreason == "OK"){
                            $bbody = $rresult->getBody();
                            $ccontent = json_decode($bbody->getContents());
                            if($ccontent->success){
                                $ccont = $ccontent->data;
                                $card_funds = @$ccont->funds->available->amount;
                                $this->addActivity('Flexm fetched card detail.', @$user_id, [$card_id], $ccontent, $uurl);

                                if($card_funds != ''){
                                  $flag = true;
                                }
                            }else{
                              $this->addActivity('Flexm error while fetching card detail.', @$user_id, [$card_id], $ccontent, $uurl);

                            }
                        }
                      }
                      if($flag){
                        break;
                      }
                    }
                    //to return firstt card's value
                    if($card_funds == ''){
                      $card_funds = 0;
                    }
                    $cont->card_funds_available = $card_funds;
                    // Activity::log('Flexm fetched card list', @$user->id);
                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    $this->addActivity('Flexm error while fetching card listing.', @$user_id, @$data, $content, $url);

                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            $this->addActivity('Flexm error while fetching card listing reason code.', @$user_id, @$data, ['code' => $code, 'reason' => $reason], $url);

            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            $this->addActivity('Flexm error while fetching card listing.', @$user_id, @$data, $jsonBody, $url);

            // Activity::log('Flexm error while fetching card list - '.json_encode($jsonBody), @$user->id);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
            // Activity::log('Flexm error while fetching card list - '.$e->getMessage(), @$user->id);
            $this->addActivity('Flexm error while fetching card listing.', @$user_id, @$data, $e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            // Activity::log('Flexm error while fetching card list - '.$e->getMessage(), @$user->id);
            $this->addActivity('Flexm error while fetching card listing.', @$user_id, @$data, $e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function retrieveCVV(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $data['card_id'] = $request->input('card_id');
        $card_id = $request->input('card_id');
        $url = self::BASE_URL.'user/wallets/cards/security/token?card_id='.$card_id;
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
                    // Activity::log('Flexm fetched card cvv', @$user->id);
                    $this->addActivity('Flexm fetched card cvv.', @$user_id, @$data, $content, $url);
                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    $this->addActivity('Flexm error while fetching card cvv.', @$user_id, @$data, $content, $url);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            $this->addActivity('Flexm error while fetching card cvv reason code.', @$user_id, @$data, ['code' => $code, 'reason' => $reason], $url);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            // Activity::log('Flexm error while retrieving cvv - '.json_encode($jsonBody), @$user->id);
            $this->addActivity('Flexm error while fetching card cvv.', @$user_id, @$data, $jsonBody, $url);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
            // Activity::log('Flexm error while retrieving cvv - '.$e->getMessage(), @$user->id);
            $this->addActivity('Flexm error while fetching card cvv.', @$user_id, @$data, $e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            // Activity::log('Flexm error while retrieving cvv - '.$e->getMessage(), @$user->id);
            $this->addActivity('Flexm error while fetching card cvv.', @$user_id, @$data, $e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function suspendCard(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $data['card_id'] = $request->input('card_id');
        $card_id = $request->input('card_id');
        $url = self::BASE_URL.'user/wallets/cards/'.$card_id;
        try{
            $flexm_token = $request->input('flexm_token');
            $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);

            $result = $client->delete($url, [
                'form_params' => [
                    'card_id' => $card_id
                ]
            ]);

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                if($content->success){
                    $msg = $content->message;
                    $cont = $content->data;
                    // Activity::log('Flexm fetched suspeneded card - '.$card_id, @$user->id);
                    $this->addActivity('Flexm suspended card.', @$user_id, @$data, $content, $url);
                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                  $this->addActivity('Flexm error while suspending card.', @$user_id, @$data, $content, $url);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            $this->addActivity('Flexm error while suspending card reason code.', @$user_id, @$data, ['code' => $code, 'reason' => $reason], $url);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            // Activity::log('Flexm error while suspending card - '.json_encode($jsonBody), @$user->id);
            $this->addActivity('Flexm error while suspending card.', @$user_id, @$data, $jsonBody, $url);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
            // Activity::log('Flexm error while suspending card - '.$e->getMessage(), @$user->id);
            $this->addActivity('Flexm error while suspending card.', @$user_id, @$data, $e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            // Activity::log('Flexm error while suspending card - '.$e->getMessage(), @$user->id);
            $this->addActivity('Flexm error while suspending card.', @$user_id, @$data, $e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function hisotryCard(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $flexm_token = $request->input('flexm_token');
        $card_id = $request->input('card_id');
        $url = self::BASE_URL.'user/wallets/cards/transactions/'.$card_id;
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
                    // Activity::log('Flexm fetched card history - '.$card_id, @$user->id);
                    $this->addActivity('Flexm fetched card history.', @$user_id, @$data, $content, $url);
                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                  $this->addActivity('Flexm error while while checking history of card.', @$user_id, @$data, $content, $url);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            $this->addActivity('Flexm error while while checking history of card reason code.', @$user_id, @$data, ['code' => $code, 'reason' => $reason], $url);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            // Activity::log('Flexm error while checking history of card - '.json_encode($jsonBody), @$user->id);
            $this->addActivity('Flexm error while while checking history of card.', @$user_id, @$data, $jsonBody, $url);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
            // Activity::log('Flexm error while checking history of card - '.$e->getMessage(), @$user->id);
            $this->addActivity('Flexm error while while checking history of card.', @$user_id, @$data, $e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            // Activity::log('Flexm error while checking history of card - '.$e->getMessage(), @$user->id);
            $this->addActivity('Flexm error while while checking history of card.', @$user_id, @$data, $e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function walletToCard(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $flexm_token = $request->input('flexm_token');
        $data['id'] = $request->input('card_id');
        $data['amount'] = $request->input('amount');

        $url = self::BASE_URL.'user/wallets/cards/funds';
        try{

            $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);

            $result = $client->post($url, [
                'form_params' => $data
            ]);

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                if($content->success){
                    $msg = $content->message;
                    $cont = $content->data;
                    // Activity::log('Flexm successfull transfer of amount from wallet to card #'.$data['id'].' Amount #'.$data['amount'], @$user->id);
                    $this->addActivity('Flexm wallet to card transfer success.', @$user_id, @$data, $content, $url);
                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    $this->addActivity('Flexm error while wallet to card transfer.', @$user_id, @$data, $content, $url);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            $this->addActivity('Flexm error while wallet to card transfer reason code.', @$user_id, @$data, ['code' => $code, 'reason' => $reason], $url);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            // Activity::log('Flexm error while wallet to card transfer - '.json_encode($jsonBody), @$user->id);
            $this->addActivity('Flexm error while wallet to card transfer.', @$user_id, @$data, $jsonBody, $url);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
            // Activity::log('Flexm error while wallet to card transfer - '.$e->getMessage(), @$user->id);
            $this->addActivity('Flexm error while wallet to card transfer.', @$user_id, @$data, $e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            // Activity::log('Flexm error while wallet to card transfer - '.$e->getMessage(), @$user->id);
            $this->addActivity('Flexm error while wallet to card transfer.', @$user_id, @$data, $e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function cardToWallet(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $flexm_token = $request->input('flexm_token');
        $card_id = $request->input('card_id');
        $data['amount'] = $request->input('amount');

        $url = self::BASE_URL.'user/wallets/cards/funds/'.$card_id;
        try{

            $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);

            $result = $client->delete($url, [
                'form_params' => $data
            ]);

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                if($content->success){
                    $msg = $content->message;
                    $cont = $content->data;
                    // Activity::log('Flexm successfull transfer of amount from card to wallet card_id -'.$card_id.' Amount #'.@$data['amount'], @$user->id);
                    $this->addActivity('Flexm card to wallet transfer success.', @$user_id, @$data, $content, $url);
                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    $this->addActivity('Flexm error while card to wallet transfer.', @$user_id, @$data, $content, $url);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            $this->addActivity('Flexm error while card to wallet transfer reason code.', @$user_id, @$data, ['code' => $code, 'reason' => $reason], $url);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            // Activity::log('Flexm error while card to wallet transfer - '.json_encode($jsonBody), @$user->id);
            $this->addActivity('Flexm error while card to wallet transfer.', @$user_id, @$data, $jsonBody, $url);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
            // Activity::log('Flexm error while card to wallet transfer - '.$e->getMessage(), @$user->id);
            $this->addActivity('Flexm error while card to wallet transfer.', @$user_id, @$data, $e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            // Activity::log('Flexm error while card to wallet transfer - '.$e->getMessage(), @$user->id);
            $this->addActivity('Flexm error while card to wallet transfer.', @$user_id, @$data, $e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function walletToWallet(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $flexm_token = $request->input('flexm_token');
        $data['mobile'] = $request->input('mobile');
        $data['mobile_country_code'] = $request->input('mobile_country_code');
        $data['amount'] = $request->input('amount');
        $data['message'] = $request->input('message');
        $url = self::BASE_URL.'user/wallets/funds/sendmoney/transfers';
        try{


            $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);

            $result = $client->post($url, [
                'form_params' => $data
            ]);

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                if($content->success){
                    $msg = $content->message;
                    $cont = $content->data;
                    // Activity::log('Flexm successfull transfer of amount from wallet to wallet Recipient - '.@$data['mobile'], @$user->id);
                    FlexmWallet::create([
                        'user_id' => $user->id,
                        'phone' => @$data['mobile'],
                        'country_code' => @$data['mobile_country_code'],
                        'amount'    => @$data['amount'],
                        'message' => @$data['message'],
                        'transaction_id' => @$cont->id,
                        'status'    => @$cont->status,
                        'from'  => 'wallet',
                        'to'    => 'wallet'
                    ]);
                    $this->addActivity('Flexm wallet to wallet transfer success.', @$user_id, @$data, $content, $url);
                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    $this->addActivity('Flexm error while wallet to wallet transfer.', @$user_id, @$data, $content, $url);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            $this->addActivity('Flexm error while wallet to wallet transfer reason code.', @$user_id, @$data, ['code' => $code, 'reason' => $reason], $url);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            // Activity::log('Flexm error while wallet to wallet transfer - '.json_encode($jsonBody), @$user->id);
            $this->addActivity('Flexm error while wallet to wallet transfer.', @$user_id, @$data, $jsonBody, $url);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
            // Activity::log('Flexm error while wallet to wallet transfer - '.$e->getMessage(), @$user->id);
            $this->addActivity('Flexm error while wallet to wallet transfer.', @$user_id, @$data, $e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            // Activity::log('Flexm error while wallet to wallet transfer - '.$e->getMessage(), @$user->id);
            $this->addActivity('Flexm error while wallet to wallet transfer.', @$user_id, @$data, $e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function remittanceProviders(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $flexm_token = $request->input('flexm_token');
        $url = self::BASE_URL.'user/wallets/funds/transfers/overseas/types';
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
                    $this->addActivity('Flexm fetched remittance provider.', @$user_id, @$data, $content, $url);
                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    $this->addActivity('Flexm error while fetching remittance provider.', @$user_id, @$data, $content, $url);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            $this->addActivity('Flexm error while fetching remittance provider reason code.', @$user_id, @$data, ['code' => $code, 'reason' => $reason], $url);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            // Activity::log('Flexm error while fetching remiitance provider - '.json_encode($jsonBody), @$user->id);
            $this->addActivity('Flexm error while fetching remittance provider.', @$user_id, @$data, $jsonBody, $url);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
            // Activity::log('Flexm error while fetching remiitance provider - '.$e->getMessage(), @$user->id);
            $this->addActivity('Flexm error while fetching remittance provider.', @$user_id, @$data, $e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            // Activity::log('Flexm error while fetching remiitance provider - '.$e->getMessage(), @$user->id);
            $this->addActivity('Flexm error while fetching remittance provider.', @$user_id, @$data, $e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function remittanceCorridors(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $flexm_token = $request->input('flexm_token');
        $provider = $request->input('provider');
        $url = self::BASE_URL.'user/wallets/funds/transfers/overseas/fees/'.$provider.'?limit=500&provider_id='.$provider.'&language=en';
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
                    $corr = [];
                    $ids = [
                      '1c383cd30b7c298ab50293adfecb7b18',
                      'c20ad4d76fe97759aa27a0c99bff6710'
                    ];
                    $wids = [
                      'a5771bce93e200c36f7cd9dfd0e5deaa',
                      '37693cfc748049e45d87b8c7d8b9aacd',
                      '6ea9ab1baa0efb9e19094440c317e21b',
                      '37693cfc748049e45d87b8c7d8b9aacd',
                      '6364d3f0f495b6ab9dcf8d3b5c6e0b01'
                    ];

                    $moneygram_ids = [
                      '8e296a067a37563370ded05f5a3bf3ec',
                      '98f13708210194c475687be6106a3b84',
                      '3c59dc048e8850243be8079a5c74d079'
                    ];
                    $hids = [
                      'c51ce410c124a10e0db5e4b97fc2af39',
                      'aab3238922bcc25a6f606eb525ffdc56',
                    ];
                    foreach($cont->fees as $dd){
                        if($dd->id == '4e732ced3463d06de0ca9a15b6153677'){
                            continue;
                        }
                        $dd->active = true;
                        $dd->display_name = @$dd->full_details->agent_name?@$dd->full_details->agent_name:$dd->partner_name;
                        if(isset($dd->full_details->agent_abbreviation) && $dd->full_details->agent_abbreviation != ''){
                          $dd->display_name = $dd->display_name.' - '.$dd->full_details->agent_abbreviation;
                        }
                        // if(isset($dd->payer_details)){
                        //   $dd->payer_details = $dd->id;
                        // }
                        
                        $payment_mode = $dd->payment_mode;
                        if(in_array($payment_mode, ['BA','BANK_DEPOSIT','BAN'])){
                            $payment_mode = 'bank_deposit';
                            // if($provider == 'moneygram'){
                            //   $dd->partner_name = @$dd->full_details->agent_name?@$dd->full_details->agent_name.' '.@$dd->full_details->agent_abreviation:$dd->partner_name;
                            // }
                        }elseif(in_array($payment_mode, ['OF', 'WILL_CALL', 'LTD_WILLCALL', 'EWALLET'])){
                            $payment_mode = 'cash_pickup';
                        }elseif(in_array($payment_mode, ['MBP','MWALLET'])){
                            $payment_mode = 'mobile_wallet';
                        }elseif(in_array($payment_mode, ['HOME_DELIVERY'])){
                            $payment_mode = 'home_delivery';
                        }

                        if(@$dd->full_details->agent_name){
                          $agent = str_replace('_',' ',strtolower($dd->full_details->agent_name));
                          // $agent = str_replace(' ','-',strtolower($agent));
                          if(in_array($dd->id, $ids) || $dd->display_name == 'CITI BANK (BANGLADESH)' || $dd->display_name == 'STATE BANK OF INDIA (BANGLADESH)'){
                            $agent = str_replace('(','',strtolower($agent));
                            $agent = str_replace(')','',strtolower($agent));
                          }
                          if (strpos($agent, '.') !== false) {
                            $agent = str_replace('.','',strtolower($agent));
                          }
                          if (strpos($agent, '(persero)') !== false) {
                            $agent = str_replace('(persero)','',strtolower($agent));
                          }
                          if (strpos($agent, 'pt') !== false ) {
                            if($dd->id != '5fef5ada04c320d0b259d0adb46e121a' && $dd->id != 'c59710c6abcf92bf62c3a94b39be9c6b'){
                              $agent = str_replace('pt','',strtolower($agent));
                            }
                          }
                          if (strpos($agent, 'tbk') !== false) {
                            $agent = str_replace('tbk','',strtolower($agent));
                          }

                          $agent = preg_replace('!\s+!', ' ', $agent);
                          $agent = trim($agent);
                          $agent = str_replace(' ','-',strtolower($agent));

                          $dd->agent_logo = 'http://flexm.s3.amazonaws.com/assets/agent-'.$agent.'.png';
                        }else{
                          $agent = $provider.'-'.strtolower($dd->receive_currency);
                          $dd->agent_logo = 'http://flexm.s3.amazonaws.com/assets/agent-'.$agent.'.png';
                        }

                        if(in_array($dd->id, $wids)){
                          $dd->agent_logo = url('images/white.jpg');
                        }

                        if(in_array($dd->id, $hids) && $payment_mode == 'home_delivery'){
                          $dd->agent_logo = url('images/white.jpg');
                        }

                        if(in_array($dd->id, $moneygram_ids) && $provider != 'moneygram'){
                          $dd->agent_logo = url('images/white.jpg');
                        }

                        if($provider == 'moneygram' && $dd->partner_name == ''){
                          $dd->display_name = "Moneygram ".$dd->receive_currency;
                        }
                        if($provider == 'homesend'){
                          if($dd->routing_param == '' && @$dd->full_details->bank_code != ''){
                            $dd->routing_param = $dd->full_details->bank_code;
                          }
                          
                          if(@$dd->full_details->code){
                            $dd->partner_name = $dd->full_details->code;
                          }
                          if($dd->receive_country == 'PHL' && $payment_mode == 'bank_deposit'){
                            // $dd->active = false;
                            // continue;
                          }

                          if($dd->receive_country == 'VNM' || $dd->receive_country == 'CHN'){
                            $dd->active = false;
                            // continue;
                          }
                        }
                        if($provider == 'moneygram' && $payment_mode == "bank_deposit"){
                          if(@$dd->full_details->require_fields){
                            $fields = $dd->full_details->require_fields;
                            foreach($fields as $field){
                              if($field->name == 'bank_name' || $field->name == 'bpi_bank_name' || $field->name == 'CIMBBANKNAME'
                                || $field->name == 'cbc_bank_name'){
                                  $dd->bank_list = $field->values;
                              }else{
                                continue;
                              }
                            }
                          }

                        }

                        $corr[$dd->receive_country][$payment_mode][] = $dd;


                    }
                    $this->addActivity('Flexm fetched remittance corridor.', @$user_id, @$data, $content, $url);
                    return response()->json(['status' => "success", 'data' => $corr, 'message'=> $msg], 200);
                }else{
                    $this->addActivity('Flexm error while fetching remittance corridor.', @$user_id, @$data, $content, $url);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            $this->addActivity('Flexm error while fetching remittance corridor reason code.', @$user_id, @$data, ['code' => $code, 'reason' => $reason], $url);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            // Activity::log('Flexm error while fetching remittance corridor - '.json_encode($jsonBody), @$user->id);
            $this->addActivity('Flexm error while fetching remittance corridor.', @$user_id, @$data, $jsonBody, $url);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
            // Activity::log('Flexm error while fetching remittance corridor - '.$e->getMessage(), @$user->id);
            $this->addActivity('Flexm error while fetching remittance corridor.', @$user_id, @$data, $e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            // Activity::log('Flexm error while fetching remittance corridor - '.$e->getMessage(), @$user->id);
            $this->addActivity('Flexm error while fetching remittance corridor.', @$user_id, @$data, $e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function remittanceCalculate(Request $request)
    {
        // \Log::debug("remittance");
        $selected_agent = $request->selected_agent;
        // \Log::debug($selected_agent['id']);
        
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $flexm_token = $request->input('flexm_token');
        $provider = $request->input('provider');
        
        // $custom_id = '';
        // $custom_id = $payer = $request->input('payer_details');
        
        $data['amount'] = $request->input('amount');
        $data['calculation_mode'] = $request->input('calculation_mode');//'source';
        $data['payment_mode'] = $request->input('payment_mode');
        $data['payment_mode_description'] = $request->input('payment_mode_description');
        $data['routing_param'] = $request->input('routing_param');
        $data['routing_type'] = $request->input('routing_type');
        $data['provider_currency'] = $request->input('provider_currency');
        $data['send_currency'] = $request->input('send_currency');
        $data['receive_country'] = $request->input('receive_country');
        $data['receive_currency'] = $request->input('receive_currency');
        $data['partner_name'] = $request->input('partner_name');
        $data['exchange_rate'] = $request->input('exchange_rate');
        $data['fixed_fee'] = $request->input('fixed_fee');
        // $data['payer_details'] = $request->input('payer_details');
        $data['provider_name'] = $provider;
        $data['id'] = @$selected_agent['id'];
        $data['language'] = 'en';
        $data['debug'] = 'true';
        
        $url = self::BASE_URL.'user/wallets/funds/transfers/overseas/calculate/'.$provider;
        try{

            $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);

            $result = $client->post($url, [
                'form_params' => $data
            ]);

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                if($content->success){
                    $msg = $content->message;
                    $cont = $content->data;
                    // Activity::log('Flexm calculated remittance amount', @$user->id);
                    $this->addActivity('Flexm calculated remittance.', @$user_id, @$data, $content, $url);
                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    $this->addActivity('Flexm error while fetching calculating remittance.', @$user_id, @$data, $content, $url);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            $this->addActivity('Flexm error while fetching calculating remittance reason code.', @$user_id, @$data, ['code'=> $code, 'reason' => $reason], $url);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            // Activity::log('Flexm error while calculating remittance - '.json_encode($jsonBody), @$user->id);
        
            $this->addActivity('Flexm error while fetching calculating remittance.', @$user_id, @$data, $jsonBody, $url);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
            // Activity::log('Flexm error while calculating remittance - '.$e->getMessage(), @$user->id);
            $this->addActivity('Flexm error while fetching calculating remittance.', @$user_id, @$data, $e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            // Activity::log('Flexm error while calculating remittance - '.$e->getMessage(), @$user->id);
            $this->addActivity('Flexm error while fetching calculating remittance.', @$user_id, @$data, $e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function remittancePurpose(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $flexm_token = $request->input('flexm_token');
        $provider = $request->input('provider');
        $url = self::BASE_URL.'user/remittance/enumerations/purpose/'.$provider;
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
                    $this->addActivity('Flexm fetched remittance purpose.', @$user_id, @$data, $content, $url);
                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    $this->addActivity('Flexm error while fetching remittance purpose.', @$user_id, @$data, $content, $url);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            $this->addActivity('Flexm error while fetching remittance purpose reason code.', @$user_id, @$data, ['code' => $code, 'reason' => $reason], $url);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            // Activity::log('Flexm error while fetching remittance purpose - '.json_encode($jsonBody), @$user->id);
            $this->addActivity('Flexm error while fetching remittance purpose.', @$user_id, @$data, $jsonBody, $url);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
            // Activity::log('Flexm error while fetching remittance purpose - '.$e->getMessage(), @$user->id);
            $this->addActivity('Flexm error while fetching remittance purpose.', @$user_id, @$data, $e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            // Activity::log('Flexm error while fetching remittance purpose - '.$e->getMessage(), @$user->id);
            $this->addActivity('Flexm error while fetching remittance purpose.', @$user_id, @$data, $e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function remittanceIncSource(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $url = self::BASE_URL.'user/wallets/funds/transfers/overseas/list/income';
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
                    $this->addActivity('Flexm fetched income source.', @$user_id, @$data, $content, $url);
                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    $this->addActivity('Flexm error while fetching income source.', @$user_id, @$data, $content, $url);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            $this->addActivity('Flexm error while fetching income source reason code.', @$user_id, @$data, ['code' => $code, 'reason' => $reason], $url);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            // Activity::log('Flexm error while fetching income source - '.json_encode($jsonBody), @$user->id);
            $this->addActivity('Flexm error while fetching income source.', @$user_id, @$data, $jsonBody, $url);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
            // Activity::log('Flexm error while fetching income source - '.$e->getMessage(), @$user->id);
            $this->addActivity('Flexm error while fetching income source.', @$user_id, @$data, $e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            // Activity::log('Flexm error while fetching income source - '.$e->getMessage(), @$user->id);
            $this->addActivity('Flexm error while fetching income source.', @$user_id, @$data, $e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }
    
    public function branchList(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $data['provider'] = $provider = $request->input('provider');
        $data['name'] = $name = $request->input('bank_name');
        $data['page'] = $page = $request->input('page');
        $data['language'] = 'en';
        $url = self::BASE_URL.'user/remittance/banks?provider='.$provider.'&language=en&name='.$name.'&limit=20&page='.$page;
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
                    $this->addActivity('Flexm fetched branch list.', @$user_id, @$data, $content, $url);
                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    $this->addActivity('Flexm error while fetching fetched branch list.', @$user_id, @$data, $content, $url);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            $this->addActivity('Flexm error while fetching branch list reason code.', @$user_id, @$data, ['code' => $code, 'reason' => $reason], $url);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            $this->addActivity('Flexm error while fetching branch list.', @$user_id, @$data, $jsonBody, $url);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
            $this->addActivity('Flexm error while fetching branch list.', @$user_id, @$data, $e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            $this->addActivity('Flexm error while fetching branch list.', @$user_id, @$data, $e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function remittanceCreate(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $phone_code= [
          'PHL' => '+63',
          'IDN' => '+62',
          'IND' => '+91',
          'INDIA' => '+91',
          'BGD' => '+880',
          'CHN' => '+86',
          'THA' => '+66',
          'VNM' => '+84',
          'MYS' => '+60',
          'NPL' => '+977',
          'LKA' => '+94'
        ];
        \Log::debug("create");
        \Log::debug(json_encode($request->input()));
        
        $flexm_token = $request->input('flexm_token');
        $provider_name = $request->input('provider');

        $data['amount'] = $request->input('amount');
        $data['first_name'] = $request->input('first_name');
        $data['last_name'] = $request->input('last_name');
        $data['address_1'] = $request->input('address_1');
        $data['address_2'] = $request->input('address_2');
        $data['birth_date'] = $request->input('birth_date');
        $data['city'] = $request->input('city');
        $data['state'] = $request->input('state');
        $data['country'] = $request->input('country');
        $data['zipcode'] = $request->input('zip_code');
        $data['nationality'] = $request->input('nationality');
        $data['reason'] = $request->input('reason');
        $data['calculation_mode'] = $request->input('calculation_mode');//'source';
        $data['payment_mode'] = $request->input('payment_mode');
        $data['payment_mode_description'] = $request->input('payment_mode_description');
        $data['routing_param'] = $request->input('routing_param');
        $data['routing_type'] = $request->input('routing_type');
        $data['provider_currency'] = $request->input('provider_currency');
        $data['send_currency'] = $request->input('send_currency');
        $data['receive_country'] = $request->input('receive_country');
        $data['receive_currency'] = $request->input('receive_currency');
        $data['partner_name'] = $request->input('partner_name');
        $data['exchange_rate'] = $request->input('exchange_rate');
        $data['fixed_fee'] = $request->input('fixed_fee');
        $data['payer_details'] = $request->input('payer_details');
        $data['source_of_income'] = $request->input('source_of_income');
        $data['code'] = $request->input('code');
        $data['receive_digit_precision'] = $request->input('receive_digit_precision');
        $data['flow_type'] = $request->input('flow_type');
        $data['routing_tag'] = $request->input('routing_tag');
        $data['destination_uri'] = $request->input('destination_uri');
        $data['bank_code'] = $request->input('bank_code');
        
        $receive_mobile_number = $data['receive_mobile_number'] = @$phone_code[$data['country']].''.$request->input('receive_mobile_number');
        $data['bank_branch_name'] = $request->input('bank_branch_name');
        $data['bank_account_number'] = $request->input('bank_account_number');
        $data['bank_ifc_code'] = $request->input('bank_ifc_code');
        
        $data['bank_name'] = $request->input('agent_name');
        $data['type'] = $provider_name;
        $data['bank_branch_code'] = $request->input('bank_branch_code');
        $data['swift_code'] = $request->input('swift_code');
        $data['language'] = 'en';
        
        if(@$data['partner_name'] == '' && $provider_name == 'homesend' && $data['bank_branch_code'] != ''){
            $data['routing_param'] = $request->input('bank_branch_code');
        }
        \Log::debug($data);
        
        if($provider_name == 'homesend'){
          if(strtolower($data['partner_name']) == 'smart money' && $data['receive_country'] == 'PHL'){
            unset($data['receive_mobile_number']);
          }
          if(strtolower($data['partner_name']) == 'gcash' && $data['receive_country'] == 'PHL'){
            unset($data['receive_mobile_number']);
          }
        }
        $url  =self::BASE_URL.'user/wallets/funds/transfers/overseas/'.$provider_name;
        try{

            $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);

            $result = $client->post($url, [
                'form_params' => $data
            ]);

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                if($content->success){
                    $msg = $content->message;
                    $cont = $content->data;
                    $this->addActivity('Flexm created remittance.', @$user_id, @$data, $content, $url);
                    // Activity::log('Flexm created remittance request', @$user->id);
                    \Log::debug('Response');
                    \Log::debug(json_encode($cont));
                    $name = $user->name;
                    $name = explode(' ',$name);
                    $first_name = @$name[0];
                    $last_name = isset($name[2])?$name[2]:@$name[1];
                    $middle_name = isset($name[2])?@$name[1]:'';
                    $sender_no = @$user->profile->phone;

                    Remittance::create([
                        'user_id' => $user->id,
                        'hash_id' => $cont->ref_id,
                        'provider' => $provider_name,
                        'delivery_method' => $data['payment_mode_description'],
                        'receive_country' => $data['receive_country'],
                        'status' => $cont->confirm,
                        'payout_agent' => $data['partner_name'],
                        // 'customer_fx' => $data['provider'],
                        'send_currency' => $data['send_currency'],
                        'send_amount' => $data['amount'],
                        'customer_fixed_fee' => $data['fixed_fee'],
                        'total_transaction_amount' => @$cont->transfer_details->total_amount,
                        'receive_currency' => $data['receive_currency'],
                        'receive_amount' => @$cont->transfer_details->amount_to_receive,
                        // 'crossrate' => $data['provider'],
                        'provider_amount_fee_currency' => $data['provider_currency'],
                        // 'provider_amount_fee' => $data['provider'],
                        'provider_exchange_rate' => @$cont->transfer_details->provider_exchange_rate,
                        // 'send_amount_rails_currency' => $data['provider'],
                        // 'send_amount_rails' => $data['provider'],
                        // 'send_amount_before_fx' => $data['provider'],
                        // 'send_amount_after_fx' => $data['provider'],
                        'routing_params' => $data['routing_param'],
                        'ref_id' => @$cont->transfer_details->reference_number,
                        // 'transaction_code' => $data['provider'],
                        'sender_first_name' => $first_name,
                        'sender_middle_name' => $middle_name,
                        'sender_last_name' => $last_name,
                        'sender_mobile_number' => $sender_no,
                        'receive_mobile_number' => @$receive_mobile_number,
                        'ben_first_name' => $data['first_name'],
                        // 'ben_middle_name' => $data['provider'],
                        'ben_last_name' => $data['last_name'],
                        // 'date_added' => $data['provider'],
                        // 'date_expiry' => $data['provider'],
                        // 'status_last_updated' => $data['provider'],
                        'create_request' => json_encode($request->except('token', 'flexm_token')),
                        'create_response' => json_encode($cont)
                    ]);
                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    $this->addActivity('Flexm error while creating remittance.', @$user_id, @$data, $content, $url);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            $this->addActivity('Flexm error while creating remittance reason code.', @$user_id, @$data, ['code' => $code, 'reason' => $reason], $url);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            // \Log::debug('Response Error');
            // \Log::debug(json_encode($jsonBody));
            // Activity::log('Flexm error while creating remittance request - '.json_encode($jsonBody), @$user->id);
            $this->addActivity('Flexm error while creating remittance.', @$user_id, @$data, $jsonBody, $url);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
            // Activity::log('Flexm error while creating remittance request - '.$e->getMessage(), @$user->id);
            $this->addActivity('Flexm error while creating remittance.', @$user_id, @$data, $e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            // Activity::log('Flexm error while creating remittance request - '.$e->getMessage(), @$user->id);
            $this->addActivity('Flexm error while creating remittance.', @$user_id, @$data, $e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function remittanceConfirm(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $flexm_token = $request->input('flexm_token');
        $data['ids'] = $request->input('ref_id');
        $url = self::BASE_URL.'user/wallets/funds/transfers/overseas/confirm';
        try{
            $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);

            $result = $client->post($url,[
                'form_params' => $data
            ]);

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                if($content->success){
                    $msg = $content->message;
                    $cont = $content->data;
                    $remit = Remittance::where('hash_id', $data['ids'])->first();
                    if($remit){
                      foreach($cont->transactions as $tran){
                        $remit->update([
                          'transaction_code' => $tran->details->transaction_code,
                          'status'  => $tran->status,
                          'remarks' => $tran->description
                        ]);
                      }
                    }
                    // Activity::log('Flexm confirmed remittance request #'.$data['ids'], @$user->id);
                    $this->addActivity('Flexm confirmed remittance request.', @$user_id, @$data, $content, $url);
                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    $this->addActivity('Flexm error while confirming remittance.', @$user_id, @$data, $content, $url);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            $this->addActivity('Flexm error while confirming remittance reason code.', @$user_id, @$data, ['code' => $code, 'reason' => $reason], $url);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            // Activity::log('Flexm error while confirming remittance - '.json_encode($jsonBody), @$user->id);
            $this->addActivity('Flexm error while confirming remittance.', @$user_id, @$data, $jsonBody, $url);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
            // Activity::log('Flexm error while confirming remittance - '.$e->getMessage(), @$user->id);
            $this->addActivity('Flexm error while confirming remittance.', @$user_id, @$data, $e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            // Activity::log('Flexm error while confirming remittance - '.$e->getMessage(), @$user->id);
            $this->addActivity('Flexm error while confirming remittance.', @$user_id, @$data, $e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function generateMerchantToken(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        try{
            $flexm_token = $request->input('flexm_token');
            $data['mid'] = $request->input('mid');
            $data['tid'] = $request->input('tid');
            $data['merchant_name'] = $request->input('merchant_name');
            $data['description'] = $request->input('description');
            $data['wallet_type_indicator'] = $request->input('wallet_type_indicator');
            echo $data['check'] = md5($data['mid'].''.$data['tid']);die();//+$data['wallet_type_indicator']);

            $data['payment_mode'] = $request->input('payment_mode');
            $data['tran_amount'] = $request->input('tran_amount');
            // echo $data['check'];die();
            $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);

            $result = $client->post(self::BASE_URL.'user/wallets/payment/token',[
                'form_params' => $data
            ]);

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

    public function getMerchantInfo(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        try{
            $flexm_token = $request->input('flexm_token');
            $merchant_token = $request->input('merchant_token');

            $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);

            $result = $client->get(self::BASE_URL.'user/wallets/payment/token/'.$merchant_token);

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
            Activity::log('Flexm error while getting merchant info - '.json_encode($jsonBody), @$user->id);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
            Activity::log('Flexm error while getting merchant info - '.$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            Activity::log('Flexm error while getting merchant info - '.$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function paymentAuthorization(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        try{
            $flexm_token = $request->input('flexm_token');
            $data['amount'] = $request->input('amount');
            $data['currency_code'] = $request->input('currency_code');
            $data['merchant_code'] = $request->input('merchant_code');
            $data['token'] = $request->input('auth_token');
            $data['wallet_type_indicator'] = 'centurion';

            $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);

            $result = $client->post(self::BASE_URL.'user/wallets/payment/auth', [
              'form_params' => $data
            ]);

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
            Activity::log('Flexm error while authorizing payment - '.json_encode($jsonBody), @$user->id);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
            Activity::log('Flexm error while authorizing payment - '.$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            Activity::log('Flexm error while authorizing payment - '.$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function createMerchant(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        try{
            $data['merchant_name'] = $request->input('merchant_name');
            $data['wallet_type_indicator'] = 'centurion';
            $data['merchant_category_code'] = $request->input('merchant_category_code');
            $sum = array_sum(str_split($data['merchant_category_code']));
            $text = ''.$data['merchant_name'].''.$sum.''.$data['wallet_type_indicator'];
            $data['check'] = md5($text);

            $client = new Client(['headers' => ['Content-type' => 'application/json']]);

            $result = $client->post(self::BASE_URL.'merchant/profile',[
                'form_params' => $data
            ]);

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
            Activity::log('Flexm error while creating merchant - '.json_encode($jsonBody), @$user->id);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
            Activity::log('Flexm error while creating merchant - '.$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            Activity::log('Flexm error while creating merchant - '.$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function createTerminal(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        try{
            $data['merchant_code'] = $request->input('merchant_code');
            $data['payment_mode'] = $request->input('payment_mode');
            $text = $data['merchant_code'].''.$data['payment_mode'];
            $data['check'] = md5($text);

            $client = new Client(['headers' => ['Content-type' => 'application/json']]);

            $result = $client->post(self::BASE_URL.'merchant/terminal/info',[
                'form_params' => $data
            ]);

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
            Activity::log('Flexm error while creatig terminal - '.json_encode($jsonBody), @$user->id);
            return response()->json(['status' => 'error', 'data' => @$jsonBody, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
            Activity::log('Flexm error while creatig terminal - '.$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            Activity::log('Flexm error while creatig terminal - '.$e->getMessage(), @$user->id);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function getMerchant(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        try{
            $merchant_code = $request->input('merchant_code');

            $client = new Client(['headers' => ['Content-type' => 'application/json']]);

            $result = $client->get(self::BASE_URL.'merchant/'.$merchant_code);

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                if($content->success){
                    $msg = $content->message;
                    $cont = $content->data;
                    if(isset($cont[0])){
                      $cont = $cont[0];
                    }
                    // $this->addActivity('Flexm error while making payment.', @$user_id, @$data, $cont);
                    return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                    // $this->addActivity('Flexm error while making payment.', @$user_id, @$data, $cont);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            // $this->addActivity('Flexm error while making payment.', @$user_id, @$data, $cont);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            Activity::log('Flexm error while getting merchant - '.json_encode($jsonBody), @$user->id);
            // $this->addActivity('Flexm error while making payment.', @$user_id, @$data, $cont);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
            Activity::log('Flexm error while getting merchant - '.$e->getMessage(), @$user->id);
            // $this->addActivity('Flexm error while making payment.', @$user_id, @$data, $cont);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            Activity::log('Flexm error while getting merchant - '.$e->getMessage(), @$user->id);
            // $this->addActivity('Flexm error while making payment.', @$user_id, @$data, $cont);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function makePayment(Request $request)
    {
        $token = $request->input('token');
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        \Log::debug($request->all());
        $url = self::BASE_URL.'user/wallets/payment/token';
        try{
            $id = $request->input('id');
            $type = $request->input('type');
            if($type == 'course'){

              $item = Course::find($id);
              if($item){
                if($item->type == 'free' || ($item->type == 'paid' && $item->fee == 0)){
                  // no need to make payment for a free course
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

              if(!$merchant){
                $merchant = Merchant::find(20);
              }
              $desc = "Course payment for course #".$id;
            }elseif($type == 'spuul'){
              $spuul_token = $request->spuul_token;
              $d['account_number'] = $request->account_number;
              $d['email'] = $request->email;
              if($spuul_token == ''){
                return response()->json(['status' => 'error', 'data' => '', 'message'=> 'Spuul token is required.'], 200);
              }
              if($d['account_number'] == ''){
                return response()->json(['status' => 'error', 'data' => '', 'message'=> 'Spuul account number is required.'], 200);
              }
              if($d['email'] == ''){
                return response()->json(['status' => 'error', 'data' => '', 'message'=> 'Spuul account email is required.'], 200);
              }
              $spuul_url = config('app.url').'api/v1/spuul/';
              $sp_data['token'] = $token;
              $sp_data['spuul_token'] = $spuul_token;
              $client = new Client();
              $result = $client->post($spuul_url.'profile',[
                'form_params' => $sp_data
              ]);

              $code = $result->getStatusCode(); // 200
              $reason = $result->getReasonPhrase(); // OK

              if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $sp_content = json_decode($body->getContents());
                if($sp_content->status == 'success'){
                  $sp_cont = $sp_content->data;
                  if(count($sp_cont->subscriptions) > 0){
                    return response()->json(['status' => 'error', 'data' => '', 'message'=> 'You have already subscribed.'], 200);
                  }
                }
              }

              //merchant created already as it will be one only
              $plan = SpuulPlan::find($id);
              if($plan){
                $amount = $plan->price;
                if($plan->type == '1'){
                  $amount = 1;
                  // only for promotional ofer
                  $d['sku_code'] = 'interactive_sg_monthly_promo_sgd';
                //   $d['sku_code'] = 'interactive_sg_monthly_sgd';
                }
                else
                  $d['sku_code'] = 'interactive_sg_yearly_sgd';

              }else{
                //plan does not exist
                return response()->json(['status' => 'error', 'data' => '', 'message'=> 'Plan does not exist.'], 200);
              }

              $vendor_id = 2;
              $merchant = Merchant::find($vendor_id);
              $desc = "Spuul subscription payment for plan #".$id;
              $other_share_per = getOption('spuul_share', 40);

            }else{
              //return error invalid type
              return response()->json(['status' => 'error', 'data' => '', 'message'=> 'Invalid type value.'], 200);
            }
            \Log::debug($merchant);
            if($merchant){
              $other_share_per = $merchant->merchant_share;;
              if($other_share_per == ""){
                $other_share_per = 0;
              }
              $merchant_code = $merchant->merchant_code;
              $merchant_name = $merchant->merchant_name;
              $mid = $merchant->mid;
              $terminal = Terminal::where('merchant_id', $merchant->id)->where('payment_mode', 1)->first();
              if($terminal){
                $tid = $terminal->tid;
              }else{
                return response()->json(['status' => 'error', 'data' => '', 'message'=> 'Merchant does not exist. Can\'t make the payment.'], 200);
              }
            }else{
              //retunr error that merchant account does not exist
              return response()->json(['status' => 'error', 'data' => '', 'message'=> 'Merchant does not exist. Can\'t make the payment.'], 200);
            }
            /* old starts
            $per = getOption('flexm_charges', '1.5');

            $other_share = ($amount*$other_share_per)/100;
            $myma_share = $amount-$other_share;
            $gst = getOption('gst_tax', '7');
            $gst_amt = ($myma_share*$gst)/100;

            $other_share = $other_share - $gst_amt;// - $charges;

            $charges = number_format($myma_share*$per/100, 2, '.','');

            $flexm_part = $charges;
            $myma_part = 0;//$charges - $flexm_part;

            $myma_share -= $flexm_part;
            //charges are inclusive
            $total = $amount;// + $charges;
            old ends */
            
            $flexm_per = getOption('flexm_charges_app', '1.5');
            $per = $merchant->myma_transaction_share;
            if($per == '' || $per == 0){
              $per = $flexm_per;
            }
            //other share is actually myma share and so keep name as it is and assign values accordingly (changed)
            $myma_share = /*$other_share =*/ ($amount*$other_share_per)/100;
            $other_share = $amount-$myma_share;
            $charges = number_format($amount*$per/100, 4, '.','');
            
            $gst = getOption('gst_tax', '7');
            $gst_amt = (($myma_share+$charges)*$gst)/100;

            $other_share = $other_share - $gst_amt;// - $charges;

            $other_share -= $charges;
            
            $flexm_part = number_format($amount*$flexm_per/100, 4, '.','');
            $myma_part = $charges-$flexm_part;

            //charges are inclusive
            $total = $amount;// + $charges;
            
            $flexm_token = $request->input('flexm_token');
            $data['mid'] = $mid;
            $data['tid'] = $tid;
            $data['tran_amount'] = $total;
            $data['description'] = $desc;
            $data['status'] = 1;
            $data['check'] = md5($data['mid'].''.$data['tid']);
            // \Log::debug(json_encode($data));
            $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);

            $result = $client->post($url,[
                'json' => $data
            ]);

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                if($content->success){
                    $msg = $content->message;
                    $cont = $content->data;
                    if($cont){
                        $clientt = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);

                        $resultt = $clientt->get(self::BASE_URL.'user/profile?debug=false');

                        $codee = $resultt->getStatusCode(); // 200
                        $reasonn = $resultt->getReasonPhrase(); // OK
                        $mobile_no = isset($user->profile)?$user->profile->phone:'';
                        $user_name = '';
                        if($codee == "200" && $reasonn == "OK"){
                            $bodyy = $resultt->getBody();
                            $contentt = json_decode($bodyy->getContents());
                            if($contentt->success){
                                $msgg = $contentt->message;
                                $contt = $contentt->data;
                                $mobile_no = $contt->mobile;
                                $user_name = @$contt->profile->full_name;
                            }
                        }
                        $merchant_token = $cont->token;

                        $dataa['amount'] = $total;
                        $dataa['currency_code'] =702;
                        $dataa['merchant_code'] =$merchant_code;
                        $dataa['token'] = $merchant_token;
                        // $dataa['wallet_type_indicator'] ='centurion';
                        \Log::debug(json_encode($dataa));
                        $clientt = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);

                        $result = $clientt->post(self::BASE_URL.'user/wallets/payment/auth', [
                            'json' => $dataa
                        ]);

                        $code = $result->getStatusCode(); // 200
                        $reason = $result->getReasonPhrase(); // OK
                        if($code == "200" && $reason == "OK"){
                            $body = $result->getBody();
                            $content = json_decode($body->getContents());
                            if($content->success){
                                $msg = $content->message;
                                $cont = $content->data;
                                //add transaction to history
                                $tran = Transactions::create([
                                    'type' => $type,
                                    'description' => @$desc,
                                    'ref_id' => $id,
                                    'phone_no' => $mobile_no,
                                    'wallet_user_name' => $user_name,
                                    'transaction_date' => $cont->created_at,
                                    'transaction_amount' => $cont->amount,
                                    'transaction_currency' => 'SGD',
                                    'transaction_ref_no' => $cont->ref_id,
                                    'transaction_status' => $cont->status_description,
                                    'transaction_code' => $cont->status_code,
                                    'mid' => $mid,
                                    'tid' => $tid,
                                    'merchant_name' => $merchant_name,
                                    'payment_mode' => 'InApp',
                                    'user_id' => $user->id,
                                    'flexm_part' => $flexm_part,
                                    'myma_part' => $myma_part,
                                    'myma_share' => $myma_share,
                                    'other_share' => $other_share,
                                    'gst' => $gst_amt,
                                    'remarks' => $request->remarks,
                                    'response'  => json_encode($cont)
                                ]);

                                //spuul subscription code
                                if($type == 'spuul' && @$spuul_token && @$d['account_number'] != '' && @$d['email'] != ''){
                                  $this->addActivity('Flexm made payment for spuul subscription #'.$id.' account_number #'.@$d['account_number'], @$user_id, @$dataa, $content, $url);
                                  $message = $user->name." made payment for spuul subscription.";
                                  event(new SendBrowserNotification($message));
                                  //Activity::log('Flexm made payment for spuul subscription #'.$id.' account_number #'.@$d['account_number'], @$user->id);
                                  try{
                                    $client = new Client(['headers' => ['Accept' => 'application/vnd.spuul.v3', 'Authorization' => 'Bearer '.$spuul_token,
                                    'Cache-Control' => 'no-cache', 'content-type' => 'multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW']]);
                                    $uurl = 'https://callbacks.spuul.com/sg_interactive/subscribe';
                                    $result = $client->post($uurl, [
                                      'form_params' => $d
                                    ]);

                                    $code = $result->getStatusCode(); // 200
                                    $reason = $result->getReasonPhrase(); // OK
                                    if($code == "200" && $reason == "OK"){
                                      $body = $result->getBody();
                                      $content = json_decode($body->getContents());
                                      $status = 'success';

                                      $now = Carbon::now();
                                      $end = Carbon::now();
                                      if($d['sku_code'] == 'interactive_sg_monthly_sgd'){
                                        $plan_type = 'monthly';
                                        $end = $end->addMonth();
                                      }else{
                                        $plan_type = 'yearly';
                                        $end = $end->addYear();
                                      }
                                      SpuulSubscription::create([
                                        'user_id' => $user->id,
                                        'transaction_id' => $tran->id,
                                        'start_date'  => $now,
                                        'end_date'  => $end,
                                        'status'  => 'active',
                                        'plan_type' => $plan_type,
                                        'email' => $d['email'],
                                        'account_number' => $d['account_number']
                                      ]);
                                      $this->addActivity('Flexm spuul subscription request is successfull', @$user_id, @$d, $content, $uurl);
                                      //Activity::log('Flexm spuul subscription request is successfull', @$user->id);
                                    }else{
                                      $content = $reason;
                                      $status = 'error';
                                    }

                                  }catch (BadResponseException $ex) {
                                      $response = $ex->getResponse();
                                      $jsonBody = json_decode((string) $response->getBody());

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
                                      $this->addActivity('Flexm error while making spuul subscription request but payment is done', @$user_id, @$d, $jsonBody, $url);
                                      //Activity::log('Flexm error while making spuul subscription request but payment is done - '.$content, @$user->id);
                                  }catch(GuzzleException $e){
                                      $content = $e->getMessage();
                                      $this->addActivity('Flexm error while making spuul subscription request but payment is done', @$user_id, @$d, $e->getMessage(), $url);
                                      $status = 'error';
                                  }catch(Exception $e){
                                      $content = $e->getMessage();
                                      $status = 'error';
                                      $this->addActivity('Flexm error while making spuul subscription request but payment is done', @$user_id, @$d, $e->getMessage(), $url);

                                      //Activity::log('Flexm error while making spuul subscription request but payment is done - '.$content, @$user->id);
                                  }

                                  $tt = Transactions::find($tran->id);
                                  $tt->update([
                                    'spuul_status' => $status,
                                    'spuul_request' => json_encode($d),
                                    'spuul_response' => json_encode($content)
                                  ]);

                                }
                                //spuul subscription code

                                if($type == 'course'){
                                  $this->addActivity('Flexm made payment for buying course #'.$id, @$user_id, @$dataa, $content, $url);
                                  //Activity::log('Flexm made payment for buying course #'.$id, @$user->id);
                                  $joined = CourseJoined::create([
                                    'user_id' => $user->id,
                                    'course_id' => $id
                                  ]);
                                  $message = $user->name." made payment for purchasing a course.";
                                  event(new SendBrowserNotification($message));
                                }
                                $this->addActivity('Flexm authorized payment', @$user_id, @$dataa, $cont, $url);

                                return response()->json(['status' => "success", 'data' => $cont, 'message'=> 'Payment has been made.'], 200);
                            }else{
                                $this->addActivity('Flexm error while authorizing payment.', @$user_id, @$dataa, $content, $url);
                                return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                            }
                        }
                        $this->addActivity('Flexm error while making payment reason code.', @$user_id, @$dataa, ['code' => $code, 'reason' => $reason], $url);
                        return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);
                    }
                    $this->addActivity('Flexm error while making payment.', @$user_id, @$data, $cont, $url);
                    return response()->json(['status' => "error", 'data' => $cont, 'message'=> $msg], 200);
                }else{
                  $this->addActivity('Flexm error while making payment.', @$user_id, @$data, $content, $url);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            $this->addActivity('Flexm error while making payment reason code.', @$user_id, @$data, ['code' => $code, 'reason' => $reason], $url);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            // Activity::log('Flexm error while making payment - '.json_encode($jsonBody), @$user->id);
            $this->addActivity('Flexm error while making payment.', @$user_id, @$data, $jsonBody, $url);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
            // Activity::log('Flexm error while making payment - '.$e->getMessage(), @$user->id);
            $this->addActivity('Flexm error while making payment.', @$user_id, @$data, $e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            // Activity::log('Flexm error while making payment - '.$e->getMessage(), @$user->id);
            $this->addActivity('Flexm error while making payment.', @$user_id, @$data, $e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function scanQR(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        try{
            $id = $request->input('id');
            $dd = explode('_', $id);
            $id = $dd[0];
            $tid = @$dd[1];

            $amount = $request->input('amount');
            if($id == ''){
              return response()->json(['status' => 'error', 'data' => '', 'message'=> 'ID is required.'], 200);
            }
            if($amount == '' || $amount <= 0){
              return response()->json(['status' => 'error', 'data' => '', 'message'=> 'Amount is required.'], 200);
            }
            $merchant = Merchant::where('mid', $id)->first();
            if($merchant){

                $flexm_per = getOption('flexm_charges_store', '0.5');
                $myma_per = $merchant->myma_transaction_share;
                if($myma_per == ''){
                  $myma_per = 0;
                }
                $per = $flexm_per+$myma_per;

                $other_share_per = $merchant->merchant_share;

                $charges = number_format($amount*$per/100, 2, '.','');
                $name = $merchant->merchant_name;
                return response()->json(['status' => 'success', 'name' => $name, 'data' => $charges, 'message'=> 'Success'], 200);

            }else{
              return response()->json(['status' => 'error', 'data' => '', 'message'=> 'Merchant does not exist. Can\'t make the payment.'], 200);
            }
        }catch(Exception $e){
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function makePaymentQR(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        \Log::debug($request->all());
        $url = self::BASE_URL.'user/wallets/payment/token';
        try{
            $id = $request->input('id');
            $dd = explode('_', $id);
            $id = $dd[0];
            $tid = @$dd[1];

            $flexm_token = $request->input('flexm_token');
            $amount = $request->input('amount');
            $merchant = Merchant::where('mid', $id)->first();

            $desc = "Instore payment of ".$amount;

            if($merchant){
              $merchant_code = $merchant->merchant_code;
              $merchant_name = $merchant->merchant_name;
              $mid = $merchant->mid;
              $terminal = Terminal::where('tid', $tid)->where('payment_mode', 2)->first();
              if(!$terminal)
                $terminal = Terminal::where('merchant_id', $merchant->id)->where('payment_mode', 2)->first();
              if($terminal){
                $tid = $terminal->tid;
              }else{
                return response()->json(['status' => 'error', 'data' => '', 'message'=> 'Merchant does not exist. Can\'t make the payment.'], 200);
              }
            }else{
              return response()->json(['status' => 'error', 'data' => '', 'message'=> 'Merchant does not exist. Can\'t make the payment.'], 200);
            }

            $flexm_per = getOption('flexm_charges_store', '0.5');
            $myma_per = $merchant->myma_transaction_share;
            if($myma_per == ''){
              $myma_per = 0;
            }
            $per = /*$flexm_per+*/$myma_per;

            $other_share_per = $merchant->merchant_share;

            $charges = $amount*$per/100;//number_format($amount*$per/100, 2, '.','');

            $flexm_part = $charges*$flexm_per/$per;
            $myma_part = $charges - $flexm_part;

            $gst = getOption('gst_tax', '7');

            $gst_amt = 0;
            $other_share = $amount-$charges;//($amount*$other_share_per)/100;
            $myma_share = 0;//$amount-$other_share;
            $gst_amt = (($myma_share+$charges)*$gst)/100;
            $gst_amt = number_format($gst_amt, 4, '.','');
            
            $other_share -= $gst_amt;
            //made change for inclusive gst
            $total = $amount;// + $charges;

            $data['mid'] = $mid;
            $data['tid'] = $tid;
            $data['tran_amount'] = $total;
            $data['description'] = $desc;
            $data['status'] = 1;
            $data['check'] = md5($data['mid'].''.$data['tid']);

            $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);

            $result = $client->post($url,[
                'json' => $data
            ]);

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
              // \Log::debug('Token generation success');
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                if($content->success){
                    $clientt = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);

                    $resultt = $clientt->get(self::BASE_URL.'user/profile?debug=false');

                    $codee = $resultt->getStatusCode(); // 200
                    $reasonn = $resultt->getReasonPhrase(); // OK
                    $mobile_no = isset($user->profile)?$user->profile->phone:'';
                    $user_name = '';
                    if($codee == "200" && $reasonn == "OK"){
                        $bodyy = $resultt->getBody();
                        $contentt = json_decode($bodyy->getContents());
                        if($contentt->success){
                            $msgg = $contentt->message;
                            $contt = $contentt->data;
                            $mobile_no = $contt->mobile;
                            $user_name = @$contt->profile->full_name;
                        }
                    }
                    $msg = $content->message;
                    $cont = $content->data;

                    if($cont){
                        $merchant_token = $cont->token;

                        $this->addActivity('Flexm fetched merchant token for physical payment.', @$user_id, @$data, $content, $url);
                        $dataa = [];
                        $dataa['amount'] = $total;
                        $dataa['currency_code'] = 702;
                        $dataa['merchant_code'] = $merchant_code;
                        $dataa['token'] = $merchant_token;
                        // $dataa['wallet_type_indicator'] = 'centurion';

                        $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);

                        $result = $client->post(self::BASE_URL.'user/wallets/payment/auth', [
                            'json' => $dataa
                        ]);

                        $code = $result->getStatusCode(); // 200
                        $reason = $result->getReasonPhrase(); // OK
                        if($code == "200" && $reason == "OK"){

                            $body = $result->getBody();
                            $content = json_decode($body->getContents());
                            if($content->success){
                                $msg = $content->message;
                                $cont = $content->data;
                                //add transaction to history
                                $this->addActivity('Flexm successful payment using physical merchant mode.', @$user_id, @$dataa, $content, $url);

                                // Activity::log('Flexm made a payment using QRcode MID #'.$mid.' TID #'.$tid. ' Amount #'.$cont->amount, @$user->id);
                                $tran = Transactions::create([
                                    'type' => 'instore',
                                    'description' => @$desc,
                                    'ref_id' => '',
                                    'phone_no' => $mobile_no,
                                    'wallet_user_name' => $user_name,
                                    'transaction_date' => $cont->created_at,
                                    'transaction_amount' => $cont->amount,
                                    'transaction_currency' => 'SGD',
                                    'transaction_ref_no' => $cont->ref_id,
                                    'transaction_status' => $cont->status_description,
                                    'transaction_code' => $cont->status_code,
                                    'mid' => $mid,
                                    'tid' => $tid,
                                    'merchant_name' => $merchant_name,
                                    'payment_mode' => 'QR',
                                    'user_id' => @$user->id,
                                    'flexm_part' => $flexm_part,
                                    'myma_part' => $myma_part,
                                    'myma_share' => $myma_share,
                                    'other_share' => $other_share,
                                    'gst' => $gst_amt,
                                    'remarks' => $request->remarks,
                                    'response'  => json_encode($cont)
                                ]);

                                return response()->json(['status' => "success", 'data' => $cont, 'message'=> 'Payment has been made.'], 200);
                            }else{
                                $this->addActivity('Flexm error while authorizing payment.', @$user_id, @$data, $content, $url);
                                return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                            }
                        }
                        $this->addActivity('Flexm error while authorizing merchant token in physical merchant mode reason code.', @$user_id, @$data, ['code' => $code, 'reason' => $reason], $url);
                        return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);
                    }
                    $this->addActivity('Flexm error while authorizing merchant token in physical merchant mode.', @$user_id, @$data, $cont, $url);
                    return response()->json(['status' => 'error', 'data' => $cont, 'message'=> $msg], 200);
                }else{
                  $this->addActivity('Flexm error while creating merchant token in physical merchant mode.', @$user_id, @$data, $content, $url);
                    return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
                }
            }
            $this->addActivity('Flexm error while creating merchant token in physical merchant mode reason code.', @$user_id, @$data, ['code' => $code, 'reason' => $reason], $url);
            return response()->json(['status' => 'error', 'data' => $reason, 'message'=> 'EXCEPTION_OCCURED'], 200);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            // Activity::log('Flexm error while qr payment - '.json_encode($jsonBody), @$user->id);
            $this->addActivity('Flexm error while creating merchant token in physical merchant mode.', @$user_id, @$data, $jsonBody, $url);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$jsonBody->message], 200);
        }catch(GuzzleException $e){
            // Activity::log('Flexm error while qr payment - '.$e->getMessage(), @$user->id);
            $this->addActivity('Flexm error while creating merchant token in physical merchant mode.', @$user_id, @$data, $e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            // Activity::log('Flexm error while qr payment - '.$e->getMessage(), @$user->id);
            $this->addActivity('Flexm error while creating merchant token in physical merchant mode.', @$user_id, @$data, $e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function getCountryList(Request $request)
    {
      $user = JWTAuth::toUser($request->input('token'));
        try{
            $data = [
              'PHL' => 'Philippines',
              'IDN' => 'Indonesia',
              'IND' => 'India ',
              'BGD' => 'Bangladesh',
              'CHN' => 'China',
              'THA' => 'Thailand',
              'VNM' => 'Vietnam',
              'MYS' => 'Malaysia',
              'NPL' => 'Nepal',
              'LKA' => 'Sri Lanka'
            ];
            return response()->json(['status' => "success", 'data' => $data, 'message'=> 'SUCCESS'], 200);

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
}
