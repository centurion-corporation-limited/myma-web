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
                          <select name="location_id" class="form-control">
                            <option value="">By Location</option>
                            @foreach($locations as $location)
                              <option value="{{ $location->id }}" @if(old('location_id', Request::get('location_id')) == $location->id) selected @endif>{{ $location->name }}</option>
                            @endforeach
                          </select>
                          --or--
                          <select name="user_id" class="form-control">
                            <option value="">By NRIC</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}" @if(old('user_id', Request::get('user_id')) == $user->id) selected @endif>{{ $user->nric }}</option>
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
                          {{--@if(Request::get('location_id') != '')--}}
                          <th>Name</th>
                          {{--@endif--}}
                          <th>NRIC</th>
                          <th>Location</th>
                          <th>Date In</th>
                          <th>Date Out</th>
                          <th>Time In</th>
                          <th>Time Out</th>
                          <th>Grade</th>
                          <th>Job Scope</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($items as $key => $item)
                        <tr>
                          <td>{{ $key+1 }}</td>
                          {{--@if(Request::get('location_id') != '')--}}
                            <td>{{ $item->name }}</td>
                          {{--@endif--}}
                          <td>{{ $item->nric }}</td>
                          <td>{{ $item->loc_name }}</td>
                          <td>{{ Carbon\Carbon::parse($item->book_date)->format('d/m/Y') }}</td>
                          <td>{{ Carbon\Carbon::parse($item->book_date)->format('d/m/Y') }}</td>
                          <td>{{ $item->time_in }}</td>
                          <td>{{ $item->time_out }}</td>
                          <td>{{ $item->grade }}</td>
                          <td>{{ $item->job_scope }}</td>
                          <!-- <td><span class="label label-success">Present</span></td> -->
                        </tr>
                        @endforeach
                      </tbody>
      </table>
    </div>
  </div>
  <div class="row hidden-print">
    <a href="{{ route('download.attendence', ['user_id' => Input::get('user_id'),'location_id' => Input::get('location_id'),'start' => Input::get('start'),
    'end' => Input::get('end')]) }}">Download Excel</a>
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

  $('[name=user_id]').on('change', function(){
    if($('[name=location_id]').val() != ''){
      $('[name=location_id]').val('');
    }
  });

  $('[name=location_id]').on('change', function(){
    if($('[name=user_id]').val() != ''){
      $('[name=user_id]').val('');
    }
  });
});
</script>
@endsection
