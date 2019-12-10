@extends('layouts.admin')

@section('content')

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Maintenance</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <form id="demo-form2" class="colord-form">
              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Fin</label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                  {{ $item->fin }}
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Status</label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                  {{ $item->status }}
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Comments</label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                  {{ $item->comments }}
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Pictures</label>
                <div class="col-md-6 col-sm-6 col-xs-12">

                </div>
              </div>


              <div class="ln_solid"></div>
              <div class="form-group">
                <div class="col-md-3 col-sm-3 col-xs-12"></div>
                <div class="col-md-6 col-sm-9 col-xs-12">
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
