@extends('layouts.admin')

@section('content')
<div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Subscriptions</h2>
                    <!-- <ul class="nav navbar-right panel_toolbox">
                      <li><a href="{{ route('admin.restaurant.add') }}"><i class="fa fa-plus fa-2x"></i></a>
                      </li>
                    </ul> -->
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <table id="datatable-responsivee" class="table table-striped table-bordered dt-responsive nowrap table-hover" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th>@sortablelink('id', 'Order ID')</th>
                          <th>@sortablelink('user_id', 'User')</th>
                          <th>@sortablelink('total', 'Amount')</th>
                          <th>@sortablelink('status_id', 'Status')</th>
                          <th class="no-sort">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($items as $key => $item)
                        <tr>
                          <td>{{ (($items->currentPage()-1)*$limit)+(++$key) }}</td>
                          <td>{{ str_pad($item->id, '7', '0', STR_PAD_LEFT) }}</td>
                          <td>{{ $item->user->name or '' }}</td>
                          <td>S${{ number_format($item->total, 2) }}</td>
                          <td>{{ $item->status->name or '' }}</td>
                          <td>
                            <a href="{{ route('admin.subscriptions.view', encrypt($item->id)) }}"><i class="fa fa-2x fa-eye"></i></a>
                            <!-- <a href="{{ route('admin.subscriptions.delete', ['id' => $item->id, '_token' => csrf_token()]) }}" data-message="Are you sure about deleting this?" class="post-deletee"><i class="fa fa-2x fa-trash-o"></i></a> -->
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
