<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
      'check',
      'login',
      'ajax/*',
      'ajax/payout/verify',
      'logout',
      'trans_browser',
      'enets',
      'customer/ajax/*',
      'merchant/ajax/*',
      'driver/ajax/*',
      'admin/food/menu/recommend'
        //
    ];
}
