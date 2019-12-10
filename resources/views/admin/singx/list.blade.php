@extends('layouts.admin')

@section('styles')
<style>
form{display: inline-block;}
@media screen and (max-width: 991px){
  .form-inline .form-control {
	margin: 10px 5px;
}
}
</style>
@endsection
@section('content')
<div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Users</h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content x_content_form">
                    <form action="{{ route('admin.singx.list') }}" method="GET" class="form-inline">
                          <div class="form-group">
                              <input type="text" placeholder="Name" name="name" value="{{ Request::input('name') }}" class="form-control input-small">
                          </div>

                          <div class="form-group">
                              <input type="text" placeholder="Email" name="email" value="{{ Request::input('email') }}" class="form-control">
                          </div>

                          <button class="btn btn-success" type="submit">Search</button>
                          <a href="{{ route('admin.singx.list') }}" class="btn btn-success">Reset</a>
                    </form>
                    <!-- <form action="{{-- route('admin.singx.export') --}}" method="GET" class="form-inline">
                        <input type="hidden" name="role" value="{{ Request::input('role') }}" >
                        <input type="hidden" name="name" value="{{ Request::input('name') }}" >
                        <input type="hidden" name="email" value="{{ Request::input('email') }}" >
                        <button class="btn btn-success" type="submit" style="margin-bottom:24px !important;">Export</button>
                    </form> -->
                    <table id="datatable-responsivee" class="table table-striped table-bordered dt-responsive nowrap foo_table" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                          <th class="no-sort">ID</th>
                          <th>@sortablelink('name')</th>
                          <th class="no-sort">Email</th>
                          <!-- <th>Role</th> -->
                          <!-- <th>Email</th> -->
                          <!-- <th class="no-sort">Action</th> -->
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($items as $key => $user)
                        <tr>
                          <td>{{ (($items->currentPage()-1)*10)+(++$key) }}</td>
                          <td>{{ $user->name }}</td>
                          <td>{{ $user->email }}</td>
                          <!-- <td> -->
                              <!-- <a href="{{ route('admin.user.view', encrypt($user->id)) }}"><i class="fa fa-2x fa-eye"></i></a> -->
                            <!-- <a href="{{ route('admin.user.edit', encrypt($user->id)) }}"><i class="fa fa-2x fa-edit"></i></a> -->
                          <!-- </td> -->
                        </tr>
                        @endforeach
                      </tbody>
      </table>
      @include('partials.paging', ['items' => $items])
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="{{ static_file('js/plugins/bootbox.min.js') }}"></script>
<script src="{{ static_file('js/plugins/bootstrap-datepicker-master/dist/js/bootstrap-datepicker.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('.input-daterange').datepicker({
            todayBtn: "linked",
            format: "yyyy-mm-dd"
        });


    });
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
