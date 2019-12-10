@extends('layouts.driver')

@section('styles')
<style>
.list-bg a{
    background-color:#b90e3b !important;
}
</style>
@endsection
@section('header')
<header class="header">
  <h2>New Trips</h2>
  <span class="back-btn"><a href="javascript:;" onclick="window.history.go(-1); return false;">
      <img src="images/icon-back-arrow.png" alt=""></a></span>
</header>
@endsection

@section('content')
<div class="page-content page-bg">
    @if(count($trips))
    @foreach($trips as $trip)
    <div class="list-bg cuisine-detail">
      <div class="cuisine-right">
          <div class="ordertxt-img-right">
            <h2>Trip Id: #{{ $trip->id }}</h2>
            <p> Price: S$ {{ $trip->price }}</p>
            <p> Total Items: 0</p>
          </div>

      </div>
      <div class="pull-right">
          <span class="re-order">
              <a class="btn-carat accept_trip" data-trip_id="{{ $trip->id }}" href="javascript:;">Accept</a>
          </span>
          <span class="re-order-2">
              <a class="btn-carat reject_trip" data-trip_id="{{ $trip->id }}" href="javascript:;">Reject</a>
          </span>
      </div>
    </div>
    @endforeach
    @else
        <h4>No new trip</h4>
    @endif
</div>
@endsection

@section('scripts')
<script>
$(document).on('click', '.accept_trip', function(){
    var obj = $(this);
    var item_id = $(this).data('trip_id');
    $.ajax({
        url:'{{ route("ajax.trip.accept") }}',
        type: "POST",
        data: {id : item_id},
        success: function(data){
             var dd = JSON.parse(data);
             if(dd.success){
                 alert(dd.msg);
                 location.reload();
             }else{
                 alert(dd.msg);
                 location.reload();
             }
        },
        error: function(data){
            alert("Something went wrong.Try again later.");
            console.log(data);
        }
    });
});

$(document).on('click', '.reject_trip', function(){
    var obj = $(this);
    var item_id = $(this).data('trip_id');
    $.ajax({
        url:'{{ route("ajax.trip.reject") }}',
        type: "POST",
        data: {id : item_id},
        success: function(data){
            var dd = JSON.parse(data);
            if(dd.success){
                location.reload();
            }else{
                alert(dd.msg);
                location.reload();
            }
        },
        error: function(data){
            alert("There was an issue while adding product to cart.Try Again");
            console.log(data);
        }
    });
});
</script>
@endsection
