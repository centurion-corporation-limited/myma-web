@extends('layouts.admin')

@section('styles')
<style>
.btn-group>.btn:last-child:not(:first-child), .btn-group>.dropdown-toggle:not(:first-child){width:64.41px;}
.form-horizontal .control-label{ padding-top:0;}
</style>
@endsection

@section('content')

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>View</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <form id="demo-form2" action="{{ route('admin.jtc.detail.edit', encrypt($item->id) ) }}" method="POST" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left colord-form">
              {{ csrf_field() }}

              @if($item->lang_en)
              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Language :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  English
                </div>
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Title :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{ $item->lang_en->title or '' }}
                </div>
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="content">Content :</label>
                <div class="col-md-6 col-sm-10 col-xs-12" style="line-height: 26px;">
                  {!! $item->lang_en->content or '' !!}
                </div>
              </div>

              <div class="form-group row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="author_id">Author :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    {{ $item->lang_en->author or '--' }}
                </div>
              </div>
              @endif

              @if($item->lang_bn)
              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Language :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  Bengali
                </div>
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Title :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{ $item->lang_bn->title or '' }}
                </div>
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="content">Content :</label>
                <div class="col-md-6 col-sm-10 col-xs-12" style="line-height: 26px;">
                  {!! $item->lang_bn->content or '' !!}
                </div>
              </div>

              <div class="form-group row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="author_id">Author :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    {{ $item->lang_bn->author or '--' }}
                </div>
              </div>
              @endif

              @if($item->lang_mn)
              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Language :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  Chinese
                </div>
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Title :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{ $item->lang_mn->title or '' }}
                </div>
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="content">Content :</label>
                <div class="col-md-6 col-sm-10 col-xs-12" style="line-height: 26px;">
                  {!! $item->lang_mn->content or '' !!}
                </div>
              </div>

              <div class="form-group row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="author_id">Author :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    {{ $item->lang_mn->author or '--' }}
                </div>
              </div>
              @endif

              @if($item->lang_ta)
              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Language :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  Tamil
                </div>
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Title :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{ $item->lang_ta->title or '' }}
                </div>
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="content">Content :</label>
                <div class="col-md-6 col-sm-10 col-xs-12" style="line-height: 26px;">
                  {!! $item->lang_ta->content or '' !!}
                </div>
              </div>

              <div class="form-group row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="author_id">Author :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    {{ $item->lang_ta->author or '--' }}
                </div>
              </div>
              @endif

              @if($item->lang_th)
              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Language :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  Thai
                </div>
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Title :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{ $item->lang_th->title or '' }}
                </div>
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="content">Content :</label>
                <div class="col-md-6 col-sm-10 col-xs-12" style="line-height: 26px;">
                  {!! $item->lang_th->content or '' !!}
                </div>
              </div>

              <div class="form-group row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="author_id">Author :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    {{ $item->lang_th->author or '--' }}
                </div>
              </div>
              @endif

              <div class="form-group row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="author_id">Type :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    @if($item->type == 'event-news')
                      Event & Attraction
                    @else
                      @if($item->type == 'custom3')
                      Embassy
                      @else
                      {{ ucfirst($item->type) }}
                      @endif
                    @endif
                </div>
              </div>

              @if($item->type == 'event-news')
              <div class="form-group row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="author_id">Dormitory :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    {{ $item->dormitory->name or '--' }}
                </div>
              </div>
              @endif
              <div class="form-group row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12">Author Image :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    @if($item->author_image)
                  <img src="{{ static_file($item->author_image) }}" height="150" />
                  @endif
                </div>
              </div>

              <div class="form-group row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12">Image :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    @if($item->image)
                  <img src="{{ static_file($item->image) }}" height="150" />
                  @endif
                </div>
              </div>

              <div class="form-group row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12">Published :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <div id="gender" class="btn-group" data-toggle="buttons">
                    <label class="btn @if(old('publish', $item->publish ) == '1') btn-primary active @else btn-default @endif" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                    &nbsp; Yes &nbsp;
                    </label>
                    <label class="btn @if(old('publish', $item->publish ) == '0') btn-primary active @else btn-default @endif" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                    No
                    </label>
                  </div>
                </div>
              </div>

              <div class="ln_solid"></div>
              <div class="form-group">
                <div class="col-md-2 col-sm-2 col-xs-12"></div>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <!-- <button class="btn btn-primary" type="button">Cancel</button> -->
                  <!-- <button class="btn btn-primary" type="reset">Reset</button> -->
                  <a href="{{ route('admin.jtc.detail.edit', encrypt($item->id)) }}" class="btn btn-success">Edit</a>
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
$('[name=language]').on('change', function(){
    var value = $(this).val();
    $('.language').addClass('hide');
    $('.'+value).removeClass('hide');

});

tinymce.init({
  selector: '.editor',
  height: 300,
  menubar: "tools",
  plugins: [
    'advlist autolink lists link image charmap print preview anchor textcolor',
    'searchreplace visualblocks code fullscreen',
    'insertdatetime media table contextmenu paste code help wordcount'
  ],
  toolbar: 'code | undo redo |  formatselect | bold italic backcolor  | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
  // content_css: [
  //   '//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
  //   '//www.tinymce.com/css/codepen.min.css']
});
</script>
@endsection
