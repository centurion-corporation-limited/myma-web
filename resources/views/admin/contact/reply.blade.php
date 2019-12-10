@extends('layouts.admin')

@section('content')

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Reply</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <form id="demo-form2" action="{{ route('admin.contact.reply', encrypt($item->id)) }}" method="POST" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left">
              {{ csrf_field() }}

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Feedback</label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                  {{ $item->content }}
                </div>
              </div>


              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Reply</label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                  @foreach($item->replies as $reply)
                    <div class="row col-md-12">
                        {{ $reply->feedback }}
                    </div>
                  @endforeach
                </div>
              </div>



              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="feedback">Message</label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                  <input type="feedback" id="feedback" name="feedback" value="{{ old('feedback') }}" class="form-control col-md-7 col-xs-12">
                </div>
              </div>

              <div class="ln_solid"></div>
              <div class="form-group">
                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                  <!-- <button class="btn btn-primary" type="button">Cancel</button> -->
                  <!-- <button class="btn btn-primary" type="reset">Reset</button> -->
                  <button type="submit" class="btn btn-success">Reply</button>
                </div>
              </div>

            </form>
          </div>
        </div>
      </div>
    </div>

@endsection
