@extends('layouts.admin')

@section('content')
<div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Terminal</h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a href="{{ route('admin.terminal.add', ['merchant_id' => Request::input('merchant_id')]) }}" class="btn btn-success">Add QR Terminal</a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content x_content_form">
                    <form action="{{ route('admin.terminal.list') }}" method="GET" class="form-inline">
                          <div class="row flex-row transaction-form">
                              <label class="control-label" for="">Name</label>
                              <div class="input-group">
                                  <input type="text" placeholder="name" name="name" value="{{ Request::input('name') }}" class="form-control input-small">
                              </div>
                          </div>

                          <div class="row flex-row transaction-form">
                              <label class="control-label" for="status">Merchant</label>
                              <div class="input-group ">
                                  <select class="form-control" name="merchant_id">
                                      <option value="" @if(Request::input('merchant_id')=="" ) selected="selected" @endif>Select a merchant</option>
                                      @foreach($names as $id => $name)
                                      <option value="{{ encrypt($id) }}" @if(Request::input('merchant_id') == encrypt($id)) selected="selected" @endif>{{ $name }}</option>
                                      @endforeach
                                  </select>
                              </div>
                          </div>
                          <div class="row flex-row button-tran">
                            <button class="btn btn-success" type="submit">Search</button>
                            <a href="{{ route('admin.terminal.list') }}" class="btn btn-success">Reset</a>
                          </div>

                    </form>
                    <table id="datatable-responsivee" class="table table-striped table-bordered dt-responsive nowrap table-hover" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th>Merchant</th>
                          <th>Location</th>
                          <th>TID</th>
                          <th>Qr Code</th>
                          <!-- <th class="no-sort">Action</th> -->
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($items as $key => $item)
                        <tr>
                          <td>{{ (($items->currentPage()-1)*10)+(++$key) }}</td>
                          <td>{{ $item->merchant->merchant_name or '--' }}</td>
                          <td>{{ $item->location }}</td>
                          <td>{{ $item->tid }}</td>
                          <td><a target="_blank" href="{{ route('admin.terminal.qrcode', encrypt($item->id)) }}">Click to view</a></td>

                          {{-- <td>
                            <a href="{{ route('admin.merchant.edit', encrypt($item->id)) }}"><i class="fa fa-2x fa-edit"></i></a>
                            @if($auth_user->can('delete.mom-category-delete'))
                            <a href="{{ route('admin.merchant.delete', ['id' => $item->id, '_token' => csrf_token()]) }}" data-message="Are you sure about deleting this?" class="post-deletee"><i class="fa fa-2x fa-trash-o"></i></a>
                            @endif
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
