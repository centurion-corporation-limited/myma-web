@extends('layouts.admin')

@section('content')

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Edit Details</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <br />
            <form id="demo-form2" action="{{ route('admin.permission.edit', $item->id) }}" method="POST" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left">
              {{ csrf_field() }}

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Name</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <input type="text" id="name" name="name" value="{{ old('name', $item->title) }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="email">Description
                </label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <textarea id="email" name="description" class="form-control">{{ old('description', $item->description) }}</textarea>
                </div>
              </div>

              @foreach($item->slug as $key => $permission)
              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="add">{{ $key }}</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <input type="checkbox" id="add" name="permissions[{{ $key }}]" @if($permission == true) checked @endif value="true" class="">
                </div>
              </div>
              @endforeach

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
@section('scripts')
<script>

</script>
@endsection
