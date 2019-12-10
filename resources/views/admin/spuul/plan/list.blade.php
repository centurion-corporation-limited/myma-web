@extends('layouts.admin')

@section('content')
<div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Plans</h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a href="{{ route('admin.spuul.plan.add') }}" class="btn btn-success">Add Plan</a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <table id="datatable-responsivee" class="table table-striped table-bordered dt-responsive nowrap foo_table" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                          <th class="no-sort">ID</th>
                          <th>@sortablelink('type', 'Type')</th>
                          <th>@sortablelink('price', 'Price')</th>
                          <th>@sortablelink('status', 'Status')</th>
                          <th class="no-sort">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($items as $key => $item)
                        <tr>
                          <td>{{ (($items->currentPage()-1)*10)+(++$key) }}</td>
                          <td>
                              @if($item->type == 1)
                              Monthly
                              @else
                              Yearly
                              @endif
                          </td>
                          <td>{{ $item->price }}</td>
                          <td>
                              @if($item->status == 1)
                              Active
                              @else
                              Inactive
                              @endif
                          </td>
                          <td>
                            <a href="{{ route('admin.spuul.plan.edit', encrypt($item->id)) }}"><i class="fa fa-2x fa-edit"></i></a>
                            {{--@if(@$item->ads->count() == 0)
                            @endif --}}
                            <a href="{{ route('admin.spuul.plan.delete', ['id' => $item->id, '_token' => csrf_token()]) }}" data-message="Are you sure about deleting this?" class="post-deletee"><i class="fa fa-2x fa-trash-o"></i></a>
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
