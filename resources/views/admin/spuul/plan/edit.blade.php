@extends('layouts.admin')

@section('styles')
@endsection

@section('content')

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Edit Plan</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <br />
            <form id="demo-form2" action="{{ route('admin.spuul.plan.edit', encrypt($item->id)) }}" method="POST" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left">
              {{ csrf_field() }}

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="role">Type <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <select id="role" class="form-control" name="type" disabled >
                      <option value="1" @if(old('type', $item->type) == '1') selected="selected" @endif>Monthly</option>
                      <option value="2" @if(old('type', $item->type) == '2') selected="selected" @endif>Yearly</option>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="price">Price <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="price" name="price" placeholder="20" value="{{ old('price', $item->price) }}" class="form-control">
                </div>
              </div>

              <div class="form-group impression_div">
                  <label class="control-label col-md-2 col-sm-2 col-xs-12" for="impressions">Status <span class="required">*</span></label>
                  <div class="col-md-6 col-sm-10 col-xs-12">
                      <select id="status" class="form-control" name="status">
                          <option value="1" @if(old('status', $item->status) == '1') selected="selected" @endif>Active</option>
                          <option value="0" @if(old('status', $item->status) == '0') selected="selected" @endif>Inactive</option>
                      </select>
                  </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="role">Order</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <select id="list_order" class="form-control" name="list_order">
                      <option value="1" @if(old('list_order', $item->list_order) == '1') selected="selected" @endif>1</option>
                      <option value="2" @if(old('list_order', $item->list_order) == '2') selected="selected" @endif>2</option>
                  </select>
                </div>
              </div>

              <div class="ln_solid"></div>
              <div class="form-group">
                <div class="col-md-2 col-sm-2 col-xs-12"></div>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <!-- <button class="btn btn-primary" type="button">Cancel</button> -->
                  <!-- <button class="btn btn-primary" type="reset">Reset</button> -->
                  {{-- @if($item->ads->count() == 0)
                  @endif --}}
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
<script>
$(document).on('ready',function(){

  $('[name=type]').on('change',function(){
     if($('[name=type]').val() == 'impression'){
         $('.impression_div').removeClass('hide');
         <?php //if($item->ads->count() == 0){ ?>
         $('.impression_div').find('[name=impressions]').prop('disabled', false);
         $('.date_div').find('[name=impressions]').prop('disabled', true);
         <?php //} ?>

         $('.date_div').addClass('hide');
     }
     else if($('[name=type]').val() == 'date'){
         $('.impression_div').addClass('hide');
         $('.date_div').removeClass('hide');
         <?php //if($item->ads->count() == 0){ ?>
         $('.impression_div').find('[name=impressions]').prop('disabled', true);
         $('.date_div').find('[name=impressions]').prop('disabled', false);
         <?php //} ?>
     }
  });
  $('[name=type]').trigger('change');


});

</script>
@endsection
