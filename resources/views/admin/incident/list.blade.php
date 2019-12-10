@extends('layouts.admin')

@section('styles')
<link href="{{  static_file('js/plugins/bootstrap-datepicker-master/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
{{--<link href="{{  static_file('js/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css') }}" rel="stylesheet">--}}
<style>

</style>
@endsection
@section('content')
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Incident Report @if($book_date) for {{ Carbon\Carbon::parse($book_date)->format('d/m/Y') }} @endif</h2>

                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content x_content_form">
                    <div class="row hidden-print">
                    <form action="{{ route('admin.incident.list') }}" method="GET" class="form-inline">
                          <div class="form-group">
                              <input type="text" placeholder="Text" name="text" value="{{ Request::input('text') }}" class="form-control input-small">
                          </div>

                          <div class="form-group">
                              {!!Form::select('dormitory_id', $dormitories, $dormitory_id, ['class' => 'form-control'])!!}
                          </div>

                          <button class="btn btn-success" type="submit">Search</button>
                          <a href="{{ route('admin.incident.list') }}" class="btn btn-success">Reset</a>
                    </form>
                    </div>

                    <table id="datatable-responsivee" class="table table-striped table-bordered dt-responsive nowrap table-hover foo_table" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                          <th>S/No</th>
                          <th>@sortablelink('date', 'Incident Date')</th>
                          <th>@sortablelink('time', 'Incident Time')</th>
                          <th>@sortablelink('location', 'Location')</th>
                          <th data-breakpoints="sm xs">@sortablelink('dormitory_id', 'Dormitory')</th>
                          <th data-breakpoints="sm xs">@sortablelink('created_at', 'Reported')</th>
                          <th class="no-sort hidden-print">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($items as $key => $item)
                        <tr>
                          <td>{{ (($items->currentPage()-1)*10)+(++$key) }}</td>
                          <td>{{ Carbon\Carbon::parse($item->date)->format('d/m/Y') }}</td>
                          <td>{{ Carbon\Carbon::parse($item->time)->format('h:i A') }}</td>
                          <td style="text-align: left;">{{ $item->location }}</td>
                          <td style="text-align: left;">{{ $item->dormitory->name or '--' }}</td>
                          <td>{{ $item->created_at->format('d/m/y H:i A') }}</td>
                          <td class="hidden-print">
                            <a href="{{ route('admin.incident.view', ['id' => encrypt($item->id)]) }}"><i class="fa fa-2x fa-eye"></i></a>
                            <a href="{{ route('admin.incident.delete', ['id' => $item->id, '_token' => csrf_token()]) }}" data-message="Are you sure about deleting this?" class="post-deletee"><i class="fa fa-2x fa-trash-o"></i></a>
                            <a href="{{ route('admin.incident.export', encrypt($item->id)) }}"><i class="fa fa-2x fa-file-pdf-o"></i></a>
                            <!-- <a href="{{ route('admin.incident.delete', ['id' => $item->id]) }}"><i class="fa fa-2x fa-eye"></i></a> -->
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
<script src="{{ static_file('js/plugins/bootstrap-datepicker-master/dist/js/bootstrap-datepicker.min.js') }}"></script>
{{-- <script src="{{ static_file('js/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js') }}"></script> --}}
<script>
$(document).ready(function(){
  $('#book_date').datepicker({
    format:"yyyy-mm-dd"
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
