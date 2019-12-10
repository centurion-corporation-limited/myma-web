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
            <h2>Create Trip</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
              @if($or_count == 0 || $bor_count == 0)
                <div class="alert alert-danger" role="alert">
                  No order found for this date and time, choose another.
                </div>
              @endif
            <br />
            @if(($delivery_date != '' && $delivery_time != '') && $or_count && $bor_count)
            <form id="demo-form2" action="{{ route('admin.trip.add') }}" method="POST" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left">
                {{ csrf_field() }}
            @else
            <form id="demo-form2" action="{{ route('admin.trip.add') }}" method="GET" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left">
            @endif

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="trip_date">Trip Date</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="date" id="trip_date" name="trip_date" value="{{ old('trip_date', $delivery_date) }}" class="form-control col-md-7 col-xs-12">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="trip_date">Delivery Time</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {!!Form::select('trip_time', ['' => 'Choose Timing', '07:00 AM' => '07:00 AM', '12:00 PM' => '12:00 PM', '07:00 PM' => '07:00 PM'], $delivery_time, ['class' => 'form-control form-control-topic'])!!}
                </div>
              </div>

              @if(($delivery_date != '' && $delivery_time != '') && $or_count && $bor_count)
              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="price">Trip Price</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="price" required name="price" value="{{ old('price') }}" class="form-control col-md-7 col-xs-12">
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
                                        <input class="outside_check" type="checkbox" name="pickup[]" value="{{ $location->id }}" @if($data[$location->id]['total']) checked disabled @endif >
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
              @endif

              <div class="ln_solid"></div>
              <div class="form-group">
                <div class="col-md-2 col-sm-2 col-xs-12"></div>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <!-- <button class="btn btn-primary" type="button">Cancel</button> -->
                  <!-- <button class="btn btn-primary" type="reset">Reset</button> -->
                  <button type="submit" class="btn btn-success">Add</button>
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
    $(document).on('click', '.inside_check', function(){
        $(this).closest('.panel-default').find('.outside_check').prop('checked', 'true');
    });
});

</script>
@endsection
