@extends('layouts.admin')

@section('styles')
<style>
.btn-group>.btn:last-child:not(:first-child), .btn-group>.dropdown-toggle:not(:first-child){width:64.41px;}
.form-horizontal .control-label{ padding-top:0;}
.badge-danger {
    color: #fff;
    background-color: #dc3545;
}
.badge {
    font-size: 14px;
    vertical-align: baseline;
    border-radius: .25rem;
}
</style>
@endsection

@section('content')

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Transaction Detail</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <br />
            <form id="demo-form2" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left border-form">

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Type :</label>
                <div class="@if($wallet) col-md-5 col-sm-5 col-xs-12 @else col-md-10 col-sm-10 col-xs-12 @endif">
                  {{ ucfirst($item->type) }}
                </div>
                @if($wallet)
                <div class="col-md-5 col-sm-5 col-xs-12">
                  <h4>
                    Uploaded Data
                  </h4>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Mobile :</label>
                <div class="@if($wallet) col-md-5 col-sm-5 col-xs-12 @else col-md-10 col-sm-10 col-xs-12 @endif">
                  {{ $item->phone_no }}
                </div>
                @if($wallet)
                <div class="col-md-5 col-sm-5 col-xs-12">
                  <span @if($wallet->mobile != $item->phone_no) class="badge badge-danger" @endif>
                  {{ $wallet->mobile or '-' }}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Wallet User Name :</label>
                <div class="@if($wallet) col-md-5 col-sm-5 col-xs-12 @else col-md-10 col-sm-10 col-xs-12 @endif">
                  {{ $item->wallet_user_name }}
                </div>
                @if($wallet)
                <div class="col-md-5 col-sm-5 col-xs-12">
                  <span @if($wallet->wallet_user_name != $item->wallet_user_name) class="badge badge-danger" @endif>
                    {{ $wallet->wallet_user_name or '-' }}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Transaction Date :</label>
                <div class="@if($wallet) col-md-5 col-sm-5 col-xs-12 @else col-md-10 col-sm-10 col-xs-12 @endif">
                  {{ $item->created_at->format('d/m/Y h:i A') }}
                </div>
                @if($wallet)
                <div class="col-md-5 col-sm-5 col-xs-12">
                  <span @if($wallet->transaction_date != $item->transaction_date) class="badge badge-danger" @endif>
                    {{ $wallet->transaction_date }}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Transaction Amount :</label>
                <div class="@if($wallet) col-md-5 col-sm-5 col-xs-12 @else col-md-10 col-sm-10 col-xs-12 @endif">
                  ${{ number_format($item->transaction_amount,4) }}
                </div>
                @if($wallet)
                <div class="col-md-5 col-sm-5 col-xs-12">
                  <span @if($wallet->transaction_amount != $item->transaction_amount) class="badge badge-danger" @endif>
                    ${{ number_format($wallet->transaction_amount,4) }}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Transaction Currency :</label>
                <div class="@if($wallet) col-md-5 col-sm-5 col-xs-12 @else col-md-10 col-sm-10 col-xs-12 @endif">
                  {{ $item->transaction_currency }}
                </div>
                @if($wallet)
                <div class="col-md-5 col-sm-5 col-xs-12">
                  <span @if($wallet->transaction_currency != $item->transaction_currency) class="badge badge-danger" @endif>
                    {{ $wallet->transaction_currency or '-' }}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Transaction Reference No :</label>
                <div class="@if($wallet) col-md-5 col-sm-5 col-xs-12 @else col-md-10 col-sm-10 col-xs-12 @endif">
                  {{ $item->transaction_ref_no }}
                </div>
                @if($wallet)
                <div class="col-md-5 col-sm-5 col-xs-12">
                  <span @if($wallet->transaction_ref_no != $item->transaction_ref_no) class="badge badge-danger" @endif>
                    {{ $wallet->transaction_ref_no or '-' }}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Transaction Status :</label>
                <div class="@if($wallet) col-md-5 col-sm-5 col-xs-12 @else col-md-10 col-sm-10 col-xs-12 @endif">
                  {{ $item->transaction_status }}
                </div>
                @if($wallet)
                <div class="col-md-5 col-sm-5 col-xs-12">
                  <span @if(strtolower($wallet->transaction_status) != strtolower($item->transaction_status)) class="badge badge-danger" @endif>
                    {{ strtoupper($wallet->transaction_status) }}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Transaction Code :</label>
                <div class="@if($wallet) col-md-5 col-sm-5 col-xs-12 @else col-md-10 col-sm-10 col-xs-12 @endif">
                  {{ $item->transaction_code }}
                </div>
                @if($wallet)
                <div class="col-md-5 col-sm-5 col-xs-12">
                  <span @if($wallet->transaction_code != $item->transaction_code) class="badge badge-danger" @endif>
                    {{ $wallet->transaction_code or '-'}}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">MID :</label>
                <div class="@if($wallet) col-md-5 col-sm-5 col-xs-12 @else col-md-10 col-sm-10 col-xs-12 @endif">
                  {{ $item->mid }}
                </div>
                @if($wallet)
                <div class="col-md-5 col-sm-5 col-xs-12">
                  <span @if($wallet->mid != $item->mid) class="badge badge-danger" @endif>
                    {{ $wallet->mid or '-'}}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">TID :</label>
                <div class="@if($wallet) col-md-5 col-sm-5 col-xs-12 @else col-md-10 col-sm-10 col-xs-12 @endif">
                  {{ $item->tid }}
                </div>
                @if($wallet)
                <div class="col-md-5 col-sm-5 col-xs-12">
                  <span @if($wallet->tid != $item->tid) class="badge badge-danger" @endif>
                    {{ $wallet->tid or '-'}}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Merchant Name :</label>
                <div class="@if($wallet) col-md-5 col-sm-5 col-xs-12 @else col-md-10 col-sm-10 col-xs-12 @endif">
                  {{ $item->merchant_name }}
                </div>
                @if($wallet)
                <div class="col-md-5 col-sm-5 col-xs-12">
                  <span @if($wallet->merchant_name != $item->merchant_name) class="badge badge-danger" @endif>
                    {{ $wallet->merchant_name or '-'}}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Payment Mode :</label>
                <div class="@if($wallet) col-md-5 col-sm-5 col-xs-12 @else col-md-10 col-sm-10 col-xs-12 @endif">
                  {{ $item->payment_mode }}
                </div>
                @if($wallet)
                <div class="col-md-5 col-sm-5 col-xs-12">
                  <span @if($wallet->payment_mode != $item->payment_mode) class="badge badge-danger" @endif>
                    {{ $wallet->payment_mode or '-'}}
                  </span>
                </div>
                @endif
              </div>

              <label class="col-xs-12" for="title">Transaction Charges</label>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">FlexM Cost :</label>
                <div class="@if($wallet) col-md-5 col-sm-5 col-xs-12 @else col-md-10 col-sm-10 col-xs-12 @endif">
                  ${{ number_format($item->flexm_part,4) }}
                </div>
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">MyMA Txn Cost :</label>
                <div class="@if($wallet) col-md-5 col-sm-5 col-xs-12 @else col-md-10 col-sm-10 col-xs-12 @endif">
                  ${{ number_format($item->myma_part,4) }}
                </div>
              </div>

              <label class="col-xs-12" for="title">Item Share</label>

              <div class="form-group row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="author_id">MyMA Comms Share :</label>
                <div class="@if($wallet) col-md-5 col-sm-5 col-xs-12 @else col-md-10 col-sm-10 col-xs-12 @endif">
                  @if(strtolower($item->type) == 'instore')
                    ${{ number_format($item->myma_share,4) }}
                  @else
                    ${{ number_format($item->myma_share,4) }}
                  @endif
                </div>
              </div>

              <div class="form-group row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="author_id">GST :</label>
                <div class="@if($wallet) col-md-5 col-sm-5 col-xs-12 @else col-md-10 col-sm-10 col-xs-12 @endif">
                  ${{ number_format($item->gst,4) }}

                </div>
              </div>

              <div class="form-group row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="author_id">Merchant Share :</label>
                <div class="@if($wallet) col-md-5 col-sm-5 col-xs-12 @else col-md-10 col-sm-10 col-xs-12 @endif">
                  @if(strtolower($item->type) == 'instore')
                    ${{ number_format( ($item->transaction_amount-$item->myma_part-$item->flexm_part-$item->gst),4) }}
                  @else
                    ${{ number_format($item->other_share,4) }}
                  @endif

                </div>
              </div>

            @if(strtolower($item->type) == 'instore')
            <div class="form-group row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="author_id">Remarks :</label>
                <div class="@if($wallet) col-md-5 col-sm-5 col-xs-12 @else col-md-10 col-sm-10 col-xs-12 @endif">
                  {{ $item->remarks }}
                  
                </div>
              </div>
            @endif
              @if($item->status != 'paid')

              <!-- <div class="form-group">
                <div class="col-md-9 col-sm-9 col-xs-9 col-md-offset-2">
                  <a href="{{ route('admin.transaction.edit', encrypt($item->id)) }}" class="btn btn-success">Edit Status</a>
                </div>
              </div> -->

              @endif
            </form>
          </div>
        </div>
      </div>
    </div>

@endsection
