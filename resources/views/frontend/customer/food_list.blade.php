@extends('layouts.customer')

@section('header')
<header class="header">
  <h2>{{ ucfirst($category_name) }}</h2>
  <span class="back-btn"><a href="javascript:;" onclick="window.history.go(-1); return false;">
      <img src="{{ static_file('customer/images/icon-back-arrow.png') }}" alt=""></a>
  </span>
</header>
@endsection
@section('content')

<div class="page-content content-pages">
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
              <p><b>Feedback:</b> {{ $item->percent }}<i class="fa fa-star"></i></p>

              <p>
                @if($item->ratings > 1)
                  <b>Ratings:</b>
                @else
                  <b>Rating:</b>
                @endif
                  {{ $item->ratings }}+
                </p>
                @endif

                <a class="btn-carat add_cart" href="javascript:;" data-name="{{ $item->name }}" data-id="{{ $item->id }}">Add To Cart</a> </div>
            </div>
        @endforeach
    @else
        <h4>No items to show</h4>
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
                  <input class="radio_fil" type="radio" name="type" value="{{ $keyy }}">
                  <label>{{ $cat }}</label>
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
@section('back-button')
<li><a href="javascript:;" onclick="window.history.go(-1); return false;"><span><img src="{{ static_file('images/icon-arrow-left.png') }}" alt=""></span>Back</a></li>
@endsection
@section('scripts')
<script>

    $(document).on('click', '.filter_class li', function(){
        $(this).find('input').prop('checked', true);
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
