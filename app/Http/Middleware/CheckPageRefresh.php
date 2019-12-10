<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Session\Store;
use Carbon\Carbon;

class CheckPageRefresh {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        if($request->user()){
            $user = $request->user();
            $current = Carbon::now();
            $last = Carbon::parse($user->last_logged);
            $diff = $current->diffInMinutes($last);
            if($diff < 60){
                $dd['last_logged'] = $current->toDateTimeString();
                $user->update($dd);
            }

        }
        return $next($request);
    }

}
