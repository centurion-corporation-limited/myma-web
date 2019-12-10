<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;

class authSpuul
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            // $request->hemant = "kiwen";
            $spuul_token = $request->input('spuul_token');
            if($spuul_token == ''){
                throw new Exception("custom");
            }else{
                $client = new Client(['headers' => ['Accept' => 'application/vnd.spuul.v3', 'Authorization' => 'Bearer '.$spuul_token]]);
                $result = $client->get('https://api.spuul.com/me');
                $code = $result->getStatusCode(); // 200
                $reason = $result->getReasonPhrase(); // OK
                if($code == "200" && $reason == "OK"){
                    // $body = $result->getBody();
                    // $content = json_decode($body->getContents());
                }
            }


        } catch (Exception $e) {
            if ($e instanceof ClientException){
                $res = Psr7\str($e->getResponse());
                if (strpos($res, 'missing or invalid') !== false) {
                    $this->getToken($request);
                }else{
                    return response()->json(['status' => 'error', 'data' => 'Token is Invalid', 'message' => 'INVALID_TOKEN'], 200);
                }
            }elseif($e->getMessage() == 'custom'){
                $this->getToken($request);
            }
            else{
                return response()->json(['status' => 'error', 'data' => $e->getMessage() , 'message' => 'EXCEPTION_OCCURED'], 200);
            }
        }
        return $next($request);
    }

    private function getToken($request){
        $refresh_token = $request->input('refresh_token');
        $client = new Client();
        if($refresh_token != ''){
            $result = $client->post('https://api.spuul.com/oauth/token',[
                'form_params' => [
                    'grant_type' => 'refresh_token',
                    // "username"   => "shona@sginteractive.com.sg",
                    // "password"   => "ab123456",
                    'client_id'  => '1e9240872cfda7989634346aa89cd3bf05d6a0462756f58d79e445788cca8d66',
                    'client_secret' => '6bdfaae72a9a41b551958c3ff0e1057a9ef25632f2f562049cba8b384dce41e3',
                    "refresh_token"  => $refresh_token
                ]
            ]);
        }else{
            $result = $client->post('https://api.spuul.com/oauth/token',[
                'form_params' => [
                    'grant_type' => 'client_credentials',
                    'client_id'  => '1e9240872cfda7989634346aa89cd3bf05d6a0462756f58d79e445788cca8d66',
                    'client_secret' => '6bdfaae72a9a41b551958c3ff0e1057a9ef25632f2f562049cba8b384dce41e3'
                ]
            ]);
        }
        $code = $result->getStatusCode(); // 200
        $reason = $result->getReasonPhrase(); // OK
        if($code == "200" && $reason == "OK"){
            $body = $result->getBody();
            $content = json_decode($body->getContents());
            $request->spuul_token = $content->access_token;
            return true;
        }else{
            return false;
        }
    }
}
