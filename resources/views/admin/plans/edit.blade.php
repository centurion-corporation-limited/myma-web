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
            <form id="demo-form2" action="{{ route('admin.advertisement.plan.edit', encrypt($item->id)) }}" method="POST" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left">
              {{ csrf_field() }}
              <div class="form-group">
                <label class="control-label col-md-2 col-sm-3 col-xs-12" for="role">Type <span class="required">*</span></label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <select id="role" class="form-control" name="type" @if($item->ads->count() > 0) disabled @endif>
                      <option value="impression" @if(old('type', $item->type) == 'impression') selected="selected" @endif>Impressions</option>
                      <option value="date" @if(old('type', $item->type) == 'date') selected="selected" @endif>Duration</option>
                  </select>
                </div>
              </div>

              <div class="form-group impression_div">
                <label class="control-label col-md-2 col-sm-3 col-xs-12" for="impressions">Impressions <span class="required">*</span></label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <input  @if($item->ads->count() > 0) disabled @endif type="number" id="impressions" name="impressions" placeholder="1000" value="{{ old('impressions', $item->impressions) }}" class="form-control">
                </div>
              </div>

              <div class="form-group hide date_div">
                <label class="control-label col-md-2 col-sm-3 col-xs-12" for="impressions">Choose an option <span class="required">*</span></label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <select disabled id="impressions" class="form-control" name="impressions">
                      <option value="7" @if(old('impressions', $item->impressions) == '7') selected="selected" @endif>Per Week</option>
                      <option value="31" @if(old('impressions', $item->impressions) == '31') selected="selected" @endif>Per Month</option>
                      <option value="365" @if(old('impressions', $item->impressions) == '365') selected="selected" @endif>Per Year</option>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-3 col-xs-12" for="price">Price <span class="required">*</span></label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <input  @if($item->ads->count() > 0) disabled @endif type="number" id="price" name="price" placeholder="20" value="{{ old('price', $item->price) }}" class="form-control">
                </div>
              </div>

              <div class="ln_solid"></div>
              <div class="form-group">
                <div class="col-md-2 col-sm-3 col-xs-12"></div>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <!-- <button class="btn btn-primary" type="button">Cancel</button> -->
                  <!-- <button class="btn btn-primary" type="reset">Reset</button> -->
                  @if($item->ads->count() == 0)
                  <button type="submit" class="btn btn-success">Update</button>
                  @endif
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
         <?php if($item->ads->count() == 0){ ?>
         $('.impression_div').find('[name=impressions]').prop('disabled', false);
         $('.date_div').find('[name=impressions]').prop('disabled', true);
         <?php } ?>

         $('.date_div').addClass('hide');
     }
     else if($('[name=type]').val() == 'date'){
         $('.impression_div').addClass('hide');
         $('.date_div').removeClass('hide');
         <?php if($item->ads->count() == 0){ ?>
         $('.impression_div').find('[name=impressions]').prop('disabled', true);
         $('.date_div').find('[name=impressions]').prop('disabled', false);
         <?php } ?>
     }
  });
  $('[name=type]').trigger('change');


});

</script>
@endsection
