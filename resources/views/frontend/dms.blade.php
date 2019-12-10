@extends('layouts.dms')

@section('styles')
<link href="{{  static_file('js/plugins/bootstrap-datepicker-master/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
<style>
.x_panel {
  margin: 10px 0px;
}
.button-tran-abs {
	width: calc(50% - 33.8%);
	position: absolute;
	bottom: 0px;
	right: 0.8%;
}
.input-group .form-control {
	border-radius: 5px !important;
}
.row.flex-row.button-tran {
    padding-right: calc(0% - 0%);
}
@media only screen and (max-width: 767px){
	.row.flex-row {
    width: 100%;
}
.footable-details th {
    width: 120px !important;
}

}
</style>
@endsection
@section('content')
  <div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
      <div class="x_title">
        <h2>Search User</h2>
        <div class="clearfix"></div>
      </div>
    	<div class="x_content">
      	<div class="form-outer">
        	<form action="{{ route('app.search.dms') }}" method="GET" class="form-inline" style="width: calc(100% + 20px);">
            <div class="row flex-row transaction-form">
              <div class="input-group">
                <input type="text" placeholder="Name" name="name" value="{{ Request::input('name') }}" class="form-control input-small">
              </div>
            </div>

            <div class="row flex-row transaction-form">
              <div class="input-group">
              	<input autocomplete="off" type="text" placeholder="DOB" name="dob" value="{{ Request::input('dob') }}" class="form-control">
              </div>
            </div>

						<div class="row flex-row transaction-form">
              <div class="input-group">
              	<input type="text" placeholder="Fin No" name="fin_no" value="{{ Request::input('fin_no') }}" class="form-control">
              </div>
            </div>

            <div class="row flex-row  button-tran">
              <button class="btn btn-success" type="submit">Search</button>

              <a href="{{ route('app.search.dms') }}" class="btn btn-success">Reset</a>
            </div>
        </form>
      </div>

      <table id="datatable-responsivee" class="table table-striped table-bordered dt-responsive nowrap foo_table" cellspacing="0" width="100%">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th data-breakpoints="xs">DOB</th>
            <th data-breakpoints="xs">Fin No</th>
            <th class="no-sort">Action</th>
          </tr>
        </thead>
        <tbody>
          @if(isset($users))
            @if($users->count() > 0)
              @foreach($users as $key => $user)
              <tr>
  							<td>{{ ++$key }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->dob }}</td>
                <td>{{ $user->fin_no }}</td>
                <td>
                  @if($user->registered_already)
                  Registered
                  @else
                  <a href="{{ url('sign') }}?dms={{ encrypt($user->id) }}"><i class="fa fa-2x fa-eye"></i></a>
                  @endif
                </td>
              </tr>
              @endforeach
            @else
            <tr>
              <td>Record does not exist.</td>
            </tr>
            @endif
          @endif
        </tbody>
      </table>
      @if(isset($users))
        @if($users->count() == 0)
          <div class="row flex-row">
            <a href="{{ url('sign') }}?name={{ request('name')}}&fin_no={{ request('fin_no') }}&dob={{ request('dob') }}" class="btn btn-success">Create an account</a>
          </div>
        @endif
      @endif
    </div>
  </div>
  </div>
  @endsection

  @section('scripts')
  <script src="{{ static_file('js/plugins/bootbox.min.js') }}"></script>
  <script src="{{ static_file('js/plugins/bootstrap-datepicker-master/dist/js/bootstrap-datepicker.min.js') }}"></script>
  <script>
  $('[name=dob]').datepicker({
  		format:"dd/mm/yyyy"
  });
	$('.foo_table').footable();

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
