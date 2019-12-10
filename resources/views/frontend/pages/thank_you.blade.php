@extends('layouts.frontend')

@section('styles')
<style>
.payment-successful {
	text-align:center;
	padding:30% 0;
}
</style>
@endsection
@section('content')

<div class="feedback">

    <div class="content-pages">

      <div class="payment-successful">
          <a href="javascript:;"><img src="{{ static_file('customer/images/icon-seucss.png') }}" alt=""></a>
          <p>Your request has been submitted successfully.</p>
          <p>We will get back to you earliest.</p>
      </div>
    </div>

</div>


@endsection

@section('scripts')
@endsection
