@extends('layouts.admin')

@section('content')

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Select Merchant</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <br />
            @if($auth_user->hasRole('food-admin'))
            <form id="demo-form2" action="{{ route('admin.payout.food.view') }}" method="GET" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left">
            @else
            <form id="demo-form2" action="{{ route('admin.payout.view') }}" method="GET" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left">
            @endif
              {{ csrf_field() }}

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="merchant_id">Select Merchant</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                      {!!Form::select('merchant_id', $items, '', ['class' => 'form-control'])!!}
                </div>
              </div>

              <div class="ln_solid"></div>
              <div class="form-group">
                <div class="col-md-2 col-sm-2 col-xs-12"></div>
                <div class="col-md-6 col-sm-10 col-xs-12">
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
