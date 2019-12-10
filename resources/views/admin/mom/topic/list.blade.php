@extends('layouts.admin')

@section('content')
<div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Topic</h2>
                    @if($auth_user->can('create.mom-topic-add'))
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a href="{{ route('admin.mom.topic.add') }}" class="btn btn-success">Add Topic</a>
                      </li>
                    </ul>
                    @endif
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content x_content_form">
                    <form action="{{ route('admin.mom.topic.list') }}" method="GET" class="form-inline">
                          <div class="form-group">
                              <input type="text" placeholder="Name" name="name" value="{{ Request::input('name') }}" class="form-control input-small">
                          </div>

                          {{-- <div class="form-group">
                              <input type="text" placeholder="Email" name="email" value="{{ Request::input('email') }}" class="form-control">
                          </div> --}}

                          <button class="btn btn-success" type="submit">Search</button>
                          <a href="{{ route('admin.mom.topic.list') }}" class="btn btn-success">Reset</a>
                    </form>
                    <table id="datatable-responsivee" class="table table-striped table-bordered dt-responsive nowrap table-hover" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th>Title</th>
                          <th class="no-sort">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($items as $key => $item)
                        <tr>
                          <td>{{ (($items->currentPage()-1)*10)+(++$key) }}</td>
                          <td>{{ $item->content($item->language)->first()->title or '' }}</td>
                          <td>
                            <a href="{{ route('admin.mom.topic.view', ['topic_id' => encrypt($item->id)]) }}"><i class="fa fa-2x fa-eye"></i></a>
                            <a href="{{ route('admin.mom.topic.edit', encrypt($item->id)) }}"><i class="fa fa-2x fa-edit"></i></a>
                            @if($auth_user->can('delete.mom-topic-delete'))
                            <a href="{{ route('admin.mom.topic.delete', ['id' => $item->id, '_token' => csrf_token()]) }}" data-message="Are you sure about deleting this?" class="post-deletee"><i class="fa fa-2x fa-trash-o"></i></a>
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

// $('body').on('click', '.post-deletee', function (event) {
//
//     event.preventDefault();
//
//     var message = $(this).data('message'),
//         url = $(this).attr('href');
//
//     bootbox.dialog({
//         message: message,
//         buttons: {
//             danger: {
//                 label: "Yes",
//                 //className: "red",
//                 callback: function () {
//                     $.ajax({
//                         url: url,
//                         // method: 'post',
//                       //  type: 'delete',
//                         //container: '#pjax-container'
//                     }).done(function(data){
//                       //console.log(data);
//                       location.reload();
//                     });
//                 }
//             },
//             success: {
//                 label: "Cancel",
//                 //className: "green"
//             }
//         }
//     });
// })
</script>
@stop
