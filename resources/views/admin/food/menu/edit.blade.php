@extends('layouts.admin')

@section('styles')
<link href="{{  static_file('js/plugins/cropper/css/style.css') }}" rel="stylesheet">
<link href="{{  static_file('js/plugins/cropper/css/style-example.css') }}" rel="stylesheet">
<link href="{{  static_file('js/plugins/cropper/css/jquery.Jcrop.css') }}" rel="stylesheet">
<link href="{{ static_file('js/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
<style>
.form-group.select_dropdown{
    line-height: inherit;
}

</style>
@endsection
@section('content')

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Edit Item</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <br />
            <form id="demo-form2" action="{{ route('admin.food_menu.edit', encrypt($item->id)) }}" method="POST" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left">
              {{ csrf_field() }}

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="type">Item Type <span class="required">*</span></label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                  @if($flag == '')
                    {!!Form::select('type', ['single' => 'Single', 'package' => 'Package'], $item->type, ['class' => 'form-control'])!!}
                  @elseif($flag == 'single')
                    {!!Form::select('type', ['single' => 'Single', 'package' => 'Package'], 'single', ['readonly', 'class' => 'form-control'])!!}
                  @else
                    {!!Form::select('type', ['single' => 'Single', 'package' => 'Package'], 'package', ['readonly', 'class' => 'form-control'])!!}
                  @endif
                </div>
              </div>

              <div class="form-group single sin_dropdown">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="type">Restaurant <span class="required">*</span></label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    @if($flag == "")
                      {!!Form::select('restaurant_id', $single_restra, $item->restaurant_id, ['class' => 'form-control'])!!}
                    @else
                      {!!Form::select('restaurant_id', $single_restra, $restaurant_id, ['readonly', 'class' => 'dont form-control'])!!}
                    @endif
                </div>
              </div>

              <div class="form-group package pkg_dropdown hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="type">Restaurant <span class="required">*</span></label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    @if($flag == "")
                      {!!Form::select('restaurant_id', $catering_restra, $item->restaurant_id, ['readonly', 'class' => 'form-control'])!!}
                    @else
                      {!!Form::select('restaurant_id', $catering_restra, $restaurant_id, ['readonly', 'class' => 'dont form-control'])!!}
                    @endif
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Name <span class="required">*</span></label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                  <input type="text" id="name" name="name" value="{{ old('name',$item->name) }}" class="form-control col-md-7 col-xs-12">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name_mn">Description <span class="required">*</span></label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                  <textarea id="desc" name="description" class="form-control col-md-7 col-xs-12">{{ old('description',$item->description) }}</textarea>
                </div>
              </div>

              <div class="form-group select_dropdown">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name_bn">Category <span class="required">*</span></label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    {!!Form::select('tags[]', $category, $item->tags, ['id' => 'tags', 'class' => 'form-control', 'multiple' => ''])!!}
                </div>
              </div>

              <div class="form-group single">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="course_id">Course <span class="required">*</span></label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                  {!!Form::select('course_id', $courses, $item->course_id, ['class' => 'form-control'])!!}
                </div>
              </div>

              <div class="form-group package hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="breakfast">Breakfast <span class="required">*</span></label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                  {!!Form::select('breakfast', $list, old('breakfast', $item->breakfast), ['class' => 'form-control'])!!}
                  <!-- <input type="text" id="breakfast" name="breakfast" value="{{ old('breakfast',$item->breakfast) }}" class="form-control col-md-7 col-xs-12"> -->
                </div>
              </div>

              <div class="form-group package hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="lunch">Lunch <span class="required">*</span></label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                  {!!Form::select('lunch', $list, old('lunch', $item->lunch), ['class' => 'form-control'])!!}
                  <!-- <input type="text" id="lunch" name="lunch" value="{{ old('lunch', $item->lunch) }}" class="form-control col-md-7 col-xs-12"> -->
                </div>
              </div>

              <div class="form-group package hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="dinner">Dinner <span class="required">*</span></label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                  {!!Form::select('dinner', $list, old('dinner', $item->dinner), ['class' => 'form-control'])!!}
                  <!-- <input type="text" id="dinner" name="dinner" value="{{ old('dinner', $item->dinner) }}" class="form-control col-md-7 col-xs-12"> -->
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="base_price">Cost Price <span class="required">*</span></label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                  <input type="text" id="base_price" name="base_price" value="{{ old('base_price', $item->base_price) }}" class="form-control col-md-7 col-xs-12">
                </div>
              </div>
              <div class="form-group">
                <div class="price_calc col-md-6 col-md-offset-2">
                  {!! $html !!}
                </div>
              </div>
              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="price">Selling Price <span class="required">*</span></label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                  <input type="text" id="price" name="price" value="{{ old('price', $item->price) }}" class="form-control col-md-7 col-xs-12">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="lunch">More Tags</label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <label>Vegetarian
                        <input type="checkbox" id="is_veg" name="is_veg" value="1" @if(old('is_veg', $item->is_veg) == 1) checked @endif class="">
                    </label>
                    <label>Halal
                        <input type="checkbox" id="is_halal" name="is_halal" value="1" @if(old('is_halal', $item->is_halal) == 1) checked @endif class="">
                    </label>
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="price">Image <span class="required">*</span></label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                  <!-- <input type="file" id="image" name="image" class="form-control col-md-7 col-xs-12"> -->
                  <div class="image_div">
                    @foreach(explode(',', $item->image) as $kk => $img)
                      <input type="hidden" name="if_img" value="1">
                      @if($img)
                      <div class="sec class_parent">
                        <div class="cropme" style="width: 200px; height: 200px;">
                          <img src="{{ static_file($img) }}" height="200" width="200" />
                        </div>
                        <input type="hidden" name="image[]">
                        <button type="button" class="remove_image" data-id="{{ $item->id }}" data-index="{{ $kk }}" ><i class="fa fa-trash"></i></button>
                      </div>
                      @endif
                    @endforeach


                    @for($i = count(explode(',', $item->image)); $i < 5 ; $i++)
                    <div class="sec class_parent">
                      <div class="cropme" style="width: 200px; height: 200px;"></div>
                      <input type="hidden" name="image[]">
                    </div>
                    @endfor
                  </div>

                </div>

              </div>

              @if($auth_user->hasRole('food-admin'))
              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12">Publish</label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                  <div id="gender" class="btn-group" data-toggle="buttons">
                    <label class="btn @if(old('published', $item->published ) == '1') btn-primary active @else btn-default @endif" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                      <input type="radio" name="published" @if(old('published', $item->published ) == '1') checked @endif value="1"> &nbsp; Yes &nbsp;
                    </label>
                    <label class="btn @if(old('published', $item->published ) == '0') btn-primary active @else btn-default @endif" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                      <input type="radio" name="published" @if(old('published', $item->published ) == '0') checked @endif value="0"> No
                    </label>
                  </div>
                </div>
              </div>
              @endif
              <div class="ln_solid"></div>
              <div class="form-group">
                <div class="col-md-9 col-sm-9 col-xs-9 col-sm-offset-2">
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
<script src="{{ static_file('js/plugins/cropper/scripts/jquery.Jcrop.js') }}"></script>
<script src="{{ static_file('js/plugins/cropper/scripts/jquery.SimpleCropper.js') }}"></script>

