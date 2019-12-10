@extends('layouts.admin')

@section('content')
<div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Dormitory Maintenance</h2>
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
                    <form action="{{ route('admin.maintenance.list') }}" method="GET" class="form-inline">
                        <div class="form-group">
                            <input type="text" placeholder="Case ID" name="id" value="{{ Request::input('id') }}" class="form-control input-small">
                        </div>

                        <div class="form-group">
                            <input type="text" placeholder="User" name="username" value="{{ Request::input('username') }}" class="form-control">
                        </div>

                        <button class="btn btn-success" type="submit">Search</button>
                        <a href="{{ route('admin.maintenance.list') }}" class="btn btn-success">Reset</a>
					</form>

                    <table id="datatable-responsivee" class="table table-striped table-bordered dt-responsive nowrap table-hover foo_table" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                          <!-- <th>ID</th> -->
                          <th>Case ID</th>
                          <th>@sortablelink('user_id', 'User')</th>
                          <th>Fin No</th>
                          <th>@sortablelink('dormitory_id', 'Dormitory')</th>
                          <th>@sortablelink('created_at', 'Created')</th>
                          <th>@sortablelink('status_id', 'Status')</th>
                          <th class="no-sort">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($items as $key => $item)
                        <tr>
                          <!-- <td>{{ ++$key }}</td> -->
                          <td>{{ $item->id }}</td>
                          <td>
                              @if($item->user && $item->user->name)
                                <a type="button" user_id="{{ $item->user_id }}" data-toggle="modal" data-target="#userModal">
                                    {{ $item->user->name or '--'}}
                                </a>
                              @else
                                --
                              @endif
                          </td>
                          <td>{{ $item->user->profile->fin_no or  '-' }}</td>
                          <td>{{ $item->dormitory->name or '--' }}</td>
                          <td>{{ $item->created_at->format('d/m/Y H:i:A ') }}</td>
                          <td><span class="label label-default">{{ $item->status->name or '--' }}</span></td>
                          <td>
                            <!-- <a href="{{ route('admin.maintenance.edit', $item->id) }}"><i class="fa fa-2x fa-edit"></i></a> -->
                            <a href="{{ route('admin.maintenance.view', encrypt($item->id)) }}"><i class="fa fa-2x fa-eye"></i></a>
                            @if($auth_user->can('delete.maintenance-delete'))
                            <a href="{{ route('admin.maintenance.delete', ['id' => $item->id, '_token' => csrf_token()]) }}" data-message="Are you sure about deleting this?" class="post-deletee"><i class="fa fa-2x fa-trash-o"></i></a>
                            @endif
                            <a href="{{ route('admin.maintenance.export', encrypt($item->id)) }}"><i class="fa fa-2x fa-file-pdf-o"></i></a>
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
