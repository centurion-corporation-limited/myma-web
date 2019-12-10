@extends('layouts.merchant')

@section('styles')
<link href="{{  static_file('js/plugins/cropper/css/style.css') }}" rel="stylesheet">
<link href="{{  static_file('js/plugins/cropper/css/style-example.css') }}" rel="stylesheet">
<link href="{{  static_file('js/plugins/cropper/css/jquery.Jcrop.css') }}" rel="stylesheet">
@endsection
@section('header')
<header class="header">
  <h2>Add Item</h2>
  <span class="back-btn"><a href="javascript:;" onclick="window.history.go(-1); return false;">
      <img src="{{ static_file('merchant/images/icon-back-arrow.png') }}" alt=""></a>
  </span>
</header>
@endsection
@section('content')
<div class="create-menu">
<form method="post" action="{{ route('merchant.item.add') }}">
    {{ csrf_field() }}
    <div class="form-group">
      <input type="hidden" class="form-control" placeholder="Food Item" name="type" value="single">
      <input type="text" class="form-control" placeholder="Food Item" name="name" value="{{ old('name') }}">
    </div>
    <div class="form-group clearfix">
        <textarea class="form-control" name="description" placeholder="Description..."></textarea>
    </div>
    <div class="form-group">
        {!!Form::select('category_id', $category, '', ['class' => 'form-control'])!!}
    </div>
    <div class="form-group">
        {!!Form::select('course_id', $courses, '', ['class' => 'form-control'])!!}
    </div>
    <div class="form-group">
      <input type="text" class="form-control" placeholder="Price" name="price" value="{{ old('price') }}">
    </div>
            <div class="form-group clearfix">
            <div class=" pull-left custom-check-form form-checkbox">
              <input id="Vegetarian" name="is_veg" value="1" type="checkbox">
              <label for="Vegetarian">Vegetarian</label>
            </div>
            <div class=" pull-left custom-check-form form-checkbox">
              <input id="Halal" name="is_halal" value="1" type="checkbox">
              <label for="Halal">Halal</label>
            </div>
          </div>

          <div class="form-group clearfix">
            <div class="clearfix">
                <div class="cropme" style="width: 100px; height: 100px;"></div>
                <input type="hidden" class="form-control col-md-7 col-xs-12" name="path">
                <!-- <img src="{{ static_file('merchant/images/img-placeholder.png') }}" alt=""> -->
                <!-- <a class="add-more" href="#"><img src="{{ static_file('merchant/images/icon-add.png') }}" alt=""></a> -->
            </div>

          </div>
<div class="text-center mg-top">
  <button type="submit" class="btn btn-default">Submit</button>
</div>
  </form>
</div>
@endsection

@section('scripts')
<script src="{{ static_file('js/plugins/cropper/scripts/jquery.Jcrop.js') }}"></script>
<script src="{{ static_file('js/plugins/cropper/scripts/jquery.SimpleCropper.js') }}"></script>
<script>
$(document).on('ready',function(){
    $('.cropme').simpleCropper();
});

</script>
@endsection
