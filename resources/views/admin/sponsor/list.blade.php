@extends('layouts.admin')

@section('content')
<div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Sponsor</h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a href="{{ route('admin.sponsor.add') }}" class="btn btn-success">Add</a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content x_content_form">
                    <div class="row hidden-print">
                    <form action="{{ route('admin.sponsor.list') }}" method="GET" class="form-inline">
                          <div class="form-group">
                            <select class="form-control" name="sponsor_id">
                              <option value="">Select a sponsor</option>
                              @foreach($sponsors as $key => $sponsor)
                                <option @if($key == Request::input('sponsor_id')) selected @endif value="{{ $key }}">{{ $sponsor }}</option>
                              @endforeach
                            </select>
                          </div>

                          <button class="btn btn-success" type="submit">Search</button>
                          <a href="{{ route('admin.sponsor.list') }}" class="btn btn-success">Reset</a>
                    </form>
                    </div>
                    <table id="datatable-responsivee" class="table table-striped table-bordered dt-responsive nowrap table-hover foo_table" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                          <th class="no-sort">S/No</th>
                          <th>@sortablelink('name', 'Name')</th>
                          <th>@sortablelink('phone', 'Phone No')</th>
                          <th>Address</th>
                          <th class="no-sort">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($items as $key => $item)
                        <tr>
                          <td>{{ (($items->currentPage()-1)*10)+(++$key) }}</td>
                          <td>{{ $item->name }}</td>
                          <td>{{ $item->phone or '-' }}</td>
                          <td>@if($item->address == '') - @else {{ $item->address }} @endif</td>
                          <td>
                            <a href="{{ route('admin.sponsor.view', ['id' => encrypt($item->id)]) }}"><i class="fa fa-2x fa-eye"></i></a>
                            <a href="{{ route('admin.sponsor.edit', encrypt($item->id)) }}"><i class="fa fa-2x fa-edit"></i></a>
                            <a href="{{ route('admin.sponsor.delete', ['id' => $item->id, '_token' => csrf_token()]) }}" data-message="Are you sure about deleting this?" class="post-deletee"><i class="fa fa-2x fa-trash-o"></i></a>
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
