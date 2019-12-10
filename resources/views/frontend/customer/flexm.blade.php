@extends('layouts.customer')

@section('styles')
<style>
h2 {
	font-size: 18px;
	font-weight: 500;
	text-align: center;
	margin: 19px 0;
}

.input-container {
    position: relative;
}

.icon {
    padding: 10px;
    background: dodgerblue;
    color: white;
    min-width: 50px;
    text-align: center;
}

/* Set a style for the submit button */
.btn {
	background-color: #5c071d;
	color: #fff;
	padding: 8px 35px;
	border-radius: 2px;
	margin-top: 21px;
	margin-left: 2px;
	margin-right: 2px;
}
.btn.pay_btn {
  background-color: #b90e3b;
}

.btn:focus {
    outline: none;
}
.btn:hover {
	background: #333;
	color: #fff;
}

.input1 {
	background-color: #f0f0f0;
	box-shadow: 1px 3px 4px #ccc;
	box-sizing: border-box;
	height: 40px !important;
	padding: 9px 45px 9px 15px !important;
}

.input_img {
	position: absolute;
	right: 9px;
	width: 24px;
	height: 24px;
	top: 8px;
}

</style>
@endsection

@section('header')
<header class="header">
  <h2>Checkout</h2>
  <span class="back-btn"><a href="javascript:;" onclick="window.history.go(-1); return false;"><img src="{{ static_file('customer/images/icon-back-arrow.png') }}" alt=""></a></span>
</header>
@endsection
@section('content')

<div class="content-pages">
 <div class="food-forum">
     <form action="{{ route('flexm.login') }}" id="pay_form" method="post">
         {{ csrf_field() }}
         <h2>Login Here</h2>
         <div class="form-topic input-container">
            <input autocomplete="off" value="{{ old('username') }}" required class="form-control input1" type="text" placeholder="Mobile No" name="mobile">
        	<img src ="{{ static_file('images/icon2.png') }}" class="img-responsive input_img " >
         </div>
         <div class="form-topic input-container">
            <input class="form-control input1" required type="password" placeholder="Password" name="password">
        	<img src ="{{ static_file('images/icon1.png') }}" class="img-responsive input_img " >
         </div>
         <div class="form-row text-center login-btns">
             <button type="submit" class="pay_btn btn">LOG IN <i class="fa fa-spinner fa-spin hide"></i></button>
             <a href="{{ route('food.customer.cart') }}" class="btn">Cancel </a>
         </div>

     </form>
</div>
<div style="margin-top: 20px;">
    <div class="col-md-12 text-center">
        <img src ="{{ static_file('images/logo2.png') }}" style="width: 170px;">
    </div>
</div>
</div>
@endsection
@section('back-button')
<li><a href="javascript:;" onclick="window.history.go(-1); return false;"><span><img src="{{ static_file('images/icon-arrow-left.png') }}" alt=""></span>Back</a></li>
@endsection
@section('scripts')
<script>
$("#pay_form").on('submit', function(){
    $('.pay_btn').addClass('disabled');
    $('.pay_btn').find('i').removeClass('hide');
});
</script>
@endsection
