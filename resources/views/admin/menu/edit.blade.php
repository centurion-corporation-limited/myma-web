@extends('layouts.admin')

@section('content')

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Manage Menu</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <br />
            <form id="demo-form2" action="{{ route('admin.menu.edit', encrypt($item->id)) }}" method="POST" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left">
              {{ csrf_field() }}

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Category</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  {!!Form::select('category_id', $categories, $item->category_id, ['class' => 'form-control'])!!}
                </div>
              </div>


              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Name</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <input type="text" id="name" name="name" value="{{ old('name', $item->name) }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name_bn">Name(Bengali)</label>
                  <div class="col-md-6 col-sm-9 col-xs-12">
                      <input type="text" id="name_bn" name="name_bn" value="{{ old('name_bn', $item->name_bn) }}" class="form-control">
                  </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name_mn">Name(Chinese)</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <input type="text" id="name_mn" name="name_mn" value="{{ old('name_mn', $item->name_mn) }}" class="form-control">
                </div>
              </div>


              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name_ta">Name(Tamil)</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <input type="text" id="name_ta" name="name_ta" value="{{ old('name_ta', $item->name_ta) }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name_th">Name(Thai)</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <input type="text" id="name_th" name="name_th" value="{{ old('name_th', $item->name_th) }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="access">Access</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <select id="access" class="form-control" name="access">
                      <option value="free" @if(old('access', $item->access) == 'free') selected="selected" @endif>Free</option>
                      <option value="registered" @if(old('access', $item->access) == 'registered') selected="selected" @endif>Registered</option>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="icon">Menu icon</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <input type="file" id="icon" name="icon" class="file_input fancy_upload">
                  @if($item->icon != '')
                    <img src="{{ static_file($item->icon) }}">
                  @endif
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="active">Status</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <select id="active" class="form-control" name="active">
                      <option value="1" @if(old('active', $item->active) == '1') selected="selected" @endif>Enabled</option>
                      <option value="0" @if(old('active', $item->active) == '0') selected="selected" @endif>Disbaled</option>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="order">Order</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <input type="text" id="order" name="order" value="{{ old('order', $item->order) }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="type">Type</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <select id="type" class="form-control" name="type">
                      {{--
                      <option value="custom" @if(old('type', $item->type) == 'custom') selected="selected" @endif>Custom(Embassy Like)</option>
                      --}}
                      <option value="" @if(old('type', $item->type) == '') selected="selected" @endif>Main Menu</option>
                      <option value="jtc" @if(old('type', $item->type) == 'jtc') selected="selected" @endif>Custom(JTC Like)</option>
                      
                  </select>
                </div>
              </div>

              <div class="ln_solid"></div>
              <div class="form-group">
                <div class="col-md-3 col-sm-3 col-xs-12"></div>
                <div class="col-md-6 col-sm-9 col-xs-12">
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
