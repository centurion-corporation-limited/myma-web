@extends('layouts.admin')

@section('styles')
<link href="{{  static_file('js/plugins/datetimepicker/build/css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet">
<link href="{{ static_file('js/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
<style>
.form-group{
    line-height: inherit;
}
.form-group input[type="radio"] {
	width: auto;
}
.select2.select2-container.select2-container--default, .select2-search__field{
    width: 100% !important;
}
</style>
@endsection
@section('content')

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Send Notification</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <br />
            <form id="demo-form2" action="{{ route('admin.notification.add') }}" method="POST" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left">
              {{ csrf_field() }}

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="sendto">Send to <span class="required">*</span></label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    <label><input type="radio" id="sendto" name="sendto" @if(old('sendto') == 'all' || true) checked @endif value="all" class="col-md-7 col-xs-12">All(App users)</label>
                    <label><input type="radio" id="sendto" name="sendto" @if(old('sendto') == 'specific') checked @endif value="specific" class="col-md-7 col-xs-12">Specific User</label>
                    <label><input type="radio" id="sendto" name="sendto" @if(old('sendto') == 'dormitory') checked @endif value="dormitory" class="col-md-7 col-xs-12">Dormitory-Wise(App users)</label>
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="sendto">Messaage Type <span class="required">*</span></label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    <label><input type="radio" name="format" @if(old('format') == 'text' || true) checked @endif value="text" class="col-md-7 col-xs-12">Text</label>
                    <label><input type="radio" name="format" @if(old('format') == 'image') checked @endif value="image" class="col-md-7 col-xs-12">Image</label>
                    <label><input type="radio" name="format" @if(old('format') == 'video') checked @endif value="video" class="col-md-7 col-xs-12">Video</label>
                </div>
              </div>

              <div class="form-group hide specific_user">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="merchant_id">User <span class="required">*</span></label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    {!!Form::select('user_id[]', $users, '', ['id' => 'user_id' , 'multiple' => 'multiple', 'class' => 'form-control'])!!}
                </div>
              </div>

              <div class="form-group hide upload_div">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="file_id">Upload <span class="required">*</span></label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    {!!Form::file('file', ['id' => 'file_id'])!!}
                </div>
              </div>

              <div class="form-group hide dormitory_type">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="merchant_id">Dormitory <span class="required">*</span></label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    {!!Form::select('dormitory_id[]', $dormitories, '', ['id' => 'dormitory_id' , 'multiple' => 'multiple', 'class' => 'form-control',])!!}
                </div>
              </div>

              {{-- <div class="form-group">
                  <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Type</label>
                  <div class="col-md-6 col-sm-9 col-xs-12">
                    {!!Form::select('type', $type, '', ['class' => 'form-control', 'required' => 'true'])!!}
                  </div>
              </div> --}}

              <div class="form-group text_div">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Message (Can be any language) <span class="required">*</span></label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <textarea id="name" name="message"  class="editor form-control">{{ old('message') }}</textarea>
                </div>
              </div>

              <!-- <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name_bn">Message(Bengali)</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <textarea id="name_bn" name="message_bn" class="form-control">{{ old('message_bn') }}</textarea>
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name_mn">Message(Chinese)</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <textarea id="name_mn" name="message_mn" class="form-control">{{ old('message_mn') }}</textarea>
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name_ta">Message(Tamil)</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <textarea id="name_ta" name="message_ta" class="form-control">{{ old('message_ta') }}</textarea>
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name_th">Message(Thai)</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <textarea id="name_th" name="message_th" class="form-control">{{ old('message_th') }}</textarea>
                </div>
              </div> -->

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="time">Send at (Leave empty if want to send right now.) </label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    <input type="text" id="time" name="send_at" class="form-control">
                </div>
              </div>

              <div class="ln_solid"></div>
              <div class="form-group">
                <div class="col-md-2 col-sm-2 col-xs-12"></div>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <button type="submit" class="btn btn-success">Send</button>
                </div>
              </div>

            </form>
          </div>
        </div>
      </div>
    </div>

@endsection
@section('scripts')
<script src="{{ static_file('js/plugins/select2/js/select2.full.min.js') }}"></script>
<script src="{{ static_file('js/plugins/datetimepicker/build/js/bootstrap-datetimepicker.min.js') }}"></script>
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
<script>

$('[name="sendto"]').on('change',function(){
    var val = $('[name="sendto"]:checked').val();
    if(val == 'specific'){
        $('.specific_user').removeClass('hide');
        $('.dormitory_type').addClass('hide');
    }else if(val == "dormitory"){
        $('.specific_user').addClass('hide');
        $('.dormitory_type').removeClass('hide');
    }else{
        $('.specific_user').addClass('hide');
        $('.dormitory_type').addClass('hide');
    }
});

$('[name=format]').on('change',function(){
    var val = $('[name=format]:checked').val();
    if(val == 'text'){
        // $('.text_div').removeClass('hide');
        $('.upload_div').addClass('hide');
    }else{
        $('.upload_div').removeClass('hide');
        // $('.text_div').addClass('hide');
    }
});
$(document).ready(function(){
    $('[name=sendto]').trigger('change');
    $('[name=format]').trigger('change');
  //   $('#time').daterangepicker({
  //   singleDatePicker: true,
  //   timePicker: true,
  //   // showDropdowns: true,
  //   // minYear: 2018,
  //   // maxYear: parseInt(moment().format('YYYY'),10),
  //   locale: {
  //     format: 'M/DD hh:mm A'
  //   }
  // });

    $('#time').datetimepicker({
      minDate: moment().add(0, 'h')
    });
});
// $("#demo-form2").validate({
//   rules: {
//     message: {
//         required: function(element) {
//             var en = $("[name=message]").val();
//             var mn = $("[name=message_mn]").val();
//             var bn = $("[name=message_bn]").val();
//             var th = $("[name=message_th]").val();
//             var ta = $("[name=message_ta]").val();
//             if(en == '' && mn == '' && bn == '' && th == '' && ta == ''){
//                 return true;
//             }else{
//                 return false;
//             }
//         }
//     },
//     sendto: {
//         required: true
//     },
//     user_id: {
//         required: function(element){
//             if($('[name=sendto]:checked').val() == 'specific'){
//                 return true;
//             }else{
//                 return false;
//             }
//         }
//     },
//     dormitory_id: {
//         required: function(element){
//             if($('[name=sendto]:checked').val() == 'dormitory'){
//                 return true;
//             }else{
//                 return false;
//             }
//         }
//     }
//   }
// });

$("#dormitory_id").select2({
    placeholder: "Select Dormitory",
});

$("#user_id").select2({
    placeholder: "Select Users",
  // tags: true,
  // createTag: function (params) {
  //     console.log(params);
  //     return {
  //       id: params.term,
  //       text: params.term,
  //       newOption: true
  //     }
  // },

});
</script>
@endsection
