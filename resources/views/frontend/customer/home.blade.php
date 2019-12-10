@extends('layouts.customer')

@section('styles')
<link rel="stylesheet" href="{{ static_file('css/owl.carousel.min.css') }}">
<link rel="stylesheet" href="{{ static_file('css/owl.theme.default.min.css') }}">

@endsection
@section('header')
<header class="header">
  <h2>Home</h2>
  <!-- <span class="icon-right"><a href="#"><img src="{{ static_file('customer/images/icon-sub.png') }}" alt=""></a></span> -->
</header>

@endsection
@section('content')
<!-- End-Header -->
<div class="page-content page-bg">
	<div class="search-container">
	    <form method="get" action="">
		  <input type="text" autocomplete="off" placeholder="Search.." name="search" class="col-xs-10">
	      <button type="submit" class="col-xs-2"><i class="fa fa-search"></i></button>
	    </form>
	</div>

<div id="myCarousel" class="carousel slide" data-ride="carousel">
  <!-- Indicators -->
  <ol class="carousel-indicators">
    @foreach($ads as $key => $ad)
    <li data-target="#myCarousel" data-slide-to="{{ $key }}"  @if($key == 0 ) class="active" @endif></li>
    <!-- <li data-target="#myCarousel" data-slide-to="1"></li> -->
    <!-- <li data-target="#myCarousel" data-slide-to="2"></li> -->
    @endforeach
  </ol>

  <!-- Wrapper for slides -->
  <div class="carousel-inner">
      @foreach($ads as $key => $ad)
    	<div class="item @if($key == 0) active @endif ">
    	    @if($ad->food_item && $ad->food)
			<a href="{{ route('food.customer.food_detail', $ad->food_item) }}">
			@endif    
				<img src="{{ static_file($ad->path) }}" alt="">
			@if($ad->food_item && $ad->food)	
			</a>
			@endif
		</div>
    <!-- <div class="item"> <img src="{{ static_file('customer/images/img-banner.jpg') }}" alt=""> </div> -->
    <!-- <div class="item"> <img src="{{ static_file('customer/images/img-banner.jpg') }}" alt=""> </div> -->
    @endforeach
  </div>
  <!-- <div class="carousel-caption"></div> -->
</div>

<!-- End-carousel -->
<div class="item-cuisine">
  @foreach($cuisine as $key => $cat)
  <a href="{{ route('food.customer.food_list', ['type' => "$cat->slug"]) }}">
      <li>{{ $cat->name }}<span><img src="@if($cat->img != '') {{ static_file($cat->img) }} @else {{ static_file('customer/images/icon-02.png') }}@endif" alt=""></span></li>
  </a>
  @endforeach
  <div class="item-view pull-right"><a href="{{ route('food.customer.cuisine') }}">View All</a></div>
</div>
<!-- End-cuisine -->

@if($category_name == '')
<div class="populat-itams clearfix">
  <div class="top-text clearfix">
    <div class="pull-left">
      <h2>Recommended for you</h2>
    </div>
    <!-- <div class="pull-right">
      <h3><a href="#">View All</a></h3>
    </div> -->
  </div>
  <div class="loop owl-carousel recommended-itam clearfix">
    @foreach($recommended as $key => $item)
    <?php
        $img = (isset($item->image) && $item->image != '')?$item->image:'customer/images/img_place.png';
    ?>
    <div class="item">
        <a href="{{ route('food.customer.food_detail', $item->id) }}">
            <img src="{{ static_file($img) }}" alt="">
        </a>
        <!-- <span>5 Left</span> -->
      <!-- <label>* 4.5</label> -->
    </div>
    @endforeach
    <!-- <div class="item"><img src="{{ static_file('customer/images/img-food-2.jpg') }}" alt=""> -->
        <!-- <span>2 Left</span> -->
      <!-- <label>* 3.5</label> -->
    <!-- </div> -->
  </div>
</div>
@endif
<div class="populat-itams clearfix">
    @if(getOption('bottom_ad'))
        <a href="{{ getOption('bottom_ad_link') }}">{{ getOption('bottom_ad') }}</a>
    @endif
</div>

@if($category_name != '')
<div class="filtered-data">
	<h1 class="food_listing">Results</h1>
	@if(count($menu))
	    @foreach($menu as $item)
	        <div class="cuisine-detail">
	          <div class="cuisine-left"> <a href="{{ route('food.customer.food_detail', $item->id) }}"><img src="{{ $item->image }}" alt=""></a> </div>
            <div class="cuisine-right">
              <h2><a href="{{ route('food.customer.food_detail', $item->id) }}">{{ $item->name }}</a></h2>
              <p><b>Price:</b> S${{ $item->price }}</p>
              <p><b>Type:</b> {{ $item->tags_text }}</p>
              @if($item->type == 'package')
              <ul>
                <li>
                  <p><b>Breakfast:</b> {{ $item->breakfast }},</p>
                </li>
                <li>
                  <p><b>Lunch:</b> {{ $item->lunch }},</p>
                </li>
                <li>
                  <p><b>Dinner:</b> {{ $item->dinner }}</p>
                </li>
              </ul>
              @endif

            @if($item->percent != '' && $item->percent != 0)
            <p><b>Feedback:</b> {{ $item->percent }}%</p>
            <p>
              @if($item->ratings > 1)
                <b>Ratings:</b>
              @else
                <b>Rating:</b>
              @endif
                {{ $item->ratings }}
              </p>
              @endif

              <a class="btn-carat add_cart" href="javascript:;" data-name="{{ $item->name }}" data-id="{{ $item->id }}">Add To Cart</a> </div>
	        </div>
	    @endforeach
		@else
		<h5>No results found</h5>
		@endif
  </div>
@endif
<div data-toggle="modal" data-target="#filterModal" class="fab" id="masterfab"><span><i class="fa fa-filter"></i></span></div>

</div>

<div id="filterModal" class="modal fade" role="dialog">
<div class="modal-dialog">
<!-- Modal content-->
<div class="modal-content">
  <div class="modal-header">
    <h4 class="modal-title">All Cuisine</h4>
      <a class="icon-close" data-dismiss="modal" href="#"><i class="fa fa-times"></i></a>
  </div>
  <div class="order-popup">
      <form method="get" action="">
        <ul class="filter_class">
          @foreach($cats as $keyy => $cat)
          <li>
            <div class="radio_btn">
              <input id="id_{{ $keyy }}" class="radio_fil" type="radio" name="type" value="{{ $keyy }}">
              <label for="id_{{ $keyy }}">{{ $cat }}</label>
            </div>
          </li>
          @endforeach
        </ul>

            <input type="submit" value="Done" class="btn btn-default form-control modal-submit">

      </form>
  </div>

</div>
</div>
</div>
@endsection

@section('scripts')
<script src="{{  static_file('js/owl.carousel.js') }}"></script>
<script>
// $(document).on('click', '.search-container button', function(){
// 	console.log("works");
// });
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
          </script>

@endsection
