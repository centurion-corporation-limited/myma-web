<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>MyMA</title>
<link href="{{  static_file('css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{  static_file('css/font-awesome.min.css') }}" rel="stylesheet">
<link href="{{  static_file('signup/css/style.css') }}" rel="stylesheet" type="text/css">
<link href="{{  static_file('signup/css/responsive.css') }}" rel="stylesheet" type="text/css">
<link href="{{  static_file('js/plugins/cropper/css/style.css') }}" rel="stylesheet">
<link href="{{  static_file('js/plugins/cropper/css/style-example.css') }}" rel="stylesheet">
<link href="{{  static_file('js/plugins/cropper/css/jquery.Jcrop.css') }}" rel="stylesheet">
<link href="{{  static_file('js/plugins/bootstrap-datepicker-master/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
<link href="{{  static_file('js/plugins/bootstrap-fancyfile-master/css/bootstrap-fancyfile.min.css') }}" rel="stylesheet">
<style>
.login-logo img {
	max-height: 100px;
	margin-bottom: 10px;
}
input[type="checkbox"] {
	display: block;
	margin: 0 5px 0 0px;
}
.chk label {
	width: 100%;
	display: flex;
	align-items: center;
	font-size: 15px;
	font-weight: 400;
}
.media {
	margin-top: -12px;
	padding: 10px;
	border-color: #dcdcdc;
}
.fancy-file input[type="file"] {
	padding: 0;
	width: 100% !important;
}
.fancy-file div input {
	padding: 0;
	width: 100% !important;
}
.fancy-file div input {
	padding: 0;
	width: 100% !important;
	font-size: 15px;
	text-overflow: ellipsis;
	overflow: hidden;
}
.fancy-file button, .fancy-file .btn {
	background: url('signup/images/icon-cm.png');
	background-repeat: no-repeat;
	background-position: center;
}
.icon-file.glyphicon {
	opacity: 0;
}
.cropme {
    margin-bottom: 0;
    margin-right: 0;
}
span.help-block {
    font-size: 16px;
}
.form-group-sm {
    margin-right: 10px;
    width: calc(50% - 13px);
}

