@extends('layouts.admin')

@section('styles')
<link href="{{  static_file('js/plugins/bootstrap-datepicker-master/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
<link href="{{  static_file('js/plugins/cropper/css/style.css') }}" rel="stylesheet">
<link href="{{  static_file('js/plugins/cropper/css/style-example.css') }}" rel="stylesheet">
<link href="{{  static_file('js/plugins/cropper/css/jquery.Jcrop.css') }}" rel="stylesheet">
<link rel="stylesheet" href="{{ static_file('js/plugins/jquery.tagsinput/src/jquery.tagsinput.css')}}">
@endsection

@section('content')

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Advertisement Performance Report</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <form id="demo-form2" class="form-horizontal form-label-left colord-form">

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Sponsor</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{ $item->sponsor->name or '-' }}
                </div>
              </div>


              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="full-name">Placeholder</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  @if($item->type == 'home') Home Slider @endif
                  @if($item->type == 'landing') Popup @endif
                  @if($item->type == 'food') Food Dashboard @endif
                  </select>
                </div>
              </div>

<!--
              <div class="form-group order_div @if($item->type != 'landing') hide @endif">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="order">Slider order </label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{ $item->slider_order }}
                </div>
              </div> -->

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="description">Description</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{ $item->description }}
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Managed By </label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    {{ $item->reportee->name or '-' }}
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12">Image</label>
                <div class="col-md-9 col-sm-9 col-xs-12">
                  @if($item->path)
                  <img src= "{{ static_file($item->path) }}" height="210" width="340" />
                  @endif
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Advertisement type</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  @if($item->adv_type == '1')
                  Impression Based
                  @else
                  Duration Based
                  @endif

                </div>
              </div>

              <div class="impression_div form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="plan_id">Plan</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{ $impressions or 0 }} - ${{ $price or 0 }}
                </div>
              </div>

              @if($item->adv_type == '1')

              <div class="date_div form-group">
                <label for="start_time"  class="control-label col-md-2 col-sm-2 col-xs-12">Viewed</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{ $item->impress->impressions or '-' }}
                </div>
              </div>

              @else
              <div class="date_div form-group">
                <label for="start_time"  class="control-label col-md-2 col-sm-2 col-xs-12">Start Date</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{$item->start}}
                </div>
              </div>

              <div class="date_div form-group">
                <label for="end_time" class="control-label col-md-2 col-sm-2 col-xs-12">End Date</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{$item->end}}
                </div>
              </div>

              @endif

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="link">External Link</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{ $item->link }}
                </div>
              </div>

              <div class="ln_solid"></div>
              <div class="form-group">
                <div class="col-md-2 col-sm-2 col-xs-12"></div>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <!-- <button class="btn btn-primary" type="button">Cancel</button> -->
                  <!-- <button class="btn btn-primary" type="reset">Reset</button> -->
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
$(document).on('ready',function(){
});
</script>
@endsection
