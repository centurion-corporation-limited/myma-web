@extends('layouts.customer')

@section('styles')
<style>
.order-popup{ position:relative;}
.order-popup h2{ text-align:center; font-size:18px; color:#767676;}
.order-popup li a{ text-decoration:none; color:#fff;}
.order-popup li {
	width: 48%;
	margin-left: 1.4%;
	color: #fff;
	font-size: 14px;
	margin-top: 25px;
	line-height: 1.2;
}
.qrcode-text-btn {
	margin: 0;
	font-weight: 400;
}
.icon-close{ position:absolute; right:-10px; top:-10px;}
.btn-default {
    background: #b90e3b;
    width: 70%;
    color: #fff;
    padding: 20px 0;
    text-transform: uppercase;
}
.btn-default:hover {
	color: #fcfcfc;
	background-color: #333;
	border-color: #333;
}


.star-text .cancel-on-png, .star-text .cancel-off-png, .star-text .star-on-png, .star-text .star-off-png,
.star-text .star-half-png{
    font-size: 1em !important;
}
.star-text-shown .cancel-on-png, .star-text-shown .cancel-off-png, .star-text-shown .star-on-png,
.star-text-shown .star-off-png, .star-text-shown .star-half-png{
    font-size: 1em !important;
}
.star-text{
    cursor: pointer;
    height: 60px;
    text-align: center;
    margin-top: 46px;
}
.qrcode-text-btn > input[type=file] {
  position: absolute;
  overflow: hidden;
  width: 1px;
  height: 1px;
  opacity: 0;
}

.cuisine-left {
	width: 60px;
}
.cuisine-right {
	float: left;
	width: calc(100% - 60px);
}
.cuisine-left img {
	border-radius: 100%;
}
@media only screen and (max-width: 360px){
.order-popup li {
	width: 47% !important;
	margin-top: 16px !important;
}
.order-popup h2 {
	font-size: 15px !important;
}
}

</style>
<link rel="stylesheet" href="{{ static_file('js/plugins/raty/lib/jquery.raty.css') }}">

@endsection
@section('header')
<header class="header">
  <h2>My Order</h2>
  <span class="back-btn"><a href="javascript:;" onclick="window.history.go(-1); return false;"><img src="{{ static_file('customer/images/icon-back-arrow.png') }}" alt=""></a></span>
</header>
@endsection
@section('content')

<div class="content-pages page-content">
@if(count($orders) || count($orders_p) || count($orders_c))
@foreach($orders_c as $order)
<div class="cuisine-detail">
	<div class="cuisine-left"> <a href="{{ route('food.customer.order.detail', $order->id) }}"><img src="{{ static_file('images/naan.png') }}" alt=""></a> </div>
	<div class="cuisine-right">
		<div class="full-row">
			<div class="left-box">
				<a href="{{ route('food.customer.order.detail', $order->id) }}">
					<h2> Order Id: #{{ $order->id }}</h2>
					<p><b>Price:</b> S${{ $order->total or '0' }}</p>
					<p><b>Dated:</b> {{ $order->created_at->format('d/m/Y') }}</p>
				</a>
			</div>
			<div class="right-box">
				@if($order->status_id > 10)
				<span class="re-order" style="margin-bottom:10px;">
					 <a class="btn-carat re_order" data-order_id="{{ $order->id }}" href="javascript:;">Re-order</a>
				</span>
				@endif

				@if($order->status_id == 11 || $order->status_id == 13)
				<span class="re-order">
					 <a class="btn-carat" href="{{ route('food.customer.order.invoice', $order->id) }}">&nbsp;Invoice&nbsp;&nbsp;</a>
				</span>
				@endif
			</div>
		</div>
		<div class="full-row">
			<div class="left-box">
				<p class="cancel-text">
						@if($order->status_id == 10)
								Order Picked Up
						@else
								{{ $order->status->name }}
						@endif
				</p>
			</div>
			<div class="right-box">

				@if($order->status_id == 11)
				<span class="confirm-order">
					 <a class="confirm_order" data-toggle="modal" data-target="#receivedModal" data-order_id="{{ $order->id }}" href="javascript:;">Order Received?</a>
				</span>
				@endif
				@if($order->rating)
				<span class="confirm-order">
					 <div class="star-text-shown" data-score="{{ $order->rating }}"></div>
				</span>
				@endif
			</div>
		</div>
	</div>
</div>
@endforeach

@foreach($orders_p as $order)
<div class="cuisine-detail">
	<div class="cuisine-left">
		<a href="{{ route('food.customer.order.detail', $order->id) }}"><img src="{{ static_file('images/naan.png') }}" alt=""></a>
	</div>
	<div class="cuisine-right">
		<div class="full-row">
			<div class="left-box">
				<a href="{{ route('food.customer.order.detail', $order->id) }}">
					<h2> Order Id: #{{ $order->id }}</h2>
					<p><b>Price:</b> S${{ $order->total or '0' }}</p>
					<p><b>Dated:</b> {{ $order->created_at->format('d/m/Y') }}</p>
				</a>
			</div>
			<div class="right-box">
				@if($order->status_id > 10)
				<span class="re-order" style="margin-bottom:10px;">
					 <a class="btn-carat re_order" data-order_id="{{ $order->id }}" href="javascript:;">Re-order</a>
				</span>
				@endif

				@if($order->status_id == 11 || $order->status_id == 13)
				<span class="re-order">
					 <a class="btn-carat" href="{{ route('food.customer.order.invoice', $order->id) }}">&nbsp;Invoice&nbsp;&nbsp;</a>
				</span>
				@endif
			</div>
		</div>
		<div class="full-row">
			<div class="left-box">
				<p class="cancel-text">
						@if($order->status_id == 10)
								Order Picked Up
						@else
								{{ $order->status->name }}
						@endif
				</p>
			</div>
			<div class="right-box">

				@if($order->status_id == 11)
				<span class="confirm-order">
					 <a class="confirm_order" data-toggle="modal" data-target="#receivedModal" data-order_id="{{ $order->id }}" href="javascript:;">Order Received?</a>
				</span>
				@endif
				@if($order->rating)
				<span class="confirm-order">
					 <div class="star-text-shown" data-score="{{ $order->rating }}"></div>
				</span>
				@endif
			</div>
		</div>
	</div>
</div>
@endforeach

  @foreach($orders as $order)
  <div class="cuisine-detail">
    <div class="cuisine-left"> <a href="{{ route('food.customer.order.detail', $order->id) }}"><img src="{{ static_file('images/naan.png') }}" alt=""></a> </div>
    <div class="cuisine-right">
			<div class="full-row">
				<div class="left-box">
					<a href="{{ route('food.customer.order.detail', $order->id) }}">
			      <h2> Order Id: #{{ $order->id }}</h2>
			      <p><b>Price:</b> S${{ $order->total or '0' }}</p>
			      <p><b>Dated:</b> {{ $order->created_at->format('d/m/Y') }}</p>
					</a>
			  </div>
				<div class="right-box">
					@if($order->status_id > 10)
	        <span class="re-order" style="margin-bottom:10px;">
	           <a class="btn-carat re_order" data-order_id="{{ $order->id }}" href="javascript:;">Re-order</a>
	        </span>
	        @endif

					@if($order->status_id == 11 || $order->status_id == 13)
	        <span class="re-order">
	           <a class="btn-carat" href="{{ route('food.customer.order.invoice', $order->id) }}">&nbsp;Invoice&nbsp;&nbsp;</a>
	        </span>
	        @endif
				</div>
			</div>
			<div class="full-row">
				<div class="left-box">
					<p class="cancel-text">
	            @if($order->status_id == 10)
	                Order Picked Up
	            @else
	                {{ $order->status->name }}
	            @endif
	        </p>
				</div>
				<div class="right-box">

	        @if($order->status_id == 11)
	        <span class="confirm-order">
	           <a class="confirm_order" data-toggle="modal" data-target="#receivedModal" data-order_id="{{ $order->id }}" href="javascript:;">Order Received?</a>
	        </span>
	        @endif
	        @if($order->rating)
	        <span class="confirm-order">
	           <div class="star-text-shown" data-score="{{ $order->rating }}"></div>
	        </span>
	        @endif
				</div>
			</div>
    </div>
  </div>
  @endforeach

 @else
 <div class="list-bg">
     <ul>
         <li>
    <h4>Have not ordered yet, Go to <a href="{{ route('food.customer.food_list') }}">Menu</a> and start ordering.</h4>
</li>
</ul>
</div>
 @endif

  <!-- <div id="receivedModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-body order-popup">
            <div class="received_sec">
              <h2>Order has been received ?</h2>

              <ul>
                <li class="btn btn-default change_status"><a href="#">Yes</a></li>
                <li class="btn btn-default" data-dismiss="modal"><a href="#" >No</a></li>
              </ul>
            </div>
            <div class="star_sec hide">
              <h2>what would you rate this order?</h2>
              <div class="star-text"></div>
            </div>
            <a class="icon-close" data-dismiss="modal" href="#"><img src="{{ static_file('merchant/images/icon-close.png') }}" alt=""></a>
        </div>
      </div>
    </div>
  </div> -->
</div>
<div id="receivedModal" class="modal fade" role="dialog">

  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Qrcode</h4>
          <a class="icon-close" data-dismiss="modal" href="#"><i class="fa fa-times"></i></a>
      </div>
      <div class="modal-body order-popup">
          <div class="received_sec">
            <h2>You need to scan the Qrcode !</h2>

            <ul>
              <li class="btn btn-default change_status">
                  <a href="javascript:;">
                      <label class=qrcode-text-btn>Scan
                        <input type=file
                               accept="image/*"
                               capture="environment"
                               onchange="openQRCamera(this);"
                               tabindex=-1>

                      </label>
                  </a>
              </li>
              <li class="btn btn-default" data-dismiss="modal"><a href="#" >No</a></li>
            </ul>
          </div>
          <div class="star_sec hide">
            <h2>what would you rate this order?</h2>
            <div class="star-text"></div>
          </div>

      </div>
    </div>
  </div>
</div>
@endsection
@section('back-button')
<li><a href="javascript:;" onclick="window.history.go(-1); return false;"><span><img src="{{ static_file('images/icon-arrow-left.png') }}" alt=""></span>Back</a></li>
@endsection
@section('scripts')
<script src="{{ static_file('js/plugins/raty/lib/jquery.raty.js') }}"></script>
<script src="{{ static_file('customer/js/qr_packed.js') }}">
</script>
<script>
function openQRCamera(node) {
  var reader = new FileReader();
  reader.onload = function() {
    node.value = "";
    qrcode.callback = function(res) {
      if(res instanceof Error) {
        alert("No QR code found. Please make sure the QR code is within the camera's frame and try again.");
      } else {
        // node.parentNode.previousElementSibling.value = res;
        alert("Thank you for ordering with us, please rate the order on next screen !!")
        changeStatus(res);
      }
    };
    qrcode.decode(reader.result);
  };
  reader.readAsDataURL(node.files[0]);
}

$('.star-text-shown').raty({
    score: function() {
      return $(this).attr('data-score');
    },
    readOnly: true,
    starType: 'i',
    number: 6
});
$('.star-text').raty({ starType: 'i',number: 6 , click: updateRating});

function updateRating(score, evt){
    var item_id = $('.change_status').attr('item-id');
    $.ajax({
        data: {item_id:item_id, score: score},
        method:'post',
        url: "{{ route('ajax.order.rate') }}",
        error: function(xhr){
            console.log("Error");
            console.log(xhr);
            // $('.well.profile').html(xhr.statusText);
        },
        success: function(xhr){
            var data = JSON.parse(xhr);
            console.log(data);
            if(data.status){
                location.reload();
            }else{
                alert("Something went wrong. Try Again");
            }
            // $('.well.profile').html(html);
        }
    });
}

$('#receivedModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var item_id = button.data('order_id');
    $('.change_status').attr('item-id', item_id);
});

// $(document).on('click', '.change_status', function(){
function changeStatus(item_id){
    // var item_id = $(this).attr('item-id');
    $.ajax({
        data: {item_id:item_id, status_id: 13},
        method:'post',
        url: "{{ route('ajax.order.update') }}",
        error: function(xhr){
            console.log("Error");
            console.log(xhr);
            // $('.well.profile').html(xhr.statusText);
        },
        success: function(xhr){
            var data = JSON.parse(xhr);
            console.log(data);
            if(data.status){
                $('.received_sec').addClass('hide');
                $('.star_sec').removeClass('hide');
                // location.reload();
            }else{
                alert("Something went wrong. Try Again");
            }
            // $('.well.profile').html(html);
        }
    });
}
// );

$(document).on('click', '.re_order', function(){
    var order_id = $(this).data('order_id');
    $.ajax({
        url:'{{ route("ajax.order.again") }}',
        type: "POST",
        data: {id : order_id},
        success: function(data){
             // obj.closest('.cuisine-detail').remove();
             var res = JSON.parse(data);
             alert(res.msg);
             console.log(res);
        },
        error: function(data){
            alert("There was an issue while adding product to cart.Try Again");
            console.log(data);
        }
    });
});

</script>
@endsection
