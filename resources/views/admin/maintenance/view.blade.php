@extends('layouts.admin')

@section('styles')
<link href="{{  static_file('css/lightbox.min.css') }}" rel="stylesheet">
<style>
.form-group{/*border: 1px solid*/;font-size:14px;line-height: 0;padding: 20px;}

.well.profile
{
    min-height: 190px;
    display: inline-block;
}
.divider
{
    border-top:1px solid rgba(0,0,0,0.1);
}
.form-horizontal .control-label{
    padding-top: 0;
    line-height: 1.47;
}
.form-horizontal .form-group{
    line-height: 1.47;
}
</style>
@endsection

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
            <form id="demo-form2" class="form-horizontal form-label-left colord-form">
                <div class="form-group">
                  <label class="control-label col-md-2 col-sm-3 col-xs-12" for="name">Case ID :</label>
                  <div class="col-md-6 col-sm-9 col-xs-12">
                    {{ $item->id }}
                  </div>
                </div>
              <div class="form-group">
                <label class="control-label col-md-2 col-sm-3 col-xs-12" for="name">User :</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  @if($item->user && $item->user->name)
                    <a type="button" user_id="{{ $item->user_id }}" data-toggle="modal" data-target="#userModal">
                        {{ $item->user->name or '--'}}
                    </a>
                  @else
                    <span style="font-size:inherit;" class="label label-warning">User does not exist.</span>
                  @endif

                </div>
              </div>
              <div class="form-group">
                <label class="control-label col-md-2 col-sm-3 col-xs-12" for="name">Fin No :</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                        {{ $item->user->profile->fin_no or '--'}}
                </div>
              </div>
              
              <div class="form-group">
                <label class="control-label col-md-2 col-sm-3 col-xs-12" for="name">Dormitory :</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  {{ $item->dormitory->name or '--' }}
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-3 col-xs-12" for="name">Location :</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  {{ $item->location }}<?php if($item->location_lang != '') { echo '&nbsp; ( '.$item->location_lang.' )'; } ?>
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-3 col-xs-12" for="name">Reported At :</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  {{ $item->created_at->format('d/m/Y H:i:A') }}
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-3 col-xs-12" for="name">Status :</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  {{ $item->status->name or '--'}}
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-3 col-xs-12">Comments :</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  {{ $item->comments }}<?php if($item->comments_lang != '') { echo '&nbsp; ( '.$item->comments_lang.' )'; } ?>
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-3 col-xs-12">Pictures :</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    @if($item->photo_1)
                    <a href="{{ static_file($item->photo_1) }}" data-lightbox="image-1">
                        <img src="{{ public_url($item->photo_1) }}" height="150">
                    </a>
                    @endif
                    @if($item->photo_2)
                    <a href="{{ static_file($item->photo_2) }}" data-lightbox="image-1">
                        <img src="{{ public_url($item->photo_2) }}" height="150">
                    </a>
                    @endif
                    @if($item->photo_3)
                    <a href="{{ static_file($item->photo_3) }}" data-lightbox="image-1">
                        <img src="{{ public_url($item->photo_3) }}" height="150" >
                    </a>
                    @endif
                    @if($item->photo_4)
                    <a href="{{ static_file($item->photo_4) }}" data-lightbox="image-1">
                        <img src="{{ public_url($item->photo_4) }}" height="150">
                    </a>
                    @endif
                    @if($item->photo_5)
                    <a href="{{ static_file($item->photo_5) }}" data-lightbox="image-1">
                        <img src="{{ public_url($item->photo_5) }}" height="150">
                    </a>
                    @endif
                </div>
              </div>

              @if($item->status_id == 3)
              <div class="form-group">
                <label class="control-label col-md-2 col-sm-3 col-xs-12" for="name">Completed At :</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  {{ isset($item->completed_at)?$item->completed_at->format('d/m/Y H:i:A'):'' }}
                </div>
              </div>
              <div class="form-group">
                <label class="control-label col-md-2 col-sm-3 col-xs-12">Remarks :</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  {{ $item->remarks }}<?php if($item->remarks_lang != '') { echo '&nbsp; ( '.$item->remarks_lang.' )'; } ?>
                </div>
              </div>
              <div class="form-group">
                <label class="control-label col-md-2 col-sm-3 col-xs-12">Uploaded after inspection</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    @foreach($item->files as $file)
                        @if($file->path)
                        <a href="{{ $file->path }}" data-lightbox="image-2">
                            <img src="{{ $file->path }}" height="150">
                        </a>
                        @endif
                    @endforeach
                </div>
              </div>
              @endif

              <div class="ln_solid"></div>
              @if($item->status_id <= 3)
              <div class="form-group">
                <div class="col-md-2 col-sm-3 col-xs-12"></div>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <!-- <button class="btn btn-primary" type="button">Cancel</button> -->
                  <!-- <button class="btn btn-primary" type="reset">Reset</button> -->
                  <a href="{{ route('admin.maintenance.edit', encrypt($item->id)) }}" class="btn btn-success">Edit</a>
                </div>
              </div>
              @endif
            </form>
          </div>
        </div>
      </div>
    </div>

@endsection
@section('scripts')
<script src="{{  static_file('js/lightbox.min.js') }}"></script>

<script>

</script>
@endsection
