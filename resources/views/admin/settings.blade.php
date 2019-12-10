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
            <h2>Settings</h2>
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

              @if(Auth::user()->hasRole('food-admin'))
               
              <div class="form-group">
                <label for="contact" class="control-label col-md-3 col-sm-3 col-xs-12">Base rate (Naanstap Charge)</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    <input type="text" class="form-control col-md-7 col-xs-12" name="options[naanstap_base_rate]" value="{{ old('options.naanstap_base_rate', getOption('naanstap_base_rate')) }}">
                </div>
              </div>

              <div class="form-group">
                <label for="contact" class="control-label col-md-3 col-sm-3 col-xs-12">Per KM rate (Naanstap Charge)</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    <input type="text" class="form-control col-md-7 col-xs-12" name="options[naanstap_km_rate]" value="{{ old('options.naanstap_km_rate', getOption('naanstap_km_rate')) }}">
                </div>
              </div>

              <div class="form-group">
                <label for="contact" class="control-label col-md-3 col-sm-3 col-xs-12">Dormitory standard rate (Naanstap Charge)</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    <input type="text" class="form-control col-md-7 col-xs-12" name="options[dormitory_standard_rate]" value="{{ old('options.dormitory_standard_rate', getOption('dormitory_standard_rate')) }}">
                </div>
              </div>

              <div class="form-group">
                <label for="bank_name" class="control-label col-md-3 col-sm-3 col-xs-12">Naanstap Terms & Conditions </label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <textarea name="options[naanstap_terms]" class="form-control editor" id="ckeditor2">{{ old('options.naanstap_terms', getOption('naanstap_terms')) }}</textarea>
                </div>
              </div>

              @else
              <div class="form-group">
                  <label for="contact" class="control-label col-md-3 col-sm-3 col-xs-12">MRT Map <span class="required">*</span></label>
                  <div class="col-md-6 col-sm-9 col-xs-12">
                      <input type="file" name="mrt_map file_input fancy_upload" />
                      @if(getOption('mrt_map'))
                      <img src="{{ static_file(getOption('mrt_map')) }}" height="200"/>
                      @endif
                      <!-- <div class="cropme" style="width: 640px; height: 410px;"></div>
                      <input type="hidden" class="form-control col-md-7 col-xs-12" name="options[mrt_map]" value="{{ old('options.mrt_map', getOption('mrt_map')) }}"> -->
                  </div>
              </div>

              <div class="form-group">
                <label for="contact" class="control-label col-md-3 col-sm-3 col-xs-12">Gst(%) <span class="required">*</span></label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    <input type="text" class="form-control col-md-7 col-xs-12" placeholder="Default is 7%" name="options[gst_tax]" value="{{ old('options.gst_tax', getOption('gst_tax')) }}">
                </div>
              </div>

              <div class="form-group">
                <label for="contact" class="control-label col-md-3 col-sm-3 col-xs-12">Contact Us No <span class="required">*</span></label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    <input type="text" class="form-control col-md-7 col-xs-12" name="options[contact_us_no]" value="{{ old('options.contact_us_no', getOption('contact_us_no')) }}">
                </div>
              </div>

              <div class="form-group">
                <label for="contact" class="control-label col-md-3 col-sm-3 col-xs-12">Contact Us Email <span class="required">*</span></label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    <input type="text" class="form-control col-md-7 col-xs-12" name="options[contact_us_email]" value="{{ old('options.contact_us_email', getOption('contact_us_email')) }}">
                </div>
              </div>

              <div class="form-group">
                <label for="contact" class="control-label col-md-3 col-sm-3 col-xs-12">Taxi Lost & Found <span class="required">*</span></label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    <textarea class="form-control col-md-7 col-xs-12 editor" name="options[taxi_lost_found]" id="editor">{{ old('options.taxi_lost_found', getOption('taxi_lost_found')) }}</textarea>
                </div>
              </div>

              <div class="form-group">
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
              </div>

              <div class="form-group">
                <label for="contact" class="control-label col-md-3 col-sm-3 col-xs-12">Taxi Feedback <span class="required">*</span></label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    <textarea class="form-control col-md-7 col-xs-12 editor" name="options[taxi_feedback]" id="editor1">{{ old('options.taxi_feedback', getOption('taxi_feedback')) }}</textarea>

                  <!-- <textarea name="options[taxi_feedback]" class="form-control ckeditor" id="ckeditor1">{{ old('options.taxi_feedback', getOption('taxi_feedback')) }}</textarea> -->
                </div>
              </div>

              <div class="form-group">
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
              </div>

              <!-- <ul>
              <li>Dial 1800 225 4523, Oversees +65 1234 1234</li>
              <li>Press 1 for english</li>
              <li>Press 6 for other Matter</li>
              <li>Press 00 for lost and found</li>
              </ul> -->
              {{-- <div class="form-group">
                <label for="contact" class="control-label col-md-3 col-sm-3 col-xs-12">Purchase Terms <span class="required">*</span></label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <textarea name="options[purchase-terms]" class="form-control ckeditor" id="ckeditor1">{{ old('options.purchase-terms', getOption('purchase-terms')) }}</textarea>
                  <!-- <input id="contact" class="form-control col-md-7 col-xs-12" type="text" name="options[purchase-terms]" value="{{ old('options.purchase-terms', getOption('purchase-terms')) }}"> -->
                </div>
              </div>

              <div class="form-group">
                <label for="bank_name" class="control-label col-md-3 col-sm-3 col-xs-12">Terms of Use <span class="required">*</span></label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <textarea name="options[terms-of-use]" class="form-control ckeditor" id="ckeditor2">{{ old('options.terms-of-use', getOption('terms-of-use')) }}</textarea>

                  <!-- <input id="bank_name" class="form-control col-md-7 col-xs-12" type="text" name="options[terms_of_use]" value="{{ old('options.terms_of_use', getOption('terms-of-use')) }}"> -->
                </div>
              </div>

              <div class="form-group">
                <label for="bank-account" class="control-label col-md-3 col-sm-3 col-xs-12">Privacy Statement <span class="required">*</span></label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <textarea name="options[privacy-statement]" class="form-control ckeditor" id="ckeditor3">{{ old('options.privacy-statement', getOption('privacy-statement')) }}</textarea>

                  <!-- <input id="bank_account" class="form-control col-md-7 col-xs-12" type="text" name="options[privacy_statement]" value="{{ old('options.privacy_statement', getOption('privacy_statement')) }}" > -->
                </div>
            </div> --}}
            @endif
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
