<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Helper\Enets;
use App\Models\Pages;
use App\Models\Merchant;
use App\Models\SpuulPlan;
use App\Models\SpuulSubscription;
use App\Models\Terminal;
use App\Models\Transactions;
use App\Models\FlexmHtml;

use App\Models\PageLang;
use App\Models\UserAuto;
use App\Models\Contact;
use App\Models\Dormitory;
use App\Models\Country;
use App\Models\UserProfile;
use App\User, JWTAuth, Activity;
use Illuminate\Contracts\Encryption\DecryptException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;

use App\Helper\RandomStringGenerator;
use Illuminate\Support\Facades\Validator;

class FrontendController extends Controller
{

    public function deleteUser(Request $request){
      // echo Carbon::parse('31/12/1994')->toDateString();//date('y-m-d',strtotime('31/12/1994'));
      // die();
    }
    public function postPayment(Request $request)
    {
        $enets = new Enets;

        try {
            // dd( csrf_field() );
            $secret_key = '592923f1-349c-4d40-94fa-28c113c75bb6';

            $KEY_ID = '97521141-bbab-4e08-9ea2-c68352053b69';

            $enets->setUmid("UMID_887770001");
            $enets->setSecretKey($secret_key);
            $enets->setKeyId($KEY_ID);
            $enets->setAmount("60.00");
            $enets->setReturnUrl(route('trans_browser'));
            $enets->setNotifyUrl(route('trans_server'));
            $enets->setMerchantReference("TESTING".rand(10000,99999));
            $enets->setEnvironment("TEST");
            $result = $enets->run();
            dd($result);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        dd($enets);
    	// return view('admin.user.edit', compact('user', 'roles'));
    }
    public function getRemit(Request $request)
    {
        // echo date('Ymd H:s:m.s');die();
        $txnReq = '{"ss":"1","msg":{"netsMid":"UMID_887770001","tid":"","submissionMode":"B","txnAmount":"1000","merchantTxnRef":"'.date('Ymd H:s:m.s').'",
            "merchantTxnDtm":"'.date('Ymd H:s:m.s').'",
            "paymentType":"SALE",
            "currencyCode":"SGD",
            "paymentMode":"DD",
            "merchantTimeZone":"+8:00",
            "b2sTxnEndURL":"'.route('trans_browser').'",
            "b2sTxnEndURLParam":"",
            "s2sTxnEndURL":"'.route('trans_server').'",
            "s2sTxnEndURLParam":"",
            "clientType":"W",
            "supMsg":"",
            "netsMidIndicator":"U",
            "ipAddress":"127.0.0.1",
            "language":"en"}}';
        $secret_key = '592923f1-349c-4d40-94fa-28c113c75bb6';

        $KEY_ID = '97521141-bbab-4e08-9ea2-c68352053b69';
        $HMAC = base64_encode($txnReq+$secret_key);

