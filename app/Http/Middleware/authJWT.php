<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Exception;

class authJWT
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
            $token = $request->input('token');
            $user = JWTAuth::toUser($token);
            // if($user->token != $token){
            //     JWTAuth::invalidate($token);
            //     throw new \Tymon\JWTAuth\Exceptions\TokenExpiredException('Token is Expired');
            // }
            if($user->blocked){
                return response()->json(['status' => 'error', 'data' =>'Your account has been blocked. Contact the administrator.', 'message' => 'BLOCKED'], 401);
            }
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                return response()->json(['status' => 'error', 'data' =>'Token is Expired', 'message' => 'TOKEN_EXPIRED'], 200);
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                return response()->json(['status' => 'error', 'data' =>'Token is Expired', 'message' => 'TOKEN_EXPIRED'], 200);
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenBlacklistedException){
                return response()->json(['status' => 'error', 'data' =>'Token is Expired', 'message' => 'TOKEN_EXPIRED'], 200);
            }else{
                return response()->json(['status' => 'error', 'data' => $e->getMessage() , 'message' => 'EXCEPTION_OCCURED'], 200);
            }
        }
        $res = $next($request);

        return $res;
    }
}
