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
            <form id="demo-form2" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left colored-form">

              <!-- <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Type :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{ ucfirst($item->type) }}
                </div>
                @if($wallet)
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <h4>
                    Uploaded Data
                  </h4>
                </div>
                @endif
              </div> -->

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Hash id :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{ $item->hash_id }}
                </div>
                @if($wallet)
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <span @if($wallet->hash_id != $item->hash_id) class="badge badge-danger" @endif>
                  {{ $wallet->hash_id or '-' }}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Provider :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{ $item->provider }}
                </div>
                @if($wallet)
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <span @if($wallet->provider != $item->provider) class="badge badge-danger" @endif>
                  {{ $wallet->provider or '-' }}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Delivery Method :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{ $item->delivery_method }}
                </div>
                @if($wallet)
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <span @if($wallet->delivery_method != $item->delivery_method) class="badge badge-danger" @endif>
                    {{ $wallet->delivery_method or '-' }}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Receive Country :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{ $item->receive_country }}
                </div>
                @if($wallet)
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <span @if($wallet->receive_country != $item->receive_country) class="badge badge-danger" @endif>
                    {{ $wallet->receive_country or '-' }}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Status :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{ $item->status }}
                </div>
                @if($wallet)
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <span @if($wallet->status != $item->status) class="badge badge-danger" @endif>
                    {{ $wallet->status or '-' }}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Payout Agent :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{ $item->payout_agent }}
                </div>
                @if($wallet)
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <span @if($wallet->payout_agent != $item->payout_agent) class="badge badge-danger" @endif>
                    {{ $wallet->payout_agent or '-' }}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Customer Fx :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{ $item->customer_fx }}
                </div>
                @if($wallet)
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <span @if($wallet->customer_fx != $item->customer_fx) class="badge badge-danger" @endif>
                    {{ $wallet->customer_fx or '-' }}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Send Currency :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{ $item->send_currency }}
                </div>
                @if($wallet)
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <span @if(strtolower($wallet->send_currency) != strtolower($item->send_currency)) class="badge badge-danger" @endif>
                    {{ strtoupper($wallet->send_currency) }}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Send Amount :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  ${{ number_format($item->send_amount,4) }}
                </div>
                @if($wallet)
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <span @if($wallet->send_amount != $item->send_amount) class="badge badge-danger" @endif>
                    ${{ number_format($wallet->send_amount,4) }}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Customer fixed fee :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  ${{ number_format($item->customer_fixed_fee,4) }}
                </div>
                @if($wallet)
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <span @if($wallet->customer_fixed_fee != $item->customer_fixed_fee) class="badge badge-danger" @endif>
                    ${{ number_format($wallet->customer_fixed_fee,4) }}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Total transaction amount :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  ${{ number_format($item->total_transaction_amount,4) }}
                </div>
                @if($wallet)
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <span @if($wallet->total_transaction_amount != $item->total_transaction_amount) class="badge badge-danger" @endif>
                    ${{ number_format($wallet->total_transaction_amount,4) }}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Transaction date :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{ $item->created_at->format('d/m/Y h:i A') }}
                </div>
                @if($wallet)
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <span @if($wallet->date_added != $item->date_added) class="badge badge-danger" @endif>
                    {{ $wallet->date_added->format('d/m/Y h:i A')}}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Receive currency :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{ $item->receive_currency }}
                </div>
                @if($wallet)
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <span @if($wallet->receive_currency != $item->receive_currency) class="badge badge-danger" @endif>
                    {{ $wallet->receive_currency or '-'}}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Receive amount :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  ${{ number_format($item->receive_amount,4) }}
                </div>
                @if($wallet)
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <span @if($wallet->receive_amount != $item->receive_amount) class="badge badge-danger" @endif>
                    ${{ number_format($wallet->receive_amount,4) }}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Crossrate :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  ${{ number_format($item->crossrate,4) }}
                </div>
                @if($wallet)
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <span @if($wallet->crossrate != $item->crossrate) class="badge badge-danger" @endif>
                    ${{ number_format($wallet->crossrate,4) }}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Provider amount fee currency :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{ $item->provider_amount_fee_currency }}
                </div>
                @if($wallet)
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <span @if(strtolower($wallet->provider_amount_fee_currency) != strtolower($item->provider_amount_fee_currency)) class="badge badge-danger" @endif>
                    {{ strtoupper($wallet->provider_amount_fee_currency) }}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Provider amount fee :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  ${{ number_format($item->provider_amount_fee,4) }}
                </div>
                @if($wallet)
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <span @if($wallet->provider_amount_fee != $item->provider_amount_fee) class="badge badge-danger" @endif>
                    ${{ number_format($wallet->provider_amount_fee,4) }}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Provider exchange rate :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  ${{ number_format($item->provider_exchange_rate,4) }}
                </div>
                @if($wallet)
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <span @if($wallet->provider_exchange_rate != $item->provider_exchange_rate) class="badge badge-danger" @endif>
                    ${{ number_format($wallet->provider_exchange_rate,4) }}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Send amount rails currency :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{ $item->send_amount_rails_currency }}
                </div>
                @if($wallet)
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <span @if($wallet->send_amount_rails_currency != $item->send_amount_rails_currency) class="badge badge-danger" @endif>
                    {{ $wallet->send_amount_rails_currency or '-'}}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Send amount rails :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{ $item->send_amount_rails }}
                </div>
                @if($wallet)
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <span @if($wallet->send_amount_rails != $item->send_amount_rails) class="badge badge-danger" @endif>
                    {{ $wallet->send_amount_rails or '-'}}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Send amount before fx :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  ${{ number_format($item->send_amount_before_fx,4) }}
                </div>
                @if($wallet)
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <span @if($wallet->send_amount_before_fx != $item->send_amount_before_fx) class="badge badge-danger" @endif>
                    ${{ number_format($wallet->send_amount_before_fx,4) }}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Send amount after fx :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  ${{ number_format($item->send_amount_after_fx,4) }}
                </div>
                @if($wallet)
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <span @if($wallet->send_amount_after_fx != $item->send_amount_after_fx) class="badge badge-danger" @endif>
                    ${{ number_format($wallet->send_amount_after_fx,4) }}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Routing params :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{ $item->routing_params }}
                </div>
                @if($wallet)
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <span @if($wallet->routing_params != $item->routing_params) class="badge badge-danger" @endif>
                    {{ $wallet->routing_params or '-'}}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Ref id :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{ $item->ref_id }}
                </div>
                @if($wallet)
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <span @if($wallet->ref_id != $item->ref_id) class="badge badge-danger" @endif>
                    {{ $wallet->ref_id or '-'}}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Transaction code :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{ $item->transaction_code }}
                </div>
                @if($wallet)
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <span @if($wallet->transaction_code != $item->transaction_code) class="badge badge-danger" @endif>
                    {{ $wallet->transaction_code or '-'}}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Sender first name :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{ $item->sender_first_name }}
                </div>
                @if($wallet)
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <span @if($wallet->sender_first_name != $item->sender_first_name) class="badge badge-danger" @endif>
                    {{ $wallet->sender_first_name or '-'}}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Sender middle name :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{ $item->sender_middle_name }}
                </div>
                @if($wallet)
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <span @if($wallet->sender_middle_name != $item->sender_middle_name) class="badge badge-danger" @endif>
                    {{ $wallet->sender_middle_name or '-'}}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Sender last name :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{ $item->sender_last_name }}
                </div>
                @if($wallet)
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <span @if($wallet->sender_last_name != $item->sender_last_name) class="badge badge-danger" @endif>
                    {{ $wallet->sender_last_name or '-'}}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Sender mobile number :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{ $item->sender_mobile_number }}
                </div>
                @if($wallet)
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <span @if($wallet->sender_mobile_number != $item->sender_mobile_number) class="badge badge-danger" @endif>
                    {{ $wallet->sender_mobile_number or '-'}}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Beneficiary first name :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{ $item->ben_first_name }}
                </div>
                @if($wallet)
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <span @if($wallet->ben_first_name != $item->ben_first_name) class="badge badge-danger" @endif>
                    {{ $wallet->ben_first_name or '-'}}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Beneficiary middle name :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{ $item->ben_middle_name }}
                </div>
                @if($wallet)
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <span @if($wallet->ben_middle_name != $item->ben_middle_name) class="badge badge-danger" @endif>
                    {{ $wallet->ben_middle_name or '-'}}
                  </span>
                </div>
                @endif
              </div>

              <div class="form-group en language row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Beneficiary last name :</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{ $item->ben_last_name }}
                </div>
                @if($wallet)
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <span @if($wallet->ben_last_name != $item->ben_last_name) class="badge badge-danger" @endif>
                    {{ $wallet->ben_last_name or '-'}}
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
