@extends('layouts.customer')

@section('styles')
<link href="{{  static_file('assets/admin/vendors/bootstrap-daterangepicker/daterangepicker.css') }}" rel="stylesheet">
<link href="{{  static_file('js/plugins/bootstrap-datepicker-master/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">

<link href="{{ static_file('js/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
<style>
.select2-container{
    width:100% !important;
}
.payment_li, .total_li {
	border-left: 1px solid #96072c;
}

.page-content {
	height: calc(100% - 94px);
}
</style>
@endsection
@section('header')
<header class="header">
<h2>Checkout</h2>
<span class="back-btn"><a href="javascript:;" onclick="window.history.go(-1); return false;"><img src="{{ static_file('customer/images/icon-back-arrow.png') }}" alt=""></a></span>
</header>
@endsection
@section('content')

<div class="page-content">
<div class="content-pages">
 <div class="tabs">
 <li class="@if(!$is_logged_in) active @endif address_li"><a class="@if(!$is_logged_in) active @endif" href="javascript:;">Address</a></li>
 <li class="total_li"><a href="javascript:;">Total</a></li>
 <li class="@if($is_logged_in) active @endif payment_li"><a class="@if($is_logged_in) active @endif" href="javascript:;">Pay</a></li>
 </div>
 @if(!$pay)
 <form method="post"  class="clearfix" action="{{ route('food.customer.payment') }}" >
     {{ csrf_field() }}