<script src="{{ static_file('js/plugins/select2/js/select2.full.min.js') }}"></script>
<script>
$(document).on('ready',function(){
    $("#tags").select2({
      tags: true,
      createTag: function (params) {
          return {
            id: params.term,
            text: params.term,
            newOption: true
          }
      },
    });

    $('.cropme').simpleCropper();

    $('[name=published]').on('change', function(){
        var obj = $(this);
        $('[name=published]').closest('label').addClass('btn-default').removeClass('btn-primary');
        obj.closest('label').addClass('btn-primary').removeClass('btn-default');
    });

  $('[name=type]').on('change',function(){
      var value = $(this).val();
      if(value == 'single'){
        $('.package').addClass('hide');
        $('.single').removeClass('hide');

        if(!$('.sin_dropdown [name=restaurant_id]').hasClass('dont')){
          $('.sin_dropdown [name=restaurant_id]').attr('disabled', false);
        }
        $('.sin_dropdown [name=restaurant_id]').attr('disabled', false);
        $('.pkg_dropdown [name=restaurant_id]').attr('disabled', true);
      }else{
          $('.package').removeClass('hide');
          $('.single').addClass('hide');

          $('.sin_dropdown [name=restaurant_id]').attr('disabled', true);
          if(!$('.pkg_dropdown [name=restaurant_id]').hasClass('dont')){
            $('.pkg_dropdown [name=restaurant_id]').attr('disabled', false);
          }

      }
  });

  $('.remove_image').on('click',function(){
      var obj = $(this);
      var index = $(this).attr('data-index');
      var id = $(this).attr('data-id');

      if(index != '' && id != ''){
        var formData = new FormData();
        formData.append('item_id', id);
        formData.append('index', index);

        $.ajax({
            method:'post',
            url: '{{ route('ajax.food.image.remove') }}',
            data: formData,
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            crossDomain: true,
            cache: false,
            contentType: false,
            processData: false,
            //beforeSend: function () {
                //    $(objct).val('Connecting...');
            //},
            error: function(xhr){
                console.log("Error");
                console.log(xhr);
            },
            success: function(data){
                if (data.status) {
                    obj.parent().find('img').remove();
                }else if (data.status == false) {
                    alert(data.message);
                }
            }
        });
      }
  });

  $('[name=type]').trigger('change');

  $('[name=is_veg]').on('change',function(){
      $('[name=is_halal]').prop('checked', false);
  });

  $('[name=is_halal]').on('change',function(){
      $('[name=is_veg]').prop('checked', false);
  });

  $('[name=base_price]').on('keyup',function(){
      var price = $(this).val();
      var type = $('[name=type]').val();
      if(type == 'single'){
        var restaurant_id = $('.sin_dropdown [name=restaurant_id]').val();
      }else if(type == 'package'){
        var restaurant_id = $('.pkg_dropdown [name=restaurant_id]').val();
      }

      if(price != '' && restaurant_id != ''){
        if(price <= 0){
          return false;
        }
        var formData = new FormData();
        formData.append('price', price);
        formData.append('restaurant_id', restaurant_id);

        $.ajax({
            method:'post',
            url: '{{ route('ajax.food.calcprice') }}',
            data: formData,
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            crossDomain: true,
            cache: false,
            contentType: false,
            processData: false,
            error: function(xhr){
                console.log("Error");
                console.log(xhr);
            },
            success: function(data){
                if (data.status) {
                    $('[name=price]').val(data.total);
                    $('.price_calc').html(data.html);
                }else if (data.status == false) {
                    alert(data.message);
                }
            }
        });
      }else{
        $('[name=price]').val(0);
      }
  });

  $('[name=price]').on('keyup',function(){
      var price = $(this).val();
      var type = $('[name=type]').val();
      if(type == 'single'){
        var restaurant_id = $('.sin_dropdown [name=restaurant_id]').val();
      }else if(type == 'package'){
        var restaurant_id = $('.pkg_dropdown [name=restaurant_id]').val();
      }

      if(price != '' && restaurant_id != ''){
        var formData = new FormData();
        formData.append('price', price);
        formData.append('restaurant_id', restaurant_id);
        formData.append('type', 'cost');

        $.ajax({
            method:'post',
            url: '{{ route('ajax.food.calcprice') }}',
            data: formData,
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            crossDomain: true,
            cache: false,
            contentType: false,
            processData: false,
            error: function(xhr){
                console.log("Error");
                console.log(xhr);
            },
            success: function(data){
                if (data.status) {
                    $('[name=base_price]').val(data.total);
                    $('.price_calc').html(data.html);
                }else if (data.status == false) {
                    alert(data.message);
                }
            }
        });
      }else{
        $('[name=base_price]').val(0);
      }
  });

});
</script>
@endsection
