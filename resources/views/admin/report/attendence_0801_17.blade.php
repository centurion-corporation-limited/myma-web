@extends('layouts.admin')

@section('styles')
<link href="{{  static_file('js/plugins/bootstrap-datepicker-master/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
{{--<link href="{{  static_file('js/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css') }}" rel="stylesheet">--}}

@endsection
@section('content')
<div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Attendence</h2>

                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <div class="row hidden-print">
                      <form method="get">
                        <div class="col-md-3 col-xs-12 form-group">
                          <select name="user_id" class="form-control">
                            @foreach($users as $user)
                              <option value="{{ $user->id }}" @if($sel_user) @if(old('user_id', $sel_user->id) == $user->id) selected @endif @endif>{{ $user->name }}</option>
                            @endforeach
                          </select>
                        </div>
                        <div class="col-md-5 col-xs-12 form-group">
                          <div class="input-group input-daterange">
                            <input type="text" placeholder="From" class="form-control" name="start">
                            <div class="input-group-addon">to</div>
                            <input type="text" placeholder="To" class="form-control" name="end">
                          </div>
                        </div>
                        <div class="col-md-3 col-xs-12">
                          <input type="submit" class="btn btn-default" value="Submit" />
                        </div>
                      </form>
                    </div>

                    <table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th>Date</th>
                          <th>Time</th>
                          <th>Status</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($items as $key => $item)
                        <tr>
                          <td>{{ $key+1 }}</td>
                          <td>{{ Carbon\Carbon::parse($item->book_date)->format('d/m/Y') }}</td>
                          <td>{{ $item->book_time }}</td>
                          <td><span class="label label-success">Present</span></td>
                        </tr>
                        @endforeach
                      </tbody>
      </table>
    </div>
  </div>
  <div class="row hidden-print">
    {{-- <a href="{{ route('download.attendence') }}">Download Excel</a> --}}
  </div>
</div>
@endsection
@section('scripts')
<script src="{{ static_file('js/plugins/bootstrap-datepicker-master/dist/js/bootstrap-datepicker.min.js') }}"></script>
{{-- <script src="{{ static_file('js/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js') }}"></script> --}}
<script>
$(document).ready(function(){
  $('.input-daterange').datepicker({
    todayBtn: "linked",
    format: "yyyy-mm-dd"
  });
});
</script>
@endsection
