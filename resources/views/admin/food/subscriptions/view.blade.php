@extends('layouts.admin')

@section('content')

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Subscriptions detail</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <br />
            <form id="demo-form2" >

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

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Status</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  {{ $item->status->name or ''}}
                </div>
              </div>

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
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Naanstap charge</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                    S${{ $item->naanstap }}

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
              <div class="ln_solid"></div>

              <table class="table table-striped table-bordered nowrap table-hover" cellspacing="0" width="100%">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>Amount</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($item->items as $key => $tt)
                  <tr>
                    <td>{{ ++$key }}</td>
                    <td>{{ $tt->item->name or '' }}</td>
                    <td>{{ $tt->quantity }}</td>
                    <td>{{ $tt->quantity*($tt->item?$tt->item->price:0) }}</td>
                    <td><a href="{{ route('admin.subscription.detail', $tt->id) }}"><i class="fa fa-eye"></i></a></td>
                  </tr>
                  @endforeach
                </tbody>
              </table>

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
