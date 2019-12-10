@extends('layouts.admin')

@section('content')
<div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Pricing plans</h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a href="{{ route('admin.advertisement.plan.add') }}" class="btn btn-success">Add Plan</a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <table id="datatable-responsivee" class="table table-striped table-bordered dt-responsive nowrap foo_table" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                          <th>S/No</th>
                          <th class="no-sort">Ad Type</th>
                          <th class="no-sort">Qty or Duration</th>
                          <!-- <th>Contact No</th> -->
                          <th class="no-sort">Price ($)</th>
                          <th class="no-sort">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($items as $key => $item)
                        <tr>
                          <td>{{ (($items->currentPage()-1)*10)+(++$key) }}</td>
                          <td>{{ ucfirst($item->type) }}</td>
                          <td>
                              @if($item->type == 'impression')
                              {{ $item->impressions }}
                              @else
                                @if($item->impressions == 7)
                                7 days
                                @elseif($item->impressions == 31)
                                30 or 31 days
                                @elseif($item->impressions == 365)
                                365 days
                                @endif
                              @endif
                          </td>
                          <td>{{ number_format($item->price,2) }}</td>
                          <td>
                            <a href="{{ route('admin.advertisement.plan.edit', encrypt($item->id)) }}"><i class="fa fa-2x fa-edit"></i></a>
                            @if($item->ads->count() == 0)
                            <a href="{{ route('admin.advertisement.plan.delete', ['id' => $item->id, '_token' => csrf_token()]) }}" data-message="Are you sure about deleting this?" class="post-deletee"><i class="fa fa-2x fa-trash-o"></i></a>
                            @endif
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
