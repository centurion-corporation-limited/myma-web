@extends('layouts.admin')

@section('content')
<div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Menu</h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a href="{{ route('admin.food_menu.add') }}" class="btn btn-success">Add Item</a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content x_content_form">
                    <form action="{{ route('admin.food_menu.list') }}" method="GET" class="form-inline">
                          <div class="row flex-row transaction-form">
                              <label class="control-label" for="">Name</label>
                              <div class="input-group">
                                  <input type="text" autocomplete="off" placeholder="Name" name="name" value="{{ Request::input('name') }}" class="form-control input-small">
                              </div>
                          </div>
                          @if(!$flag)
                          <div class="row flex-row transaction-form">
                            <div class="input-group ">
                              {!!Form::select('merchant_id', $merchants, Request::input('merchant_id'), ['class' => 'form-control'])!!}
                            </div>
                          </div>
                          @endif
                          <div class="row flex-row button-tran">
                            <button class="btn btn-success" type="submit">Search</button>
                            <a href="{{ route('admin.food_menu.list')}}" class="btn btn-success">Reset</a>
                          </div>
                    </form>
                    <table id="datatable-responsivee" class="table table-striped table-bordered dt-responsive nowrap table-hover" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th>@sortablelink('name', 'Name')</th>
                          @if($auth_user->hasRole('food-admin'))
                          <th>@sortablelink('type', 'Type')</th>
                          <th>@sortablelink('restaurant_id', 'Merchant')</th>
                          @endif
                          <th>@sortablelink('published', 'Status')</th>
                          @if($auth_user->hasRole('food-admin'))
                            @if(!$flag)
                            <th>Recommended</th>
                            @endif
                          @endif
                          <th class="no-sort">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($items as $key => $item)
                        <tr>
                          <td>{{ (($items->currentPage()-1)*$limit)+(++$key) }}</td>
                          <td>{{ $item->name }}</td>
                          @if($auth_user->hasRole('food-admin'))
                          <td>{{ $item->type }}</td>
                          <td>{{ $item->restaurant->merchant->name or '-' }}</td>
                          @endif
                          <td>@if($item->published) Published @else Pending @endif</td>
                          @if($auth_user->hasRole('food-admin'))
                            @if(!$flag)
                            <td>@if($item->published)<input item_id="{{ encrypt($item->id) }}" class="recommended_click" type="checkbox" @if($item->recommended) checked @endif >@endif</td>
                            @endif
                          @endif  
                          <td>
                            <a href="{{ route('admin.food_menu.edit', encrypt($item->id)) }}"><i class="fa fa-2x fa-edit"></i></a>
                            <a href="{{ route('admin.food_menu.delete', ['id' => encrypt($item->id), '_token' => csrf_token()]) }}" data-message="Are you sure about deleting this?" class="post-deletee"><i class="fa fa-2x fa-trash-o"></i></a>
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
});

$(document).on('click', '.recommended_click', function (event) {
        var item_id = $(this).attr('item_id');
        var formData = new FormData();
        formData.append('item_id', item_id);
        // formData.append('csrf_token', '{{ csrf_token() }}');

        $.ajax({
            method:'post',
            url: '{{ route('admin.food_menu.recommended') }}',
            data: formData,
            crossDomain: true,
            cache: false,
            contentType: false,
            processData: false,
            //beforeSend: function () {
                //    $(objct).val('Connecting...');
            //},
            error: function(xhr){
                console.log("Error");
                console.log(xhr);
            },
            success: function(data){
                console.log(data);
                var obj = data;
                if (obj.status == 'success') {
                    location.reload();
                }else if (obj.status == 'error') {
                    alert(obj.message);
                }
            }
        });
});
</script>
@stop
