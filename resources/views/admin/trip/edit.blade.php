@extends('layouts.admin')

@section('styles')
<style>
.form-group{line-height: inherit;}
</style>
@endsection
@section('content')

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Edit Trip</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <br />
            <form id="demo-form2" action="{{ route('admin.trip.edit', encrypt($item->id)) }}" method="POST" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left">
              {{ csrf_field() }}

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="trip_date">Trip Date</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input disabled type="date" id="trip_date" name="trip_date" value="{{ old('trip_date' ,$item->trip_date) }}" class="form-control col-md-7 col-xs-12">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="trip_date">Delivery Time</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {!!Form::select('trip_time', ['' => 'Choose Timing', '07:00 AM' => '07:00 AM', '12:00 PM' => '12:00 PM', '07:00 PM' => '07:00 PM'], $item->trip_time, ['class' => 'form-control form-control-topic', 'disabled' => 'true'])!!}
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="price">Trip Price</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="price" name="price" value="{{ old('price' ,$item->price) }}" class="form-control col-md-7 col-xs-12">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="email">Pickup Locations</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <div class="panel-group" id="accordion">
                    @foreach($locations as $location)
                        @if(count($data[$location->id]['orders']))
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <input type="checkbox" name="pickup[]" value="{{ $location->id }}" @if($data[$location->id]['disabled']) disabled @endif @if($data[$location->id]['total']) checked @endif>
                                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
                                            {{ $location->address }}
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapseOne" class="panel-collapse collapse in">
                                    <div class="panel-body">
                                        @foreach($data[$location->id]['orders'] as $order)
                                        <div class="row">
                                            <div class="col-md-4 col-xs-12">
                                                <label>
                                                    <input @if($order->checked) checked @endif @if($order->disabled) disabled @endif type="checkbox" value="{{ $order->id }}" name="orders[{{ $location->id }}][{{$order->id}}]">Order #{{ $order->id }}
                                                </label><br>
                                            </div>
                                            <div class="col-md-8 col-xs-12">
                                                <span>(Delivery Address : {{ $order->dormitory?$order->dormitory->name:($order->address) }})</span>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                  </div>
                </div>
              </div>

              <div class="ln_solid"></div>
              <div class="form-group">
                <div class="col-md-2 col-sm-2 col-xs-12"></div>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <!-- <button class="btn btn-primary" type="button">Cancel</button> -->
                  <!-- <button class="btn btn-primary" type="reset">Reset</button> -->
                  <button type="submit" class="btn btn-success">Update</button>
                </div>
              </div>

            </form>
          </div>
        </div>
      </div>
    </div>

@endsection
@section('scripts')
<script>
$(document).on('ready',function(){
  if($('#role').val() == 'employee'){
    $('.show_nric').removeClass('hide');
  }else{
    $('.show_nric').addClass('hide');
  }
});
function showNRIC(value){
  if(value == 'employee'){
    $('.show_nric').removeClass('hide');
  }else{
    $('.show_nric').addClass('hide');
  }
}
</script>
@endsection
