<?php

namespace App\Http\Middleware;

use Closure;
use Exception;

class Flexm
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
        $res = $next($request);
        $content = $res->content();
        if($content != ''){
          $content = json_decode($content);
        }

        if(@$content->message == 'Your session has expired. Please login again.'){
          return response()->json(['status' => 'error', 'data' =>'Flexm Token is Expired', 'message' => 'FLEXM_EXPIRED'], 401);
        }else{
            return $res;
        }

    }
}
