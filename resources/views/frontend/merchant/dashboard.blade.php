@extends('layouts.merchant')

@section('header')

<header class="header">
  <h2>Home</h2>
  <span class="icon-right"><a href="{{ route('merchant.package.subscribed') }}"><img src="{{ static_file('merchant/images/icon-food.png') }}" alt=""></a>
      <a href="{{ route('merchant.order.history') }}"><img src="{{ static_file('merchant/images/icon-history.png') }}" alt=""></a>
  </span>
</header>
@endsection

@section('content')
<div class="page-content page-bg">
@foreach($orders as $order)
<div class="list-bg">
  <ul>
    <li>
      <label><strong>Batch Id</strong></label>
      <span>: <a href="{{ route('merchant.order.view', $order->id) }}">{{ $order->batch_id }}</a></span>
    </li>
    <li>
      <label>Order Id</label>
      <span>: <a href="{{ route('merchant.order.view', $order->id) }}">#{{ $order->id }}</a></span>
    </li>
    <li>
      <label>Pickup Time</label>
      <span>: @if($morning > 0)11:00 AM @else 07:00 PM @endif</span>
    </li>
  </ul>
  <span class="totel-qty">Total Qty : {{ $order->item_count }}</span>
  @if($order->status_id == 9 || $count == $c_status)
    <a class="btn-status active" href="javascript:;">Ready For Pick up</a>
  @else
    <a class="btn-status" item-id="{{ $order->id }}" data-toggle="modal" data-target="#myModal">Update Status</a>
  @endif
</div>
@endforeach

@foreach($orders_p as $order)
<div class="list-bg">
  <ul>
    <li>
      <label><strong>Batch Id</strong></label>
      <span>: <a href="{{ route('merchant.order.view', $order->id) }}">{{ $order->batch_id }}</a></span>
    </li>
    <li>
      <label>Order Id</label>
      <span>: <a href="{{ route('merchant.order.view', $order->id) }}">#{{ $order->id }}</a></span>
    </li>
    <li>
      <label>Pickup Time</label>
      <span>: @if($morning > 0)11:00 AM @else 07:00 PM @endif</span>
    </li>
  </ul>
  <span class="totel-qty">Total Qty : {{ $order->item_count }}</span>
  @if($order->status_id == 9 || $count == $c_status)
    <a class="btn-status active" href="javascript:;">Ready For Pick up</a>
  @else
    <a class="btn-status" item-id="{{ $order->id }}" data-toggle="modal" data-target="#myModal">Update Status</a>
  @endif
</div>
@endforeach

<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-body order-popup">
        <h2>Change Status to Packed ?</h2>
        <ul>
          <li class="btn btn-default change_status"><a href="#">Yes</a></li>
          <li class="btn btn-default" data-dismiss="modal"><a href="#" >No</a></li>
        </ul>
        <a class="icon-close" data-dismiss="modal" href="#"><img src="{{ static_file('merchant/images/icon-close.png') }}" alt=""></a> </div>
    </div>
  </div>
</div>
</div>
@endsection
@section('scripts')
<script>
$(document).on('click', '.change_status', function(){
    var item_id = $(this).attr('item-id');
    $.ajax({
        data: {item_id:item_id},
        method:'post',
        url: "{{ route('ajax.order.update') }}",
        error: function(xhr){
            console.log("Error");
            console.log(xhr);
            // $('.well.profile').html(xhr.statusText);
        },
        success: function(xhr){
            console.log("Success");
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
});

$('#myModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var item_id = button.attr('item-id');
    $('.change_status').attr('item-id', item_id);
});
</script>
@endsection
