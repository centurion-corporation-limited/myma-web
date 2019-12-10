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
            <h2>Add Item</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <form id="demo-form2" action="{{ route('admin.food_menu.add') }}" method="POST" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left">
              {{ csrf_field() }}

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="type">Item Type <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  @if($flag == '')
                    {!!Form::select('type', ['single' => 'Single', 'package' => 'Package'], '', ['class' => 'form-control'])!!}
                  @elseif($flag == 'single')
                    {!!Form::select('type', ['single' => 'Single'], 'single', ['class' => 'form-control'])!!}
                  @else
                    {!!Form::select('type', ['package' => 'Package'], 'package', ['class' => 'form-control'])!!}
                  @endif
                </div>
              </div>

              <div class="form-group single sin_dropdown">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="type">Restaurant <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    {!!Form::select('restaurant_id', $single_restra, '', ['class' => 'form-control'])!!}
                </div>
              </div>

              <div class="form-group package pkg_dropdown hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="type">Restaurant <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    {!!Form::select('restaurant_id', $catering_restra, '', ['disabled', 'class' => 'form-control'])!!}
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Name <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name_mn">Description <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <textarea id="desc" name="description" class="form-control">{{ old('description') }}</textarea>
                </div>
              </div>

              <div class="form-group select_dropdown">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name_bn">Category <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    {!!Form::select('tags[]', $category, '', ['id' => 'tags', 'class' => 'form-control', 'multiple' => ''])!!}
                </div>
              </div>

              <div class="form-group single">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="course_id">Course <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {!!Form::select('course_id', $courses, '', ['class' => 'form-control'])!!}
                </div>
              </div>

              <div class="form-group package hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="breakfast">Breakfast <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {!!Form::select('breakfast', $list, old('breakfast'), ['class' => 'form-control'])!!}
                  <!-- <input type="text" id="breakfast" name="breakfast" value="{{ old('breakfast') }}" class="form-control"> -->
                </div>
              </div>

              <div class="form-group package hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="lunch">Lunch <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {!!Form::select('lunch', $list, old('lunch'), ['class' => 'form-control'])!!}
                  <!-- <input type="text" id="lunch" name="lunch" value="{{ old('lunch') }}" class="form-control"> -->
                </div>
              </div>

              <div class="form-group package hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="dinner">Dinner <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {!!Form::select('dinner', $list, old('dinner'), ['class' => 'form-control'])!!}
                  <!-- <input type="text" id="dinner" name="dinner" value="{{ old('dinner') }}" class="form-control"> -->
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="base_price">Cost Price <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="base_price" name="base_price" value="{{ old('base_price') }}" class="form-control">
                </div>
              </div>
              <div class="form-group">
                <div class="price_calc col-md-6 col-md-offset-2">
                </div>
              </div>
              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="price">Selling Price <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="price" name="price" value="{{ old('price') }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="lunch">More Tags</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <label>Vegetarian
                        <input type="checkbox" id="is_veg" name="is_veg" value="1" class="">
                    </label>
                    <label>Halal
                        <input type="checkbox" id="is_halal" name="is_halal" value="1" class="">
                    </label>
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="price">Image <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <div class="image_div">
                    <div class="sec">
                      <div class="cropme" style="width: 200px; height: 200px;"></div>
                      <input type="hidden" class="form-control" name="image[]">
                    </div>
                    <div class="sec">
                      <div class="cropme" style="width: 200px; height: 200px;"></div>
                      <input type="hidden" class="form-control" name="image[]">
                    </div>
                    <div class="sec">
                      <div class="cropme" style="width: 200px; height: 200px;"></div>
                      <input type="hidden" class="form-control" name="image[]">
                    </div>
                    <div class="sec">
                      <div class="cropme" style="width: 200px; height: 200px;"></div>
                      <input type="hidden" class="form-control" name="image[]">
                    </div>
                    <div class="sec">
                      <div class="cropme" style="width: 200px; height: 200px;"></div>
                      <input type="hidden" class="form-control" name="image[]">
                    </div>
                  </div>
                  <!-- <button type="button" class="add_more">Add More</button> -->
                </div>
              </div>

              <!-- <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="price">Image <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <div class="cropme" style="width: 200px; height: 200px;"></div>
                    <input type="hidden" class="form-control" name="path_2">
                </div>
              </div>
              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="price">Image <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <div class="cropme" style="width: 200px; height: 200px;"></div>
                    <input type="hidden" class="form-control" name="path_3">
                </div>
              </div>
              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="price">Image <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <div class="cropme" style="width: 200px; height: 200px;"></div>
                    <input type="hidden" class="form-control" name="path_4">
                </div>
              </div>
              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="price">Image <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <div class="cropme" style="width: 200px; height: 200px;"></div>
                    <input type="hidden" class="form-control" name="path_5">
                </div>
              </div> -->
              @if($auth_user->hasRole('food-admin'))
              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12">Publish <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <div id="gender" class="btn-group" data-toggle="buttons">
                    <label class="btn @if(old('published') == '1') btn-primary active @else btn-default @endif" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                      <input type="radio" name="published" @if(old('published') == '1') checked @endif value="1"> &nbsp; Yes &nbsp;
                    </label>
                    <label class="btn @if(old('published') == '0') btn-primary active @else btn-default @endif" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                      <input type="radio" name="published" @if(old('published') == '0') checked @endif value="0"> No
                    </label>
                  </div>
                </div>
              </div>
              @endif
              <div class="ln_solid"></div>
              <div class="form-group">
                <div class="col-md-2 col-sm-2 col-xs-12"></div>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <!-- <button class="btn btn-primary" type="button">Cancel</button> -->
                  <!-- <button class="btn btn-primary" type="reset">Reset</button> -->
                  <button type="submit" class="btn btn-success">
                    @if(!$auth_user->hasRole('food-admin'))
                    Submit for proof reading
                    @else
                    Add
                    @endif
                  </button>
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

  $('[name=published]').on('change', function(){
      var obj = $(this);
      $('[name=published]').closest('label').addClass('btn-default').removeClass('btn-primary');
      obj.closest('label').addClass('btn-primary').removeClass('btn-default');
  });

  // $('.add_more').on('click', function(){
  //     var obj = $('.image_div');
  //     var cnt = $('.cropme').length;
  //     if(cnt > 4){
  //         alert("Maximum images allowed are 5.");
  //         return false;
  //     }
  //     obj.append('<div class="sec"><div class="cropme" style="width: 200px; height: 200px;"></div><input type="hidden" class="form-control" name="image[]"></div>');
  // });


  $("#tags").select2({
    tags: true,
    createTag: function (params) {
        console.log(params);
        return {
          id: params.term,
          text: params.term,
          newOption: true
        }
        // var formData = new FormData();
        // formData.append('name', params.term);
        //
        // $.ajax({
        //     url:'{{ route('ajax.add.tag') }}',
        //     data: formData,
        //     method:'post',
        //     success:function(d){
        //         console.log(d);
        //         return {
        //           id: d.id,
        //           text: d.term,
        //           newOption: true
        //         }
        //     },
        //     error:function(d){
        //         return {
        //           id: params.id,
        //           text: params.term,
        //           newOption: true
        //         }
        //     }
        // });
    },

  });

  $('.cropme').simpleCropper();

  $('[name=type]').on('change',function(){
      var value = $(this).val();
      if(value == 'single'){
        $('.package').addClass('hide');
        $('.single').removeClass('hide');

        $('.sin_dropdown [name=restaurant_id]').attr('disabled', false);
        $('.pkg_dropdown [name=restaurant_id]').attr('disabled', true);
      }else{
          $('.package').removeClass('hide');
          $('.single').addClass('hide');

          $('.sin_dropdown [name=restaurant_id]').attr('disabled', true);
          $('.pkg_dropdown [name=restaurant_id]').attr('disabled', false);
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
