@extends('layouts.customer')

@section('styles')
<style>
body {margin: 0;}
.coupon {float: left;width: 100%;margin: 0 auto;padding: 0;background: #f3f6f9;}
.enter-coupon {float: left;width: 100%;margin: 0 auto;padding: 20px;box-sizing: border-box;position: relative;}
.enter-coupon form {float: left;width: 100%;position: relative;margin: 0 auto;padding: 0;}
.enter-coupon input {float: left;width: 100%;border: none;box-shadow: 0 0 1px #ccc;padding: 15px 90px 15px 15px;
	box-sizing: border-box;}
.enter-coupon button {position: absolute;right: 10px;left: auto;border: none;
	background: transparent;color: #d10101;	text-transform: uppercase;font-weight: 600;
	font-size: 14px;top: 0;letter-spacing: 0.5px;height: 100%;text-align: center;
	width: 90px;box-sizing: border-box;cursor: pointer;
}
.enter-coupon button:focus{outline: none;}
.avl-cup {float: left;width: 100%;background: #fff;	padding: 0;}
.avl-cup > h2 {
	float: left;
	width: 100%;
	font-size: 16px;
	font-family: roboto;
	padding: 20px 20px 0;
	margin: 0;
	font-weight: 400;
	letter-spacing: 0.5px;
	text-transform: capitalize;
	color: #666;
	box-sizing: border-box;
}
.card-box {
	float: left;
	width: 100%;
	margin: 0 auto;
	padding: 20px;
	border-bottom: 1px solid #dcdcdc;
	box-sizing: border-box;
}
.card-row {
	float: left;
	width: 100%;
	margin: 0 auto;
	padding: 0;
}
.left {
	float: left;
	width: auto;
}
.right {
	float: right;
	width: auto;
}
.code {
	max-width: 200px;
	white-space: nowrap;
	overflow: hidden;
	font-weight: 600;
	font-family: Roboto;
	color: #333;
	font-size: 17px;
	background: #ffd800;
	padding: 5px 15px;
	border-radius: 5px;
}
.code-btn {
	border: none;
	background: transparent;
	color: #d10101;
	text-transform: uppercase;
	font-weight: 600;
	font-size: 14px;
	letter-spacing: 0.5px;
	/* height: 100%; */
	text-align: center;
	width: 90px;
	box-sizing: border-box;
	cursor: pointer;
	padding: 3px 0;
}
.cou-title {
	float: left;
	width: 100%;
	margin: 10px auto 0;
}
.cou-title h2 {
	font-family: Roboto;
	font-size: 14px;
	color: #444;
	font-weight: 400;
	margin: 0;
}
.cou-title p {
	font-size: 14px;
	font-family: Roboto;
	color: #999;
	margin: 5px 0 5px;
	line-height: 1.3;
}

.apl-cup {
	float: left;
	width: 92%;
	margin: 15px 4%;
	padding: 15px;
	box-sizing: border-box;
	border: 1px dashed #ccc;
	display: flex;
	align-items: center;
}
.dis-img {
	float: left;
	width: auto;
	padding: 5px;
	box-sizing: border-box;
	height: 35px;
}
.dis-img img {
	max-height: 25px;
}
.apl-cup .text {
	float: left;
	width: auto;
	padding: 5px;
}
.apl-cup .text {
	float: left;
	width: auto;
	padding: 5px;
	font-family: Roboto;
	font-size: 15px;
	color: #333;
}
.apl-cup .text span {
	float: left;
	width: 100%;
	color: #999;
	font-size: 0.8em;
}
.cross {float: right;width: auto;padding: 5px;box-sizing: border-box;height: 30px;cursor: pointer;}
.cross img {height: 20px;opacity: 0.8;}
.coupon-wrap {float: left;	width: 100%;	display: flex;}

#cancel_btn {
	margin: 20px 15px 20px -20px;
	background: #d10101;
	border: none;
	font-size: 13px;
	color: #fff;
	line-height: 47px;
	padding: 0 10px;
	text-decoration: none;
}
</style>
@endsection
@section('header')
<header class="header">
  <h2>Apply Discount</h2>
</header>
@endsection
@section('content')

<div class="content-pages page-content page-cart">
<div class="coupon">
	<div class="coupon-wrap">
			<div class="enter-coupon">
	        	<input name="code" value="" placeholder="Enter coupon code" type="text">
	          <button id="apply_code" type="button">apply</button>
	    </div>
	    <a href="{{ route('food.customer.cart') }}" id="cancel_btn" type="button">CANCEL</a>
	</div>

	  <div class="avl-cup">
    	<h2>Coupons</h2>
		@if(count($coupons))
			@foreach($coupons as $coup)
	        <div class="card-box">
	        	<div class="card-row">
	            	<div class="left">
	                	<span class="code">{{ $coup->code }}</span>
	                </div>
	                <div class="right">
	                	<button type="button" data-id="{{ $coup->code }}" class="code-btn apply_code">Apply</button>
	                </div>
	            </div>
	            <div class="card-row">
	            	<div class="cou-title">
	                	<h2>For @if($coup->restra_type == 'single') A La Carte @else Package @endif</h2>
	                  <p>Discount Value @if($coup->type == 'direct') S${{ $coup->value }} @else {{ $coup->value }}% @endif</p>
	                </div>
	            </div>
	        </div>
			@endforeach
		@else
		<div class="card-box">
			No coupon available.
		</div>
		@endif
    </div>
</div>

</div>

@endsection
@section('back-button')
<li><a href="javascript:;" onclick="window.history.go(-1); return false;"><span><img src="{{ static_file('images/icon-arrow-left.png') }}" alt=""></span>Back</a></li>
@endsection
@section('scripts')
<script>
$(document).on('click', '#apply_code', function(){
    var code = $('[name=code]').val();
	if(code != ''){
		$.ajax({
			url:'{{ route("ajax.apply.coupon") }}',
			type: "post",
			data: {code : code,'_token': '{{ csrf_token() }}'},
			success: function(data){
				var res = JSON.parse(data);
				console.log(res);
				if(res.error){
					alert(res.msg);
				}else{
					window.location = '{{ route("food.customer.cart") }}';
				}
			},
			error: function(data){
				alert("There was an issue while adding product to cart.Try Again");
				console.log(data);
			}
		});

	}else{
		alert("Coupon code is required");
	}

});

$(document).on('click', '.apply_code', function(){
    var code = $(this).data('id');

    $.ajax({
        url:'{{ route("ajax.apply.coupon") }}',
        type: "post",
		data: {code : code,'_token': '{{ csrf_token() }}' },
        success: function(data){
             var res = JSON.parse(data);
             console.log(res);
			 if(res.error){
				 alert(res.msg);
			 }else{
				 window.location = '{{ route("food.customer.cart") }}';
			 }
             // $('.total').text('S$ '+res.total);
        },
        error: function(data){
            alert("There was an issue while adding product to cart.Try Again");
            console.log(data);
        }
    });

});

$(document).on('click', '.remove_item', function(){
    var obj = $(this);
    var item_id = $(this).closest('.cuisine-detaill').data('id');
    $.ajax({
        url:'{{ route("ajax.remove.cart") }}',
        type: "POST",
        data: {id : item_id},
        success: function(data){
             obj.closest('.cuisine-detaill').remove();
             alert("Product removed from cart");
             location. reload(true);
             var res = JSON.parse(data);
             console.log(res);
             console.log(res.count);
             $('.total').text('S$ '+res.total);
        },
        error: function(data){
            alert("There was an issue while adding product to cart.Try Again");
            console.log(data);
        }
    });
});
</script>
@endsection