@endif
 <div class="address @if($is_logged_in) hide @endif">
     <div class="food-forum">
         <div class="form-group  clearfix">
            <div class="custom-radio-form">
              <input id="s1" type="radio" name="deliver_type" class="" value="reception" checked>
              <label for="s1">Deliver at Reception</label>
            </div>
         </div>
         <div class="form-group clearfix @if($type == 'package') hide @endif" >
            <div class="custom-radio-form ">
              <input id="s2" type="radio" name="deliver_type" class="" value="inperson">
              <label for="s2">Deliver-in-person at dormitory (Extra charge)</label>
            </div>
         </div>
         <div class="form-group clearfix @if($type == 'package') hide @endif">
            <div class="custom-radio-form ">
              <input id="s3" type="radio" name="deliver_type" class="" value="inperson_address">
              <label for="s3">Deliver-in-person within Singapore (Extra charge)</label>
            </div>
         </div>
      <!-- <div class="form-topic">
        <label>
            <input type="radio" name="deliver_type" class="" value="reception" checked> Deliver at Reception
        </label></br>
        <label>
            <input type="radio" name="deliver_type" class="" value="inperson"> Deliver in-person
        </label>
      </div> -->
      @if($type == 'single')
      <div class="form-topic">
          <input value="{{ old('delivery_date', session('checkout.delivery_date')) }}" onfocus="blur();" type="text" class="delivery_date form-control" placeholder="Delivery Date*" name="delivery_date" pattern="^(0?[1-9]|[12][0-9]|3[01])[\/\-](0?[1-9]|1[012])[\/\-]\d{4}$">
      </div>
      <div class="form-topic blocked_time">
          {!!Form::select('delivery_time', $block, old('delivery_time', session('checkout.delivery_time')), ['class' => 'form-control form-control-topic date_area'])!!}
      </div>
      <div class="form-topic hide full_time">
          {!!Form::select('delivery_time', ['07:00 AM' => '07:00 AM', '12:00 PM' => '12:00 PM', '07:00 PM' => '07:00 PM'], old('delivery_time', session('checkout.delivery_time')), ['disabled' => true, 'class' => 'date_area form-control form-control-topic'])!!}
      </div>
      @endif
      @if($type == 'package')
      <div class="form-topic">
          <input value="{{ old('delivery_date', session('checkout.delivery_date')) }}" onfocus="blur();" type="text" class="form-control start_date" placeholder="Start Date*" pattern="^(0?[1-9]|[12][0-9]|3[01])[\/\-](0?[1-9]|1[012])[\/\-]\d{4}$" name="delivery_date">
      </div>
      @endif
      <div class="form-topic">
          <input type="text" class="form-control" placeholder="Name*" value="{{ $auth_user->name }}" disabled>
      </div>
      <div class="form-topic">
          <input type="text" data-mask="0000-0000" class="form-control" placeholder="Phone No*" value="{{ old('phone_no', $auth_user->profile?$auth_user->profile->phone:session('cart_data.phone_no','')) }}" name="phone_no" @if(isset($auth_user->profile->phone)) readonly @endif>
      </div>
      <div class="dorm_address">
          <div class="form-topic">
            <select class="form-control form-control-topic" name="dormitory_id">
              <option value="">Please select</option>
              @foreach($dormitory as $id => $name)
              <option value="{{ $id }}">{{ $name }}</option>
              @endforeach
            </select>
            {{-- {!!Form::select('dormitory_id', $dormitory, $auth_user->profile?$auth_user->profile->dormitory_id:'', ['class' => 'form-control form-control-topic'])!!} --}}
          </div>
      </div>
      <!-- <div class="or-text">
      ------------OR--------------
      </div> -->
      <div class="postal_address">
          <div class="form-topic">
              <select class="form-control" name="saved_address_id" id="e6">
                  <option value="">Select an option</option>
                @foreach($saved_address as $add)
                  <option value="{{ $add->id }}" latitude="{{ $add->latitude }}" longitude="{{ $add->longitude }}" >{{ $add->address }}</option>
                @endforeach
              </select>
          </div>
          <div class="text-center"> -OR- </div>
          <div class="">
              <select class="form-control" name="address_id" id="e7"></select>
          </div>
          <div class="form-topic blk_no hide">
              <input type="hidden" name="address" value="{{ session('cart_data.address') }}">
              <input type="hidden" name="latitude" value="{{ session('cart_data.latitude') }}">
              <input type="hidden" name="longitude" value="{{ session('cart_data.longitude') }}">
              <div class="row">
                  <div class="col-xs-6">
                    <label>BLK</label>
                      <input type="text" name="block_no_1" data-mask="00" class="form-control" placeholder="00" value="{{ session('checkout.block_no_1') }}">
                  </div>
                  <div class="col-xs-6">
                    <label>Unit</label>
                      <input type="text" name="block_no_2" data-mask="000" class="form-control" placeholder="000" value="{{ session('checkout.block_no_2') }}">
                      <input type="hidden" name="naanstap" value="{{ session('cart_data.naanstap') }}">
                      <input type="hidden" name="distance" value="{{ session('cart_data.distance') }}">
                  </div>
              </div>
              <!-- <input type="text" name="room_no" class="form-control" placeholder="Room No*" value=""> -->
          </div>
      </div>

      <div class="checkout tab-btns-grup">
        <ul>
          <li><a href="{{ route('food.customer.cart') }}" class="btn top-btn">Cancel</a></li>
          <li class="current"><button type="button" class="btn top-btn total_li">Next</button></li>
        </ul>
      </div>

    </div>
</div>
<div class="total hide">
    <div class="total-pay">
        <ul>
            <li>
                <label>Sub Total:</label>
                <span class="total">S${{ number_format($sub_total,2) }} </span>
            </li>
            <li>
                <label>Discount:</label>
                <span class="discount">S${{ number_format($dis_val,2) }} </span>
            </li>
            <li>
                <label>Naanstap Charge:</label>
                <span class="charge">S$<span class="naanstap">0.00</span> </span>
            </li>
            <li>
                <label>Total:</label>
                <span class="total val">S${{ number_format($total,2) }}</span>
            </li>
        </ul>

    </div>
    <div class="checkout tab-btns-grup">
      <input type="hidden" name="pay" value="{{ $pay }}">
      <ul>
      <li>
        <a href="{{ route('food.customer.cart') }}" class="btn top-btn">Cancel</a>
      </li>
      @if($pay)

        <li class="current">
          <button type="button" class="btn top-btn payment_li">Next</button>
        </li>
      @else
        <li class="current">
          <button type="submit" class="btn top-btn">Next</button>
        </li>
      </form>
      @endif

      </ul>
    </div>

</div>

