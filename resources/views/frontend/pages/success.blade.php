@extends('layouts.flexm')

<header class="header">
  <h2>Spuul Subscription</h2>
</header>

@section('content')

<div class="content-pages-success">

  <div class="payment-successful"> <a href="javascript:;"><img src="{{ static_file('customer/images/icon-seucss.png') }}" alt=""></a>
    <p>Your payment has been successfull for the subscription.</p>
    <p>Payment Ref ID - {{ $ref_no or 0 }}</p>
  </div>
</div>
@endsection
