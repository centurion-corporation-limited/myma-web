<?php

namespace App\Http\Middleware;

use Closure,  Auth;

class CheckIfEmployee
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

         if ( Auth::check() &&  Auth::user()->hasRole('employee') )
         {
           abort(401);
         }
         return $next($request);

     }
}
