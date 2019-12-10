@extends('layouts.merchant')

@section('header')
<header class="header">
  <h2>Item Details</h2>
  <span class="back-btn"><a href="javascript:;" onclick="window.history.go(-1); return false;">
      <img src="{{ static_file('merchant/images/icon-back-arrow.png') }}" alt=""></a>
  </span>
</header>
@endsection

@section('content')
<div class="page-content page-bg">
@foreach($items as $item)
<div class="list-bg" item-id="{{ $item->id }}" @if($item->restaurant_status_id == 8) data-toggle="modal" data-target="#statusModal" @endif>
  <ul>
    <li>
      <label>Item Name</label>
      <span>: {{ $item->item->name }}</span></li>
    <li>
      <label>Qty</label>
      <span>: {{ $item->quantity }}</span></li>
  </ul>
  <span class="@if($item->restaurant_status_id == 9) de-text @else cn-text @endif">
      {{ $item->restaurant_status->name }}
  </span>
</div>
@endforeach
</div>

  <!-- Modal -->
  <div id="statusModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-body order-popup">
          <h2>Change Status to Packed ?</h2>
          <ul>
            <li class="btn btn-default change_status"><a href="javascript:;" class="">Yes</a></li>
            <li class="btn btn-default" data-dismiss="modal"><a href="javascript:;">No</a></li>
          </ul>
          <a class="icon-close" data-dismiss="modal" href="javascript:;"><img src="{{ static_file('merchant/images/icon-close.png') }}" alt=""></a> </div>
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
        url: "{{ route('ajax.item.update') }}",
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

$('#statusModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var item_id = button.attr('item-id');
    $('.change_status').attr('item-id', item_id);
});
</script>
@endsection
