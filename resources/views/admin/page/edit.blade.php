@extends('layouts.admin')

@section('content')

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Edit Page</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <br />
            <form id="demo-form2" action="{{ route('admin.page.edit', encrypt($item->id)) }}" method="POST" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left">
              {{ csrf_field() }}

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="type">Language <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    {!!Form::select('language', $languages, $item->lang_content->first()?$item->lang_content->first()->language:'english', ['class' => 'form-control'])!!}
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Title <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <input type="text" id="title" name="title" value="{{ old('title', $item->content('english')->first()?$item->content('english')->first()->title:'') }}" class="language english form-control">
                    <input type="text" id="title" name="title_bn" value="{{ old('title_bn', $item->content('bengali')->first()?$item->content('bengali')->first()->title:'') }}" class="language hide bengali form-control">
                    <input type="text" id="title" name="title_mn" value="{{ old('title_mn', $item->content('mandarin')->first()?$item->content('mandarin')->first()->title:'') }}" class="language hide mandarin form-control">
                    <input type="text" id="title" name="title_ta" value="{{ old('title_ta', $item->content('tamil')->first()?$item->content('tamil')->first()->title:'') }}" class="language hide tamil form-control">
                    <input type="text" id="title" name="title_th" value="{{ old('title_th', $item->content('thai')->first()?$item->content('thai')->first()->title:'') }}" class="language hide thai form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12">Content <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <textarea class="language hide editor english form-control" name="content" id="editor">{{ old('content', $item->content('english')->first()?$item->content('english')->first()->content:'') }}</textarea>
                    <textarea class="language hide editor bengali form-control" name="content_bn" >{{ old('content_bn', $item->content('bengali')->first()?$item->content('bengali')->first()->content:'') }}</textarea>
                    <textarea class="language hide editor mandarin form-control" name="content_mn" >{{ old('content_mn', $item->content('mandarin')->first()?$item->content('mandarin')->first()->content:'') }}</textarea>
                    <textarea class="language hide editor tamil form-control" name="content_ta" >{{ old('content_ta', $item->content('tamil')->first()?$item->content('tamil')->first()->content:'') }}</textarea>
                    <textarea class="language hide editor thai form-control" name="content_th" >{{ old('content_th', $item->content('thai')->first()?$item->content('thai')->first()->content:'') }}</textarea>
                </div>
              </div>

              <div class="ln_solid"></div>
              <div class="form-group">
                <div class="col-md-3 col-sm-2 col-xs-12"></div>
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
<script src="{{ static_file('js/plugins/tinymce/tinymce.min.js') }}"></script>
<script>
$(document).on('ready', function(){
    $(document).on('change', '[name=language]', function(){
        var val = $(this).val();
        tinymce.remove('.editor');
        $('.language').addClass('hide');
        $('.'+val).removeClass('hide');
        tinymce.init({
          selector: '.editor'+'.'+val,
          height: 500,
          menubar: false,
          plugins: [
            'advlist autolink lists link image charmap print preview anchor textcolor',
            'searchreplace visualblocks code fullscreen',
            'insertdatetime media table contextmenu paste code help wordcount'
          ],
          toolbar: 'code insert | undo redo |  formatselect | bold italic backcolor  | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
        });
    });
    $('[name=language]').trigger('change');

});

// tinymce.init({
//   selector: '#editor',
//   height: 500,
//   menubar: false,
//   plugins: [
//     'advlist autolink lists link image charmap print preview anchor textcolor',
//     'searchreplace visualblocks code fullscreen',
//     'insertdatetime media table contextmenu paste code help wordcount'
//   ],
//   toolbar: 'code insert | undo redo |  formatselect | bold italic backcolor  | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
// });
</script>
@endsection
