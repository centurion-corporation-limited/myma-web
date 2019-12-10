@extends('layouts.flexm')

@section('styles')
<style>
h2{font-size: 26px ;font-weight: 500;text-align: center;margin: 25px 0;}
.input-container {position: relative;}
.icon { padding: 10px; background: dodgerblue; color: white; min-width: 50px; text-align: center;}
/* Set a style for the submit button */
.btn { background-color: #b90e3b/*#edbc06*/; color: white; padding: 12px 50px;border-radius: 10px; margin-top: 25px;}
.btn:focus{outline: none;}
/*
    border: none; cursor: pointer; width:  150px; opacity: 0.9;
	border-radius : 10px; margin-left:80px;font-size: 1.2em;position:center;
} */
.btn:hover { opacity: 1;}
.input1{background-color: #f0f0f0;border: none;border-color: transparent;box-shadow: 1px 3px 4px #ccc;
    box-sizing: border-box;}
.input_img {  position: absolute;  right: 15px; width: 32px;  height: 32px;  top: 12px;}
.header{
  padding: 1px !important;
}
</style>
@endsection

@section('header')
<header class="header">
  <h2>FlexM Login</h2>
</header>
@endsection
@section('content')

<div class="content-pages">
 <div class="food-forum">
     <form action="{{ route('flexm.login') }}" id="pay_form" method="post">
         {{ csrf_field() }}
         <h2>Login Here</h2>
         <div class="form-topic input-container">
            <input value="{{ $token }}" required type="hidden" name="token">
            <input value="spuul" required type="hidden" name="type">
            <input autocomplete="off" value="{{ old('username') }}" required class="form-control input1" type="text" placeholder="Mobile No" name="mobile">
        	   <img src ="{{ static_file('images/icon2.png') }}" class="img-responsive input_img " >
         </div>
         <div class="form-topic input-container">
            <input class="form-control input1" required type="password" placeholder="Password" name="password">
        	<img src ="{{ static_file('images/icon1.png') }}" class="img-responsive input_img " >
         </div>
         <div class="form-row text-center">
             <button type="submit" class="pay_btn btn">LOG IN <i class="fa fa-spinner fa-spin hide"></i></button>
         </div>
     </form>
</div>
<div style="margin-top: 20px;">
    <div class="col-md-12 text-center">
        <img src ="{{ static_file('images/logo2.png') }}" style="width: 150px;">
    </div>
</div>
</div>
@endsection

@section('scripts')
<script>
$("#pay_form").on('submit', function(){
    $('.pay_btn').addClass('disabled');
    $('.pay_btn').find('i').removeClass('hide');
});
</script>
@endsection
