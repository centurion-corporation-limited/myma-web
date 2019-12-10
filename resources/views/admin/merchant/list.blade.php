@extends('layouts.admin')

@section('content')
<div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>List Physical Merchant</h2>
                    @if($auth_user->can('create.mom-category-add'))
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a href="{{ route('admin.merchant.add') }}" class="btn btn-success">Add Merchant</a>
                      </li>
                    </ul>
                    @endif
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content x_content_form">
                    <form action="{{ route('admin.merchant.list') }}" method="GET" class="form-inline">
                          <div class="row flex-row transaction-form">
                              <label class="control-label" for="name">Name</label>
                              <div class="input-group ">
                                  <select class="form-control" name="name">
                                      <option value="" @if(Request::input('name')=="" ) selected="selected" @endif>Select a name</option>
                                      @foreach($names as $name)
                                      <option value="{{ $name }}" @if(Request::input('name') == $name) selected="selected" @endif>{{ $name }}</option>
                                      @endforeach
                                  </select>
                              </div>
                          </div>
                          <div class="row flex-row button-tran">
                            <button class="btn btn-success" type="submit">Search</button>
                            <a href="{{ route('admin.merchant.list') }}" class="btn btn-success">Reset</a>
                          </div>
                    </form>
                    <table id="datatable-responsivee" class="table table-striped table-bordered dt-responsive nowrap table-hover foo_table" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                          <th class="no-sort">ID</th>
                          <th>@sortablelink('merchant_name', 'Title')</th>
                          <th>@sortablelink('location', 'Location')</th>
                          <th>@sortablelink('mid', 'MID')</th>
                          <!-- <th class="no-sort">TID</th> -->
                          <!-- <th class="no-sort">QR Code</th> -->
                          <th class="no-sort">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($items as $key => $item)
                        <tr>
                          <td>{{ (($items->currentPage()-1)*10)+(++$key) }}</td>
                          <td>{{ $item->merchant_name }}</td>
                          <td>
                            @foreach($item->terminals as $key => $ter)
                              @if($key != 0)
                                ,
                              @endif
                              {{ $ter->location or '-' }}
                            @endforeach
                          </td>
                          <td>{{ $item->mid }}</td>
                          <!-- <td>{{ $item->terminal->tid or '-' }}</td> -->
                          <!-- <td><a target="_blank" href="{{ route('admin.flexm.qrcode', encrypt($item->id)) }}">Click to view</a></td> -->
                          <td>
                            <a href="{{ route('admin.terminal.list', ['merchant_id' => encrypt($item->id)]) }}"><i class="fa fa-2x fa-eye"></i></a>
                            <a href="{{ route('admin.merchant.edit', encrypt($item->id)) }}"><i class="fa fa-2x fa-edit"></i></a>
                            {{--@if($auth_user->can('delete.mom-category-delete'))
                            @endif--}}
                             <!--<a href="{{ route('admin.merchant.delete', ['id' => $item->id, '_token' => csrf_token()]) }}" data-message="Are you sure about deleting this?" class="post-deletee"><i class="fa fa-2x fa-trash-o"></i></a> -->
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
