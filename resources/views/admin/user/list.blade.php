@extends('layouts.admin')

@section('styles')

<style>
.button-tran-abs {
	width: calc(50% - 33.8%);
	position: absolute;
	bottom: 0px;
	right: 0.8%;
}
.input-group .form-control {
	border-radius: 5px !important;
}
@media only screen and (max-width: 991px){
  .row.flex-row:first-child, .row.flex-row:last-child {
  	width: 50%;
  }
  .row.flex-row.button-tran {
	padding-right: calc(50% - 33%);
}
}
</style>
@endsection
@section('content')
<div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Users</h2>
                    <ul class="nav navbar-right panel_toolbox">
                        <li><a href="{{ route('admin.user.add') }}" class="btn btn-success">Add User</a>
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <div class="form-outer">
                      <form action="{{ route('admin.user.list') }}" method="GET" class="form-inline" style="width: calc(100% + 20px);">

                            <div class="row flex-row transaction-form">
                              <div class="input-group">
                                <input type="hidden" name="role" value="{{ Request::input('role') }}" >
                                <input type="text" placeholder="Name" name="name" value="{{ Request::input('name') }}" class="form-control input-small">
                                </div>
                            </div>

														@if(Request::input('role') == 'app-user')
														<div class="row flex-row transaction-form">
                                <div class="input-group">
                                <input type="text" placeholder="Mobile No" name="phone" value="{{ Request::input('phone') }}" class="form-control">
                                </div>
                            </div>

														<div class="row flex-row transaction-form">
                                <div class="input-group">
                                <input type="text" placeholder="Fin No" name="fin_no" value="{{ Request::input('fin_no') }}" class="form-control">
                                </div>
                            </div>
														@endif

                            <div class="row flex-row transaction-form">
                                <div class="input-group">
                                <input type="text" placeholder="Email" name="email" value="{{ Request::input('email') }}" class="form-control">
                                </div>
                            </div>

							<div class="row flex-row transaction-form">
                                <div class="input-group">
                                	{!!Form::select('dormitory_id', $dorm, Request::input('dormitory_id'), ['class' => 'form-control'])!!}
                                </div>
                            </div>
                            @if(Request::input('role') == 'app-user')
							<div class="row flex-row transaction-form">
                                <div class="input-group">
                                {!!Form::select('good_for_wallet', $goods, Request::input('good_for_wallet'), ['class' => 'form-control'])!!}
                                </div>
                            </div>

							<div class="row flex-row transaction-form">
                                <div class="input-group">
                                <input type="text" placeholder="Profile Updated At" name="updated_at" value="{{ Request::input('updated_at') }}" class="form-control">
                                </div>
                            </div>
							@endif

														<div class="row flex-row  button-tran">
                              <button class="btn btn-success" type="submit">Search</button>
                              <a href="{{ route('admin.user.list', ['role' => Request::input('role') ]) }}" class="btn btn-success">Reset</a>
                            </div>


                      </form>
                      <form action="{{ route('admin.user.export') }}" method="GET" class="form-inline button-tran-abs">
                          <input type="hidden" name="role" value="{{ Request::input('role') }}" >
                          <input type="hidden" name="name" value="{{ Request::input('name') }}" >
                          <input type="hidden" name="email" value="{{ Request::input('email') }}" >
                          <input type="hidden" name="dormitory_id" value="{{ Request::input('dormitory_id') }}" >
                          <input type="hidden" name="fin_no" value="{{ Request::input('fin_no') }}" >
                          <input type="hidden" name="phone" value="{{ Request::input('phone') }}" >
                          <input type="hidden" name="good_for_wallet" value="{{ Request::input('good_for_wallet') }}" >
						  <input type="hidden" name="updated_at" value="{{ Request::input('updated_at') }}" >
                          <button class="btn btn-success" type="submit">Export</button>
                      </form>
                    </div>

                    <table id="datatable-responsivee" class="table table-striped table-bordered dt-responsive nowrap foo_table" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th>@sortablelink('name')</th>
                          <th class="no-sort" data-breakpoints="xs sm">Email</th>
                          @if($role_id == 'app-user')
                            <th>@sortablelink('type', 'Verified')</th>
						    <th>@sortablelink('register_by', 'Registered By')</th>
                            <th>@sortablelink('good_for_wallet', 'Good For Wallet')</th>
							<th>@sortablelink('created_at', 'Signup Date')</th>
						    <th>@sortablelink('updated_at', 'Updation Date')</th>
                          @endif
                          <!-- <th>Role</th> -->
                          <!-- <th>Email</th> -->
                          <th class="no-sort">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($users as $key => $user)
                        <tr>
													<td>{{ (($users->currentPage()-1)*50)+(++$key) }}</td>
                          <td>{{ $user->name }}</td>
                          <td>{{ $user->email }}</td>
                          {{--<td>{{ $user->roles[0]->name }}</td>
                          @if($user->profile)
                          <td>{{$user->profile->contact }}</td>
                          @else
                          @endif--}}
                          @if($role_id == 'app-user')
                            <td>@if($user->type == 'free') Not-Verified @else Verified @endif</td>
						    <td>{{ $user->register_by }}</td>
                            <td>@if($user->good_for_wallet == 'Y')
			                    Yes
			                    @elseif($user->good_for_wallet == 'N')
			                    No
			                    @elseif($user->good_for_wallet == 'C')
			                    Corrected
			                    @elseif($user->good_for_wallet == 'D')
			                    Done
			                   @endif
			                </td>
							<td>{{ $user->created_at->format('d/m/Y') }}</td>
                            <td>
								@if($user->updated_at != $user->created_at)
									{{ $user->updated_at->format('d/m/Y') }}
								@endif
							</td>
                          @endif
                          <!-- <td>{{ $user->email }}</td> -->
                          <td>
														@if($auth_user->can('view.user-list'))
                            <a href="{{ route('admin.user.view', encrypt($user->id)) }}"><i class="fa fa-2x fa-eye"></i></a>
														@endif
														@if($auth_user->can('update.user-edit'))
                            <a href="{{ route('admin.user.edit', encrypt($user->id)) }}"><i class="fa fa-2x fa-edit"></i></a>
														@endif
                            @if($user->id != $auth_user->id && $auth_user->can('delete.user-delete'))
                            <a href="{{ route('admin.user.delete', ['id' => $user->id, '_token' => csrf_token()]) }}" data-message="Are you sure about deleting account?" class="post-deletee"><i class="fa fa-2x fa-trash-o"></i></a>
                            @endif
                            @if($user->hasRole('employee'))
                            <!-- <a href="{{ route('admin.user.module.list', encrypt($user->id)) }}"><i class="fa fa-2x fa-eye"></i></a> -->
                            @endif
                          </td>
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
