@extends('layouts.driver')

@section('styles')
<style>
.list-bg a{
    color: #777777;
}
input[type="checkbox"] {
    display: block;
}
.list-bg a.btn{
    color: #ffffff;
    background-color:#b90e3b !important;
    margin-left: 50%;
    transform: translateX(-50%);
}
.sign_div{
    width: 100%;
}
</style>
<link type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/south-street/jquery-ui.css" rel="stylesheet">
<link type="text/css" href="{{ static_file('js/plugins/sign/css/jquery.signature.css') }}" rel="stylesheet">

@endsection
@section('header')
<header class="header">
  <h2>Order Details</h2>
  <span class="back-btn"><a href="javascript:;" onclick="window.history.go(-1); return false;">
      <img src="{{ static_file('driver/images/icon-back-arrow.png') }}" alt=""></a>
  </span>
</header>
@endsection

@section('content')
<div class="page-content page-bg">
    @foreach($data as $key => $item)
    <div class="list-bg row">

      <div class="col-xs-1">
      @if($enable_deliver)
          @if($item['status'] == 'Picked')
                  <input type="checkbox" class="ids" name="ids[]" value="{{ $item['ids'] }}">
          @endif
      @else
          @if($item['status'] == 'Packed')
                  <input type="checkbox" class="ids" name="ids[]" value="{{ $item['ids'] }}">
          @endif
      @endif
      </div>
      <ul class="col-xs-10">
        <li>
              <label>Item Name</label>
              <span>: {{ $item['name'] }}</span>
        </li>
        <li>
              <label>Qty</label>
              <span>: {{ $item['qty'] }}</span>
        </li>
        <li>
              <label>Status</label>
              <span>: {{ $item['status'] }}</span>
        </li>
      </ul>
    </div>
    @endforeach
    <div class="list-bg">
        <a href="#" class="btn" data-toggle="modal" data-target="#myModal">Update Status / 更新状态</a>
    </div>
</div>

<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-body order-popup">
        <form method="post" action="{{ route('driver.update.status') }}">
            {{ csrf_field() }}
            <h2>Update Status</h2>
            <div class="form-group">
                <select name="status_id" class="form-control">
                    @if($enable_deliver)
                        <option value="11">Delivered</option>
                    @else
                        <option value="10">Picked</option>
                    @endif
                </select>
            </div>
            <div class="form-group">
              <input type="hidden" class="form-control" name="item_ids">
              <input type="text" class="form-control" placeholder="Name" name="name">
            </div>
            <div class="form-group sign_div">
              <!-- <input type="text" class="sign_div form-control" placeholder="Signature" name="sign"> -->
            </div>
            <div class="text-center mg-top" >
                <button type="submit" class="btn btn-default">Update</button>
            </div>
        </form>
        <a class="icon-close" data-dismiss="modal" href="#"><img src="{{ static_file('merchant/images/icon-close.png') }}" alt=""></a> </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
$('#myModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);

    if($('.ids:checked').length == 0){
        alert("Select atleast one item.");
        return false;
    }else{
        var arr = [];
        $('.ids:checked').each(function(e){
            var val = $(this).val().split(',');

            $.each(val, function(index,key){
                arr.push(parseInt(key));
            });
        });

        var arr_json = arr.join(',');
        $('[name=item_ids]').val(arr_json);
    }
});
</script>
<!--[if IE]>
<script type="text/javascript" src="js/excanvas.js"></script>
<![endif]-->
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script type="text/javascript" src="{{ static_file('js/plugins/sign/js/jquery.ui.touch-punch.js') }}"></script>
<script type="text/javascript" src="{{ static_file('js/plugins/sign/js/jquery.signature.js') }}"></script>
<script>
    $('.sign_div').signature();
    // $(selector).signature('toDataURL', 'image/jpeg', 0.8);
</script>
@endsection
