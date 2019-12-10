@extends('layouts.admin')

@section('content')
<div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Menu List</h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a href="{{ route('admin.menu.add') }}" class="btn btn-success">Add Menu</a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content x_content_form">
                    <form action="{{ route('admin.menu.list') }}" method="GET" class="form-inline">
                          <div class="form-group">
                              <input type="text" placeholder="Name" name="name" value="{{ Request::input('name') }}" class="form-control input-small">
                          </div>

                          <div class="form-group">
                              {!!Form::select('status', ['' => 'Please select status', '1' => 'Enabled', '0' => 'Disabled'], Request::input('status'), ['class' => 'form-control'])!!}
                          </div>

                          <div class="form-group">
                              {!!Form::select('access', ['0' => 'Please select type', 'free' => 'Free', 'registered' => 'Registered'], Request::input('access'), ['class' => 'form-control'])!!}
                          </div>

                          <button class="btn btn-success" type="submit">Search</button>
                          <a href="{{ route('admin.menu.list') }}" class="btn btn-success">Reset</a>
                    </form>
                    <table id="datatable-responsivee" class="table table-striped table-bordered dt-responsive nowrap foo_table" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th>@sortablelink('name', 'Name')</th>
                          <th>@sortablelink('active', 'Status')</th>
                          <th>@sortablelink('order', 'Order')</th>
                          <th class="no-sort">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($items as $key => $item)
                        <tr>
                          <td>{{ (($items->currentPage()-1)*10)+(++$key) }}</td>
                          <td>{{ $item->name }}</td>
                          <td>@if($item->active) Enabled @else Disabled @endif</td>
                          <td>{{ $item->order }}</td>
                          <td>
                            <a href="{{ route('admin.menu.edit', encrypt($item->id)) }}"><i class="fa fa-2x fa-edit"></i></a>
                            {{-- <a href="{{ route('admin.content.delete', ['id' => $item->id, '_token' => csrf_token()]) }}" data-message="Are you sure about deleting this?" class="post-deletee"><i class="fa fa-2x fa-trash-o"></i></a> --}}
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
