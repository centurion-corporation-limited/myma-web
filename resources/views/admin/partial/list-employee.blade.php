<div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Schedule for {{ $user->name }}</h2>

                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th>Location</th>
                          <th>Arrival Time</th>
                          <th>End Time</th>
                          <th>Created By</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($items as $key => $item)
                        <tr>
                          <td>{{ $key+1 }}</td>
                          <td>@if($item->location){{ $item->location->name }}@endif</td>
                          <td>@if($item->arrival_time){{ Carbon\Carbon::parse($item->arrival_time)->format('d M, Y g:i A') }}@endif</td>
                          <td>@if($item->end_time){{ Carbon\Carbon::parse($item->end_time)->format('d M, Y g:i A') }}@endif</td>
                          <td>{{ $item->creator->name }}</td>
                          <td>
                            <a href="{{ route('admin.schedule.edit', $item->id) }}"><i class="fa fa-2x fa-edit"></i></a>
                            <a href="{{ route('admin.schedule.delete', ['id' => $item->id, '_token' => csrf_token()]) }}" data-message="Are you sure about deleting this?" class="post-delete"><i class="fa fa-2x fa-trash-o"></i></a>
                          </td>
                        </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
