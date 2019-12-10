@extends('layouts.flexm')

@section('styles')
<style>
body {
margin: 0;
font-family: 'Roboto', sans-serif;
}
.flxM-bal {
float: left;
width: 100%;
padding: 15px;
box-sizing: border-box;
}
.title-box .title {
float: left;
}
.title-box .content {
float: right;
margin-right: 6px;
}
form {
	display: inline-block;
	width: 100%;
}
.form-content {
float: left;
width: 100%;
}

.form-content .form-label {
	float: left;
	flex: 50% 0;
	padding-right: 5px;
}
.form-content .form-cont {
	float: right;
	margin-right: 0;
	font-weight: 700;
	flex: 0 50%;
	text-align: right;
	padding-left: 5px;
}
.row.border-row {
border-bottom: 2px solid #dcdcdc;
}
.mb-40 {
margin-bottom: 40px;
}
.form-content .form-cont span {
margin-right: 10px;
}
.btn-wrapper {
float: left;
width: 100%;
margin: 30px auto 0;
padding: 0;
text-align: center;
}
.btn-wrapper button:hover {
background: #333;
color: #fff;
}
.title-box {
	float: left;
	width: 100%;
	margin: 0 auto;
	margin-bottom: 0px;
	padding: 15px 7px;
	font-size: calc(14px + 1vw);
	font-weight: 700;
	box-sizing: border-box;
	letter-spacing: 0px;
}
.form-content .row {
	float: left;
	width: 100%;
	padding: 15px 9px;
	box-sizing: border-box;
	font-size: calc(13px + 0.6vw);
	margin: 0 auto;
	display: flex;
	justify-content: space-between;
}
.btn-wrapper button {
background: #b90e3b;
color: #fff;
border: none;
padding: 15px 20px;
border-radius: 4px;
font-size: 3.5vw;
cursor: pointer;
}
@media only screen and (max-width: 380px){
  .form-content .row {
  	padding: 12px 9px;
  	font-size: calc(12px + 0.5vw);
  }
  .title-box {
  	font-size: calc(12px + 1vw);
  }
}
</style>
@endsection

@section('content')

<header class="header">
  <h2>FlexM</h2>
</header>

  <form id="pay_form" method="post" action="{{ route('frontend.spuul.checkout') }}">
    {{ csrf_field() }}
    <div class="flxM-bal">
        <div class="title-box mb-40">
            <div class="title">FlexM Wallet Balance</div>
            <div class="content">S$ {{ $wallet }}</div>
        </div>
        <div class="form-content">
            <div class="row border-row">
                <div class="pull-left form-label">Item</div>
                <div class="pull-right form-cont">{{ $description }}</div>
            </div>
            <div class="row">
                <div class="pull-left form-label">Price</div>
                <div class="pull-right form-cont">S$ {{ $plan->price }}</div>
            </div>
        </div>

        <div class="form-content">
            <div class="row border-row">
                <div class="pull-left form-label">Transaction Charges</div>
                <div class="pull-right form-cont">S$ {{ $share_amount }}</div>
            </div>
            <div class="row text-right">
                <div class="pull-left form-label"></div>
                <div class="pull-right form-cont"><span>Total</span> S$ {{ $plan->price+$share_amount }}</div>
            </div>
        </div>
        <div class="btn-wrapper">
            <button type="submit" class="pay_btn">Make Payment <i class="fa fa-spinner fa-spin hide"></i></button>
        </div>
    </div>
  </form>

@endsection
@section('scripts')
<script>
$("#pay_form").on('submit', function(){
    $('button[type=submit], input[type=submit]').prop('disabled',true);
    $('.pay_btn').find('i').removeClass('hide');
});
</script>
@endsection
