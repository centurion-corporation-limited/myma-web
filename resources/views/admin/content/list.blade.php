@extends('layouts.admin')

@section('content')
<div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Course content</h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li>
                          <a href="{{ route('admin.content.add', ['course_id' => $course_id]) }}" class="btn btn-success">Add Content</a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content x_content_form">
                    <form action="{{ route('admin.content.list') }}" method="GET" class="form-inline">
                        <div class="form-group">
                            <select class="form-control" name="course_id">
                                <option value="0">Please select course</option>
                                @foreach($courses as $ke => $val)
                                    <option value="{{ $ke }}" @if($sel_val == $val) selected @endif>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <input type="text" placeholder="Title" name="title" value="{{ Request::input('title') }}" class="form-control input-small">
                        </div>

                        {{-- <div class="form-group">
                            {!!Form::select('type', ['Please select type','course' => 'Course', 'training' => 'Training'], '', ['class' => 'form-control'])!!}
                        </div> --}}

                        <button class="btn btn-success" type="submit">Search</button>
                        <a href="{{ route('admin.content.list')}}" class="btn btn-success">Reset</a>
                    </form>
                    <table id="datatable-responsivee" class="table table-striped table-bordered dt-responsive nowrap foo_table" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th>@sortablelink('type', 'Title')</th>
                          <th>@sortablelink('type', 'Type')</th>
                          <th>@sortablelink('order', 'Order')</th>
                          <!-- <th>Email</th> -->
                          <th class="no-sort">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($items as $key => $item)
                        <tr>
                          <td>{{ (($items->currentPage()-1)*10)+(++$key) }}</td>
                          <td>{{ $item->title }}</td>
                          <td>{{ $item->type }}</td>
                          <td>{{ $item->order }}</td>
                          <td>
                            <a href="{{ route('admin.content.edit', encrypt($item->id)) }}"><i class="fa fa-2x fa-edit"></i></a>
                            <a href="{{ route('admin.content.delete', ['id' => $item->id, '_token' => csrf_token()]) }}" data-message="Are you sure about deleting this?" class="post-deletee"><i class="fa fa-2x fa-trash-o"></i></a>
                            {{-- <a href="{{ route('admin.user.module.list', $item->id) }}"><i class="fa fa-2x fa-eye"></i></a> --}}

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
