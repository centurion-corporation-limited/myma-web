@extends('layouts.admin')

@section('styles')
<style>
form{display: inline-block;}
</style>
@endsection
@section('content')
<div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Flexm users list </h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content x_content_form">
                    <form action="{{ route('admin.flexm.user.list') }}" method="GET" class="form-inline">
                          <div class="form-group">
                              <input type="text" placeholder="Name" name="name" value="{{ Request::input('name') }}" class="form-control">
                          </div>

                          <div class="form-group">
                              <input type="text" placeholder="Email" name="email" value="{{ Request::input('email') }}" class="form-control">
                          </div>

                          <button class="btn btn-success" type="submit">Search</button>
                          <a href="{{ route('admin.user.list', ['role' => Request::input('role') ]) }}" class="btn btn-success">Reset</a>
                    </form>
                    <form action="{{ route('admin.flexm.user.export') }}" method="GET" class="form-inline">
                        <input type="hidden" name="name" value="{{ Request::input('name') }}" >
                        <input type="hidden" name="email" value="{{ Request::input('email') }}" >
                        <button class="btn btn-success" type="submit" style="margin-bottom:24px !important;">Export</button> 
                    </form>
                    <table id="datatable-responsivee" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th>Name</th>
                          <th>Email</th>
                          <th>Phone</th>
                          <th>Status</th>
                          <th>Flexm Status</th>
                          <th>Reason</th>
                          <!-- <th class="no-sort">Action</th> -->
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($users as $key => $user)
                        <tr>
                          <td>{{ (($users->currentPage()-1)*10)+(++$key) }}</td>
                          <td>{{ $user->name }}</td>
                          <td>{{ $user->email }}</td>
                          <td>{{ $user->profile->phone or '-' }}</td>
                          <td>@if($user->flexm_account == 1) Registered @else Error @endif</td>
                          <td>{{ $user->flexm_status }}</td>
                          <td>{{ $user->flexm_error_text }}</td>
                        </tr>
                        @endforeach
                      </tbody>
      </table>
      @include('partials.paging', ['items' => $users])
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
