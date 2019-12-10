<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Exception;

class customAuth
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
            if(\Auth::guest()){
                $user = JWTAuth::toUser($request->input('token'));
            }
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                return view('frontend.customer.error');
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                return view('frontend.customer.error');
            }else{
                //\Log::debug(json_encode($e->getMessage()));
                 abort(403, 'Your session has been expired. You need to login.');
                // return view('frontend.customer.error');
                // return response()->json(['status' => 'error', 'data' => $e->getMessage() , 'message' => 'EXCEPTION_OCCURED'], $e->getStatusCode());
            }
        }
        return $next($request);
    }
}
