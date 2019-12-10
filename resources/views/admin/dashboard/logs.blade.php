@extends('layouts.admin')

@section('styles')

<style>
.paging_full_numbers{
    height:88px;
}
.dataTables_filter {
	width: auto;
}

</style>
@endsection
@section('content')
<div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Logs</h2>
                    <!-- <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                      <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                        <ul class="dropdown-menu" role="menu">
                          <li><a href="#">Settings 1</a>
                          </li>
                          <li><a href="#">Settings 2</a>
                          </li>
                        </ul>
                      </li>
                      <li><a class="close-link"><i class="fa fa-close"></i></a>
                      </li>
                    </ul> -->
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content x_content_form">
                    <form action="{{ route('admin.activity.logs') }}" method="GET" class="form-inline">

                          <div class="row flex-row transaction-form">
                              <label class="control-label" for="key">Keyword</label>
                              <div class="input-group">
                                <input type="text" placeholder="Type anything to search" name="keyword" value="{{ Request::input('keyword') }}" class="form-control input-small">

                              </div>
                          </div>

                          <!-- <div class="row flex-row transaction-form">
                              <label class="control-label" for="dormitory_id">Date range </label>
                              <div class="input-group input-daterange">
                                  <input autocomplete="off" type="text" placeholder="From" class="form-control" name="start" value="{{ Request::input('start') }}">
                                  <div class="input-group-addon">to</div>
                                  <input autocomplete="off" type="text" placeholder="To" class="form-control" name="end" value="{{ Request::input('end') }}">
                              </div>
                          </div> -->

                          <div class="row flex-row  button-tran">
                            <button class="btn btn-success" type="submit">Search</button>
                            <a href="{{ route('admin.activity.logs')}}" class="btn btn-success">Reset</a>
                            <!-- <button class="btn btn-success request_btn" type="button">Show Request/Response</button> -->
                          </div>
                    </form>
                    <table id="htmltableID" class="table table-striped table-bordered dt-responsive nowrap table-hover foo_table" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th class="no-sort">Activity</th>
                          <th class="no-sort">User</th>
                          <th class="no-sort">Phone</th>
                          <th class="no-sort">Role</th>
                          <th class="no-sort">IP</th>
                          <th data-breakpoints="sm xs" class="">Time</th>
                          <th data-breakpoints="lg md sm xs" class="request">Url</th>
                          <th data-breakpoints="lg md sm xs" class="request">Request</th>
                          <th data-breakpoints="lg md sm xs" class="request">Response</th>

                          <!-- <th class="no-sort">Action</th> -->
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($items as $key => $item)
                        <tr>
                          <td>{{ (($items->currentPage()-1)*50)+(++$key) }}</td>
                          <td>{{ $item->text }}</td>
                          <td>{{ $item->user->name or '-' }}</td>
                          <td>{{ $item->user->profile->phone or '-' }}</td>
                          <td>{{ $item->role or '' }}</td>
                          <td>{{ $item->ip_address }}</td>
                          <td>{{ $item->created_at->format('d/m/Y h:i A') }}</td>
                          <td class="request">
                            @if($item->url)
                              {{ $item->url }}
                            @endif
                          </td>
                          <td class="request">
                            @if($item->request)
                              {{ $item->request }}
                            @endif
                          </td>
                          <td class="request">
                            @if($item->response)
                              {{ $item->response }}
                            @endif
                          </td>
                          {{-- <td>
                            <!-- <a href="{{ route('admin.advertisement.edit', $item->id) }}"><i class="fa fa-2x fa-edit"></i></a> -->
                            <!-- <a href="{{ route('admin.logs.delete', ['id' => $item->id, '_token' => csrf_token()]) }}" data-message="Are you sure about deleting this?" class="post-deletee"><i class="fa fa-2x fa-trash-o"></i></a> -->
                            <!-- <a href="{{ route('admin.advertisement.edit', $item->id) }}"><i class="fa fa-2x fa-bar-chart-o"></i></a> -->
                           </td> --}}
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
$('.request_btn').on('click', function(){
  if($(this).hasClass('opened')){
    $(this).removeClass('opened');
    $('.request').addClass('hide');
  }else{
    $(this).addClass('opened');
    $('.request').removeClass('hide');
  }


});
    // oTable = $('#htmltableID').dataTable({
    //     // "processing": true,
    //     // "serverSide": true,
    //
    //     "sPaginationType": "simple_numbers",
    //     "bServerSide": true,
    //     ajax:{
    //         'url' :  "{{ route('admin.activity.logs') }}",
    //         'data': function ( d ) {
    //             d.myKey = $('input[type=search]').val();
    //         }
    //     },
    //     "sServerMethod": "GET",
    //     "iDisplayLength": 10
    // });
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
