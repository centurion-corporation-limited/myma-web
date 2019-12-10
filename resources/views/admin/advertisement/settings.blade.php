@extends('layouts.admin')

@section('styles')
<link href="{{  static_file('js/plugins/cropper/css/style.css') }}" rel="stylesheet">
<link href="{{  static_file('js/plugins/cropper/css/style-example.css') }}" rel="stylesheet">
<link href="{{  static_file('js/plugins/cropper/css/jquery.Jcrop.css') }}" rel="stylesheet">

@endsection
@section('content')
<!-- page content -->

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Bottom Ad Settings</h2>
            <!-- <ul class="nav navbar-right panel_toolbox">
              <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
              </li>
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                <ul class="dropdown-menu" role="menu">
                  <li><a href="#">Settings 1</a>
                  </li>
                  <li><a href="#">Settings 2</a>
                  </li>
                </ul>
              </li>
              <li><a class="close-link"><i class="fa fa-close"></i></a>
              </li>
            </ul> -->
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <form id="demo-form2" action="{{ route('admin.advertisement.food') }}" method="POST" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left">
              {{ csrf_field() }}

              <div class="form-group">
                <label for="contact" class="control-label col-md-3 col-sm-3 col-xs-12">Bottom ad content for food app</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    <textarea class="form-control col-md-7 col-xs-12" name="options[bottom_ad]">{{ old('options.bottom_ad', getOption('bottom_ad')) }}</textarea>
                </div>
              </div>

              <div class="form-group">
                <label for="contact" class="control-label col-md-3 col-sm-3 col-xs-12">Link for Bottom ad</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    <input type="text" class="form-control col-md-7 col-xs-12" name="options[bottom_ad_link]" value="{{ old('options.bottom_ad_link', getOption('bottom_ad_link')) }}">
                </div>
              </div>

              <div class="ln_solid"></div>
              <div class="form-group">
                <div class="col-md-6 col-sm-9 col-xs-12 col-md-offset-3">
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
tinymce.init({
  selector: '.editor',
  height: 250,
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

<!-- <script src="{{ static_file('js/plugins/cropper/scripts/jquery.Jcrop.js') }}"></script>
<script src="{{ static_file('js/plugins/cropper/scripts/jquery.SimpleCropper.js') }}"></script>
<script src="{{ static_file('js/plugins/ckeditor/ckeditor.js') }}"></script>


var CkeditorContent = function() {
    var e = function() {
        $.each($(".ckeditor"), function() {
            var e = $(this).attr("id");
            $("#cke_" + e).length || CKEDITOR.replace(e, {
                filebrowserBrowseUrl: '{{ static_file("js/plugins/ckfinder/ckfinder.html") }}'
            })
        })
    };
    return {
        init: function() {
            CKEDITOR.editorConfig = function(e) {
                e.filebrowserBrowseUrl = '{{ static_file("js/plugins/ckfinder/ckfinder.html") }}'
            }, e()
        }
    }
}(); -->
<script>
// jQuery(document).ready(function() {
//     $('.cropme').simpleCropper();
//     <!-- CkeditorContent.init() -->
// });
</script>
@stop