<div class="payment @if(!$is_logged_in) hide @endif">
<div class="total">
    <div class="total-pay">
        <ul>
            <li>
                <label>Wallet Balance:</label>
                <span class="total_span">S${{ $wallet or 0 }} </span>
            </li>
            <!-- <li>
                <label>Total Amount:</label>
                <span class="total_span val">S${{ $total }}</span>
            </li>
            <li>
                <label>Flexm Transaction charges:</label>
                <span class="total_span val">S${{ $charges }}</span>
            </li> -->
            <li>
                <label>Total Amount to Pay:</label>
                <span class="total_span val">S${{ number_format($total,2) }}</span>
            </li>
        </ul>
        <div class="term-sec">
            <div class="chkbox">
                <input type="checkbox" name="tnc" value="1" class="checkbox">
                <span></span>
            </div>
            <label>I abide by naanstap <a href="{{ route('naanstap.tnc') }}">terms and conditions</a>.</label>
        </div>
    </div>
 <!-- <div class="form-topic">
 <label>Account Holder Name</label>
   <input type="text" class="form-control" placeholder="Name" name="card_name" value="test" required>
 </div>
 <div class="form-topic">
 <label>Card Number</label>
   <input type="text" class="form-control" data-mask="0000-0000-0000-0000" value="1234567887654321" placeholder="xxxx xxxx xxxx xxxx" name="card_number" required>
 </div>
  <div class="form-topic-sm">
 <label>EXP Month</label>
    <input type="number" data-mask="00" max="12" min="1" class="form-control" value="12" placeholder="MM" required name="card_month">

 </div>
    <div class="form-topic-sm">
    <label>EXP Year</label>
      <input type="number" data-mask="0000" max="2100" min="2018" class="form-control" value="2019" placeholder="YYYY" name="card_year" required>
 </div>
   <div class="form-topic-sm">
 <label>CVV</label>
   <input type="password" data-mask="0000" class="form-control" name="card_cvv" value="123" required>
 </div> -->

  <div class="top-up-row">
     @if($pay)
        <form method="post" id="pay_form" action="{{ route('food.customer.pay') }}" >
             {{ csrf_field() }}
            <input type="hidden" name="wallet" value="{{ $wallet or 0 }}">
            <input type="hidden" name="total" value="{{ $total }}">
     @endif

     <div class="checkout tab-btns-grup">
      <input name="pay" value="0" type="hidden">
      <ul>
      <li>
      <a href="{{ route('food.customer.cart') }}" class="btn top-btn">Cancel</a>
      </li>
              <li class="current">
        <button type="submit" class="pay_btn btn top-btn">Pay Now <i class="fa fa-spinner fa-spin hide"></i></button>
        </li>


      </ul>
    </div>

 </div>
</div>
</div>
</form>
</div>
</div>
@endsection
@section('back-button')
<li><a href="javascript:;" onclick="window.history.go(-1); return false;"><span><img src="{{ static_file('images/icon-arrow-left.png') }}" alt=""></span>Back</a></li>
@endsection
@section('scripts')
<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.js'></script>
<script src="{{ static_file('js/plugins/select2/js/select2.full.min.js') }}"></script>

<script src="{{  static_file('assets/admin/vendors/moment/min/moment.min.js') }}"></script>
<script src="{{  static_file('assets/admin/vendors/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<script src="{{  static_file('js/plugins/bootstrap-datepicker-master/dist/js/bootstrap-datepicker.min.js') }}"></script>

<script>
$(document).on('ready', function(){
  $("#pay_form").on('submit', function(){

      if(!$('[name=tnc]').prop('checked')){
        alert("You need to abide by naanstap terms and conditions to proceed.");
        return false;
      }
      $('.pay_btn').addClass('disabled');
      $('.pay_btn').find('i').removeClass('hide');
  });
});

$('input').on('focus', function () {
    // $('.footer').addClass('hidden');
});

