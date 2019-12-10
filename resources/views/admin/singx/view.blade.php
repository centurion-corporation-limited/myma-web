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
            <h2>Detail</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <br />
            <form id="demo-form2" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left">

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">User :</label>
                <div class="col-md-5 col-sm-5 col-xs-12">
                  {{ $item->user->name or '-' }}
                </div>
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Account Number :</label>
                <div class="col-md-5 col-sm-5 col-xs-12">
                  {{ $item->accountNumber }}
                </div>
                @if($wallet)
                <div class="col-md-5 col-sm-5 col-xs-12">
                  <span @if($wallet->accountNumber != $item->accountNumber) class="badge badge-danger" @endif>
                  {{ $wallet->accountNumber or '-' }}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Transaction Date :</label>
                <div class="col-md-5 col-sm-5 col-xs-12">
                  {{ $item->transactionDT }}
                </div>
                @if($wallet)
                <div class="col-md-5 col-sm-5 col-xs-12">
                  <span @if($wallet->transactionDT != $item->transactionDT) class="badge badge-danger" @endif>
                    {{ $wallet->transactionDT or '-' }}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">User Txn Id :</label>
                <div class="col-md-5 col-sm-5 col-xs-12">
                  {{ $item->userTxnId }}
                </div>
                @if($wallet)
                <div class="col-md-5 col-sm-5 col-xs-12">
                  <span @if($wallet->userTxnId != $item->userTxnId) class="badge badge-danger" @endif>
                    {{ $wallet->userTxnId or '-' }}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Transaction Id :</label>
                <div class="col-md-5 col-sm-5 col-xs-12">
                  {{ $item->transactionId }}
                </div>
                @if($wallet)
                <div class="col-md-5 col-sm-5 col-xs-12">
                  <span @if($wallet->status != $item->transactionId) class="badge badge-danger" @endif>
                    {{ $wallet->transactionId or '-' }}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Account Map Id :</label>
                <div class="col-md-5 col-sm-5 col-xs-12">
                  {{ $item->accountMapId }}
                </div>
                @if($wallet)
                <div class="col-md-5 col-sm-5 col-xs-12">
                  <span @if($wallet->accountMapId != $item->accountMapId) class="badge badge-danger" @endif>
                    {{ $wallet->accountMapId or '-' }}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Activity Id :</label>
                <div class="col-md-5 col-sm-5 col-xs-12">
                  {{ $item->activityId }}
                </div>
                @if($wallet)
                <div class="col-md-5 col-sm-5 col-xs-12">
                  <span @if($wallet->activityId != $item->activityId) class="badge badge-danger" @endif>
                    {{ $wallet->activityId or '-' }}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Contact Id :</label>
                <div class="col-md-5 col-sm-5 col-xs-12">
                  {{ $item->contactId }}
                </div>
                @if($wallet)
                <div class="col-md-5 col-sm-5 col-xs-12">
                  <span @if(strtolower($wallet->contactId) != strtolower($item->contactId)) class="badge badge-danger" @endif>
                    {{ strtoupper($wallet->contactId) }}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Customer Id :</label>
                <div class="col-md-5 col-sm-5 col-xs-12">
                  {{ $item->customerId }}
                </div>
                @if($wallet)
                <div class="col-md-5 col-sm-5 col-xs-12">
                  <span @if($wallet->customerId != $item->customerId) class="badge badge-danger" @endif>
                    {{ $wallet->customerId or '-'}}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Enquiry Id :</label>
                <div class="col-md-5 col-sm-5 col-xs-12">
                  {{ $item->enquiryId }}
                </div>
                @if($wallet)
                <div class="col-md-5 col-sm-5 col-xs-12">
                  <span @if($wallet->enquiryId != $item->enquiryId) class="badge badge-danger" @endif>
                    {{ $wallet->enquiryId or '-'}}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Reason Id :</label>
                <div class="col-md-5 col-sm-5 col-xs-12">
                  {{ $item->reasonId }}
                </div>
                @if($wallet)
                <div class="col-md-5 col-sm-5 col-xs-12">
                  <span @if($wallet->reasonId != $item->reasonId) class="badge badge-danger" @endif>
                    {{ $wallet->reasonId or '-'}}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Receiver Bank Id :</label>
                <div class="col-md-5 col-sm-5 col-xs-12">
                  {{ $item->receiverBankId }}
                </div>
                @if($wallet)
                <div class="col-md-5 col-sm-5 col-xs-12">
                  <span @if($wallet->receiverBankId != $item->receiverBankId) class="badge badge-danger" @endif>
                    {{ $wallet->receiverBankId or '-'}}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Receiver Relationship :</label>
                <div class="col-md-5 col-sm-5 col-xs-12">
                  {{ $item->receiverRelationship }}
                </div>
                @if($wallet)
                <div class="col-md-5 col-sm-5 col-xs-12">
                  <span @if($wallet->receiverRelationship != $item->receiverRelationship) class="badge badge-danger" @endif>
                    {{ $wallet->receiverRelationship or '-'}}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Promo Code :</label>
                <div class="col-md-5 col-sm-5 col-xs-12">
                  {{ $item->promoCode }}
                </div>
                @if($wallet)
                <div class="col-md-5 col-sm-5 col-xs-12">
                  <span @if($wallet->promoCode != $item->promoCode) class="badge badge-danger" @endif>
                    {{ $wallet->promoCode or '-'}}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Txn reference :</label>
                <div class="col-md-5 col-sm-5 col-xs-12">
                  {{ $item->txnreference }}
                </div>
                @if($wallet)
                <div class="col-md-5 col-sm-5 col-xs-12">
                  <span @if($wallet->txnreference != $item->txnreference) class="badge badge-danger" @endif>
                    {{ $wallet->txnreference or '-'}}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Receiver Name :</label>
                <div class="col-md-5 col-sm-5 col-xs-12">
                  {{ $item->receiverName }}
                </div>
                @if($wallet)
                <div class="col-md-5 col-sm-5 col-xs-12">
                  <span @if($wallet->receiverName != $item->receiverName) class="badge badge-danger" @endif>
                    {{ $wallet->receiverName or '-'}}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Bank Name :</label>
                <div class="col-md-5 col-sm-5 col-xs-12">
                  {{ $item->bankName }}
                </div>
                @if($wallet)
                <div class="col-md-5 col-sm-5 col-xs-12">
                  <span @if($wallet->bankName != $item->bankName) class="badge badge-danger" @endif>
                    {{ $wallet->bankName or '-' }}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Country Name :</label>
                <div class="col-md-5 col-sm-5 col-xs-12">
                  {{ $item->countryName }}
                </div>
                @if($wallet)
                <div class="col-md-5 col-sm-5 col-xs-12">
                  <span @if(strtolower($wallet->countryName) != strtolower($item->countryName)) class="badge badge-danger" @endif>
                    {{ strtoupper($wallet->countryName) }}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">First Name :</label>
                <div class="col-md-5 col-sm-5 col-xs-12">
                  {{ $item->firstName }}
                </div>
                @if($wallet)
                <div class="col-md-5 col-sm-5 col-xs-12">
                  <span @if($wallet->firstName != $item->firstName) class="badge badge-danger" @endif>
                    {{ $wallet->firstName or '-'}}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Last Name :</label>
                <div class="col-md-5 col-sm-5 col-xs-12">
                  {{ $item->lastName }}
                </div>
                @if($wallet)
                <div class="col-md-5 col-sm-5 col-xs-12">
                  <span @if($wallet->lastName != $item->lastName) class="badge badge-danger" @endif>
                    {{ $wallet->lastName or '-'}}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Sent amount :</label>
                <div class="col-md-5 col-sm-5 col-xs-12">
                  ${{ number_format($item->sent_amount,4) }}
                </div>
                @if($wallet)
                <div class="col-md-5 col-sm-5 col-xs-12">
                  <span @if($wallet->sent_amount != $item->sent_amount) class="badge badge-danger" @endif>
                    ${{ number_format($wallet->sent_amount,4) }}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Singx fee :</label>
                <div class="col-md-5 col-sm-5 col-xs-12">
                  ${{ number_format($item->singx_fee,4) }}
                </div>
                @if($wallet)
                <div class="col-md-5 col-sm-5 col-xs-12">
                  <span @if($wallet->singx_fee != $item->singx_fee) class="badge badge-danger" @endif>
                    ${{ number_format($wallet->singx_fee,4) }}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Received Amount :</label>
                <div class="col-md-5 col-sm-5 col-xs-12">
                  ${{ number_format($item->received_amount,4) }}
                </div>
                @if($wallet)
                <div class="col-md-5 col-sm-5 col-xs-12">
                  <span @if($wallet->received_amount != $item->received_amount) class="badge badge-danger" @endif>
                    ${{ number_format($wallet->received_amount,4) }}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Exchange rate :</label>
                <div class="col-md-5 col-sm-5 col-xs-12">
                  ${{ number_format($item->exchange_rate,4) }}
                </div>
                @if($wallet)
                <div class="col-md-5 col-sm-5 col-xs-12">
                  <span @if($wallet->exchange_rate != $item->exchange_rate) class="badge badge-danger" @endif>
                    ${{ number_format($wallet->exchange_rate,4) }}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Myma Part :</label>
                <div class="col-md-5 col-sm-5 col-xs-12">
                  ${{ number_format($item->myma_part,4) }}
                </div>
                @if($wallet)
                <div class="col-md-5 col-sm-5 col-xs-12">
                  <span @if($wallet->myma_part != $item->myma_part) class="badge badge-danger" @endif>
                    ${{ number_format($wallet->myma_part,4) }}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Singx Part :</label>
                <div class="col-md-5 col-sm-5 col-xs-12">
                  ${{ number_format($item->singx_part,4) }}
                </div>
                @if($wallet)
                <div class="col-md-5 col-sm-5 col-xs-12">
                  <span @if($wallet->singx_part != $item->singx_part) class="badge badge-danger" @endif>
                    ${{ number_format($wallet->singx_part,4) }}
                  </span>
                </div>
                @endif
              </div>

              <div class="ln_solid"></div>
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
