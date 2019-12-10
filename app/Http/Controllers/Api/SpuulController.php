<?php

namespace App\Http\Controllers\Api;

use App\User;
use App\Models\BusStop;
use App\Models\SpuulPlan;
use Illuminate\Http\Request;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\BadResponseException;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use Illuminate\Support\Facades\Validator;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use JWTAuth, Carbon\Carbon;

class SpuulController extends Controller
{
    const BASE_URL = 'https://test-api.flexm.sg/';
    public function getToken(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $data = [];
        $data['email'] = $email = $request->email;
        $pass = $request->password;

        try{

            // if($email == 'last1@yopmail.com'){
            //     $email = 'shona@sginteractive.com.sg';
            //     $pass = "ab123456";
            // }

            $client = new Client();
            $result = $client->post('https://api.spuul.com/oauth/token',[
                'form_params' => [
                    'grant_type' => 'password',//'client_credentials',
                    'username' => $email,
                    'password' => $pass,
                    'client_id' => '1e9240872cfda7989634346aa89cd3bf05d6a0462756f58d79e445788cca8d66',
                    'client_secret' => '6bdfaae72a9a41b551958c3ff0e1057a9ef25632f2f562049cba8b384dce41e3'
                ]
            ]);
            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                addActivity('Spuul successful login', @$user_id, @$data, @$content);
                return response()->json(['status' => 'success', 'data' => $content, 'message'=> 'DATA'], 200);
            }else{
                addActivity('Spuul error in login', @$user_id, @$data, @$content);
                return response()->json(['status' => 'error', 'data' => 'Exception Occured', 'message'=> 'EXCEPTION_OCCURED'], 200);
            }
            addActivity('Spuul error in login reason code', @$user_id, @$data, ['code' => $code, 'reason' => $reason]);

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            $msg = @$jsonBody->message?@$jsonBody->message:@$jsonBody->error_description;
            addActivity('Spuul error in login', @$user_id, @$data, @$jsonBody);
            return response()->json(['status' => 'error', 'data' => @$msg, 'message'=> @$msg], 200);
        }catch(GuzzleException $e){
            addActivity('Spuul error in login', @$user_id, @$data, @$e->getMessage());
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            addActivity('Spuul error in login', @$user_id, @$data, @$e->getMessage());
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function browse(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $data = [];
        $spuul_token = $request->spuul_token;
        try{
                $client = new Client(['headers' => ['Accept' => 'application/vnd.spuul.v3', 'Authorization' => 'Bearer '.$spuul_token]]);
                $result = $client->get('https://api.spuul.com/channels'
                // , array(
                    // 'query' => [
                        // 'channel_code' => 'must_watch'
                    // ]
                // )
            );
            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                addActivity('Spuul successfully fetched channels', @$user_id, @$data, @$content);
                return response()->json(['status' => 'success', 'data' => $content, 'message'=> 'DATA'], 200);
            }else{
                addActivity('Spuul error in fetching channel', @$user_id, @$data, @$content);
                return response()->json(['status' => 'error', 'data' => 'Exception Occured', 'message'=> 'EXCEPTION_OCCURED'], 200);
            }
            addActivity('Spuul error in fetching channel', @$user_id, @$data, ['code' => $code, 'reason' => $reason]);
        }catch(Exception $e){
            addActivity('Spuul error in fetching channel', @$user_id, @$data, @$e->getMessage());
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function picks(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $data = [];
        $spuul_token = $request->spuul_token;
        try{
            $client = new Client(['headers' => ['Accept' => 'application/vnd.spuul.v3', 'Authorization' => 'Bearer '.$spuul_token]]);
            $result = $client->get('https://api.spuul.com/picks');
            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                foreach($content as $cont){
                    $id = $cont->id;
                    $client = new Client(['headers' => ['Accept' => 'application/vnd.spuul.v3', 'Authorization' => 'Bearer '.$spuul_token]]);
                    $rresult = $client->get('https://api.spuul.com/picks/'.$id.'/videos?per_page=2');
                    $code = $rresult->getStatusCode(); // 200
                    $reason = $rresult->getReasonPhrase(); // OK
                    if($code == "200" && $reason == "OK"){
                        $bbody = $rresult->getBody();
                        $ccontent = json_decode($bbody->getContents());
                        $cont->videos = $ccontent;
                    }
                }
                addActivity('Spuul fetched picks', @$user_id, @$data, @$content);
                return response()->json(['status' => 'success', 'data' => $content, 'message'=> 'DATA'], 200);
            }else{
                addActivity('Spuul error in fetching picks', @$user_id, @$data, @$content);
                return response()->json(['status' => 'error', 'data' => 'Exception Occured', 'message'=> 'EXCEPTION_OCCURED'], 200);
            }
            addActivity('Spuul error in fetching picks', @$user_id, @$data, ['code' => $code, 'reason' => $reason]);
        }catch(Exception $e){
            addActivity('Spuul error in fetching picks', @$user_id, @$data, @$e->getMesssage());
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function pickById(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $data = [];
        $spuul_token = $request->spuul_token;
        $id = $request->input('id');
        try{
            $client = new Client(['headers' => ['Accept' => 'application/vnd.spuul.v3', 'Authorization' => 'Bearer '.$spuul_token]]);
            $result = $client->get('https://api.spuul.com/picks/'.$id.'/videos');
            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                addActivity('Spuul fetched pick by id', @$user_id, @$data, @$content);
                return response()->json(['status' => 'success', 'data' => $content, 'message'=> 'DATA'], 200);
            }else{
                addActivity('Spuul error in fetching pick by id', @$user_id, @$data, @$content);
                return response()->json(['status' => 'error', 'data' => 'Exception Occured', 'message'=> 'EXCEPTION_OCCURED'], 200);
            }
            addActivity('Spuul error in fetching pick by id reason code', @$user_id, @$data, ['code' => $code, 'reason' => $reason]);
        }catch(Exception $e){
            addActivity('Spuul error in fetching pick by id', @$user_id, @$data, @$e->getMessage());
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function getDetail(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $data = [];
        $spuul_token = $request->spuul_token;
        $data['id'] = $id = $request->input('id');
        try{
            $client = new Client(['headers' => ['Accept' => 'application/vnd.spuul.v3', 'Authorization' => 'Bearer '.$spuul_token]]);
            $result = $client->get('https://api.spuul.com/videos/'.$id);
            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                addActivity('Spuul fetched video detail', @$user_id, @$data, @$content);
                return response()->json(['status' => 'success', 'data' => $content, 'message'=> 'DATA'], 200);
            }else{
                addActivity('Spuul error in fetching video detail', @$user_id, @$data, @$content);
                return response()->json(['status' => 'error', 'data' => 'Exception Occured', 'message'=> 'EXCEPTION_OCCURED'], 200);
            }
            addActivity('Spuul error in fetching video detail reason code', @$user_id, @$data, ['code' => $code, 'reason' => $reason]);
        }catch(Exception $e){
            addActivity('Spuul error in fetching video detail', @$user_id, @$data, @$e->getMessage());
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function getCarousels(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $data = [];
        $spuul_token = $request->spuul_token;
        try{
            $client = new Client(['headers' => ['Accept' => 'application/vnd.spuul.v3', 'Authorization' => 'Bearer '.$spuul_token]]);
            $result = $client->get('https://api.spuul.com/carousels/');
            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                if(count($content)){
                    $first = current($content);

                    $client = new Client(['headers' => ['Accept' => 'application/vnd.spuul.v3', 'Authorization' => 'Bearer '.$spuul_token]]);
                    $result = $client->get('https://api.spuul.com/carousels/'.$first->id.'/banners/');
                    $code = $result->getStatusCode(); // 200
                    $reason = $result->getReasonPhrase(); // OK
                    if($code == "200" && $reason == "OK"){
                        $body = $result->getBody();
                        $content = json_decode($body->getContents());
                        addActivity('Spuul fetched carousel', @$user_id, @$data, @$content);
                        return response()->json(['status' => 'success', 'data' => $content, 'message'=> 'DATA'], 200);
                    }
                    addActivity('Spuul error in fetching carousel', @$user_id, @$data, ['code' => $code, 'reason' => $reason]);
                }
                return response()->json(['status' => 'error', 'data' => [], 'message'=> 'DATA'], 200);

            }else{
                addActivity('Spuul error in fetching carousel', @$user_id, @$data, ['code' => $code, 'reason' => $reason]);

                return response()->json(['status' => 'error', 'data' => 'Exception Occured', 'message'=> 'EXCEPTION_OCCURED'], 200);
            }

        }catch(Exception $e){
            addActivity('Spuul error in fetching carousel', @$user_id, @$data, $e->getMessage());
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function search(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $data = [];
        $spuul_token = $request->spuul_token;
        $data['limit'] = $limit = ($request->input('limit') != '')?$request->input('limit'):4;
        $data['type'] = $type = ($request->input('type') != '')?$request->input('type'):'';
        $data['page'] = $page = ($request->input('page') != '')?$request->input('page'):1;
        $data['keyword'] = $keyword = strtolower($request->input('keyword'));
        try{
            $client = new Client(['headers' => ['Accept' => 'application/vnd.spuul.v3', 'Authorization' => 'Bearer '.$spuul_token]]);
            $result = $client->get('https://api.spuul.com/search?keywords='.$keyword.'&page='.$page.'&per_page='.$limit.'&search_for='.$type);
            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                addActivity('Spuul successsful search', @$user_id, @$data, $content);
                return response()->json(['status' => 'success', 'data' => $content, 'message'=> 'DATA'], 200);
            }else{
                addActivity('Spuul error in searching reason code', @$user_id, @$data, ['code' => $code, 'reason' => $reason]);
                return response()->json(['status' => 'error', 'data' => 'Exception Occured', 'message'=> 'EXCEPTION_OCCURED'], 200);
            }

        }catch(Exception $e){
            addActivity('Spuul error in fetching carousel', @$user_id, @$data, $e->getmessage());
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function profile(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $data = [];
        $data['spuul_token'] =$spuul_token = $request->spuul_token;
        try{
            $client = new Client(['headers' => ['Accept' => 'application/vnd.spuul.v3', 'Authorization' => 'Bearer '.$spuul_token]]);
            $result = $client->get('https://api.spuul.com/me');
            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                // if(isset($content->email) && $content->email == 'shona@sginteractive.com.sg'){
                //     $content->email = 'last1@yopmail.com';
                // }
                addActivity('Spuul successsfully fetched profile', @$user_id, @$data, $content);
                return response()->json(['status' => 'success', 'data' => $content, 'message'=> 'DATA'], 200);
            }else{
                addActivity('Spuul error in reason code', @$user_id, @$data, ['code' => $code, 'reason' => $reason]);
                return response()->json(['status' => 'error', 'data' => 'Exception Occured', 'message'=> 'EXCEPTION_OCCURED'], 200);
            }

        }catch (ClientException $e) {
            $res = Psr7\str($e->getResponse());
            // if (strpos($res, 'missing or invalid') !== false) {
            //     echo 'true';
            // }else{
            // return response()->json(['status' => 'error', 'data' => $request->input('hemant'), 'message'=> 'EXCEPTION_OCCURED'], 200);
              addActivity('Spuul error in fetching profile', @$user_id, @$data, Psr7\str($e->getResponse()));
                return response()->json(['status' => 'error', 'data' => Psr7\str($e->getResponse()), 'message'=> 'EXCEPTION_OCCURED'], 200);

            // }
            // echo Psr7\str($e->getRequest());
            // echo Psr7\str($e->getResponse());
        }catch(Exception $e){
            addActivity('Spuul error in fetching profile', @$user_id, @$data, $e->getMessage());
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function planList(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $data = [];
        $data['spuul_token'] = $spuul_token = $request->spuul_token;
        try{
            $plans = SpuulPlan::where('status', '1')->orderBy('list_order', 'asc')->get();
            $data = [];
            foreach($plans as $plan){
                $d = [];
                $d['id'] = $plan->id;
                if($plan->type == 1){
                    $d['name'] = 'Monthly Premium';
                    $d['discount'] = true;
                    $d['value'] = 1.00;
                    $d['value_text'] = 'for 1st MONTH';
                    $d['text'] = '';//'$1 for new users for first month only. After this period our regular monthly subscription price will be applicable.';
                }else{
                    $d['name'] = 'Yearly Premium';
                    $d['discount'] = false;
                }
                $d['price'] = $plan->price;
                $data[] = $d;
            }
            // $data[0]['id'] = 1;
            // $data[0]['name'] = 'Monthly Premium';
            // $data[0]['price'] = '4.99 SGD';
            //
            // $data[1]['id'] = 2;
            // $data[1]['name'] = 'Yearly Premium';
            // $data[1]['price'] = '49.99 SGD';

            return response()->json(['status' => 'success', 'data' => $data, 'message'=> 'DATA'], 200);

            // $client = new Client(['headers' => ['Accept' => 'application/vnd.spuul.v3', 'Authorization' => 'Bearer '.$spuul_token]]);
            // $result = $client->get('https://api.spuul.com/plan_pricings');
            // $code = $result->getStatusCode(); // 200
            // $reason = $result->getReasonPhrase(); // OK
            // if($code == "200" && $reason == "OK"){
            //     $body = $result->getBody();
            //     $content = json_decode($body->getContents());
            //     return response()->json(['status' => 'success', 'data' => $content, 'message'=> 'DATA'], 200);
            // }else{
            //     return response()->json(['status' => 'error', 'data' => 'Exception Occured', 'message'=> 'EXCEPTION_OCCURED'], 200);
            // }

        }catch(Exception $e){
            addActivity('Spuul error in fetching plan list', @$user_id, @$data, $e->getMessage());
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'spuul_token' => 'required',
            'flexm_token' => 'required',
            'plan_id' => 'required',
        ]);
    }

    // public function subscribe(Request $request)
    // {
    //     $spuul_token = $request->spuul_token;
    //     try{
    //         $data = $request->all();
    //         $validator = $this->validator($data);
    //
    //         if ($validator->fails()) {
    //           $errors = $validator->errors();
    //           $message = [];
    //           $msg = '';
    //           foreach($errors->messages() as $key => $error){
    //               $message[$key] = $error[0];
    //               $msg = $error[0];
    //               break;
    //           }
    //           return response()->json(['status' => 'error', 'data' => '', 'message' => $msg], 200);
    //         }
    //
    //         $flexm_token = $request->input('flexm_token');
    //         $plan_id = $request->input('plan_id');
    //         $plan_amount = 49.99;
    //         $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);
    //         $result = $client->get(self::BASE_URL.'user/wallets');
    //
    //         $code = $result->getStatusCode(); // 200
    //         $reason = $result->getReasonPhrase(); // OK
    //         if($code == "200" && $reason == "OK"){
    //             $body = $result->getBody();
    //             $content = json_decode($body->getContents());
    //             if($content->success){
    //                 $msg = $content->message;
    //                 $cont = $content->data;
    //                 $amount = $cont->funds_available_amount;
    //                 if($amount < $plan_amount){
    //                     $msg = "Insufficient funds in your flexm wallet";
    //                     return response()->json(['status' => "error", 'data' => '', 'message'=> $msg], 200);
    //                 }else{
    //                     $dat['mobile'] = '';
    //                     $dat['mobile_country_code'] = 65;
    //                     $dat['amount'] = $plan_amount;
    //                     $dat['message'] = "Spuul subscription charges for Plan #".$plan_id;
    //
    //                     $client = new Client(['headers' => ['Content-type' => 'application/json', 'X-TOKEN' => $flexm_token]]);
    //
    //                     $result = $client->post(self::BASE_URL.'user/wallets/funds/sendmoney/transfers', [
    //                         'form_params' => $dat
    //                     ]);
    //
    //                     $code = $result->getStatusCode(); // 200
    //                     $reason = $result->getReasonPhrase(); // OK
    //                     if($code == "200" && $reason == "OK"){
    //                         $body = $result->getBody();
    //                         $content = json_decode($body->getContents());
    //                         if($content->success){
    //                             $msg = $content->message;
    //                             $cont = $content->data;
    //                             return response()->json(['status' => "success", 'data' => $cont, 'message'=> $msg], 200);
    //                         }else{
    //                             return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
    //                         }
    //                     }
    //                 }
    //             }else{
    //                 return response()->json(['status' => 'error','data' => $content, 'message'=> $content->message], 200);
    //             }
    //         }
    //
    //         // $data[0]['id'] = 1;
    //         // $data[0]['name'] = 'Monthly Premium';
    //         // $data[0]['price'] = '4.99 SGD';
    //         //
    //         // $data[1]['id'] = 2;
    //         // $data[1]['name'] = 'Yearly Premium';
    //         // $data[1]['price'] = '49.99 SGD';
    //
    //     }catch (BadResponseException $ex) {
    //         $response = $ex->getResponse();
    //         $jsonBody = json_decode((string) $response->getBody());
    //         if($jsonBody->extended_code == 419){
    //             return response()->json(['status' => 'error', 'data' => $jsonBody->errors, 'message'=> 'Your session has expired. Please login again on flexm.'], 200);
    //         }
    //         return response()->json(['status' => 'error', 'data' => $jsonBody->errors, 'message'=> $jsonBody->message], 200);
    //     }catch(GuzzleException $e){
    //         return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
    //     }catch(Exception $e){
    //         return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
    //     }
    // }

    public function subscription(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $data = [];
        $spuul_token = $request->spuul_token;
        try{
            $client = new Client(['headers' => ['Accept' => 'application/vnd.spuul.v3', 'Authorization' => 'Bearer '.$spuul_token]]);
            $result = $client->get('https://api.spuul.com/users/1/support_details');
            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());

                return response()->json(['status' => 'success', 'data' => $content, 'message'=> 'DATA'], 200);
            }else{
                return response()->json(['status' => 'error', 'data' => 'Exception Occured', 'message'=> 'EXCEPTION_OCCURED'], 200);
            }

        }catch (ClientException $e) {
            $res = Psr7\str($e->getResponse());
            // if (strpos($res, 'missing or invalid') !== false) {
            //     echo 'true';
            // }else{
            // return response()->json(['status' => 'error', 'data' => $request->input('hemant'), 'message'=> 'EXCEPTION_OCCURED'], 200);
                return response()->json(['status' => 'error', 'data' => Psr7\str($e->getResponse()), 'message'=> 'EXCEPTION_OCCURED'], 200);

            // }
            // echo Psr7\str($e->getRequest());
            // echo Psr7\str($e->getResponse());
        }catch(Exception $e){
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function unsubscribe(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $data = [];
        $spuul_token = $request->spuul_token;
        $id = $request->subscription_id;

        try{
            return response()->json(['status' => 'success', 'data' => [], 'message'=> 'Your subscription has been cancelled. Your current Premium plan will continue until the end of your billing period.'], 200);
            // $client = new Client(['headers' => ['Accept' => 'application/vnd.spuul.v3', 'Authorization' => 'Bearer '.$spuul_token]]);
            // $result = $client->get('https://api.spuul.com/subscriptions/'.$id.'/cancel');
            // $code = $result->getStatusCode(); // 200
            // $reason = $result->getReasonPhrase(); // OK
            // if($code == "200" && $reason == "OK"){
            //     $body = $result->getBody();
            //     $content = json_decode($body->getContents());
            //     return response()->json(['status' => 'success', 'data' => [], 'message'=> 'Your subscription has been cancelled. Your current Premium plan will continue until the end of your billing period.'], 200);
            //
            // }else{
            //     return response()->json(['status' => 'error', 'data' => 'Exception Occured', 'message'=> 'EXCEPTION_OCCURED'], 200);
            // }

        }catch (ClientException $e) {
            $res = Psr7\str($e->getResponse());
            return response()->json(['status' => 'error', 'data' => Psr7\str($e->getResponse()), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function restoreSubscription(Request $request)
    {
        $spuul_token = $request->spuul_token;
        try{
            $client = new Client(['headers' => ['Accept' => 'application/vnd.spuul.v3', 'Authorization' => 'Bearer '.$spuul_token]]);
            $result = $client->get('https://api.spuul.com/me');
            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                if(isset($content->email) && $content->email == 'shona@sginteractive.com.sg'){
                    $content->email = 'last1@yopmail.com';
                }
                return response()->json(['status' => 'success', 'data' => $content, 'message'=> 'DATA'], 200);
            }else{
                return response()->json(['status' => 'error', 'data' => 'Exception Occured', 'message'=> 'EXCEPTION_OCCURED'], 200);
            }

        }catch (ClientException $e) {
            $res = Psr7\str($e->getResponse());
                return response()->json(['status' => 'error', 'data' => Psr7\str($e->getResponse()), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function register(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $data = [];
        $spuul_token = $request->spuul_token;
        $token = $request->input('token');
        $email = $request->input('email');
        $first_name = $request->input('first_name');
        $last_name = $request->input('last_name');
        $phone = $request->input('phone');
        $pass = $request->input('password');
        $gender = 'male';
        if($user->profile && $user->profile->gender){
          $gender = $user->profile->gender;
        }

        $dob = "";
        if($user->profile && $user->profile->dob && $user->profile->dob != '0000-00-00'){
          $dob = $user->profile->dob;
          $dob = Carbon::parse($dob)->format('d-m-Y');
        }

        $name = explode(' ', $user->name);
        $data = $post_data = [
            "first_name"            => $first_name,
            "last_name"             => $last_name,
            "email"                 => $email,
            "email_confirmation"    => $email,
            "password"              => $pass,
            "password_confirmation" => $pass,
            "gender"                => $gender,
            "birth"                 => $dob,
            "country"               => "sg",
            "agree_terms"           => "1",
            "msisdn"                => "",//"6511111111"
        ];

        unset($data['password']);
        unset($data['password_confirmation']);
        \Log::debug($data);
        \Log::debug($post_data);
        try{

            $client = new Client(['headers' => ['Accept' => 'application/vnd.spuul.v3', 'Authorization' => 'Bearer '.$spuul_token]]);
            $result = $client->post('https://api.spuul.com/users/register', array(
                'form_params' => $post_data
            ));
            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "201" && $reason == "Created"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());
                addActivity('Spuul successsfully registered', @$user_id, @$data, $content);
                return response()->json(['status' => 'success', 'data' => $content, 'message'=> 'DATA'], 200);
            }else{
                addActivity('Spuul error in registration reason code', @$user_id, @$data, ['code' => $code, 'reason' => $reason]);
                return response()->json(['status' => 'success', 'data' => $reason, 'message'=> $code], 200);
                return response()->json(['status' => 'error', 'data' => 'Exception Occured', 'message'=> 'EXCEPTION_OCCURED'], 200);
            }

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());

            $msg = '';
            $msg = @$jsonBody->message[0]?@$jsonBody->message[0]:@$jsonBody->error_description;
            addActivity('Spuul error in registration', @$user_id, @$data, $jsonBody);
            return response()->json(['status' => 'error', 'data' => @$msg, 'message'=> @$msg], 200);
        }catch(GuzzleException $e){
            addActivity('Spuul error in registration', @$user_id, @$data, $e->getMessage());
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            addActivity('Spuul error in registration', @$user_id, @$data, $e->getMessage());
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function forgot(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $data = [];
        $token = $request->input('token');
        $email = $request->input('email');
        $user = JWTAuth::toUser($token);

        try{
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
                $spuul_token = $content->access_token;

            }else{
                return response()->json(['status' => 'error', 'data' => 'Exception Occured', 'message'=> 'EXCEPTION_OCCURED'], 200);
            }

            $client = new Client(['headers' => ['Accept' => 'application/vnd.spuul.v3', 'Authorization' => 'Bearer '.$spuul_token]]);
            $result = $client->get('https://api.spuul.com/users/forget_password', array(
                'query' => [
                    'email' => $email
                ]
                // 'debug' => true
            ));
            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());

                return response()->json(['status' => 'success', 'data' => $content, 'message'=> 'DATA'], 200);
            }else{
                return response()->json(['status' => 'error', 'data' => 'Exception Occured', 'message'=> 'EXCEPTION_OCCURED'], 200);
            }

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());

            $msg = '';
            $msg = @$jsonBody->message?@$jsonBody->message:@$jsonBody->error_description;

            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$msg], 200);
        }catch(GuzzleException $e){
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function reset(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $data = [];
        $spuul_token = $request->spuul_token;
        $token = $request->input('token');
        $email = $request->input('email');

        try{

            $client = new Client(['headers' => ['Accept' => 'application/vnd.spuul.v3', 'Authorization' => 'Bearer '.$spuul_token]]);
            $result = $client->put('https://api.spuul.com/users/reset_password ', array(
                'query' => [
                    'email' => $email
                ]
            ));
            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());

                return response()->json(['status' => 'success', 'data' => $content, 'message'=> 'DATA'], 200);
            }else{
                return response()->json(['status' => 'error', 'data' => 'Exception Occured', 'message'=> 'EXCEPTION_OCCURED'], 200);
            }

        }catch (ClientException $e) {
            $res = Psr7\str($e->getResponse());
            // if (strpos($res, 'missing or invalid') !== false) {
            //     echo 'true';
            // }else{
            // return response()->json(['status' => 'error', 'data' => $request->input('hemant'), 'message'=> 'EXCEPTION_OCCURED'], 200);
                return response()->json(['status' => 'error', 'data' => Psr7\str($e->getResponse()), 'message'=> 'EXCEPTION_OCCURED'], 200);

            // }
            // echo Psr7\str($e->getRequest());
            // echo Psr7\str($e->getResponse());
        }catch(Exception $e){
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }


    public function cancelSubscription(Request $request){
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $data = [];
        $spuul_token = $request->spuul_token;
        $data['account_number'] = $d['account_number'] = $request->subscription_id;
        $url = 'https://callbacks.spuul.com/sg_interactive/unsubscribe';
        try{
          $client = new Client(['headers' => ['Accept' => 'application/vnd.spuul.v3', 'Authorization' => 'Bearer '.$spuul_token,
          'Cache-Control' => 'no-cache', 'content-type' => 'multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW']]);

          $result = $client->post('https://callbacks.spuul.com/sg_interactive/unsubscribe', [
            'form_params' => $d
          ]);

          $code = $result->getStatusCode(); // 200
          $reason = $result->getReasonPhrase(); // OK
          if($code == "200" && $reason == "OK"){
            $body = $result->getBody();
            $content = json_decode($body->getContents());
            addActivity('Spuul successfull cancel subscription', @$user_id, @$data, $content, $url);
            return response()->json(['status' => 'success', 'data' => $content, 'message'=> 'DATA'], 200);

          }else{
            addActivity('Spuul cancel subscription reason code', @$user_id, @$data, ['code' => $code, 'reason' => $reason], $url);
            return response()->json(['status' => 'error', 'data' => $code, 'message'=> $reason], 200);

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
            addActivity('Spuul error in cancel subscription', @$user_id, @$data, $jsonBody, $url);
            return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> @$msg], 200);
        }catch(GuzzleException $e){
            addActivity('Spuul error in cancel subscription', @$user_id, @$data, $e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> $e->getMessage()], 200);
        }catch(Exception $e){
            addActivity('Spuul error in cancel subscription', @$user_id, @$data, $e->getMessage(), $url);
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> $e->getMessage()], 200);
        }
    }

    public function subscribe(Request $request){
        $user = JWTAuth::toUser($request->input('token'));
        $user_id = $user->id;
        $data = [];
        $spuul_token = $request->spuul_token;
        $data['account_number'] = $d['account_number'] = $request->account_number;
        $data['email'] = $d['email'] = $request->email;
        $data['sku_code'] = $d['sku_code'] = 'interactive_sg_monthly_sgd';

        try{
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
            addActivity('Spuul successfull subscription', @$user_id, @$data, $content);
            return response()->json(['status' => 'success', 'data' => $content, 'message'=> 'DATA'], 200);

          }else{
            addActivity('Spuul error in subscription', @$user_id, @$data, ['code' => $code, 'reason' => $reason]);
            return response()->json(['status' => 'error', 'data' => $code, 'message'=> $reason], 200);

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
            addActivity('Spuul error in subscription', @$user_id, @$data, $jsonBody);
            return response()->json(['status' => 'success', 'data' => @$jsonBody->errors, 'message'=> $msg], 200);
        }catch(GuzzleException $e){
            addActivity('Spuul error in subscription', @$user_id, @$data, $e->getMessage());
            return response()->json(['status' => 'success', 'data' => $e->getMessage(), 'message'=> $e->getMessage()], 200);
        }catch(Exception $e){
            addActivity('Spuul error in subscription', @$user_id, @$data, $e->getMessage());
            return response()->json(['status' => 'success', 'data' => $e->getMessage(), 'message'=> $e->getMessage()], 200);
        }
    }


}
