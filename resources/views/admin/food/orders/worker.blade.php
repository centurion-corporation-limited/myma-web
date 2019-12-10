@extends('layouts.admin')

@section('content')
<div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Batch List</h2>

                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <form action="{{ route('admin.batch.worker') }}" method="GET" class="form-inline">
                          <div class="form-group">
                            <div class="row flex-row transaction-form">
                                <label class="control-label" for="dormitory_id">Date</label>
                                <input autocomplete="off" type="text" placeholder="From" class="form-control" name="date" value="{{ Request::input('date') }}">

                            </div>
                          </div>

                          <div class="form-group">
                            <div class="form-control-btn">

                          <button class="btn btn-success" type="submit">Search</button>
                          <a href="{{ route('admin.batch.worker')}}" class="btn btn-success">Reset</a>
                          </div>
                          </div>
                    </form>
                    <table id="datatable-responsivee" class="table table-striped table-bordered dt-responsive nowrap table-hover" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th>Batch Id</th>
                          <th>Address</th>
                          <th>Date</th>
                          <th class="no-sort">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($items as $key => $item)
                        <tr>
                          <td>{{ ++$key }}</td>
                          <td>{{ $item['batch_id'] }}</td>
                          <td>{{ $item['address'] }}</td>
                          <td>{{ $item['delivery_date'] }}</td>
                          <td>
                            <a title="Download List" target="_blank" href="{{ route('admin.batch.worker.export', $item['batch_id']) }}"><i class="fa fa-2x fa-file-text"></i></a>
                          </td>
                        </tr>
                        @endforeach
                      </tbody>
      </table>

    </div>
  </div>
</div>
@endsection

@section('scripts')

<script>
$(document).ready(function(){
  $('[name=date]').datepicker({

  });
});
</script>
@stop
