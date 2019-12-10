@extends('layouts.admin')

@section('content')
<style>
@media only screen and (max-width: 991px){
  .form-inline .form-control {
  	margin: 5px;
  }
  .form-inline .form-group ~ .btn {
  	margin-top: 5px;
  }
}

</style>
<div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Contact Request</h2>
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
                    <form action="{{ route('admin.contact.list') }}" method="GET" class="form-inline">
                          <div class="form-group">
                              <input type="text" placeholder="Name" name="name" value="{{ Request::input('name') }}" class="form-control input-small">
                          </div>

                          <div class="form-group">
                              <input type="text" placeholder="Email" name="email" value="{{ Request::input('email') }}" class="form-control input-small">
                          </div>

                          <div class="form-group">
                              <input type="text" placeholder="Phone" name="phone" value="{{ Request::input('phone') }}" class="form-control input-small">
                          </div>

                          <button class="btn btn-success" type="submit">Search</button>
                          <a href="{{ route('admin.contact.list') }}" class="btn btn-success">Reset</a>
                    </form>
                    <table id="datatable-responsivee" class="table table-striped table-bordered dt-responsive nowrap table-hover foo_table" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th>@sortablelink('name', 'Name')</th>
                          <th >@sortablelink('email', 'Email')</th>
                          <th data-breakpoints="sm xs">@sortablelink('phone', 'Phone')</th>
                          <th data-breakpoints="sm xs">@sortablelink('created_at', 'Date')</th>
                          <th class="no-sort">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($items as $key => $item)
                        <tr>
                          <td>{{ (($items->currentPage()-1)*10)+(++$key) }}</td>
                          <td>{{ $item->name }}</td>
                          <td>{{ $item->email }}</td>
                          <td>{{ $item->phone }}</td>
                          <td>{{ $item->created_at->format('d/m/Y H:i A') }}</td>
                          <td>
                            <!-- <a href="{{ route('admin.contact.reply', encrypt($item->id)) }}"><i class="fa fa-2x fa-reply"></i></a> -->
                            <a href="{{ route('admin.contact.view', encrypt($item->id)) }}"><i class="fa fa-2x fa-eye"></i></a>
                            <a href="{{ route('admin.contact.delete', ['id' => $item->id, '_token' => csrf_token()]) }}" data-message="Are you sure about deleting this?" class="post-deletee"><i class="fa fa-2x fa-trash-o"></i></a>

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
