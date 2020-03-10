@extends('layouts.admin')

@section('styles')
<style>
.form-control{
    /* width:30%; */
}
.form-horizontal .control-label{
    padding-top: 0;
    line-height: 1.47;
}
.form-horizontal .form-group{
    line-height: 1.47;
}
.status_div .row{padding-left:10px;}
</style>
<link href="{{  static_file('js/plugins/cropper/css/style.css') }}" rel="stylesheet">
<link href="{{  static_file('js/plugins/cropper/css/style-example.css') }}" rel="stylesheet">
<link href="{{  static_file('js/plugins/cropper/css/jquery.Jcrop.css') }}" rel="stylesheet">
@endsection
@section('content')

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Update maintenance status</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <br />
            <form id="demo-form2" action="{{ route('admin.maintenance.edit', encrypt($item->id)) }}" method="POST" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left">
              {{ csrf_field() }}

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Case ID</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  {{ $item->id }}
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">User</label>
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
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Dormitory</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  {{ $item->dormitory->name or '--' }}
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Location</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  {{ $item->location }}
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Comments</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  {{ $item->comments }}
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Pictures</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    @if($item->photo_1)
                    <a href="{{ static_file($item->photo_1) }}" data-lightbox="image-1">
                        <img src="{{ public_url($item->photo_1) }}" height="100" width="100">
                    </a>
                    @endif
                    @if($item->photo_2)
                    <a href="{{ static_file($item->photo_2) }}" data-lightbox="image-1">
                        <img src="{{ public_url($item->photo_2) }}" height="100" width="100">
                    </a>
                    @endif
                    @if($item->photo_3)
                    <a href="{{ static_file($item->photo_3) }}" data-lightbox="image-1">
                        <img src="{{ public_url($item->photo_3) }}" height="100" width="100">
                    </a>
                    @endif
                    @if($item->photo_4)
                    <a href="{{ static_file($item->photo_4) }}" data-lightbox="image-1">
                        <img src="{{ public_url($item->photo_4) }}" height="100" width="100">
                    </a>
                    @endif
                    @if($item->photo_5)
                    <a href="{{ static_file($item->photo_5) }}" data-lightbox="image-1">
                        <img src="{{ public_url($item->photo_5) }}" height="100" width="100">
                    </a>
                    @endif
                </div>
              </div>

              @if($item->status_id <= 3)
              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Status</label>
                <div class="col-md-3 col-sm-3 col-xs-12">
                    {!!Form::select('status_id', $status, $item->status_id, ['class' => 'form-control'])!!}
                </div>
              </div>

              <div class="hide status_div">
                  <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="remarks">Remarks</label>
                    <div class="col-md-6 col-sm-9 col-xs-12">
                      <textarea name="remarks" id="remarks" class="form-control col-md-12 col-xs-12">{{ old('remarks' )}}</textarea>
                    </div>
                  </div>

                  <div class="form-group add_more_div">
                      <div class="row">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Images</label>
                        <div class="col-md-6 col-sm-9 col-xs-12">
                            <input type="file" name="image[]">
                        </div>
                        <div class="col-md-2">
                            <i class="add_more fa fa-plus fa-2x"></i>
                        </div>
                      </div>
                  </div>
              </div>
              @else
              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Remarks :</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  {{ $item->remarks }}
                </div>
              </div>
              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Uploaded after inspection</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    @foreach($item->files as $file)
                        @if($file->path)
                        <a href="{{ $file->path }}" data-lightbox="image-2">
                            <img src="{{ $file->path }}" height="100" width="100">
                        </a>
                        @endif
                    @endforeach
                </div>
              </div>
              @endif
              <div class="ln_solid"></div>
              @if($item->status_id <= 3)

              <div class="form-group">
                <div class="col-md-3 col-sm-3 col-xs-12"></div>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <!-- <button class="btn btn-primary" type="button">Cancel</button> -->
                  <!-- <button class="btn btn-primary" type="reset">Reset</button> -->
                  <button type="submit" class="btn btn-success">Update</button>
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
<script src="{{ static_file('js/plugins/cropper/scripts/jquery.Jcrop.js') }}"></script>
<script src="{{ static_file('js/plugins/cropper/scripts/jquery.SimpleCropper.js') }}"></script>
<script>
 // $('.cropme').simpleCropper();
 $('.add_more').on('click', function(){
     $('.add_more_div').append('<div class="row">'+
       '<div class="col-md-6 col-sm-9 col-xs-12 col-sm-offset-3 ">'+
           '<input type="file" name="image[]">'+
       '</div>'+
       '<div class="col-md-2">'+
           '<i class="remove_more fa fa-times fa-2x"></i>'+
       '</div>'+
     '</div>')
 });

 $(document).on('click', '.remove_more', function(){console.log("Works");
     $(this).closest('.row').remove();
 })

 $(document).on('change', '[name=status_id]', function(){
    var val = $(this).val() ;
    if(val == 3){
        $('.status_div').removeClass('hide');
    }else{
        $('.status_div').addClass('hide');

    }
 });
</script>
@endsection
