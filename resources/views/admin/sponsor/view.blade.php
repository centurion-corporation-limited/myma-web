@extends('layouts.admin')

@section('content')

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Sponsor Details</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <br />
            <form id="demo-form2" class="form-horizontal form-label-left colored-form">
              {{ csrf_field() }}

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="order">Name</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{ $item->name }}
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="email">Email</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{ $item->email }}
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="phone">Phone No</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{ $item->phone }}
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="address">Address</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{ $item->address }}
                </div>
              </div>

              <div class="ln_solid"></div>
              <div class="form-group">
                <div class="col-md-2 col-sm-2 col-xs-12"></div>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <!-- <button class="btn btn-primary" type="button">Cancel</button> -->
                  <!-- <button class="btn btn-primary" type="reset">Reset</button> -->
                  <a href="{{ route('admin.sponsor.edit', encrypt($item->id)) }}" class="btn btn-success">Update</a>
                  <!-- <button type="submit" class="btn btn-success">Update</button> -->
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
