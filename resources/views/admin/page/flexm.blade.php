@extends('layouts.admin')

@section('styles')

@endsection
@section('content')
<!-- page content -->

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Flexm Pages</h2>
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
            <form id="demo-form2" action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left">
              {{ csrf_field() }}

              <div class="form-group">
                <label for="contact" class="control-label col-md-3 col-sm-3 col-xs-12">User Guide</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    <textarea class="form-control col-md-7 col-xs-12 editor" name="options[flexm_user_guide_content]" id="editor">{{ old('options.flexm_user_guide_content', getOption('flexm_user_guide_content')) }}</textarea>
                </div>
              </div>

              <div class="form-group">
                <label for="contact" class="control-label col-md-3 col-sm-3 col-xs-12">FAQ's</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    <textarea class="form-control col-md-7 col-xs-12 editor" name="options[flexm_myma_faq_content]" id="editor">{{ old('options.flexm_myma_faq_content', getOption('flexm_myma_faq_content')) }}</textarea>
                </div>
              </div>

              {{-- <div class="form-group">
                <label for="contact" class="control-label col-md-3 col-sm-3 col-xs-12">How To</label>
                <div class="col-md-6 col-sm-9 col-xs-12">

                    <input type="file" name="flexm_howto_content" class="file_input fancy_upload" />
                    @if(getOption('flexm_howto_content'))
                    <a href="{{ getOption('flexm_howto_content') }}" target="_blank">File - click to view</a>
                    @endif
                </div>
              </div> --}}
            
            <div class="form-group">
                <label for="flexm_support_content" class="control-label col-md-3 col-sm-3 col-xs-12">Support</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    <textarea class="form-control col-md-7 col-xs-12 editor" name="options[flexm_support_content]" id="editor">{{ old('options.flexm_support_content', getOption('flexm_support_content')) }}</textarea>
                </div>
              </div>
              
              <div class="form-group">
                <label for="contact" class="control-label col-md-3 col-sm-3 col-xs-12">T&C</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    <textarea class="form-control col-md-7 col-xs-12 editor" name="options[flexm_tnc]" id="editor">{{ old('options.flexm_tnc', getOption('flexm_tnc')) }}</textarea>
                </div>
              </div>
              
              

              <!-- <div class="form-group">
                <label for="contact" class="control-label col-md-3 col-sm-3 col-xs-12">Taxi Lost & Found (Bengali)</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    <textarea class="form-control col-md-7 col-xs-12 editor" name="options[taxi_lost_found_bn]" id="editor">{{ old('options.taxi_lost_found_bn', getOption('taxi_lost_found_bn')) }}</textarea>
                </div>
              </div>

              <div class="form-group">
                <label for="contact" class="control-label col-md-3 col-sm-3 col-xs-12">Taxi Lost & Found (Chinese)</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    <textarea class="form-control col-md-7 col-xs-12 editor" name="options[taxi_lost_found_mn]" id="editor">{{ old('options.taxi_lost_found_mn', getOption('taxi_lost_found_mn')) }}</textarea>
                </div>
              </div>

              <div class="form-group">
                <label for="contact" class="control-label col-md-3 col-sm-3 col-xs-12">Taxi Lost & Found (Tamil)</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    <textarea class="form-control col-md-7 col-xs-12 editor" name="options[taxi_lost_found_ta]" id="editor">{{ old('options.taxi_lost_found_ta', getOption('taxi_lost_found_ta')) }}</textarea>
                </div>
              </div>

              <div class="form-group">
                <label for="contact" class="control-label col-md-3 col-sm-3 col-xs-12">Taxi Lost & Found (Thai)</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    <textarea class="form-control col-md-7 col-xs-12 editor" name="options[taxi_lost_found_th]" id="editor">{{ old('options.taxi_lost_found_th', getOption('taxi_lost_found_th')) }}</textarea>
                </div>
              </div> -->

              <div class="form-group">
                <label for="contact" class="control-label col-md-3 col-sm-3 col-xs-12">Remittance T&C</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    <textarea class="form-control col-md-7 col-xs-12 editor" name="options[remittance_terms]" id="editor1">{{ old('options.remittance_terms', getOption('remittance_terms')) }}</textarea>
                </div>
              </div>
              
              <div class="form-group">
                <label for="contact" class="control-label col-md-3 col-sm-3 col-xs-12">How To</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    <textarea class="form-control col-md-7 col-xs-12 editor" name="options[flexm_howto_content]" id="editor">{{ old('options.flexm_howto_content', getOption('flexm_howto_content')) }}</textarea>
                </div>
              </div>
              <!-- <div class="form-group">
                <label for="contact" class="control-label col-md-3 col-sm-3 col-xs-12">Taxi Feedback (Bengali)</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    <textarea class="form-control col-md-7 col-xs-12 editor" name="options[taxi_feedback_bn]" id="editor1">{{ old('options.taxi_feedback_bn', getOption('taxi_feedback_bn')) }}</textarea>
                </div>
              </div>

              <div class="form-group">
                <label for="contact" class="control-label col-md-3 col-sm-3 col-xs-12">Taxi Feedback (Chinese)</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    <textarea class="form-control col-md-7 col-xs-12 editor" name="options[taxi_feedback_mn]" id="editor1">{{ old('options.taxi_feedback_mn', getOption('taxi_feedback_mn')) }}</textarea>
                </div>
              </div>

              <div class="form-group">
                <label for="contact" class="control-label col-md-3 col-sm-3 col-xs-12">Taxi Feedback (Tamil)</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    <textarea class="form-control col-md-7 col-xs-12 editor" name="options[taxi_feedback_ta]" id="editor1">{{ old('options.taxi_feedback_ta', getOption('taxi_feedback_ta')) }}</textarea>
                </div>
              </div>

              <div class="form-group">
                <label for="contact" class="control-label col-md-3 col-sm-3 col-xs-12">Taxi Feedback (Thai)</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    <textarea class="form-control col-md-7 col-xs-12 editor" name="options[taxi_feedback_th]" id="editor1">{{ old('options.taxi_feedback_th', getOption('taxi_feedback_th')) }}</textarea>
                </div>
              </div> -->

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
