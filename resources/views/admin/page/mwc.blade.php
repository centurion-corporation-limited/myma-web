@extends('layouts.admin')

@section('content')

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Manage MWC page links</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <br />
            <form id="demo-form2" action="{{ route('admin.mwc.list') }}" method="POST" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left">
              {{ csrf_field() }}

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Who We Are</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="title" name="options[who_we_are]" value="{{ old('options.who_we_are', getOption('who_we_are')) }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">What We Do</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="title" name="options[what_we_do]" value="{{ old('options.what_we_do', getOption('what_we_do')) }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Help</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="title" name="options[mwc_help]" value="{{ old('options.mwc_help', getOption('mwc_help')) }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Kiosks</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="title" name="options[mwc_kiosks]" value="{{ old('options.mwc_kiosks', getOption('mwc_kiosks')) }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Legal Clinic</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="title" name="options[mwc_legal_clinic]" value="{{ old('options.mwc_legal_clinic', getOption('mwc_legal_clinic')) }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Fair Network</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="title" name="options[fair_network]" value="{{ old('options.fair_network', getOption('fair_network')) }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Contact</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="title" name="options[mwc_contact]" value="{{ old('options.mwc_contact', getOption('mwc_contact')) }}" class="form-control">
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
<script src="{{ static_file('js/plugins/tinymce/tinymce.min.js') }}"></script>
<script>
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
