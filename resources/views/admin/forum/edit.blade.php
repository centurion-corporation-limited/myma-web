@extends('layouts.admin')

@section('content')

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Edit Forum</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <br />
            <form id="demo-form2" action="{{ route('admin.forum.edit', encrypt($item->id)) }}" method="POST" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left">
              {{ csrf_field() }}

              <!-- <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="language">Language</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <select id="language" class="form-control" name="language">
                      <option value="en">English</option>
                      <option value="mn">Chinese</option>
                      <option value="ta">Tamil</option>
                      <option value="bn">Bengali</option>
                  </select>
                </div>
              </div> -->

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="title">Title</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    @if($bad_words == 'true')
                    <input type="hidden" id="bad_words" name="bad_words" value="true">
                    @endif
                  <input type="text" id="title" name="title" value="{{ old('title', $item->title) }}" class="control-label">
                </div>
              </div>


              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="content">Content</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <textarea id="content" class="control-label" name="content">{{ old('content', $item->content)}}</textarea>
                </div>
              </div>

              <div class="ln_solid"></div>
              <div class="form-group">
                <div class="col-md-3 col-sm-3 col-xs-12"></div>
                <div class="col-md-6 col-sm-9 col-xs-12">
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
<script>
$('[name=language]').on('change', function(){
    var value = $(this).val();
    $('.language').addClass('hide');
    $('.'+value).removeClass('hide');

});
</script>

@endsection
