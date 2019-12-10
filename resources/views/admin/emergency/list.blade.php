@extends('layouts.admin')

@section('content')
<div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Emergency Numbers</h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li>
                          @if($auth_user->can('create.emergency-add'))
                          <a href="{{ route('admin.emergency.add') }}" class="btn btn-success">Add</a>
                          @endif
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content x_content_form">
                    <div class="row hidden-print">
                    <form action="{{ route('admin.emergency.list') }}" method="GET" class="form-inline">
                          <div class="form-group">
                              <input type="text" placeholder="Name or Phone No" name="name" value="{{ Request::input('name') }}" class="form-control input-small">
                          </div>

                          <div class="form-group">
                              {!!Form::select('category_id', $categories, $category_id, ['class' => 'form-control'])!!}
                          </div>

                          <button class="btn btn-success" type="submit">Search</button>
                          <a href="{{ route('admin.emergency.list') }}" class="btn btn-success">Reset</a>
                    </form>
                    </div>
                    <table id="datatable-responsivee" class="table table-striped table-bordered dt-responsive nowrap foo_table" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th>@sortablelink('name', 'Name')</th>
                          <th>@sortablelink('value', 'Telephone No.')</th>
                          <th>@sortablelink('category_id', 'Category')</th>
                          <!-- <th>Contact No</th> -->
                          <!-- <th>Email</th> -->
                          <th class="no-sort">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($items as $key => $item)
                        <tr>
                          <td>{{ (($items->currentPage()-1)*10)+(++$key) }}</td>
                          <td>{{ $item->name }}</td>
                          <td>{{ $item->value }}</td>
                          <td>{{ $item->category->name or '' }}</td>
                          <td>
                            <a href="{{ route('admin.emergency.edit', encrypt($item->id)) }}"><i class="fa fa-2x fa-edit"></i></a>
                            <a href="{{ route('admin.emergency.delete', ['id' => $item->id, '_token' => csrf_token()]) }}" data-message="Are you sure about deleting this?" class="post-deletee"><i class="fa fa-2x fa-trash-o"></i></a>
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