/* Reveal footer when input is blurred */
$('input').on('blur', function () {
    // $('.footer').removeClass('hidden');
});
$('.delivery_date').datepicker({
   format:"dd/mm/yyyy",
   startDate: moment().add({{ $day }}, 'days').format('D/M/Y')
}).on('changeDate',function(val){
    var val = $(this).val();
    var after = val.split('/');

    var start = new Date(after[2],(after[1]-1), after[0]),
    end   = new Date(),
    diff  = new Date(start - end),
    days  = diff/1000/60/60/24;
    if(days > 1){
        $('.blocked_time').addClass('hide');
        $('.full_time').removeClass('hide');
        $('.full_time .date_area').attr('disabled', false);
        $('.blocked_time .date_area').attr('disabled', true);
    }else{
        $('.blocked_time').removeClass('hide');
        $('.full_time').addClass('hide');
        $('.full_time .date_area').attr('disabled', true);
        $('.blocked_time .date_area').attr('disabled', false);
    }
});

$('.delivery_date').mask('00/00/0000');

$('.start_date').datepicker({
   format:"dd/mm/yyyy",
   startDate: moment().add({{ $day }}, 'days').format('D/M/Y')
 });

$('.start_date').mask('00/00/0000');

localStorage.setItem('total', {{ $total }});
$('[name=deliver_type]').on('change', function(){
    var val = $('[name=deliver_type]:checked').val();

    if(val == 'inperson_address'){
        $('.dorm_address').addClass('hide');
        $('.postal_address').removeClass('hide');
    }else{
        $('.postal_address').addClass('hide');
        $('.dorm_address').removeClass('hide');
    }
});

$('[name=deliver_type]').trigger('change');
$(document).on('click', '.tabs li.address_li', function(){
    $('.tabs li.address_li').addClass('active');
    $('.tabs li.address_li a').addClass('active');
    $('.address').removeClass('hide');

    $('.tabs li.payment_li').removeClass('active');
    $('.tabs li.payment_li a').removeClass('active');
    $('.payment').addClass('hide');
    $('.tabs li.total_li').removeClass('active');
    $('.tabs li.total_li a').removeClass('active');
    $('.total').addClass('hide');
});

$(document).on('click', '.total_li', function(){
    var val = $('[name=deliver_type]:checked').val();
    if( val == 'reception'){
        localStorage.setItem('naanstap', 0);
    }

    if( val == 'inperson'){
        var naanstap_std = {{ $naanstap_std_charge }};
        var charge = naanstap_std*{{ $flexm_per}}/100;
        charge = (naanstap_std+charge);

        localStorage.setItem('naanstap', charge);
    }

    if( $("[name=delivery_date]").val() == '' ) {
        @if($type == 'single')
            alert('Delivery date is required.');
        @else
            alert('Start date is required.');
        @endif
        return false
    }
    if( ($("[name=phone_no]").val() == '') ) {
        alert('Phone no is required.');
        return false
    }else if((val == 'reception' && $("[name=dormitory_id]").val() == '')){
        alert('Select a dorm to proceed.');
        return false
    }else if((val == 'inperson' && $("[name=dormitory_id]").val() == '')){
        alert('Select a dorm to proceed.');
        return false
    }else if((val == 'inperson_address' && $("[name=address]").val() == '')){
        alert('Add an address.');
        return false
    }else{
        var naan = localStorage.getItem('naanstap');
        var total = localStorage.getItem('total');
        naan = parseFloat(naan).toFixed(2);
        total = (parseFloat(total)+parseFloat(naan)).toFixed(2);


        $('.naanstap').text(naan);
        $('span.total.val').text('S$'+total);
        $('[name=naanstap]').val(naan);
        $('.tabs li.total_li').addClass('active');
        $('.tabs li.total_li a').addClass('active');
        $('.total').removeClass('hide');

        $('.tabs li.address_li').removeClass('active');
        $('.tabs li.address_li a').removeClass('active');
        $('.address').addClass('hide');
        $('.tabs li.payment_li').removeClass('active');
        $('.tabs li.payment_li a').removeClass('active');
        $('.payment').addClass('hide');
    }
});