        return view('frontend.pages.remit', compact('txnReq', 'KEY_ID', 'HMAC'));
    }

    public function getServer(Request $request)
    {
        $enets = new Enets;
        $secret_key = '592923f1-349c-4d40-94fa-28c113c75bb6';

        $KEY_ID = '97521141-bbab-4e08-9ea2-c68352053b69';

        try {
            $enets->setSecretKey($secret_key);
            $response = $enets->getBackendResponse();
            // \Log::debug($response);
            // \Log::debug($enets->getNetsMessage());
            // fwrite($file,print_r($response,true).PHP_EOL);
            // fwrite($file,print_r($enets->getNetsMessage(),true).PHP_EOL);
        } catch (Exception $e) {
            // \Log::debug($e->getMessage());
            // fwrite($file,$e->getMessage().PHP_EOL);
        }
        die();
    }

    public function getBrowser(Request $request)
    {
        $enets = new Enets;
        $secret_key = '592923f1-349c-4d40-94fa-28c113c75bb6';

        $KEY_ID = '97521141-bbab-4e08-9ea2-c68352053b69';
        try {
            $enets->setSecretKey($secret_key);
            $response = $enets->getFrontendResponse();
            // var_dump($response);
            // var_dump($enets->getNetsMessage());
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        // \Log::debug("browser_side");
        // \Log::debug($request->input());
        die();
    }

    public function getPage($id, Request $request)
    {
        $language = $request->language?$request->language:'english';
        $page = Pages::findOrFail($id);
        if($page){
            $page->title = $page->content($language)->first()?$page->content($language)->first()->title:($page->lang_content()->first()?$page->lang_content()->first()->title:'');
            $page->content = $page->content($language)->first()?$page->content($language)->first()->content:($page->lang_content()->first()?$page->lang_content()->first()->content:'');
        }

        return view('frontend.pages.common', compact('page'));
    }

    public function getFlexmTerms(Request $request)
    {
        $remittance_content = getOption('remittance_terms');
        $title = '';
        $flexm_content = getOption('flexm_tnc');
        return view('frontend.pages.flexm', compact('title', 'flexm_content', 'remittance_content'));
    }

    public function getRemittanceTerms(Request $request)
    {
            $title = '';
            $content = getOption('remittance_terms');
           return view('frontend.pages.flexm', compact('title', 'content'));
    }

    public function getFlexmGuide()
    {
        $page = new \stdClass();
        $page->content = getOption('flexm_user_guide_content');
        $page->title = 'User Guide';

        return view('frontend.pages.common', compact('page'));
    }

    public function getFlexmFaq()
    {
        $page = new \stdClass();
        $page->content = getOption('flexm_myma_faq_content');
        $page->title = "FAQ's";

        return view('frontend.pages.flexm_faq', compact('page'));
    }

    public function getFlexmHow()
    {
        $page = new \stdClass();
        $page->content = getOption('flexm_howto_content');
        $page->title = '';

        return view('frontend.pages.common', compact('page'));
    }
    
    public function getFlexmSupport()
    {
        $page = new \stdClass();
        $page->content = getOption('flexm_support_content');
        $page->title = '';

        return view('frontend.pages.common', compact('page'));
    }
    //flexm end
    public function getSignuppp(Request $request)
    {
           return view('frontend.signup_new');
    }

    public function getSignupp(Request $request)
    {
           return view('frontend.signup');
    }

    public function getSignup(Request $request)
    {
        $id = $request->dms;
        $user = '';
        if($id != ''){
            try{
                $id = decrypt($id);
            }catch(DecryptException $e){
                  abort('404');
            }
            $user = UserAuto::find($id);

        }
        $dormitories = Dormitory::pluck('name', 'id');
        $nationalities = Country::pluck('nationality', 'id');
        // dd($nationalities);
        // return view('emails.flexm_account_created', compact('user'));
        return view('frontend.sign', compact('user', 'dormitories', 'nationalities'));
    }

    public function getSignupSuccess(Request $request)
    {
           return view('frontend.created');
    }
    
    public function getDms(Request $request)
    {
        $name = $request->name;
        $fin_no = $request->fin_no;
        $dob = $request->dob;

        $users = UserAuto::query();
        if($name != '' || $fin_no != '' || $dob != ''){
          $users = UserAuto::query();

          if($name != ''){
            // $name = strtoupper($name);
            $users->where('name', 'like', "%{$name}%");
          }
          if($fin_no != ''){
            $searchValue = $fin_no;
            $items = UserAuto::all()->filter(function($record) use($searchValue) {
                        $email = $record->fin_no;
                        try{
                            $email = Crypt::decrypt($email);
                        }catch(DecryptException $e){

                        }
                        if(($email) == $searchValue) {
                            return $record;
                        }
            })->pluck('id');
            // dd($items);
            $users->whereIn('id', $items);
          }
          if($dob != ''){
            $dob = Carbon::createFromFormat('d/m/Y', $dob)->toDateString();
            $users->whereDate('dob', $dob);
          }
          $users = $users->get();

          foreach($users as $user){
            $exist = UserProfile::where('fin_no', $user->fin_no)->first();
            if($exist){
              $user->registered_already = true;
            }else{
              $user->registered_already = false;
            }
          }
        }
        return view('frontend.dms', compact('users'));
    }
    public function getTnc(Request $request)
    {
        $page = PageLang::find(4);

        return view('frontend.pages.tnc', compact('page'));
    }

    public function getPayment(Request $request)
    {
           return view('frontend.pages.payment');
    }

    public function getPrivacy(Request $request)
    {
           return view('frontend.pages.privacy');
    }

    public function getFAQ(Request $request)
    {
           return view('frontend.pages.faq');
    }

    public function getInfoContact(Request $request)
    {
        $token = $request->token;
        $lang = $request->language;
        if($token){
            $user = JWTAuth::toUser($request->input('token'));//User::find($id);
        }else{
            $user = [];
        }
        return view('frontend.pages.contact', compact('user'));
    }

    public function postContact(Request $request)
    {
           $data = $request->only('name', 'email', 'phone', 'description');
           Contact::create($data);
           return view('frontend.pages.thank_you');
           // dd($data);
    }

    public function thankYou()
    {
           return view('frontend.pages.thank_you');
           // dd($data);
    }

    public function getAre(Request $request)
    {
           return view('frontend.mwc.are');
    }

    public function getClinic(Request $request)
    {
           return view('frontend.mwc.clinic');
    }

    public function getContact(Request $request)
    {
           return view('frontend.mwc.contact');
    }

    public function getDo(Request $request)
    {
           return view('frontend.mwc.do');
    }

    public function getFairSignup(Request $request)
    {
           return view('frontend.mwc.fair_signup');
    }

    public function getFair(Request $request)
    {
           return view('frontend.mwc.fair');
    }

    public function getHelp(Request $request)
    {
           return view('frontend.mwc.help');
    }

    public function getKiosk(Request $request)
    {
           return view('frontend.mwc.kiosk');
    }

    public function html($user_id, Request $request)
    {
      $flexmhtml = FlexmHtml::where('user_id', $user_id)->orderBy('id', 'desc')->first();
      $html = $flexmhtml->html;//\App\Models\Option::getOption('html');
      $html = str_replace('\"', '"', $html);
      return view('frontend.pages.html', compact('html'));
    }

    public function getFlexmLogin(Request $request)
    {
      try{
        $id = $request->input('id');
        $id = decrypt($id);
        $ids = explode('_', $id);
        $user_id = $ids[0];
        $plan_id = $ids[1];
        $sub_id = $ids[2];
        session(['spuul_user_id' => $user_id]);
        session(['spuul_plan_id' => $plan_id]);
        session(['spuul_sub_id' => $sub_id]);
        $user = User::find($user_id);
        $token = JWTAuth::fromUser($user);
        return view('frontend.pages.payment', compact('token'));
      }catch(DecryptException $e){
        abort('404');
      }
    }

    public function getCheckout(Request $request)
    {
        $user_id = session('spuul_user_id');
        $plan_id = session('spuul_plan_id');
        $user = User::find($user_id);
        $plan = SpuulPlan::find($plan_id);
        $wallet = session('wallet');

        if($user && $plan){
            $type = ($plan->type == 1)?'monthly':'yearly';
            $description = "Spuul ".$type." subscription plan";
            $merchant = Merchant::find(2);
            $other_share_per = 0.75;
            if($merchant){
              $other_share_per = $merchant->myma_transaction_share;
            }
            $myma_txn_share = getOption('flexm_charges_app', '0.75');
            $total_per = $myma_txn_share+$other_share_per;
            $share_amount = number_format(($plan->price*$total_per)/100, '2');
            return view('frontend.pages.checkout', compact('share_amount', 'plan', 'description', 'user', 'wallet'));
        }else{
          abort('404');
        }

    }

    public function postCheckout(Request $request)
    {
        $user_id = session('spuul_user_id');
        $plan_id = session('spuul_plan_id');
        $sub_id = session('spuul_sub_id');

        $flexm_token = session('flexm_token');
        $user = User::find($user_id);
        $plan = SpuulPlan::find($plan_id);
        $sub = SpuulSubscription::find($sub_id);

        $wallet = session('wallet');
        $type = "spuul";
        $id = $plan_id;
        $base_url = config('app.flexm_end_point');

        if($user && $plan && $sub){
          try{
            $type = ($plan->type == 1)?'monthly':'yearly';
            $description = "Spuul ".$type." subscription plan";
            $merchant = Merchant::find(2);
            $other_share_per = 0.75;
            if($merchant){
              $other_share_per = $merchant->myma_transaction_share;
            }
            $myma_txn_share = getOption('flexm_charges_app', '0.75');
            $total_per = $myma_txn_share+$other_share_per;
            $share_amount = number_format(($plan->price*$total_per)/100, '2');

            $d['account_number'] = $sub->account_number;
            $d['email'] = $sub->email;
            if($plan->type == '1')
              $d['sku_code'] = 'interactive_sg_monthly_sgd';
            else
              $d['sku_code'] = 'interactive_sg_yearly_sgd';

            $spuul_share_item_amount = getOption('spuul_share', 40);

            $merchant_code = $merchant->merchant_code;
            $merchant_name = $merchant->merchant_name;
            $mid = $merchant->mid;
            $terminal = Terminal::where('merchant_id', $merchant->id)->where('payment_mode', 1)->first();
            if($terminal){
              $tid = $terminal->tid;
            }else{
              dd('Terminal does not exist for the merchant');
            }
            $amount = $plan->price;
            $charges = $share_amount;

            $flexm_part = $charges/2;
            $myma_part = $charges - $flexm_part;

            $other_share = ($amount*$spuul_share_item_amount)/100;
            $myma_share = $amount-$other_share;
            $gst = getOption('gst_tax', '7');
            $gst_amt = ($myma_share*$gst)/100;

            $total = $amount + $charges;

            $data['mid'] = $mid;
            $data['tid'] = $tid;
            $data['tran_amount'] = $total;
            $data['description'] = $description;
            $data['status'] = 1;
            $data['check'] = md5($data['mid'].''.$data['tid']);

            $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);

            $result = $client->post($base_url.'user/wallets/payment/token',[
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

                        $resultt = $clientt->get($base_url.'user/profile?debug=false');

                        $codee = $resultt->getStatusCode(); // 200
                        $reasonn = $resultt->getReasonPhrase(); // OK
                        $mobile_no = isset($user->profile)?$user->profile->phone:'';
                        $wallet_user = '';
                        if($codee == "200" && $reasonn == "OK"){
                            $bodyy = $resultt->getBody();
                            $contentt = json_decode($bodyy->getContents());
                            if($contentt->success){
                                $msgg = $contentt->message;
                                $contt = $contentt->data;
                                $mobile_no = $contt->mobile;
                                $wallet_user = @$contt->profile->preferred_name;
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

                        $result = $clientt->post($base_url.'user/wallets/payment/auth', [
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
                                    'ref_id' => $id,
                                    'phone_no' => $mobile_no,
                                    'wallet_user_name' => $wallet_user,
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
                                    'response'  => json_encode($cont)
                                ]);
                                $sub->payment_done = 1;
                                $sub->save();

                                $now = Carbon::parse($sub->end_date)->addDay();
                                $end = Carbon::parse($sub->end_date)->addDay();
                                if($d['sku_code'] == 'interactive_sg_monthly_sgd'){
                                  $plan_type = 'monthly';
                                  $end = $end->addMonth();
                                }else{
                                  $plan_type = 'yearly';
                                  $end = $end->addYear();
                                }

                                $subc = SpuulSubscription::create([
                                  'user_id' => $user->id,
                                  'transaction_id' => $tran->id,
                                  'start_date'  => $now,
                                  'end_date'  => $end,
                                  'status'  => 'paid',
                                  'plan_type' => $plan_type,
                                  'email' => $d['email'],
                                  'account_number' => $d['account_number']
                                ]);

                                Activity::log('Paid for the spuul subscription Start-on #'.$now.' End-on #'.$end, @$user->id);
                                $ref_no = $cont->ref_id;

                                $request->session()->forget('spuul_user_id');
                                $request->session()->forget('spuul_plan_id');
                                $request->session()->forget('spuul_sub_id');

                                return view('frontend.pages.success', compact('ref_no'));
                            }else{
                                \Log::debug($content);
                                return redirect()->back()->withErrors(
                                    @$content->message
                                );
                            }
                        }
                        return redirect()->back()->withErrors(
                            @$reason
                        );
                    }
                    return redirect()->back()->withErrors(
                        @$msg
                    );
                }else{
                  return redirect()->back()->withErrors(
                      @$content->message
                  );
                }
            }
            return redirect()->back()->withErrors(
                @$reason
            );

          }catch (BadResponseException $ex) {
              $response = $ex->getResponse();
              $jsonBody = json_decode((string) $response->getBody());
              // Activity::log('Flexm error while making payment for spuul subscription before expiration - '.json_encode($jsonBody), @$user->id);
              return redirect()->back()->withErrors(
                  @$jsonBody->messages
              );
          }catch(GuzzleException $e){
              // Activity::log('Flexm error while making payment for spuul subscription before expiration- '.$e->getMessage(), @$user->id);
              return redirect()->back()->withErrors(
                  @$e->getMessage()
              );
          }catch(Exception $e){
              // Activity::log('Flexm error while making payment for spuul subscription before expiration- '.$e->getMessage(), @$user->id);
              return redirect()->back()->withErrors(
                  @$e->getMessage()
              );
          }
        }else{
          abort('404');
        }

    }

    public function getSuccess(Request $request)
    {
           return view('frontend.pages.success');
    }


    // public function maskCardNumber($number) {
    //     $vis = $number.slice(-4);
    //     $countNum = '';
    //     $count = 0;
    //
    //     for ($i = ($number.strlength()) - 4; i > 0; i--) {
    //       $count++;
    //       if ($count === 4) {
    //         $count = 0;
    //         $countNum += '* ';
    //       } else {
    //         $countNum += '*';
    //       }
    //     }
    //     return ($countNum + $vis);
    // }
    //
    // public function cardDecrypt($access_token, $user_hash_id, $card_number) {
    //   if (!!$access_token && !!$user_hash_id && !!$card_number && !!$window.APP_CONFIG.ih_base_url) {
    //     $hashed_details = sha256($user_hash_id + $access_token + $window.APP_CONFIG.ih_base_url);
    //     $base_64_card_number = decodeBase64($card_number);
    //     $decoded_card_number = rc4MM($hashed_details, $base_64_card_number);
    //
    //     return $decoded_card_number;
    //   }
    // }
    //
    // public function cardDecryptText($access_token, $user_hash_id, $card_number) {
    //   if (!!$access_token && !!$user_hash_id && !!$card_number && !!$window.APP_CONFIG.ih_base_url) {
    //     $hashed_details = sha256($user_hash_id + $access_token + $window.APP_CONFIG.ih_base_url);
    //     $base_64_card_number = decodeBase64($card_number);
    //     $decoded_card_number = rc4MM($hashed_details, $base_64_card_number);
    //
    //     return maskCardNumber($decoded_card_number);
    //   }
    // }
    //
    // function rc4MM ($key, $str) {
    //   $s = [], $j = 0, $x, $res = '';
    //   for ($i = 0; $i < 256; $i++) {
    //       $s[$i] = $i;
    //   }
    //   for ($i = 0; $i < 256; $i++) {
    //       $j = ($j + $s[$i] + $key.charCodeAt($i % $key.strlength())) % 256;
    //       $x = $s[$i];
    //       $s[$i] = $s[$j];
    //       $s[$j] = $x;
    //   }
    //   $i = 0;
    //   $j = 0;
    //   for ($y = 0; $y < $str.strlength(); $y++) {
    //       $i = ($i + 1) % 256;
    //       $j = ($j + $s[$i]) % 256;
    //       $x = $s[$i];
    //       $s[$i] = $s[$j];
    //       $s[$j] = $x;
    //       $res += String.fromCharCode($str.charCodeAt($y) ^ $s[($s[$i] + $s[$j]) % 256]);
    //   }
    //   return $res;
    // }
    //
    // function decodeBase64($input) {
    //   $keyStr = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=';
    //   $output = "";
    //   $chr1 = $chr2 = $chr3 = "";
    //   $enc1 = $enc2 = $enc3 = $enc4 = "";
    //   $i = 0;
    //
    //   // remove all characters that are not A-Z, a-z, 0-9, +, /, or =
    //   $base64test = /[^A-Za-z0-9\+\/\=]/g;
    //   if ($base64test.exec($input)) {
    //       DIE("There were invalid base64 characters in the input text.\n" +
    //           "Valid base64 characters are A-Z, a-z, 0-9, '+', '/',and '='\n" +
    //           "Expect errors in decoding.");
    //   }
    //   $input = $input.replace(/[^A-Za-z0-9\+\/\=]/g, "");
    //
    //   do {
    //       $enc1 = $keyStr.indexOf($input.charAt($i++));
    //       $enc2 = $keyStr.indexOf($input.charAt($i++));
    //       $enc3 = $keyStr.indexOf($input.charAt($i++));
    //       $enc4 = $keyStr.indexOf($input.charAt($i++));
    //
    //       $chr1 = ($enc1 << 2) | ($enc2 >> 4);
    //       $chr2 = (($enc2 & 15) << 4) | ($enc3 >> 2);
    //       $chr3 = (($enc3 & 3) << 6) | $enc4;
    //
    //       $output = $output + String.fromCharCode($chr1);
    //
    //       if ($enc3 != 64) {
    //           $output = $output + String.fromCharCode($chr2);
    //       }
    //       if ($enc4 != 64) {
    //           $output = $output + String.fromCharCode($chr3);
    //       }
    //
    //       $chr1 = $chr2 = $chr3 = "";
    //       $enc1 = $enc2 = $enc3 = $enc4 = "";
    //
    //   } while ($i < $input.strlength());
    //   $output = utf8Decode($output)
    //   return $output;
    // }
    //
    // function utf8Decode ($utftext) {
    //   $string = "";
    //   $i = 0;
    //   $c = 0, $c1 = 0, $c2 = 0, $c3 = 0;
    //
    //   while ( $i < $utftext.strlength() ) {
    //
    //       $c = $utftext.charCodeAt($i);
    //
    //       if ($c < 128) {
    //           $string += String.fromCharCode($c);
    //           $i++;
    //       }
    //       else if(($c > 191) && ($c < 224)) {
    //           $c2 = $utftext.charCodeAt($i+1);
    //           $string += String.fromCharCode((($c & 31) << 6) | ($c2 & 63));
    //           $i += 2;
    //       }
    //       else {
    //           $c2 = $utftext.charCodeAt($i+1);
    //           $c3 = $utftext.charCodeAt($i+2);
    //           $string += String.fromCharCode((($c & 15) << 12) | (($c2 & 63) << 6) | ($c3 & 63));
    //           $i += 3;
    //       }
    //   }
    //   return string;
    // }
    //
    // public function sha256 ($data) {
    //   $rotateRight = function($n, $x) {
    //       return (($x >>> $n) | ($x << (32 - $n)));
    //   }
    //   $choice = function($x, $y, $z) {
    //       return (($x & $y) ^ (~$x & $z));
    //   }
    //
    //   function majority($x, $y, $z) {
    //       return (($x & $y) ^ ($x & $z) ^ ($y & $z));
    //   }
    //
    //   function sha256_Sigma0($x) {
    //       return (rotateRight(2, $x) ^ rotateRight(13, $x) ^ rotateRight(22, $x));
    //   }
    //
    //   function sha256_Sigma1($x) {
    //       return (rotateRight(6, $x) ^ rotateRight(11, $x) ^ rotateRight(25, $x));
    //   }
    //
    //   function sha256_sigma0($x) {
    //       return (rotateRight(7, $x) ^ rotateRight(18, $x) ^ ($x >>> 3));
    //   }
    //
    //   function sha256_sigma1($x) {
    //       return (rotateRight(17, $x) ^ rotateRight(19, $x) ^ ($x >>> 10));
    //   }
    //
    //   function sha256_expand($W, $j) {
    //       return ($W[$j & 0x0f] += sha256_sigma1($W[($j + 14) & 0x0f]) + $W[($j + 9) & 0x0f] +
    //           sha256_sigma0($W[($j + 1) & 0x0f]));
    //   }
    //
    //   /* Hash constant words K: */
    //   $K256 = array(
    //       0x428a2f98, 0x71374491, 0xb5c0fbcf, 0xe9b5dba5,
    //       0x3956c25b, 0x59f111f1, 0x923f82a4, 0xab1c5ed5,
    //       0xd807aa98, 0x12835b01, 0x243185be, 0x550c7dc3,
    //       0x72be5d74, 0x80deb1fe, 0x9bdc06a7, 0xc19bf174,
    //       0xe49b69c1, 0xefbe4786, 0x0fc19dc6, 0x240ca1cc,
    //       0x2de92c6f, 0x4a7484aa, 0x5cb0a9dc, 0x76f988da,
    //       0x983e5152, 0xa831c66d, 0xb00327c8, 0xbf597fc7,
    //       0xc6e00bf3, 0xd5a79147, 0x06ca6351, 0x14292967,
    //       0x27b70a85, 0x2e1b2138, 0x4d2c6dfc, 0x53380d13,
    //       0x650a7354, 0x766a0abb, 0x81c2c92e, 0x92722c85,
    //       0xa2bfe8a1, 0xa81a664b, 0xc24b8b70, 0xc76c51a3,
    //       0xd192e819, 0xd6990624, 0xf40e3585, 0x106aa070,
    //       0x19a4c116, 0x1e376c08, 0x2748774c, 0x34b0bcb5,
    //       0x391c0cb3, 0x4ed8aa4a, 0x5b9cca4f, 0x682e6ff3,
    //       0x748f82ee, 0x78a5636f, 0x84c87814, 0x8cc70208,
    //       0x90befffa, 0xa4506ceb, 0xbef9a3f7, 0xc67178f2
    //   );
    //
    //   /* global arrays */
    //   $ihash, $count, $buffer;
    //   $sha256_hex_digits = "0123456789abcdef";
    //
    //   /* Add 32-bit integers with 16-bit operations (bug in some JS-interpreters:
    //   overflow) */
    //   function safe_add($x, $y) {
    //       $lsw = ($x & 0xffff) + ($y & 0xffff);
    //       $msw = ($x >> 16) + ($y >> 16) + ($lsw >> 16);
    //       return ($msw << 16) | ($lsw & 0xffff);
    //   }
    //
    //   /* Initialise the SHA256 computation */
    //   function sha256_init() {
    //       $ihash = array();
    //       $count = array();
    //       $buffer = array();
    //       $count[0] = $count[1] = 0;
    //       $ihash[0] = 0x6a09e667;
    //       $ihash[1] = 0xbb67ae85;
    //       $ihash[2] = 0x3c6ef372;
    //       $ihash[3] = 0xa54ff53a;
    //       $ihash[4] = 0x510e527f;
    //       $ihash[5] = 0x9b05688c;
    //       $ihash[6] = 0x1f83d9ab;
    //       $ihash[7] = 0x5be0cd19;
    //   }
    //
    //   /* Transform a 512-bit message block */
    //   function sha256_transform() {
    //       // var a, b, c, d, e, f, g, h, T1, T2;
    //       $W = array();
    //
    //       /* Initialize registers with the previous intermediate value */
    //       $a = $ihash[0];
    //       $b = $ihash[1];
    //       $c = $ihash[2];
    //       $d = $ihash[3];
    //       $e = $ihash[4];
    //       $f = $ihash[5];
    //       $g = $ihash[6];
    //       $h = $ihash[7];
    //
    //       /* make 32-bit words */
    //       for ($i = 0; $i < 16; $i++)
    //           $W[$i] = (($buffer[($i << 2) + 3]) | ($buffer[($i << 2) + 2] << 8) | ($buffer[($i << 2) + 1] << 16) | ($buffer[$i << 2] << 24));
    //
    //       for ($j = 0; $j < 64; $j++) {
    //           $T1 = $h + sha256_Sigma1($e) + choice($e, $f, $g) + $K256[$j];
    //           if ($j < 16) $T1 += $W[$j];
    //           else $T1 += sha256_expand($W, $j);
    //           $T2 = sha256_Sigma0($a) + majority($a, $b, $c);
    //           $h = $g;
    //           $g = $f;
    //           $f = $e;
    //           $e = safe_add($d, $T1);
    //           $d = $c;
    //           $c = $b;
    //           $b = $a;
    //           $a = safe_add($T1, $T2);
    //       }
    //
    //       /* Compute the current intermediate hash value */
    //       $ihash[0] += $a;
    //       $ihash[1] += $b;
    //       $ihash[2] += $c;
    //       $ihash[3] += $d;
    //       $ihash[4] += $e;
    //       $ihash[5] += $f;
    //       $ihash[6] += $g;
    //       $ihash[7] += $h;
    //   }
    //
    //   /* Read the next chunk of data and update the SHA256 computation */
    //   function sha256_update($data, $inputLen) {
    //       $i = $index = $curpos = 0;
    //       /* Compute number of bytes mod 64 */
    //       $index = (($count[0] >> 3) & 0x3f);
    //       $remainder = ($inputLen & 0x3f);
    //
    //       /* Update number of bits */
    //       if (($count[0] += ($inputLen << 3)) < ($inputLen << 3)) $count[1]++;
    //       $count[1] += ($inputLen >> 29);
    //
    //       /* Transform as many times as possible */
    //       for ($i = 0; $i + 63 < $inputLen; $i += 64) {
    //           for (var $j = $index; $j < 64; $j++)
    //               $buffer[$j] = $data.charCodeAt($curpos++);
    //           sha256_transform();
    //           $index = 0;
    //       }
    //
    //       /* Buffer remaining input */
    //       for ($j = 0; $j < $remainder; $j++)
    //           $buffer[$j] = $data.charCodeAt($curpos++);
    //   }
    //
    //   /* Finish the computation by operations such as padding */
    //   function sha256_final() {
    //       $index = (($count[0] >> 3) & 0x3f);
    //       $buffer[$index++] = 0x80;
    //       if ($index <= 56) {
    //           for ($i = $index; $i < 56; $i++)
    //               $buffer[$i] = 0;
    //       } else {
    //           for ($i = $index; $i < 64; $i++)
    //               $buffer[$i] = 0;
    //           sha256_transform();
    //           for ($i = 0; $i < 56; $i++)
    //               $buffer[$i] = 0;
    //       }
    //       $buffer[56] = ($count[1] >>> 24) & 0xff;
    //       $buffer[57] = ($count[1] >>> 16) & 0xff;
    //       $buffer[58] = ($count[1] >>> 8) & 0xff;
    //       $buffer[59] = $count[1] & 0xff;
    //       $buffer[60] = ($count[0] >>> 24) & 0xff;
    //       $buffer[61] = ($count[0] >>> 16) & 0xff;
    //       $buffer[62] = ($count[0] >>> 8) & 0xff;
    //       $buffer[63] = $count[0] & 0xff;
    //       sha256_transform();
    //   }
    //
    //   /* Split the internal hash values into an array of bytes */
    //   function sha256_encode_bytes() {
    //       $j = 0;
    //       $output = array();
    //       for ($i = 0; $i < 8; $i++) {
    //           $output[$j++] = (($ihash[$i] >>> 24) & 0xff);
    //           $output[$j++] = (($ihash[$i] >>> 16) & 0xff);
    //           $output[$j++] = (($ihash[$i] >>> 8) & 0xff);
    //           $output[$j++] = ($ihash[$i] & 0xff);
    //       }
    //       return $output;
    //   }
    //
    //   /* Get the internal hash as a hex string */
    //   function sha256_encode_hex() {
    //       $output = '';
    //       for ($i = 0; $i < 8; $i++) {
    //           for ($j = 28; $j >= 0; $j -= 4)
    //               $output += $sha256_hex_digits.charAt(($ihash[$i] >>> $j) & 0x0f);
    //       }
    //       return $output;
    //   }
    //
    //   sha256_init();
    //   sha256_update($data, $data.strlength());
    //   sha256_final();
    //   return sha256_encode_hex();
    // }
    
    protected function validator(array $data)
    {
        $rules = [
            'name' => 'required|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|unique:user_profile',
            'password' => ['required',
               'min:8',
               //'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\X])(?=.*[!$#%]).*$/',
               'confirmed'],
              //'required|min:8|confirmed',
            // 'wp_expiry' => 'required',
            'fin_no' => 'required|unique:user_profile',
            'profile_pic' => 'required',
            'wp_front' => 'required',
            'wp_back' => 'required',
            'gender' => 'required',
            'dob' => 'required',
            'country_id' => 'required',
        ];

        $messages = [
            'phone.required'      => 'The mobile number field is required.',
            'phone.unique'        => 'The mobile number has already been taken.',
            'country_id.required' => 'The Nationality field is required.',
        ];
        return Validator::make($data, $rules, $messages);
    }

    public function verifyFin($fin_no)
    {
        // $fin_no = 'G5098264K';
        try{
            $client = new Client(); //GuzzleHttp\Client
            $result = $client->get("http://residents.centurioncorp.com.sg/mymaapi/api/resident?json={'fin_no':'".$fin_no."','phone_no':'','gender':''}");
            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());

                return $content;
            }else{
                return false;
            }
        }catch(Exception $e){
            return false;
        }
    }

    public function registerCustNew(Request $request)
    {
        $data = $request->except('profile_pic');

        \Log::debug($data);
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
          $errors = $validator->errors();
          $message = [];
          foreach($errors->messages() as $key => $error){
             $message[$key] = $error[0];
             break;
          }
          return redirect()->back()->withErrors($message)->withInput();
        }

        try{


            $searchValue = strtolower($data['email']);
            if($searchValue != ""){
              $items = User::all()->filter(function($record) use($searchValue) {
                          $email = $record->email;
                          try{
                              $email = Crypt::decrypt($email);
                          }catch(DecryptException $e){

                          }
                          if(($email) == $searchValue) {
                              return $record;
                          }
              });
              if(count($items)){
                  return redirect()->back()->withErrors(['email' => 'Email has already been taken.'])->withInput();
              }
            }

            $type = 'free';
            $dorm = '';
            $fin = '';
            if(isset($data['fin_no']) && $data['fin_no'] != ''){
                    $type = 'registered';
                    $fin = $this->verifyFin($data['fin_no']);
                    if($fin && $fin->verified){
                        $dorm = Dormitory::where('full_name', $fin->dormitory)->first();
                        $type = 'registered_verified';

                    }
            }
            $user = User::create([
                'name' => $data['name'],
                'email' => strtolower($data['email']),
                'password' => bcrypt($data['password']),
                'fcm_token' => @$data['fcm_token'],
                'type' => $type,
                'email_confirm_key' => str_random(),
                'number_verified' => 0,
                // 'otp' => $otp,
                'country_id' => @$data['country_id']
            ]);

            if(isset($data['fin_no']) && $data['fin_no'] != ''){
                    \QrCode::format('png')->size(400)->generate($data['fin_no'], '../public/files/qrcodes/'.$user->id.'.png');
            }

            if($fin != '' && $type == 'registered'){
                Activity::log('Fin no not registered with centurioncorp', $user->id);
                $user->qr_code = 'files/qrcodes/'.$user->id.'.png';
            }
            if($type == 'registered_verified'){

                $user->qr_code = 'files/qrcodes/'.$user->id.'.png';
            }

            $user->save();

            if($request->hasFile('profile_pic')){
              $file = $request->file('profile_pic');

              $folder = 'files/profile';

              if (!is_dir($folder)) {
                  mkdir($folder, 755, true);
              }
              $filename = $file->getClientOriginalName();

              $filename = preg_replace("/[^a-zA-Z0-9.]/", "_", $filename);
              $filename = str_replace("_", " ", $filename);
              $actual_name   = pathinfo($filename, PATHINFO_FILENAME);
              $original_name = $actual_name;
              $extension     = pathinfo($filename, PATHINFO_EXTENSION);

              $i = 1;
              while(file_exists($folder . '/' . $actual_name . "." . $extension))
              {
                  $actual_name = (string) $original_name . $i;
                  $filename    = $actual_name . "." . $extension;
                  $i++;
              }
              $full_file_name  = $folder . '/' . $filename;
              $file->move($folder, $filename);

              // $photo_path = uploadPhoto($photo, $folder);

              $data['profile_pic'] = $full_file_name;

            }

            if($request->hasFile('wp_front')){
              $file = $request->file('wp_front');

              $folder = 'files/permit';

              if (!is_dir($folder)) {
                  mkdir($folder, 755, true);
              }
              $filename = $file->getClientOriginalName();

              $filename = preg_replace("/[^a-zA-Z0-9.]/", "_", $filename);
              $filename = str_replace("_", " ", $filename);
              $actual_name   = pathinfo($filename, PATHINFO_FILENAME);
              $original_name = $actual_name;
              $extension     = pathinfo($filename, PATHINFO_EXTENSION);

              $i = 1;
              while(file_exists($folder . '/' . $actual_name . "." . $extension))
              {
                  $actual_name = (string) $original_name . $i;
                  $filename    = $actual_name . "." . $extension;
                  $i++;
              }
              $full_file_name  = $folder . '/' . $filename;
              $file->move($folder, $filename);

              // $photo_path = uploadPhoto($photo, $folder);

              $data['wp_front'] = $full_file_name;

            }

            if($request->hasFile('wp_back')){
              $file = $request->file('wp_back');

              $folder = 'files/permit';

              if (!is_dir($folder)) {
                  mkdir($folder, 755, true);
              }
              $filename = $file->getClientOriginalName();

              $filename = preg_replace("/[^a-zA-Z0-9.]/", "_", $filename);
              $filename = str_replace("_", " ", $filename);
              $actual_name   = pathinfo($filename, PATHINFO_FILENAME);
              $original_name = $actual_name;
              $extension     = pathinfo($filename, PATHINFO_EXTENSION);

              $i = 1;
              while(file_exists($folder . '/' . $actual_name . "." . $extension))
              {
                  $actual_name = (string) $original_name . $i;
                  $filename    = $actual_name . "." . $extension;
                  $i++;
              }
              $full_file_name  = $folder . '/' . $filename;
              $file->move($folder, $filename);

              // $photo_path = uploadPhoto($photo, $folder);

              $data['wp_back'] = $full_file_name;

            }

              if(isset($data['dob']) && $data['dob'] != ''){
                $data['dob'] = Carbon::createFromFormat('d/m/Y', $data['dob'])->toDateString();
              }
              if(isset($data['wp_expiry']) && $data['wp_expiry'] != ''){
                $data['wp_expiry'] = Carbon::createFromFormat('d/m/Y', $data['wp_expiry'])->toDateString();
              }

            $user_profile = [
                'phone' => @$data['phone'],
                'fin_no' => strToUpper(@$data['fin_no']),
                'profile_pic' => @$data['profile_pic'],
                'gender' => @$data['gender'],
                'dob' => @$data['dob'],
                'block' => @$data['block'],
                'sub_block' => @$data['sub_block'],
                'floor_no' => @$data['floor_no'],
                'unit_no' => @$data['unit_no'],
                'room_no' => @$data['room_no'],
                'zip_code' => @$data['zip_code'],
                'street_address' => @$data['street_address'],
                'wp_front' => @$data['wp_front'],
                'wp_back' => @$data['wp_back'],
                'wp_expiry' => @$data['wp_expiry'],
                'dormitory_id' => ($dorm != '')?$dorm->id:@$data['dormitory_id'],
            ];
            $user->profile()->create($user_profile);

            Activity::log('Account is created.', $user->id);
            /** Assign role*/
            $role = 'app-user';
            $user->assignRole($role);

            $generator = new RandomStringGenerator;
            $tokenLength = 32;

            $token = $generator->generate($tokenLength);
            $flag = true;
            while($flag){
                $exist = User::where('uid', $token)->first();
                if($exist){
                  $token = $generator->generate($tokenLength);
                }else{
                  $flag = false;
                }
            }
            $user->uid = $token;
            $user->save();

            //if($user->email != '')
              //event(new VerifyEmailEvent($user->id));

            // \Event::fire('user.created', [$user->id]);
            return redirect()->route('signup.form.success');

            //return response()->json(['status' => 'success', 'data' => 'Registration successfull.', 'message' => 'ACCOUNT_CREATED']);

        }catch (Exception $e){
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }
  }
