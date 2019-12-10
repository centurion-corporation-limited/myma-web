<?php

namespace App\Http\Middleware;

use Closure,  Auth;

class CheckIfBlocked
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

         if ( Auth::check() &&  Auth::user()->hasRole('employee') && Auth::user()->blocked)
         {
           return response()->json(['status' => 'error', 'data' => 'Your account is blocked', 'message' => 'USER_BLOCKED'], 401);
         }
         return $next($request);

     }
}
