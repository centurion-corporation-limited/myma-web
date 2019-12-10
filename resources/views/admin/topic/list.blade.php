@extends('layouts.admin')

@section('content')
<div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Topics</h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li>
                          @if($auth_user->can('create.topic-add'))
                          <a href="{{ route('admin.topic.add') }}" class="btn btn-success">Add Topic</a>
                          @endif

                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content x_content_form">
                    <form action="{{ route('admin.topic.list') }}" method="GET" class="form-inline">
                          <div class="form-group">
                              <input type="text" placeholder="Title" name="name" value="{{ Request::input('name') }}" class="form-control input-small">
                          </div>

                          {{-- <div class="form-group">
                              <input type="text" placeholder="Email" name="email" value="{{ Request::input('email') }}" class="form-control">
                          </div> --}}

                          <button class="btn btn-success" type="submit">Search</button>
                          <a href="{{ route('admin.topic.list') }}" class="btn btn-success">Reset</a>
                    </form>
                    <table id="datatable-responsivee" class="table table-striped table-bordered dt-responsive nowrap table-hover foo_table" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th>@sortablelink('title', 'Title')</th>
                          <th>@sortablelink('share', 'Share')</th>
                          <th class="no-sort">Likes</th>
                          <th class="no-sort">Forums</th>
                          <th class="no-sort">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($items as $key => $item)
                        <tr>
                          <td>{{ (($items->currentPage()-1)*10)+(++$key) }}</td>
                          <td>{{ $item->title }}</td>
                          <td>{{ $item->share }}</td>
                          <td>{{ $item->likes_count }}</td>
                          <td>{{ $item->forum_count }}</td>
                          <td>
                            <a href="{{ route('admin.topic.edit', encrypt($item->id)) }}"><i class="fa fa-2x fa-edit"></i></a>
                            <a href="{{ route('admin.topic.delete', ['id' => $item->id, '_token' => csrf_token()]) }}" data-message="Are you sure about deleting this?" class="post-deletee"><i class="fa fa-2x fa-trash-o"></i></a>
                            <a href="{{ route('admin.forum.list', ['topic_id' => encrypt($item->id)]) }}"><i class="fa fa-2x fa-eye"></i></a>
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
