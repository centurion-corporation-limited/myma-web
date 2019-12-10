@extends('layouts.admin')

@section('content')

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Order detail</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <form id="demo-form2" class="colord-form" >
              <div class="form-group">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12">Item Type</label>
                  <div class="col-md-6 col-sm-9 col-xs-12">
                      @if($item->type == 'single')
                        A la Carte
                      @else
                        Package
                      @endif
                  </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Transaction Id</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  {{ $item->transaction_id }}
                </div>
              </div>
              @if($auth_user->hasRole('food-admin'))
              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Status</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  {{ $item->status->name or ''}}
                </div>
              </div>
              @endif
              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">User</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  {{ $item->user->name or '' }}
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Address</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    @if($item->dormitory)
                        {{ $item->dormitory->name }}
                    @else
                        {{ $item->block_no}}, {{ $item->address }}
                    @endif
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Delivery type</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    {{ ucfirst($item->delivery_type) }}

                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Total</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    S${{ $item->total }}
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Discount</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    S${{ $item->discount }}

                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Delivery charge</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    S${{ $item->naanstap }}
                </div>
              </div>

              @if($auth_user->hasRole('food-admin'))

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Naanstap share</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    S${{ $item->transaction->naanstap_pay or '-' }}
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">WLC share</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    S${{ $item->transaction->myma_share or '-' }}
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Flexm charges</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    S${{ @$item->transaction->flexm_part+@$item->transaction->myma_part }}
                </div>
              </div>
              @endif

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Merchant share</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    S${{ $item->transaction->food_share or '-' }}
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Delivery Date</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    {{ $item->delivery_date }}

                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Delivery Time</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    {{ $item->delivery_time }}

                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Driver Name</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    {{ $item->driver->name or '-' }}

                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Merchant Rep.</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    {{ $item->merchant_rep or '-' }}
                </div>
              </div>

              {{-- <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Picked At</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    {{ $item->delivery_time }}

                </div>
              </div> --}}

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Customer Rep.</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    {{ $item->customer_rep or '-' }}
                </div>
              </div>

              {{-- <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Delivery At</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    {{ $item->delivery_time }}

                </div>
              </div> --}}
              <div class="ln_solid"></div>

              <table class="table table-striped table-bordered nowrap table-hover" cellspacing="0" width="100%">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>Amount</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($item->items as $key => $tt)
                  <tr>
                    <td>{{ ++$key }}</td>
                    <td>{{ $tt->item->name or '' }}</td>
                    <td>{{ $tt->quantity }}</td>
                    <td>{{ $tt->quantity*@$tt->item->price }}</td>
                  </tr>
                  @endforeach
                </tbody>
              </table>

              @if($item->type == 'package')
              <div class="ln_solid"></div>

              <table class="table table-striped table-bordered nowrap table-hover" cellspacing="0" width="100%">
                <thead>
                  <tr>
                    <th>Date</th>
                    <th>Breakfast</th>
                    <th>Lunch</th>
                    <th>Dinner</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($item->subs as $key => $tt)
                  <tr>
                    <td>{{ $tt->delivery_date }}</td>
                    <td>{{ $tt->breakfast }}</td>
                    <td>{{ $tt->lunch }}</td>
                    <td>{{ $tt->dinner }}</td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
              @endif
              <div class="ln_solid"></div>
              <div class="form-group">
                <div class="col-md-3 col-sm-3 col-xs-12"></div>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <!-- <button class="btn btn-primary" type="button">Cancel</button> -->
                  <!-- <button class="btn btn-primary" type="reset">Reset</button> -->
                  <!-- <a href="{{ route('admin.order.edit', encrypt($item->id)) }}" class="btn btn-success">Edit</a> -->

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

</script>
@endsection
