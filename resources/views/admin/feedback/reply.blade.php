@extends('layouts.admin')

@section('styles')
<style>
.userComment {
	margin-bottom: 15px;
	border: 1px solid #ececec;
	border-radius: 2px;
	float: left;
	width: 100%;
}
.user-head {
	background: #f1f4f6;
	width: 100%;
	margin: 0 auto;
	padding: 7px 15px;
	color: #000;
	float: left;
}
.userName {
	float: left;
	font-size: 1.2em;
	max-width: calc(100% - 150px);
	width: 100%;
	white-space: nowrap;
	overflow: hidden;
	text-overflow: ellipsis;
	padding: 2px 0;
}
.time {
	float: right;
	width: 150px;
	text-align: right;
	padding: 4px 0px;
	color: #666;
}
.userData {
	float: left;
	width: 100%;
	padding: 15px;
}
.userName span {
	color: #666;
	font-size: 0.9em;
	padding-right: 5px;
}
</style>

@endsection
@section('content')

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Feedback Reply</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <br />
            <form id="demo-form2" action="{{ route('admin.feedback.reply', encrypt($item->id)) }}" method="POST" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left">
              {{ csrf_field() }}

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Name</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  {{ $item->name }}
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Email</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  {{ $item->email }}
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Phone No</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  {{ $item->phone }}
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Feedback Time</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    <p>{{ $item->created_at->format('d/m/Y H:i A') }}</p>
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Feedback</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  {{ $item->content }}<?php if($item->content_lang != '') { echo '&nbsp; ( '.$item->content_lang.' )'; } ?>
                </div>
              </div>


              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Reply</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  @foreach($item->replies as $reply)
                    <div class="row col-md-12">
                        <div class="userComment">
                            <div class="user-head">
                                <div class="userName"><span>Replied by :</span> {{ $reply->user->name or '--' }} </div>
                                <div class="time">{{ $reply->created_at->format('d/m/Y H:i A') }}</div>
                            </div>
                            <div class="userData"> {{ $reply->feedback }} </div>
                        </div>

                    </div>
                    @endforeach
                </div>
              </div>



              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="feedback">Message <span class="required">*</span></label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <input type="feedback" id="feedback" name="feedback" value="{{ old('feedback') }}" class="form-control">
                </div>
              </div>

              <div class="ln_solid"></div>
              <div class="form-group">
								<div class="col-md-3 col-sm-3 col-xs-12"></div>
                <div class="col-md-6 col-sm-9 col-xs-12">
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

@section('scripts')
<script>
$('#demo-form2').on('submit', function(){
	$('.btn-success').prop('disabled', 'true');
});
</script>
@endsection
