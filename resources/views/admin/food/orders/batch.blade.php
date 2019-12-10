@extends('layouts.admin')

@section('styles')
<link href="{{  static_file('js/plugins/bootstrap-datepicker-master/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
<style>
.button-tran-abs {
    bottom: 0;
}
</style>

@endsection
@section('content')
<div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Batch Search</h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <form action="{{ route('admin.batch.search') }}" method="GET" class="form-inline">

                      <div class="row flex-row transaction-form">
                          <label class="control-label" for="batch_id">Batch Id </label>
                          <div class="input-group">
                            <input type="text" placeholder="Batch Id" name="batch_id" value="{{ Request::input('batch_id') }}" class="form-control input-small">
                          </div>
                      </div>

                          <div class="row flex-row  button-tran">
                            <button class="btn btn-success" type="submit">Search</button>
                            <a href="{{ route('admin.batch.search')}}" class="btn btn-success">Reset</a>

                          </div>
                    </form>

                    <table id="datatable-responsivee" class="table table-striped table-bordered dt-responsive nowrap table-hover" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th>@sortablelink('id', 'Order ID')</th>
                          <th>@sortablelink('user_id', 'Customer')</th>
                          <th>@sortablelink('type', 'Order type')</th>
                          <th>@sortablelink('total', 'Amount')</th>
                          <th class="no-sort">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($items as $key => $item)
                        <tr>
                          <td>{{-- (($items->currentPage()-1)*10)+(++$key) --}}{{ ++$key }}</td>
                          <td>{{ str_pad($item->id, '7', '0', STR_PAD_LEFT) }}</td>
                          <td>{{ $item->user->name or '' }}</td>
                          <td>{{ $item->type }}</td>
                          <td>S${{ number_format($item->total, 2) }}</td>
                          {{--<td>{{ $item->status->name or '' }}</td>--}}
                          <td>
                            <a title="View Order" href="{{ route('admin.order.view', encrypt($item->id)) }}"><i class="fa fa-2x fa-eye"></i></a>

                        </tr>
                        @endforeach
                      </tbody>
      </table>

    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="{{ static_file('js/plugins/bootstrap-datepicker-master/dist/js/bootstrap-datepicker.min.js') }}"></script>
<script>
$(document).ready(function() {
    $('.input-daterange').datepicker({
        todayBtn: "linked",
        format: "dd/mm/yyyy"//"yyyy-mm-dd"
    });
});
</script>
@stop
