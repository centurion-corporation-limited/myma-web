@extends('layouts.admin')

@section('styles')
<style>
.btn-group>.btn:last-child:not(:first-child), .btn-group>.dropdown-toggle:not(:first-child){width:64.41px;}
.form-horizontal .control-label{ padding-top:0;}
</style>
@endsection

@section('content')

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Edit Transaction Detail</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <br />
            <form id="demo-form2" action="{{ route('admin.transaction.edit', encrypt($item->id) ) }}" method="POST" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left">
              {{ csrf_field() }}

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Type :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{ ucfirst($item->type) }}
                </div>
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Transaction Date :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{ $item->transaction_date }}
                </div>
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Transaction Amount :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{ $item->transaction_amount }}
                </div>
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Transaction Reference No :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{ $item->transaction_ref_no }}
                </div>
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Transaction Status :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{ $item->transaction_status }}
                </div>
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Transaction Code :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{ $item->transaction_code }}
                </div>
              </div>

              <label class="col-xs-12" for="title">Transaction Charges</label>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="flexm_part">Flexm Share </label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    {{ $item->flexm_part }}
                    {{-- <input type="text" id="flexm_part" name="flexm_part" value="{{ old('flexm_part', $item->flexm_part) }}" class="form-control"> --}}
                </div>
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="myma_part">Myma Share </label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    {{ $item->myma_part }}
                    {{-- <input type="text" id="myma_part" name="myma_part" value="{{ old('myma_part', $item->myma_part) }}" class="form-control"> --}}
                </div>
              </div>

              <label class="col-xs-12" for="title">Item Share</label>

              <div class="form-group row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="myma_share">Myma Share </label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    {{ $item->myma_share }}
                    {{-- <input type="text" id="myma_share" name="myma_share" value="{{ old('myma_share', $item->myma_share) }}" class="form-control"> --}}
                </div>
              </div>

              <div class="form-group row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="other_share">Merchant Share </label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    {{ $item->other_share }}
                    {{-- <input type="text" id="other_share" name="other_share" value="{{ old('other_share', $item->other_share) }}" class="form-control"> --}}
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="myma_status">Status <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <select id="myma_status" class="form-control" name="status">
                      <option value="pending" @if(old('status', $item->status) == 'pending') selected="selected" @endif>Pending</option>
                      <option value="paid" @if(old('status', $item->status) == 'paid') selected="selected" @endif>Paid</option>
                  </select>
                </div>
              </div>

              <div class="ln_solid"></div>
              <div class="form-group">
                <div class="col-md-2 col-sm-2 col-xs-12"></div>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <button type="submit" class="btn btn-success">Update</button>
                </div>
              </div>

            </form>
          </div>
        </div>
      </div>
    </div>

@endsection
