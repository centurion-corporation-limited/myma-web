@extends('layouts.admin')

@section('styles')
<link href="{{  static_file('js/plugins/bootstrap-datepicker-master/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
<style>
.button-tran-abs {
    bottom: 0;
}
</style>

@endsection
@section('content')
<div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Order Invoices</h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <form action="{{ route('admin.order.invoices') }}" method="GET" class="form-inline">

                      <div class="row flex-row transaction-form">
                          <label class="control-label" for="dormitory_id">Date range </label>
                          <div class="input-group input-daterange">
                              <input autocomplete="off" type="text" placeholder="From" class="form-control" name="start" value="{{ Request::input('start') }}">
                              <div class="input-group-addon">to</div>
                              <input autocomplete="off" type="text" placeholder="To" class="form-control" name="end" value="{{ Request::input('end') }}">
                          </div>
                      </div>
                          <!-- <div class="form-group">
                              {!!Form::select('type', ['Please select placeholder', 'home' => 'Home Slider', 'landing' => 'Popup'], '', ['class' => 'form-control'])!!}
                          </div>

                          <div class="form-group">
                              {!!Form::select('status', ['Please select status', 'completed' => 'Completed', 'running' => 'Running', 'inactive' => 'Inactive'], '', ['class' => 'form-control'])!!}
                          </div>

                          <div class="form-group">
                              {!!Form::select('adv_type', ['Please select Ad type', '2' => 'Date', '1' => 'Impression'], '', ['class' => 'form-control'])!!}
                          </div> -->
                          <div class="row flex-row  button-tran">
                            <button class="btn btn-success" type="submit">Download</button>
                            <a href="{{ route('admin.order.invoices')}}" class="btn btn-success">Reset</a>

                          </div>
                    </form>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="{{ static_file('js/plugins/bootstrap-datepicker-master/dist/js/bootstrap-datepicker.min.js') }}"></script>
<script>
$(document).ready(function() {
    $('.input-daterange').datepicker({
        todayBtn: "linked",
        format: "dd/mm/yyyy"//"yyyy-mm-dd"
    });
});
</script>
@stop
