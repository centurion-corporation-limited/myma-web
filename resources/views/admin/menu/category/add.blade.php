@extends('layouts.admin')

@section('content')

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Add Category</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <br />
            <form id="demo-form2" action="{{ route('admin.menu.category.add') }}" method="POST" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left">
              {{ csrf_field() }}

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Name</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <input type="text" id="name" name="name" value="{{ old('name') }}" class="control-label">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name_bn">Name(Bengali)</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <input type="text" id="name_bn" name="name_bn" value="{{ old('name_bn') }}" class="control-label">
                </div>
              </div>

              <div class="form-group">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name_mn">Name(Chinese)</label>
                  <div class="col-md-6 col-sm-9 col-xs-12">
                      <input type="text" id="name_mn" name="name_mn" value="{{ old('name_mn') }}" class="control-label">
                  </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name_ta">Name(Tamil)</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <input type="text" id="name_ta" name="name_ta" value="{{ old('name_ta') }}" class="control-label">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name_th">Name(Thai)</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <input type="text" id="name_th" name="name_th" value="{{ old('name_th') }}" class="control-label">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="order">Order</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <input type="text" id="order" name="order" value="{{ old('order') }}" class="control-label">
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