$(document).on('click', '.payment_li', function(){
    var val = $('[name=deliver_type]:checked').val();
    if( $("[name=delivery_date]").val() == '' ) {
        alert('Start date is required.');
        return false
    }
    if( ($("[name=phone_no]").val() == '') ) {
        alert('Phone No is required.');
        return false
    }else if((val == 'inperson_address' && $("[name=address]").val() == '')){
        alert('Add an address.');
        return false
    }
    // if( (val == 'reception' && $("[name=phone_no]").val() == '') ) {
    //     alert('Phone No is required.');
    // }else if((val == 'inperson' && $("[name=address]").val() == '')){
    //     alert('Add an address.');
    // }
    else{
        $('.tabs li.payment_li').addClass('active');
        $('.tabs li.payment_li a').addClass('active');
        $('.address').addClass('hide');

        $('.tabs li.address_li').removeClass('active');
        $('.tabs li.address_li a').removeClass('active');
        $('.payment').removeClass('hide');
        $('.tabs li.total_li').removeClass('active');
        $('.tabs li.total_li a').removeClass('active');
        $('.total').addClass('hide');

    }
});

var dest = '';
var origin = {lat: 1.34275276800003, lng: 103.95019519};

  function formatRepo(repo) {
      if (repo.loading) {
          return repo.text;
      }
      return repo.ADDRESS;
  }

  function formatRepoSelection(repo) {

       if(repo.BLK_NO != undefined){
           $('.blk_no').removeClass('hide');
           // $('[name=block_no]').val(repo.BLK_NO);
       }
       if(repo.ADDRESS != undefined){
           $('[name=address]').val(repo.ADDRESS);
           $('[name=latitude]').val(repo.LATITUDE);
           $('[name=longitude]').val(repo.LONGITUDE);
            dest = {lat: parseFloat(repo.LATITUDE), lng:parseFloat(repo.LONGITUDE)};
            initMap(origin, dest);
       }
      return repo.ADDRESS || repo.text;
   }

$("#e7").select2({
    ajax: {
      url: "{{ route('ajax.get.address') }}",
      method: 'POST',
      dataType: 'json',
      delay: 250,
      data: function (params) {
        return {
          q: params.term, // search term
          page: params.page
        };
      },
      processResults: function (data, params) {
        params.page = params.page || 1;

        return {
          results: data.items,
          pagination: {
            more: (params.page * 10) < data.total_count
          }
        };
      },
      cache: true
    },

    placeholder: 'Search for address by Postal code',
    escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
    minimumInputLength: 3,
    templateResult: formatRepo,
    templateSelection: formatRepoSelection
});

$("#e6").select2({
    placeholder: 'Choose from saved addresses',
});

</script>

<script>
      function initMap(origin, destination) {

        // var destinationB = {lat: 50.087, lng: 14.421};

        var service = new google.maps.DistanceMatrixService;
        service.getDistanceMatrix({
          origins: [origin],
          destinations: [destination],
          travelMode: 'DRIVING',
          unitSystem: google.maps.UnitSystem.METRIC,
          avoidHighways: false,
          avoidTolls: false
        }, function(response, status) {
          if (status !== 'OK') {
            alert('Error was: ' + status);
          } else {
            var originList = response.originAddresses;
            var destinationList = response.destinationAddresses;
            var outputDiv = document.getElementById('output');
            var km_val = 0;

            for (var i = 0; i < originList.length; i++) {
              var results = response.rows[i].elements;

              for (var j = 0; j < results.length; j++) {
                km_val = results[j].distance.value/1000;
              }
            }
            var base_rate = {{ getoption('naanstap_base_rate', 0) }};
            var km_rate = {{ getoption('naanstap_km_rate', 0) }};
            var total = (base_rate + km_val*km_rate).toFixed(2);
            localStorage.setItem('naanstap',total);
            $('[name=distance]').val(km_val);
            // return km_val;
          }
        });
      }

      $('[name=saved_address_id]').on('change', function(){
        $('[name=address_id]').val('');
        var obj = $(this);
        dest = {lat: parseFloat(obj.attr('latitude')), lng:parseFloat(obj.attr('longitude'))};
        initMap(origin, dest);
      });

      $('[name=address_id]').on('change', function(){
        $('[name=saved_address_id]').val('');
      });

    </script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCkSPjh4Y3-xSptr_UITq3RtpYe5Fwa0ho">
    </script>
@endsection
