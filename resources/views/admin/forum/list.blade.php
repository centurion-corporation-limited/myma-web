@extends('layouts.admin')

@section('content')
<style>
@media only screen and (max-width: 800px) {

.foo_table > thead > tr > th {
	padding: 11px 2px 11px 22px !important;
}
.foo_table > tbody > tr > td {
	padding: 8px 4px !important;
}
}
</style>
<div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Forums</h2>
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
                  <div class="x_content x_content_form">
                    <form action="{{ route('admin.forum.list') }}" method="GET" class="form-inline">
                          <div class="form-group">
                              <input type="text" placeholder="Title" name="name" value="{{ Request::input('name') }}" class="form-control input-small">
                          </div>

                          {{-- <div class="form-group">
                              <input type="text" placeholder="Email" name="email" value="{{ Request::input('email') }}" class="form-control">
                          </div> --}}

                          <button class="btn btn-success" type="submit">Search</button>
                          <a href="{{ route('admin.forum.list') }}" class="btn btn-success">Reset</a>
                    </form>
                    <table id="datatable-responsivee" class="table table-striped table-bordered dt-responsive nowrap table-hover foo_table" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th>@sortablelink('title', 'Title')</th>
                          <th>@sortablelink('user_id', 'Created By')</th>
                          <th>@sortablelink('created_at', 'Created On')</th>
                          <th data-breakpoints="xs sm">@sortablelink('share', 'Share')</th>
                          <th class="no-sort">Likes</th>
                          <th class="no-sort" data-breakpoints="xs sm">Last Comment</th>
                          <th class="no-sort" data-breakpoints="xs sm">Total Comments</th>
                          <th class="no-sort">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($items as $key => $item)
                        <tr>
                          <td>{{ (($items->currentPage()-1)*10)+(++$key) }}</td>
                          <td>{{ $item->title }}</td>
                          <td>
                              @if(@$item->user->name)
                                <a type="button" user_id="{{ $item->user_id }}" data-toggle="modal" data-target="#userModal">
                                    {{ $item->user->name or '--'}}
                                </a>
                              @else
                                --
                              @endif
                          </td>
                          <td>{{ $item->created_at->format('d/m/Y') }}</td>
                          <td>{{ $item->share }}</td>
                          <td>{{ $item->likes_count }}</td>
                          <td>{{ isset($item->latestComment[0])?$item->latestComment[0]->created_at->format('d/m/Y'):'--' }}</td>
                          <td>{{ $item->comments_count }}</td>
                          <td>
                        {{-- @if($bad_words == 'true') --}}
                            <a href="{{ route('admin.forum.edit', ['id' => encrypt($item->id), 'bad_words' => 'true']) }}"><i class="fa fa-2x fa-edit"></i></a>
                            <a href="{{ route('admin.forum.delete', ['id' => $item->id, '_token' => csrf_token()]) }}" data-message="Are you sure about deleting this?" class="post-deletee"><i class="fa fa-2x fa-trash-o"></i></a>
                        {{-- @endif --}}
                        @if($reported == 'true')
                            <a href="{{ route('admin.forum.unreport', ['id' => encrypt($item->id)]) }}" title="Remove Report"><i class="fa fa-2x fa-times-circle"></i></a>
                            <!-- <a href="{{ route('admin.forum.delete', ['id' => $item->id, '_token' => csrf_token()]) }}" data-message="Are you sure about deleting this?" class="post-deletee"><i class="fa fa-2x fa-trash-o"></i></a> -->
                        @endif
                            <a href="{{ route('admin.forum.view', encrypt($item->id)) }}"><i class="fa fa-2x fa-eye"></i></a>
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
