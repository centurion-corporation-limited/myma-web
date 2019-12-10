@extends('layouts.admin')

@section('styles')
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
            <h2>Edit Category</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <br />
            <form id="demo-form2" action="{{ route('admin.mom.category.edit', encrypt($item->id)) }}" method="POST" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left">
              {{ csrf_field() }}

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="type">Language <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    {!!Form::select('language', $languages, $item->language, ['class' => 'form-control'])!!}

                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Title <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <input type="text" id="title" name="title" value="{{ old('title',$item->content('english')->first()?$item->content('english')->first()->title:'') }}" class="english language form-control">
                    <input type="text" id="title" name="title_bn" value="{{ old('title_bn',$item->content('bengali')->first()?$item->content('bengali')->first()->title:'') }}" class="bengali hide language form-control">
                    <input type="text" id="title" name="title_mn" value="{{ old('title_mn',$item->content('mandarin')->first()?$item->content('mandarin')->first()->title:'') }}" class="mandarin hide language form-control">
                    <input type="text" id="title" name="title_th" value="{{ old('title_th',$item->content('thai')->first()?$item->content('thai')->first()->title:'') }}" class="thai hide language form-control">
                    <input type="text" id="title" name="title_ta" value="{{ old('title_ta',$item->content('tamil')->first()?$item->content('tamil')->first()->title:'') }}" class="tamil hide language form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="active">Status</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <select id="active" class="form-control" name="active">
                      <option value="1" @if(old('active', $item->active) == '1') selected="selected" @endif>Enabled</option>
                      <option value="0" @if(old('active', $item->active) == '0') selected="selected" @endif>Disbaled</option>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="image">Image <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <div class="colapsImg">
                    <div class="placehoderImg">
                      <div class="cropme" style="width: 640px; height: 410px;"></div>
                      <input type="hidden" class="form-control" name="path">
                    </div>
                    @if($item->image)
                    <div class="orgImg">
                      <input type="hidden" name="have_image" value="1">
                      <img src= "{{ static_file($item->image) }}" height="410" width="640" style="max-width: inherit;" />
                    </div>
                    @endif
                  </div>
                  <!-- <input type="file" id="image" name="image" value="{{ old('image') }}" class="form-control"> -->
                </div>
              </div>
              <div class="ln_solid"></div>
              <div class="form-group">
                <div class="col-md-2 col-sm-2 col-xs-12"></div>
                <div class="col-md-6 col-sm-10 col-xs-12">
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
<script src="{{ static_file('js/plugins/cropper/scripts/jquery.Jcrop.js') }}"></script>
<script src="{{ static_file('js/plugins/cropper/scripts/jquery.SimpleCropper.js') }}"></script>
<script>
$(document).on('ready',function(){
    $(document).on('change', '[name=language]', function(){
        var val = $(this).val();
        $('.language').addClass('hide');
        $('.'+val).removeClass('hide');
    });
    $('[name=language]').trigger('change');
});

// $( "#demo-form2" ).validate({
//   rules: {
//     title: {
//         required: function(element) {
//           return $("[name=language]").val() == 'english';
//         }
//     },
//     title_bn: {
//       required: function(element) {
//         return $("[name=language]").val() == 'bengali';
//       }
//     }
//   }
// });
$(document).on('ready',function(){
    $('.cropme').simpleCropper();
});


</script>
@endsection
