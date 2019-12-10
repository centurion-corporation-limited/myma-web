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
                <div class="col-md-10 col-sm-10 col-xs-12">
                  {{ ucfirst($item->type) }}
                </div>
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Mobile :</label>
                <div class="col-md-10 col-sm-10 col-xs-12">
                  {{ $item->phone_no }}
                </div>
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Wallet User Name :</label>
                <div class="col-md-10 col-sm-10 col-xs-12">
                  {{ $item->wallet_user_name }}
                </div>
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">MID :</label>
                <div class="col-md-10 col-sm-10 col-xs-12">
                  {{ $item->mid }}
                </div>
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">TID :</label>
                <div class="col-md-10 col-sm-10 col-xs-12">
                  {{ $item->tid }}
                </div>
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Merchant Name :</label>
                <div class="col-md-10 col-sm-10 col-xs-12">
                  {{ $item->merchant_name }}
                </div>
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Transaction Date :</label>
                <div class="col-md-10 col-sm-10 col-xs-12">
                  {{ $item->created_at }}
                </div>
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Transaction Currency :</label>
                <div class="col-md-10 col-sm-10 col-xs-12">
                  {{ $item->transaction_currency }}
                </div>
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Transaction Reference No :</label>
                <div class="col-md-10 col-sm-10 col-xs-12">
                  {{ $item->transaction_ref_no }}
                </div>
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Transaction Status :</label>
                <div class="col-md-10 col-sm-10 col-xs-12">
                  {{ $item->transaction_status }}
                </div>
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Transaction Code :</label>
                <div class="col-md-10 col-sm-10 col-xs-12">
                  {{ $item->transaction_code }}
                </div>
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Transaction Amount :</label>
                <div class="col-md-10 col-sm-10 col-xs-12">
                  {{ $item->transaction_amount }}
                </div>
              </div>
              
              <label class="col-xs-12" for="title">Transaction Charges</label>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">WLC Share :</label>
                <div class="col-md-10 col-sm-10 col-xs-12">
                  {{ $item->myma_share }}
                </div>
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Naanstap Share :</label>
                <div class="col-md-10 col-sm-10 col-xs-12">
                  {{ $item->naanstap_pay }}
                </div>
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Flexm Share :</label>
                <div class="col-md-10 col-sm-10 col-xs-12">
                  {{ $item->flexm_part+$item->myma_part }}
                </div>
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Delivery Charges :</label>
                <div class="col-md-10 col-sm-10 col-xs-12">
                  {{ $item->order->naanstap or 0 }}
                </div>
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Discount :</label>
                <div class="col-md-10 col-sm-10 col-xs-12">
                  {{ $item->order->discount or 0 }}
                </div>
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Merchant Share :</label>
                <div class="col-md-10 col-sm-10 col-xs-12">
                  {{ $item->food_share }}
                </div>
              </div>

            </form>
          </div>
        </div>
      </div>
    </div>

@endsection
