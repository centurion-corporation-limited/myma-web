@extends('layouts.admin')

@section('content')
<div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Orders</h2>
                    <!-- <ul class="nav navbar-right panel_toolbox">
                      <li><a href="{{ route('admin.restaurant.add') }}"><i class="fa fa-plus fa-2x"></i></a>
                      </li>
                    </ul> -->
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <form action="{{ route('admin.order.list') }}" method="GET" class="form-inline">
                          <!-- <div class="form-group">
                              {!!Form::select('type', ['Please select placeholder', 'home' => 'Home Slider', 'landing' => 'Popup'], '', ['class' => 'form-control'])!!}
                          </div>

                          <div class="form-group">
                              {!!Form::select('adv_type', ['Please select Ad type', '2' => 'Date', '1' => 'Impression'], '', ['class' => 'form-control'])!!}
                          </div> -->
                          <div class="form-group">
                              {!!Form::select('status_id', $statuses, Request::input('status_id'), ['class' => 'form-control'])!!}
                          </div>

                          <div class="form-group">
                            <div class="form-control-btn">

                          <button class="btn btn-success" type="submit">Search</button>
                          <a href="{{ route('admin.order.list')}}" class="btn btn-success">Reset</a>
                          </div>
                          </div>
                    </form>
                    <table id="datatable-responsivee" class="table table-striped table-bordered dt-responsive nowrap table-hover" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th>@sortablelink('id', 'Order ID')</th>
                          <th>@sortablelink('delivery_date', 'Order Date')</th>
                          <th>Package Name</th>
                          <th>@sortablelink('user_id', 'User')</th>
                          <th>@sortablelink('total', 'Amount')</th>
                          @if($auth_user->hasRole('food-admin'))
                          <th>@sortablelink('status_id', 'Status')</th>
                          @endif
                          <th class="no-sort">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($items as $key => $item)
                        <tr>
                          <td>{{ (($items->currentPage()-1)*$limit)+(++$key) }}</td>
                          <td>{{ str_pad($item->id, '7', '0', STR_PAD_LEFT) }}</td>
                          <td>{{ $item->delivery_date }}</td>
                          <td>
                            <?php $i = 0; ?>
                            @foreach($item->items as $order_item)
                            @if($order_item->item)
                            {{ $order_item->item->name }}
                            @endif
                            @endforeach

                          </td>
                          <td>{{ $item->user->name or '' }}</td>
                          <td>S${{ number_format($item->total, 2) }}</td>
                          @if($auth_user->hasRole('food-admin'))
                          <td>{{ $item->status->name or '' }}</td>
                          @endif
                          <td>
                            <a title="View Order" href="{{ route('admin.order.view', encrypt($item->id)) }}"><i class="fa fa-2x fa-eye"></i></a>

                              <a title="Customer Invoice" target="_blank" href="{{ route('food.customer.order.print', $item->id) }}"><i class="fa fa-2x fa-file-text"></i></a>
                              <!-- <a title="WLC Invoice" target="_blank" href="{{ route('admin.order.invoice.wlc', $item->id) }}"><i class="fa fa-2x fa-file-text"></i></a> -->
                              <!-- <a title="Merchant Invoice" target="_blank" href="{{ route('admin.order.invoice.merchant', $item->id) }}"><i class="fa fa-2x fa-file-text"></i></a> -->

                            @if($item->status_id == 11 || $item->status_id == 13)
                            @endif
                            <!-- <a href="{{ route('admin.order.delete', ['id' => $item->id, '_token' => csrf_token()]) }}" data-message="Are you sure about deleting this?" class="post-deletee"><i class="fa fa-2x fa-trash-o"></i></a> -->
                          </td>
                        </tr>
                        @endforeach
                      </tbody>
      </table>
      @include('partials.paging', $items)

    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="{{ static_file('js/plugins/bootbox.min.js') }}"></script>
<script>
$('body').on('click', '.post-delete', function (event) {
    event.preventDefault();

    var message = $(this).data('message'),
        url = $(this).attr('href');

    bootbox.dialog({
        message: message,
        buttons: {
            danger: {
                label: "Yes",
                //className: "red",
                callback: function () {
                    $.ajax({
                        url: url,
                      //  type: 'delete',
                        //container: '#pjax-container'
                    }).done(function(data){
                      //console.log(data);
                      location.reload();
                    });
                }
            },
            success: {
                label: "Cancel",
                //className: "green"
            }
        }
    });
})
</script>
@stop
