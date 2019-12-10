@extends('layouts.admin')

@section('content')

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Manage page links</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <br />
            <form id="demo-form2" action="{{ route('admin.links.list') }}" method="POST" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left">
              {{ csrf_field() }}

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">ASPRI</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="title" name="options[aspri_link]" value="{{ old('options.aspri_link', getOption('aspri_link')) }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="games">Games</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="games" name="options[games_link]" value="{{ old('options.games_link', getOption('games_link')) }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="custom1">4D</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="custom1" name="options[4d_link]" value="{{ old('options.4d_link', getOption('4d_link')) }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="custom2">TOTO</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="custom2" name="options[toto_link]" value="{{ old('options.toto_link', getOption('toto_link')) }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="wifi">Wifi</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="wifi" name="options[wifi_link]" value="{{ old('options.wifi_link', getOption('wifi_link')) }}" class="form-control">
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
