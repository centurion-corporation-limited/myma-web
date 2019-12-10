@extends('layouts.admin')

@section('styles')
<link href="{{  static_file('js/plugins/bootstrap-datepicker-master/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">

@endsection
@section('content')

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Select role</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <br />
            <form id="demo-form2" action="{{ route('admin.user.list') }}" method="GET" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left">
              {{-- csrf_field() --}}

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="full-name">Select Role</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                      {!!Form::select('role', $items, '', ['class' => 'form-control'])!!}

                </div>
              </div>

              <div class="ln_solid"></div>
              <div class="form-group">
                <div class="col-md-3 col-sm-3 col-xs-3 col-md-offset-2">
                  <!-- <button class="btn btn-primary" type="button">Cancel</button> -->
                  <!-- <button class="btn btn-primary" type="reset">Reset</button> -->
                  <button type="submit" class="btn btn-success">Submit</button>
                </div>
              </div>

            </form>
          </div>
        </div>
      </div>
    </div>

@endsection

@section('scripts')
<script src="{{ static_file('js/plugins/bootstrap-datepicker-master/dist/js/bootstrap-datepicker.min.js') }}"></script>
<script>
$(document).on('ready',function(){
  $('.date-picker').datepicker({
    format:"yyyy-mm-dd"
  });

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
