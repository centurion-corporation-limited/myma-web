<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Session\Store;

class SessionTimeout {

    protected $session;
    protected $timeout = 60;

    public function __construct(Store $session){
        $this->session = $session;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // if(! $this->session->get('lastActivityTime')){
        //     echo "outside";
        //     $this->session->put('lastActivityTime', time());
        // }
        // elseif(time() - $this->session->get('lastActivityTime') > $this->timeout){
        //     echo "inside";
        //     $this->session->forget('lastActivityTime');
        //     // $cookie = cookie('intend', $isLoggedIn ? url()->current() : 'dashboard');
        //     // $email = $request->user()->email;
        //     auth()->logout();
        //     echo 'You had not activity in '.$this->timeout/60 .' minutes ago.';//->withInput(compact('email'))->withCookie($cookie);
        // }
        // $bag = \Session::getMetadataBag();
        // $max = \Config::get('session.lifetime') * 60;
        // if ($bag && $max < (time() - $bag->getLastUsed())) {
        //     dd("expired");
        // }
        if($_SERVER['REMOTE_ADDR'] == '122.173.21.189'){
            // dd(json_decode($request->cookies->get('P8N2LRhxv2A0Q0bB1ktZDqxFKENVlIMQ5W6wqJvX')));
        }
            // dd(\Cookie::get('laravel_session'));
        // $isLoggedIn ? $this->session->put('lastActivityTime', time()) : $this->session->forget('lastActivityTime');
        return $next($request);
    }

}
