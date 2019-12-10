
@extends('layouts.customer')

@section('styles')
<style>
.page-content {
	height: calc(100% - 94px);
}

</style>
@endsection
@section('header')
<header class="header">
  <h2>Cart</h2>
</header>
@endsection
@section('content')
<div class="content-pages page-content">
  @if(count($cart))
    @foreach($cart as $item)
		<div class="cuisine-detail cuisine-detaill" data-id="{{ $item->rowId }}">
        <div class="cuisine-left"> <a href="#"><img src="{{ static_file($item->image) }}" alt=""></a> </div>
        <div class="cuisine-right">
            <h2><a href="#">{{ $item->name }}</a></h2>
            @if(isset($item->options['discount_type']) && $item->options['discount_type'] != "")
						<p><del>Price: S$ {{ $item->price }}</del></p>
						<?php
							$type = $item->options['discount_type'];
							if($type == 'direct'){
								$dis_value = $item->options['value'];
							}else{
								$dis_value = $item->price*$item->options['value']/100;
							}
							$val = $item->price-$dis_val;
						?>
						<p>Discounted Price: S$ {{ $val }}</p>
						@else
						<p>Price: S$ {{ $item->price }}</p>
						@endif
            <!-- <p>Cook : 15 mins</p> -->
            <ul>
                <li class="dec_item fillter-list"><a href="javascript:;"><img src="{{ static_file('customer/images/icon-min.png') }}" alt=""></a></li>
                <li class="count fillter-list">{{ $item->qty }}</li>
                <li class="inc_item fillter-list"><a href="javascript:;"><img src="{{ static_file('customer/images/icon-max.png') }}" alt=""></a></li>
            </ul>
            <span class="icon-delete remove_item"><a href="javascript:;"><img src="{{ static_file('customer/images/icon-delete.png') }}" alt=""></a></span>
        </div>
    </div>
    @endforeach
	@if(!$discount)
    <a href="{{ route('food.customer.discount') }}">
	<div class="apl-cup">
        	<div class="dis-img">
            	<img src="{{ static_file('customer/images/badge.png')}}">
            </div>
            <div class="text">Apply Coupon
				<!-- <span>This is my coupon ha ha ha</span> -->
			</div>
            <div class="cross">
				<img src="{{ static_file('customer/images/angleR.png') }}">
			</div>
    </div>
	</a>
	@else
	<div class="apl-cup">
        	<div class="dis-img">
            	<img src="{{ static_file('customer/images/badge.png')}}">
            </div>
            <div class="text">Coupon applied
				<!-- <span>This is my coupon ha ha ha</span> -->
			</div>
            <div class="cross delete_coupon">
				<img src="{{ static_file('customer/images/cross.png') }}">
			</div>
    </div>
	@endif
    <!-- <div class="cuisine-detail">
        <a href="{{ route('food.customer.discount') }}"><h2>APPLY DISCOUNT <span class="pull-right">></span></h2></a>
    </div> -->
  @else
    <h4>Cart is empty</h4>
  @endif
 <div class="total-pay">
     @if(count($cart))
 <ul>
     <li>
      <label>Sub Total:</label>
     <span class="total">S${{ number_format($total,2) }} </span>

     </li>
     <li>
      <label>Discount:</label>
     <span class="discount">S${{ number_format($dis_val, 2) }} </span>

     </li>
 <!-- <li>
  <label>Naanstap Charge:</label>
 <span class="charge">S$0 </span>

 </li> -->

 <li>
  <label>Total:</label>
 <span class="total">S${{ number_format(($total-$dis_val),2) }} </span>

 </li>
 </ul>
 @endif
 </div>
</div>
<div class="checkout">
	<ul>
		<li class="current"><a href="{{ route('food.customer.home') }}">Continue Shopping</a></li>
		<li><a @if($total == 0) href="javascript:;" @else href="{{ route('food.customer.checkout') }}" @endif>Checkout</a></li>
	</ul>
</div>

@endsection
@section('back-button')
<li><a href="javascript:;" onclick="window.history.go(-1); return false;"><span><img src="{{ static_file('images/icon-arrow-left.png') }}" alt=""></span>Back</a></li>
@endsection
@section('scripts')
<script>
$(document).on('click', '.delete_coupon', function(){
    $.ajax({
        url:'{{ route("ajax.remove.coupon") }}',
        type: "post",
		data: {'_token': '{{ csrf_token() }}' },
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

$(document).on('click', '.inc_item', function(){
    var count = parseInt($(this).prev().text());

    count = count+1;
    var item_id = $(this).closest('.cuisine-detaill').data('id');
    $.ajax({
        url:'{{ route("ajax.update.cart") }}',
        type: "post",
        data: {id : item_id, qty: count},
        success: function(data){
             // obj.closest('.cuisine-detaill').remove();
             // alert("Product removed from cart");
             var res = JSON.parse(data);
             console.log(res);
             $('.total').text('S$ '+res.total);
        },
        error: function(data){
            alert("There was an issue while adding product to cart.Try Again");
            console.log(data);
        }
    });
    $(this).prev().text(count);

});
$(document).on('click', '.dec_item', function(){
    var count = parseInt($(this).next().text());
    if(count > 1){
        var item_id = $(this).closest('.cuisine-detaill').data('id');
        count = count-1;
        $.ajax({
            url:'{{ route("ajax.update.cart") }}',
            type: "POST",
            data: {id : item_id, qty: count},
            success: function(data){
                 // obj.closest('.cuisine-detaill').remove();
                 // alert("Product removed from cart");
                 var res = JSON.parse(data);
                 console.log(res);
                 $('.total').text('S$ '+res.total);
            },
            error: function(data){
                alert("There was an issue while adding product to cart.Try Again");
                console.log(data);
            }
        });

        $(this).next().text(count);
    }
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
