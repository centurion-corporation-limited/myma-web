@extends('layouts.admin')

@section('content')
<style>
@media only screen and (max-width: 800px){
#datatable-responsivee th:first-child, #datatable-responsivee td:first-child {
	display: none;
}
.foo_table > thead > tr > th.no-sort {
	padding: 11px 4px !important;
}
}
</style>
<div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>@if(@$event) {{ @$event->content($event->language)->first()->title }} @else Detail @endif</h2>
                    <ul class="nav navbar-right panel_toolbox">
                        <li><a href="{{ route('admin.jtc.detail.add', ['event_id' => @$event->id]) }}" class="btn btn-success">Add</a>
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content x_content_form">
                    <form action="{{ route('admin.jtc.detail.list') }}" method="GET" class="form-inline">
                          <div class="form-group">
															<input type="hidden" name="menu_type" value="{{ Request::input('menu_type') }}">
                              <input type="text" placeholder="Title" name="name" value="{{ Request::input('name') }}" class="form-control input-small">
                          </div>

													{{-- <div class="form-group">
                              <input type="text" placeholder="Email" name="email" value="{{ Request::input('email') }}" class="form-control">
                          </div> --}}

                          <button class="btn btn-success" type="submit">Search</button>
                          <a href="{{ route('admin.jtc.detail.list', ['menu_type' => request('menu_type')]) }}" class="btn btn-success">Reset</a>
                    </form>
                    <table id="datatable-responsivee" class="table table-striped table-bordered dt-responsive nowrap table-hover display foo_table" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th style="width:30%;">@sortablelink('title')</th>
                          <th>@sortablelink('author_id', 'Author')</th>
													<th class="no-sort">Feedbacks</th>
                          <th class="no-sort">Likes</th>
                          <th >@sortablelink('created_at', 'Publish date')</th>
                          <th >@sortablelink('publish', 'Published')</th>
                          <th >@sortablelink('created_by', 'Created By')</th>
                          <th class="no-sort">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($items as $key => $item)
                        <tr>
                          <td>{{ (($items->currentPage()-1)*10)+(++$key) }}</td>
                          <td>{{ $item->title }}</td>
                          <td>
                              {{ $item->author }}
                              {{--
                              @if($item->author->name)
                                <a type="button" user_id="{{ $item->author_id }}" data-toggle="modal" data-target="#userModal">
                                    {{ $item->author->name or '--'}}
                                </a>
                              @else
                                --
                              @endif
                              --}}
                          </td>
													<td>
														<a data-toggle="tooltip" data-placement="top" title="Click to view the comments" href="{{ route('admin.jtc.comments.list', ['topic_id' => encrypt($item->id)]) }}">
															{{ $item->comments_count }}
														</a>
													</td>
                          <td>{{ $item->likes_count }}</td>
                          <td>{{ $item->created_at->format('d/m/Y') }}</td>
                          <td>@if($item->publish) Yes @else No @endif</td>
                          <td>{{ $item->creator->name or '--' }}</td>

                          <td>
                            <a data-toggle="tooltip" data-placement="top" title="Click to view the content" href="{{ route('admin.jtc.detail.view', encrypt($item->id)) }}"><i class="fa fa-2x fa-eye"></i></a>
                            <a data-toggle="tooltip" data-placement="top" title="Click to edit the content." href="{{ route('admin.jtc.detail.edit', encrypt($item->id)) }}"><i class="fa fa-2x fa-edit"></i></a>
                            <a data-toggle="tooltip" data-placement="top" title="Click to delete the content." href="{{ route('admin.jtc.detail.delete', ['id' => $item->id, '_token' => csrf_token()]) }}" data-message="Are you sure about deleting this?" class="post-deletee"><i class="fa fa-2x fa-trash-o"></i></a>
                            {{-- <a href="{{ route('admin.services.view', $item->id) }}"><i class="fa fa-2x fa-eye"></i></a> --}}
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
