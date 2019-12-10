@extends('layouts.admin')

@section('content')
<div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Dormitory List</h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li>
                          @if($auth_user->can('create.dormitory-add'))
                          <a href="{{ route('admin.dormitory.add') }}" class="btn btn-success">Add</a>
                          @endif
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <table id="datatable-responsivee" class="table table-striped table-bordered dt-responsive nowrap table-hover foo_table" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th>@sortablelink('manager_id', 'Manager')</th>
                          <th>@sortablelink('name', 'Dormitory')</th>
                          <th>@sortablelink('status_id', 'Status')</th>
                          <th class="no-sort">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($items as $key => $item)
                        <tr>
                          <td>{{ (($items->currentPage()-1)*10)+(++$key) }}</td>
                          <td>
                              @if(isset($item->manager) && $item->manager->name)
                                <a type="button" user_id="{{ $item->manager_id }}" data-toggle="modal" data-target="#userModal">
                                    {{ $item->manager->name or '--'}}
                                </a>
                              @else
                                --
                              @endif
                          </td>
                          <td>{{ $item->name or '--' }}</td>
                          <td><span class="label label-default">{{ $item->status->name or '--' }}</span></td>
                          <td>
                            <a href="{{ route('admin.dormitory.edit', encrypt($item->id)) }}"><i class="fa fa-2x fa-edit"></i></a>
                            <a href="{{ route('admin.dormitory.delete', ['id' => $item->id, '_token' => csrf_token()]) }}" data-message="Are you sure about deleting this?" class="post-deletee"><i class="fa fa-2x fa-trash-o"></i></a>
                            <!-- <a href="{{ route('admin.dormitory.view', encrypt($item->id)) }}"><i class="fa fa-2x fa-eye"></i></a> -->

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
