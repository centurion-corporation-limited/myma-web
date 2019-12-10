<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Schema, Cart, Auth, Activity;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        Validator::extend('olderThan', function($attribute, $value, $parameters)
        {
            $minAge = ( ! empty($parameters)) ? (int) $parameters[0] : 13;
            return (new \DateTime)->diff(new \DateTime($value))->y >= $minAge;
        });
        
        Validator::extend('alpha_spaces', function($attribute, $value)
        {
            return preg_match('/^[\pL\s]+$/u', $value);
        });
        
        Schema::defaultStringLength(191);

      // if(Auth::user()->hasRole('customer')){
          view()->composer('layouts.customer', function($view){
            $view->with('cart_count',
                Cart::count()
            );
          });
          
          view()->composer('layouts.nofollow', function($view){
            $view->with('cart_count',
                Cart::count()
            );
          });

          // $languages = array(
          //     'english' => 'English',
          //     'bengali' => 'Bengali',
          //     'mandarin' => 'Chinese',
          //     'tamil' => 'Tamil',
          //     'thai' => 'Thai'
          // );
          // view()->composer('layouts.admin', function($view){
          //   $view->with('languages',
          //       $languages
          //   );
          // });

      // }

        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
