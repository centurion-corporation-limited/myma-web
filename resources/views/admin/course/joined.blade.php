@extends('layouts.admin')

@section('content')
<div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Joinees</h2>
                    <!-- <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                      <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                        <ul class="dropdown-menu" role="menu">
                          <li><a href="#">Settings 1</a>
                          </li>
                          <li><a href="#">Settings 2</a>
                          </li>
                        </ul>
                      </li>
                      <li><a class="close-link"><i class="fa fa-close"></i></a>
                      </li>
                    </ul> -->
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <table id="datatable-responsivee" class="table table-striped table-bordered dt-responsive nowrap table-hover foo_table" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th>@sortablelink('user_id', 'User')</th>
                          <!-- <th>Title</th>
                          <th>Type</th>
                          <th>Duration</th>
                          <th>Fee</th> -->
                          <!-- <th class="no-sort">Action</th> -->
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($items as $key => $item)
                        <tr>
                          <td>{{ (($items->currentPage()-1)*10)+(++$key) }}</td>
                          <td>
                              @if($item->user)
                                <a type="button" user_id="{{ $item->user_id }}" data-toggle="modal" data-target="#userModal">
                                    {{ $item->user->name or '--'}}
                                </a>
                              @else
                                --
                              @endif
                          </td>
                          <!-- <td>{{ $item->title }}</td>
                          <td>{{ $item->course_type }}</td>
                          <td>{{ $item->duration }}</td>
                          <td>{{ $item->fee }}</td> -->
                          <!-- <td> -->
                            {{--<a href="{{ route('admin.course.edit', $item->id) }}"><i class="fa fa-2x fa-edit"></i></a>
                            <a href="{{ route('admin.course.delete', ['id' => $item->id, '_token' => csrf_token()]) }}" data-message="Are you sure about deleting this?" class="post-deletee"><i class="fa fa-2x fa-trash-o"></i></a>
                            <a href="{{ route('admin.content.list', ['course_id' => $item->id]) }}"><i class="fa fa-2x fa-eye"></i></a>

                            @if($item->course_type == 'course')
                                <a href="{{ route('admin.content.add', ['course_id' => $item->id]) }}"><i class="fa fa-2x fa-plus"></i></a>
                            @endif
                            <a href="{{ route('admin.course.joinees', ['course_id' => $item->id]) }}"><i class="fa fa-2x fa-user"></i></a>
                            --}}
                          <!-- </td> -->
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