@media only screen and (max-width: 767px){
	.form-group-sm {
    margin-right: 0;
    width: 100%;
}
}
@media only screen and (max-width: 480px){
	.login-logo img {
    max-height: 70px;
	}
	.login-form {
    width: 80%;
}
.btn-default {
    font-size: 20px;
    width: 100%;
}
.chk label {
    font-size: 16px;
}
.media {
	margin-right: 4px;
}

}
@media only screen and (max-width: 320px){
	.media {
		margin-right: 20px;
	}

	.media + .media {
    margin-top: 20px;
}
}
</style>
</head>
<body class="login">
<div class="login-wrapper">
  <div class="login-logo"> <a href="#"><img src="{{ static_file('signup/images/myMALogo - Copy.png') }}" alt=""></a> </div>
  <div class="login-form">
    @if (isset($errors) && count($errors) > 0)
    <div class="clearfix"></div>
        <?php
            $errs = array_unique($errors->all(), SORT_REGULAR);
        ?>
        <div class="alert alert-danger">
            <ul>
                @foreach ($errs as $error)
                    <li>{!! $error !!}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if (Session::has('flash_message'))
    <div class="clearfix"></div>
        <div class="alert alert-{!! Session::get('flash_level') !!}">
            {!! Session::get('flash_message') !!}
        </div>
    @endif
    <form id="signupForm" action="{{ route('app.register.new') }}" method="POST" enctype='multipart/form-data'>
      {{ csrf_field() }}
			<div class="form-group name">
          <input type="text" class="form-control icon-user" name="name" placeholder="Full Name*" value="{{ old('name', isset($user->name)?$user->name:request('name')) }}" required="" />
					<span class="help-block">
	          <small></small>
	        </span>
      </div>
      <i class="fa fa-info-circle"></i>Enter First and Last Name
			<div class="form-group email">
          <input type="email" class="form-control icon-email" name="email" placeholder="Email" value="{{ old('email') }}" />
					<span class="help-block">
	          <small></small>
	        </span>
      </div>
			<div class="form-group phone">
          <input type="text" class="form-control icon-phone" name="phone" pattern="[0-9]{8}" title="Enter a number." placeholder="Mobile Number*" value="{{ old('phone') }}" required="" />
					<span class="help-block">
	          <small></small>
	        </span>
      </div>

			<div class="form-group password">
          <input type="password" class="form-control icon-pass" name="password" placeholder="Password*" pattern="^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\W])(?=\S*[\d])\S*$" required="" />
					<span class="help-block">
	          <small></small>
	        </span>
      </div>
      <i class="fa fa-info-circle"></i>Password should have at least one number (0-9), one lower case (a-z) and one upper case (A-Z) letter, one special character (~!@#$%^&*()+-?) and minimum length of 8.
			<div class="form-group confirm_password">
          <input type="password" class="form-control icon-pass" name="password_confirmation" placeholder="Confirm Password*" required="" />
					<span class="help-block">
	          <small></small>
	        </span>
      </div>
			<div class="form-group fin_no">
          <input type="text" class="form-control icon-fin" required="" pattern="[STFGstfg]{1}[0-9]{7}[a-zA-Z]{1}" title="Fin no format not correct." name="fin_no" placeholder="Fin No*" value="{{ old('fin_no', isset($user->fin_no)?$user->fin_no:request('fin_no')) }}" />
					<span class="help-block">
	          <small></small>
	        </span>
      </div>
      <h2>Selfie*</h2>
      <div class="media profile_pic dum">
				<!-- <div class="cropme" style="width: 100px; height: 100px;"></div>
        <input type="hidden" name="profile_pic" class="form-control"> -->
        <input type="file" name="profile_pic" required>
				<span class="help-block">
					<small></small>
				</span>
        <!-- <a href="#"><img src="{{ static_file('signup/images/icon-cm.png') }}" alt=""></a>  -->
      </div>
      <h2>Work Permit Photo*</h2>
			<label>Wp Front</label>
      <div class="media wp_front dum">
				<!-- <div class="cropme" style="width: 100px; height: 100px;"></div>
        <input type="hidden" name="wp_front" class="form-control"> -->
        <input type="file" name="wp_front" required>
				<span class="help-block">
					<small></small>
				</span>
        <!-- <a href="#"><img src="{{ static_file('signup/images/icon-cm.png') }}" alt=""></a>  -->
      </div><br><br><br>
			<label>Wp Back</label>
      <div class="media wp_back dum">
				<!-- <div class="cropme" style="width: 100px; height: 100px;"></div>
        <input type="hidden" name="wp_back" class="form-control"> -->
        <input type="file" name="wp_back" required>
				<span class="help-block">
					<small></small>
				</span>
        <!-- <a href="#"><img src="{{ static_file('signup/images/icon-cm.png') }}" alt=""></a>  -->
      </div>
      <h2>WP Expiry</h2>
			<div class="form-group wp_expiry">
					<input type="text" autocomplete="off" class="form-control icon-date" name="wp_expiry" placeholder="dd/mm/yyyy" value="{{ old('wp_expiry', @$user->wp_expiry_formatted) }}" />
					<span class="help-block">
	          <small></small>
	        </span>
			</div>

      <div class="form-group icon-gender gender" >
        <select class="form-control form-controls" name="gender" required>
          <option value="">Gender*</option>
          <option value="male" @if(old('gender') == 'male') selected @endif>Male</option>
          <option value="female" @if(old('gender') == 'female') selected @endif>Female</option>
        </select>
				<span class="help-block">
					<small></small>
				</span>
      </div>

      <div class="form-group dob dum">
        <input type="text" autocomplete="off" name="dob" required value="{{ old('dob', isset($user->dob_formatted)?$user->dob_formatted:request('dob')) }}" class="form-control icon-date-cal" placeholder="Date of Birth*">
        <div class="icon-date-pic"> <a href="javascript:;"><img src="{{ static_file('signup/images/icon-date.png') }}" alt=""></a> </div>
				<span class="help-block">
          <small></small>
        </span>
      </div>
			<div class="form-group icon-dromatry ">
        <select class="form-control form-controls" name="country_id" required>
          <option value="">Select Nationality* </option>
          @foreach($nationalities as $id => $name)
						<option value="{{ $id }}" @if(old('country_id', @$user->country_id) == $id) selected @endif>{{ $name }}</option>
					@endforeach
				</select>
      </div>
      <div class="form-group icon-dromatry ">
        <select class="form-control form-controls" name="dormitory_id" required>
          <option value="">Select Dormitory/Address* </option>
          @foreach($dormitories as $id => $name)
						<option value="{{ $id }}" @if(old('dormitory_id', @$user->dormitory_id) == $id) selected @endif>{{ $name }}</option>
					@endforeach
					<option value="other">Others</option>
        </select>
      </div>
			<div class="hide other_div">
	      <div class="form-group">
	        <input type="text" name="street_address" class="form-control icon-street" placeholder="Street Address*" value="{{ old('street_address') }}">
	      </div>
	      <div class="form-group form-group-sm">
	        <input type="text" name="block" class="form-control form-control-sm" placeholder="Block" value="{{ old('block') }}">
	      </div>
	      <div class="form-group form-group-sm">
	        <input type="text" name="sub_block" class="form-control form-control-sm" placeholder="Sub Block" value="{{ old('sub_block') }}">
	      </div>
	      <div class="form-group form-group-sm">
	        <input type="text" name="unit_no" class="form-control form-control-sm" placeholder="Unit No" value="{{ old('unit_no') }}">
	      </div>
	      <div class="form-group form-group-sm">
	        <input type="text" name="floor_no" class="form-control form-control-sm" placeholder="Floor" value="{{ old('floor_no') }}">
	      </div>
	      <div class="form-group form-group-sm">
	        <input type="text" name="room_no" class="form-control form-control-sm" placeholder="Room No" value="{{ old('room_no') }}">
	      </div>
	      <div class="form-group form-group-sm">
	        <input type="text" name="zip_code" class="form-control form-control-sm" placeholder="Postal Code*" value="{{ old('zip_code') }}">
	      </div>
			</div>
      <div class="chk terms dum">
        <label>
          <input required type="checkbox" name="terms" requird {{ old('terms') }} @if(old('terms') == 1) checked @endif value="1">
          <span>I Agree with <a href="{{ url('terms') }}" target="_blank">terms and conditions</a><span>
        </label>
				<span class="help-block">
					<small></small>
				</span>
      </div>
      <div class="mg-top">
        <button type="submit" id='submit_btn' class="btn btn-default">Create new account> <i class='hide fa fa-spinner fa-spin'></i></button>
      </div>
    </form>
  </div>
</div>
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Error</h4>
      </div>
      <div class="modal-body">
        <p class=error_text> </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
<script src="{{ static_file('js/jquery.min.js') }}"></script>
<script src="{{ static_file('js/bootstrap.min.js') }}"></script>
<script src="{{ static_file('js/plugins/bootstrap-datepicker-master/dist/js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ static_file('js/plugins/cropper/scripts/jquery.Jcrop.js') }}"></script>
<script src="{{ static_file('js/plugins/cropper/scripts/custom.js') }}"></script>
<script defer src="{{  static_file('js/plugins/bootstrap-fancyfile-master/js/bootstrap-fancyfile.min.js') }}"></script>
<script>
$('[name=dob]').datepicker({
   format:"dd/mm/yyyy",
   //startDate: '1'
});
$(document).on('ready', function(){
  $('.fancy_upload').fancyfile({
      text  : '',
      // style : 'btn-info',
      placeholder : ''
  });

	$('.cropme').simpleCropper();
});

$('.icon-date-pic').on('click', function(){
    $('[name=dob]').trigger('focus');
});

$('[name=dormitory_id]').on('change', function(){
		var val = $(this).val();
		if(val == 'other'){
			$('.other_div').removeClass('hide');
			$('[name=street_address]').attr('required', true);
			$('[name=zip_code]').attr('required', true);
		}else{
			$('.other_div').addClass('hide');
			$('[name=zip_code]').attr('required', false);
		}
});

$('[name=wp_expiry]').datepicker({
   format:"dd/mm/yyyy",
   startDate: '1'
});

$('formm').on('submit',function(e){
    	e.preventDefault();
			$('#signupForm .dum').removeClass('has-error');
			$('#signupForm .dum .help-block small').text('');
      var accept = $('[name=terms]').is(':checked');
      if(!accept){
				// alert("Accept terms and conditions to proceed.");
				$('#signupForm .terms').addClass('has-error');
        $('#signupForm .terms .help-block small').text("Accept terms and conditions to proceed.");
				return false;
			}

			var dob = $('[name=dob]').val().split('/');
			var setDate = new Date(parseInt(dob[2]) + 18, parseInt(dob[1]) - 1, parseInt(dob[0]));

			var currdate = new Date();

			if (currdate >= setDate) {
			  //alert("above 18");
			} else {
				$('#signupForm .dob').addClass('has-error');
        $('#signupForm .dob .help-block small').text("Age must be above 18 years to proceed.");
			  // alert("Age must be above 18 years to proceed.");
				return false;
			}

      var profile = $('input[name=profile_pic]').val();//[0].files[0];
      var wp_back = $('input[name=wp_back]').val();//[0].files[0];
      var wp_front = $('input[name=wp_front]').val();//[0].files[0];

			// if($('input[name=profile_pic]')[0].files.length == 0){
			// 	profile = '';
			// }
			//
			// if($('input[name=wp_back]')[0].files.length == 0){
			// 	wp_back = '';
			// }
			//
			// if($('input[name=wp_front]')[0].files.length == 0){
			// 	wp_front = '';
			// }
			$('#submit_btn').addClass('disabled');
			$('#submit_btn').find('i').removeClass('hide');
      // var formData = new FormData();
      // formData.append('name', $('[name=name]').val());
      // formData.append('email', $('[name=email]').val());
      // formData.append('phone', $('[name=phone]').val());
      // formData.append('password', $('[name=password]').val());
      // formData.append('password_confirmation', $('[name=confirm_password]').val());
      // formData.append('fin_no', $('[name=fin_no]').val());
      // formData.append('profile_pic', profile);
      // formData.append('wp_front', wp_front);
      // formData.append('wp_back', wp_back);
      // formData.append('wp_expiry', $('[name=wp_expiry]').val());
      // formData.append('gender', $('[name=gender]').val());
      // formData.append('dob', $('[name=dob]').val());
      // formData.append('street_address', $('[name=street_address]').val());
      // formData.append('block', $('[name=block]').val());
      // formData.append('sub_block', $('[name=sub_block]').val());
      // formData.append('unit_no', $('[name=unit_no]').val());
      // formData.append('floor_no', $('[name=floor_no]').val());
      // formData.append('room_no', $('[name=room_no]').val());
      // formData.append('zip_code', $('[name=zip_code]').val());
      //
      //
      // $.ajax({
      //     method:'post',
      //     url: '{{ route('app.register') }}',
      //     data: formData,
      //     headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
      //     crossDomain: true,
      //     cache: false,
      //     contentType: false,
      //     processData: false,
			// 		beforeSubmit: function(){
			// 				$('#signupForm .dum').removeClass('has-error');
			// 				$('#signupForm .dum .help-block small').text('');
      //
      //         $('#signupForm .form-group').removeClass('has-error');
      //         $('#signupForm .form-group .help-block small').text('');
      //     },
      //     error: function(xhr){
      //         console.log("Error");
      //         console.log(xhr);
      //     },
      //     success: function(data){
      //       console.log(data);
      //         if (data.status == 'success') {
      //             window.location.href="{{ route('signup.form.success') }}";
      //         }else {
      //             //alert(data.message);
			// 						if(data.message.name){
      //                 $('#signupForm .name').addClass('has-error');
      //                 $('#signupForm .name .help-block small').text(data.message.name);
      //             }
      //             if(data.message.email){
      //                 $('#signupForm .email').addClass('has-error');
      //                 $('#signupForm .email .help-block small').text(data.message.email);
      //             }
      //             if(data.message.password){
      //                 $('#signupForm .password').addClass('has-error');
      //                 $('#signupForm .password .help-block small').text(data.message.password);
      //             }
      //             if(data.message.confirmed_password){
      //                 $('#signupForm .confirm_password').addClass('has-error');
      //                 $('#signupForm .confirm_password .help-block small').text(data.message.confirmed_password);
      //             }
      //             if(data.message.fin_no){
      //                 $('#signupForm .fin_no').addClass('has-error');
      //                 $('#signupForm .fin_no .help-block small').text(data.message.fin_no);
      //             }
      //             if(data.message.profile_pic){
      //                 $('#signupForm .profile_pic').addClass('has-error');
      //                 $('#signupForm .profile_pic .help-block small').text(data.message.profile_pic);
      //             }
      //             if(data.message.wp_front){
      //                 $('#signupForm .wp_front').addClass('has-error');
      //                 $('#signupForm .wp_front .help-block small').text(data.message.wp_front);
      //             }
      //             if(data.message.wp_back){
      //                 $('#signupForm .wp_back').addClass('has-error');
      //                 $('#signupForm .wp_back .help-block small').text(data.message.wp_back);
      //             }
			// 						if(data.message.wp_expiry){
      //                 $('#signupForm .wp_expiry').addClass('has-error');
      //                 $('#signupForm .wp_expiry .help-block small').text(data.message.wp_expiry);
      //             }
			// 						if(data.message.gender){
      //                 $('#signupForm .gender').addClass('has-error');
      //                 $('#signupForm .gender .help-block small').text(data.message.gender);
      //             }
			// 						if(data.message.dob){
      //                 $('#signupForm .dob').addClass('has-error');
      //                 $('#signupForm .dob .help-block small').text(data.message.dob);
      //             }
      //
			// 						// $('.error_text').text(data.message);
			// 						// $('#myModal').modal();
      //         }
      //     },
			// 		complete: function(){
			// 			$('#submit_btn').removeClass('disabled');
			// 			$('#submit_btn').find('i').addClass('hide');
			// 		}
      // });

});
</script>
</body>
</html>
