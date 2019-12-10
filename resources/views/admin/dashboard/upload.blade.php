@extends('layouts.admin')

@section('content')
<div class="col-md-8 col-sm-8 col-xs-8 col-md-offset-2">

@include('errors.flash-message')
@include('errors.error')
</div>
    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-8 col-sm-8 col-xs-8 col-md-offset-2">
        <div class="x_panel">
          <div class="x_title">
            <h2>Upload File</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <br />
            <form id="demo-form2" action="{{ route('admin.upload.flexm') }}" method="POST" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left">
              {{ csrf_field() }}

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="language">Type</label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                  <select id="language" class="form-control" name="type" required>
                      <option value="">Please select</option>
                      <option value="remittance">Remittance</option>
                      <option value="wallet">Transactions</option>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">File</label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                  <input type="file" class="" name="file" required>
                </div>
              </div>

              <div class="ln_solid"></div>
              <div class="form-group">
                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                  <!-- <button class="btn btn-primary" type="button">Cancel</button> -->
                  <!-- <button class="btn btn-primary" type="reset">Reset</button> -->
                  <button type="submit" class="btn btn-success">Upload</button>
                </div>
              </div>

            </form>
          </div>
        </div>
      </div>
    </div>

@endsection
