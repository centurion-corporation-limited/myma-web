@extends('layouts.admin')

@section('content')

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Edit Dormitory</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <br />
            <form id="demo-form2" action="{{ route('admin.dormitory.edit', encrypt($item->id)) }}" method="POST" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left">
              {{ csrf_field() }}

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Dormitory Name <span class="required">*</span></label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <input type="text" id="name" name="name" value="{{ old('name', $item->name) }}" class="form-control">
                </div>
              </div>


              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="full-name">Manager <span class="required">*</span></label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    <select class="form-control" name="manager_id">
                        @foreach($users as $user)
                        <option value="{{ $user->id }}" @if(old('manager_id', $item->manager_id) == $user->id) selected @endif>{{ $user->email }}</option>
                        @endforeach
                    </select>

                </div>
              </div>


              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="status_id">Manager</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    <select id="status_id" class="form-control" name="status_id">
                        <option value="4" @if(old('status_id', $item->status_id) == '4') selected="selected" @endif>Active</option>
                        <option value="5" @if(old('status_id', $item->status_id) == '5') selected="selected" @endif>Disabled</option>
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
</script>
@endsection
