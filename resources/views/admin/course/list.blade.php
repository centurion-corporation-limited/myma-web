@extends('layouts.admin')

@section('content')
<div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Courses</h2>
                    <ul class="nav navbar-right panel_toolbox">
                        <li><a href="{{ route('admin.course.add') }}" class="btn btn-success">Add Course</a>
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content x_content_form">
                    <form action="{{ route('admin.course.list') }}" method="GET" class="form-inline">
                        <div class="form-group">
                            <input type="text" placeholder="Title" name="title" value="{{ Request::input('title') }}" class="form-control input-small">
                        </div>

                          <div class="form-group">
                              {!!Form::select('type', ['Please select type','course' => 'E-Learning', 'training' => 'Training'], '', ['class' => 'form-control'])!!}
                          </div>

                          <button class="btn btn-success" type="submit">Search</button>
                          <a href="{{ route('admin.course.list')}}" class="btn btn-success">Reset</a>
                    </form>
                    <table id="datatable-responsivee" class="table table-striped table-bordered dt-responsive nowrap table-hover foo_table" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th>@sortablelink('vendor_id', 'Merchant')</th>
                          <th>@sortablelink('title', 'Title')</th>
                          <th data-breakpoints="sm xs">@sortablelink('course_type', 'Type')</th>
                          <th data-breakpoints="sm xs">@sortablelink('fee', 'Fee')</th>
                          <th>Status</th>
                          <th class="no-sort">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($items as $key => $item)
                        <tr>
                          <td>{{ (($items->currentPage()-1)*10)+(++$key) }}</td>
                          <td>
                              @if($item->vendor)
                                <a type="button" user_id="{{ $item->vendor_id }}" data-toggle="modal" data-target="#userModal">
                                    {{ $item->vendor->name or '--'}}
                                </a>
                              @else
                                --
                              @endif
                          </td>
                          <td>{{ $item->title }}</td>
                          <td>@if($item->course_type == 'course') E-Learning @else {{ ucfirst($item->course_type) }} @endif </td>
                          <td>{{ $item->fee }}</td>
                          <td>{{ $item->status }}</td>
                          <td>
                            <a href="{{ route('admin.course.edit', encrypt($item->id)) }}"><i class="fa fa-2x fa-edit"></i></a>
                            <a href="{{ route('admin.course.delete', ['id' => $item->id, '_token' => csrf_token()]) }}" data-message="Are you sure about deleting this?" class="post-deletee"><i class="fa fa-2x fa-trash-o"></i></a>
                            <a href="{{ route('admin.content.list', ['course_id' => encrypt($item->id)]) }}"><i class="fa fa-2x fa-eye"></i></a>
                            {{--
                            @if($item->course_type == 'course')
                                <a href="{{ route('admin.content.add', ['course_id' => $item->id]) }}"><i class="fa fa-2x fa-plus"></i></a>
                            @endif--}}
                            <a href="{{ route('admin.course.joinees', ['course_id' => encrypt($item->id)]) }}"><i class="fa fa-2x fa-user"></i></a>

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
