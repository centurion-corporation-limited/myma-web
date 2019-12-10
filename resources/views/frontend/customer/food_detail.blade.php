@extends('layouts.customer')

@section('styles')
<link rel="stylesheet" href="{{ static_file('css/owl.carousel.min.css') }}">
<link rel="stylesheet" href="{{ static_file('css/owl.theme.default.min.css') }}">
<style>
.add_cart{margin: 0;}
</style>
@endsection

@section('header')
<header class="header">
  <h2>Details</h2>
  <span class="back-btn"><a href="javascript:;" onclick="window.history.go(-1); return false;"><img src="{{ static_file('customer/images/icon-back-arrow.png') }}" alt=""></a></span>
</header>
@endsection

@section('content')

<div class="page-content content-pages">
  <div class="post-details">
    <div class="row_carousel">
      <div id="myCarousel" class="carousel slide" data-ride="carousel">
        <!-- Indicators -->
        <ol class="carousel-indicators">
            @foreach($item->image as $key => $img)
             <li data-target="#myCarousel" data-slide-to="{{ $key }}"  @if($key == 0 ) class="active" @endif></li>
             @endforeach
        </ol>

        <!-- Wrapper for slides -->
        <div class="carousel-inner">
            @foreach($item->image as $key => $img)
             <div class="item @if($key == 0) active @endif ">
      				<img src="{{ static_file($img) }}" alt="">
      		  </div>
             @endforeach
        </div>
        <!-- <div class="carousel-caption"></div> -->
      </div>
    </div>
      <!-- <a href="#"><img src="{{-- $item->image --}}"></a> -->
      <!-- <span><a href="#"><img src="{{ static_file('customer/images/icon-share.png') }}" alt=""></a></span> -->
    <div class="post-data">
    <h2>{{ $item->name }} <a class="pull-right btn-carat add_cart" href="javascript:;" data-name="{{ $item->name }}" data-id="{{ $item->id }}">Add To Cart</a></h2>
    <p>{{ $item->description }}</p>
    @if($item->is_veg || $item->is_halal) <h2>Food type</h2> @endif
    <p> @if($item->is_veg) Vegetarian @endif @if($item->is_halal) Halal @endif</p>
    @if($item->tags_text != '')
      <h2>Tags</h2>
      <ul class="tags">
        @foreach($item->tags_array as $tt)
          <li>{{ $tt }}</li>
        @endforeach
      </ul>
    @endif
    @if($item->type == 'package')
    <div class="food-box">
      <div class="box">
        <h2>Breakfast</h2>
        <p>{{ $item->breakfast }}</p>
      </div>
      <div class="box">
        <h2>Lunch</h2>
        <p>{{ $item->lunch }}</p>
      </div>
      <div class="box">
        <h2>Dinner</h2>
        <p>{{ $item->dinner }}</p>
      </div>
    </div>
    @endif

    @if($item->percent != '' && $item->percent != 0)

    <h2>Feedback</h2>
    <p>{{ $item->percent }}<i class="fa fa-star"></i></p>
    <h2>Rating</h2>
    <p>{{ $item->ratings }}+</p>

    @endif
  </div>
  </div>
</div>

@endsection
@section('back-button')
<li><a href="javascript:;" onclick="window.history.go(-1); return false;"><span><img src="{{ static_file('images/icon-arrow-left.png') }}" alt=""></span>Back</a></li>
@endsection
@section('scripts')
<script src="{{  static_file('js/owl.carousel.js') }}"></script>
<script>
$(document).on('click', '.add_cart', function(){
    var obj = $(this);
    var item_id = $(this).data('id');
    var item_name = $(this).data('name');
    $.ajax({
        url:'{{ route("ajax.add.cart") }}',
        type: "POST",
        data: {id : item_id},
        success: function(data){
             var res = JSON.parse(data);
             console.log(res);
             if(res.success){
                 obj.off('click');
                 alert(item_name+" added to cart");

                 $('.badge.cart_count').text(res.count);
             }else{
                 alert(res.message);
             }

        },
        error: function(data){
            alert("There was an issue while adding product to cart.Try Again");
            console.log(data);
        }
    })
});
            jQuery(document).ready(function($) {
              $('.loop').owlCarousel({
                center: true,
				margin: 10,
                items: 0,
                loop: true,
                responsive: {
                  320: {
                    items: 2
                  }
                }
              });
              $('.nonloop').owlCarousel({
                center: true,
                items: 0,
                loop: false,
                margin: 10,
                responsive: {
                  600: {
                    items: 2
                  }
                }
              });
            });
</script>

@endsection
