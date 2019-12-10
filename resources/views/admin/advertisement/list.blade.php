@extends('layouts.admin')

@section('content')
<div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Advertisement</h2>
                    <ul class="nav navbar-right panel_toolbox">
                        <li><a href="{{ route('admin.advertisement.add') }}" class="btn btn-success">Add</a>
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content x_content_form">
                    <form action="{{ route('admin.advertisement.list') }}" method="GET" class="form-inline">
                          <div class="form-group">
                              {!!Form::select('type', ['Please select placeholder', 'home' => 'Home Slider', 'landing' => 'Popup'], '', ['class' => 'form-control'])!!}
                          </div>

                          <div class="form-group">
                              {!!Form::select('status', ['Please select status', 'completed' => 'Completed', 'running' => 'Running', 'inactive' => 'Inactive'], '', ['class' => 'form-control'])!!}
                          </div>

                          <div class="form-group">
                              {!!Form::select('adv_type', ['Please select Ad type', '2' => 'Date', '1' => 'Impression'], '', ['class' => 'form-control'])!!}
                          </div>
                          <div class="form-group">
                            <div class="form-control-btn">

                          <button class="btn btn-success" type="submit">Search</button>
                          <a href="{{ route('admin.advertisement.list')}}" class="btn btn-success">Reset</a>
                          </div>
                          </div>
                    </form>
                    <table id="datatable-responsivee" class="table table-striped table-bordered dt-responsive nowrap table-hover foo_table" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                          <th class="no-sort">ID</th>
                          <th class="no-sort">Sponsor</th>
                          <th>@sortablelink('type', 'Type')</th>
                          <th>@sortablelink('adv_type', 'Ad Type')</th>
                          <th data-breakpoints="sm xs" >@sortablelink('end', 'Ending On')</th>
                          <th data-breakpoints="sm xs" class="no-sort"> No of Impression</th>
                          <th>@sortablelink('status', 'Status')</th>
                          <th class="no-sort">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($items as $key => $item)
                        <tr>
                          <td>{{ (($items->currentPage()-1)*10)+(++$key) }}</td>
                          <td>{{ $item->sponsor->name or '-' }}</td>
                          <td>@if($item->type == 'landing') Popup @else Home Slider @endif</td>
                          <td>@if($item->adv_type == 1) Impression @else Date @endif</td>
                          <td>@if($item->adv_type != 1){{ $item->end }} @else - @endif</td>
                          <td>@if($item->adv_type == 1){{ $item->impress->impressions or '-' }} @else - @endif</td>
                          <td>{{ $item->status or 'Running' }}</td>
                          <td>
                            <a title="Performance Report" href="{{ route('admin.advertisement.view', encrypt($item->id)) }}"><i class="fa fa-2x fa-eye"></i></a>
                            <a href="{{ route('admin.advertisement.edit', encrypt($item->id)) }}"><i class="fa fa-2x fa-edit"></i></a>
                            <a href="{{ route('admin.advertisement.delete', ['id' => $item->id, '_token' => csrf_token()]) }}" data-message="Are you sure about deleting this?" class="post-deletee"><i class="fa fa-2x fa-trash-o"></i></a>
                            <!-- <a href="{{ route('admin.advertisement.edit', encrypt($item->id)) }}"><i class="fa fa-2x fa-bar-chart-o"></i></a> -->
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
