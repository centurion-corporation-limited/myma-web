@extends('layouts.admin')

@section('content')

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Add Menu</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <br />
            <form id="demo-form2" action="{{ route('admin.menu.add') }}" method="POST" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left">
              {{ csrf_field() }}

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Category</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  {!!Form::select('category_id', $categories, '', ['class' => 'form-control'])!!}
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Name</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name_bn">Name(Bengali)</label>
                  <div class="col-md-6 col-sm-9 col-xs-12">
                      <input type="text" id="name_bn" name="name_bn" value="{{ old('name_bn') }}" class="form-control">
                  </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name_mn">Name(Chinese)</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <input type="text" id="name_mn" name="name_mn" value="{{ old('name_mn') }}" class="form-control">
                </div>
              </div>


              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name_ta">Name(Tamil)</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <input type="text" id="name_ta" name="name_ta" value="{{ old('name_ta') }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name_th">Name(Thai)</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <input type="text" id="name_th" name="name_th" value="{{ old('name_th') }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="access">Access</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <select id="access" class="form-control" name="access">
                      <option value="free" @if(old('access') == 'free') selected="selected" @endif>Free</option>
                      <option value="registered" @if(old('access') == 'registered') selected="selected" @endif>Registered</option>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="icon">Menu icon</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <input type="file" id="icon" name="icon" class="file_input fancy_upload">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="active">Status</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <select id="active" class="form-control" name="active">
                      <option value="1" @if(old('active') == '1') selected="selected" @endif>Enabled</option>
                      <option value="0" @if(old('active') == '0') selected="selected" @endif>Disbaled</option>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="order">Order</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <input type="text" id="order" name="order" value="{{ old('order') }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="type">Type</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <select id="type" class="form-control" name="type">
                      {{--<option value="" @if(old('type') == '') selected="selected" @endif>Simple Menu</option>
                      <option value="custom" @if(old('type') == 'custom') selected="selected" @endif>Custom(Embassy Like)</option>
                      --}}
                      <option value="jtc" @if(old('type') == 'jtc') selected="selected" @endif>Custom(JTC Like)</option>
                      
                  </select>
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
