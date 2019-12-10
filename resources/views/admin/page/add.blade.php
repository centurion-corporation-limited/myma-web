@extends('layouts.admin')

@section('content')

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Add Page</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <br />
            <form id="demo-form2" action="{{ route('admin.page.add') }}" method="POST" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left">
              {{ csrf_field() }}

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="type">Language <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    {!!Form::select('language', $languages, '', ['class' => 'form-control'])!!}
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Title <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <input type="text" id="title" name="title" value="{{ old('title') }}" class="language english form-control">
                    <input type="text" id="title_bn" name="title_bn" value="{{ old('title_bn') }}" class="language hide bengali form-control">
                    <input type="text" id="title_mn" name="title_mn" value="{{ old('title_mn') }}" class="language hide mandarin form-control">
                    <input type="text" id="title_ta" name="title_ta" value="{{ old('title_ta') }}" class="language hide tamil form-control">
                    <input type="text" id="title_th" name="title_th" value="{{ old('title_th') }}" class="language hide thai form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12">Content <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <textarea class="language english form-control editor" name="content" id="editor">{{ old('content') }}</textarea>
                    <textarea class="language hide bengali form-control editor" name="content_bn" >{{ old('content_bn') }}</textarea>
                    <textarea class="language hide mandarin form-control editor" name="content_mn" >{{ old('content_mn') }}</textarea>
                    <textarea class="language hide tamil form-control editor" name="content_ta" >{{ old('content_ta') }}</textarea>
                    <textarea class="language hide thai form-control editor" name="content_th" >{{ old('content_th') }}</textarea>
                </div>
              </div>

              <div class="ln_solid"></div>
              <div class="form-group">
                <div class="col-md-2 col-sm-2 col-xs-12"></div>
                <div class="col-md-6 col-sm-10 col-xs-12">
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
<script src="{{ static_file('js/plugins/tinymce/tinymce.min.js') }}"></script>
<script>
// $(document).on('ready',function(){
//     var validator = $( "#demo-form2" ).validate({
//       rules: {
//         title: {
//             required: function(element) {
//               return $("[name=language]").val() == 'english';
//             }
//         },
//         title_bn: {
//           required: function(element) {
//             return $("[name=language]").val() == 'bengali';
//           }
//         },
//         title_mn: {
//           required: function(element) {
//             return $("[name=language]").val() == 'mandarin';
//           }
//         },
//         title_ta: {
//             required: function(element) {
//               return $("[name=language]").val() == 'tamil';
//             }
//         },
//         title_th: {
//           required: function(element) {
//             return $("[name=language]").val() == 'thai';
//           }
//         },
//         content: {
//           required: function(element) {
//             return $("[name=language]").val() == 'english';
//           }
//         },
//         content_bn: {
//           required: function(element) {
//             return $("[name=language]").val() == 'bengali';
//           }
//         },
//         content_mn: {
//           required: function(element) {
//             return $("[name=language]").val() == 'mandarin';
//           }
//         },
//         content_ta: {
//           required: function(element) {
//             return $("[name=language]").val() == 'tamil';
//           }
//         },
//         content_th: {
//           required: function(element) {
//             return $("[name=language]").val() == 'thai';
//           }
//         }
//       }
//     });
// });
$(document).on('change', '[name=language]', function(){
    var val = $(this).val();
    $('.language').addClass('hide');
    $('.'+val).removeClass('hide');

    tinymce.remove('.editor');
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

tinymce.init({
  selector: '#editor',
  height: 500,
  menubar: false,
  plugins: [
    'advlist autolink lists link image charmap print preview anchor textcolor',
    'searchreplace visualblocks code fullscreen',
    'insertdatetime media table contextmenu paste code help wordcount'
  ],
  toolbar: 'code insert | undo redo |  formatselect | bold italic backcolor  | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
  // content_css: [
  //   '//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
  //   '//www.tinymce.com/css/codepen.min.css']
});
</script>
@endsection
