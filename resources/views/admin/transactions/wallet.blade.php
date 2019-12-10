@extends('layouts.admin')

@section('styles')
<link href="{{  static_file('js/plugins/bootstrap-datepicker-master/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
<style>
.button-tran-abs {
    bottom: 0;
}
.not_verified{
    background-color: #dc3545 !important;
    color: #fff;
}
</style>

@endsection
@section('content')
<div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
        <div class="x_title">
            <h2>Wallet Report </h2>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <div class="form-outer">
                <form action="{{ route('admin.transactions.wallet') }}" method="GET" class="form-inline" style="width: calc(100% + 20px);">
                    
                        <div class="row flex-row transaction-form">
                            <label class="control-label" for="dormitory_id">Date range </label>
                            <div class="input-group input-daterange">
                                <input autocomplete="off" type="text" placeholder="From" class="form-control" name="start" value="{{ Request::input('start') }}">
                                <div class="input-group-addon">to</div>
                                <input autocomplete="off" type="text" placeholder="To" class="form-control" name="end" value="{{ Request::input('end') }}">
                            </div>
                        </div>

                        <div class="row flex-row transaction-form">
                            <label class="control-label" for="">Transaction ID</label>
                            <div class="input-group">
                                <input type="text" autocomplete="off" placeholder="Transaction ID" name="transaction_id" value="{{ Request::input('transaction_id') }}" class="form-control input-small">
                            </div>
                        </div>

                        <div class="row flex-row transaction-form">
                            <label class="control-label" for="email">Email</label>
                            <div class="input-group">
                                <input autocomplete="off" type="email" placeholder="Email" name="email" value="{{ Request::input('email') }}" class="form-control input-small">
                            </div>
                        </div>

                        <div class="row flex-row  button-tran">
                            <button class="btn btn-success" type="submit">Search</button>
                            <a href="{{ route('admin.transactions.wallet') }}" class="btn btn-success">Reset</a>
                        </div>
                    </form>
                    <!--<form action="{{ route('admin.transactions.export') }}" method="GET" class="form-inline button-tran-abs">-->
                    <!--    <input type="hidden" name="transaction_id" value="{{ Request::input('transaction_id') }}">-->
                    <!--    <input type="hidden" name="type" value="{{ $type or 'inapp' }}">-->
                    <!--    <input type="hidden" name="mid" value="{{ Request::input('mid') }}">-->
                    <!--    <input type="hidden" name="start" value="{{ Request::input('start') }}">-->
                    <!--    <input type="hidden" name="end" value="{{ Request::input('end') }}">-->
                    <!--    <input type="hidden" name="email" value="{{ Request::input('email') }}">-->
                    <!--    <input type="hidden" name="email" value="{{ Request::input('email') }}" > -->
                    <!--    <button class="btn btn-success" type="submit">Export</button>-->
                    <!--</form>-->
            </div>
            <table class="table table-striped table-bordered dt-responsive nowrap table-hover foo_table" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <!-- <th>ID</th> -->
                        <th>@sortablelink('created_at', 'Txn Date')</th>
                        <th>@sortablelink('transaction_id', 'Txn ID')</th>
                        <th>@sortablelink('status', 'Status')</th>
                        <th data-breakpoints="xs sm">@sortablelink('user_id', 'User')</th>
                        <th data-breakpoints="xs sm">@sortablelink('amount', 'Txn Amount')</th>
                        <th data-breakpoints="xs sm md">To</th>
                        <th data-breakpoints="xs sm md">Message</th>
                        @if(false && $user->hasRole('admin'))
                            <th class="no-sort">Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $key => $item)
                    <tr @if($item->report_status) class="not_verified" @endif>
                        <!-- <td>{{ ++$key }}</td> -->
                        <td>{{ $item->created_at->format('d/m/Y h:i A') }}</td>
                        <td>{{ $item->transaction_id }}</td>
                        <td>{{ $item->status }}</td>
                        <td>
                            @if(isset($item->user) && $item->user->name)
                            <a type="button" user_id="{{ $item->user_id }}" data-toggle="modal" data-target="#userModal">
                                {{ $item->user->name or '--'}}
                            </a>
                            @else
                            --
                            @endif
                        </td>
                        <td class="amount">${{ $item->amount }}</td>
                        <td class="amount">{{ $item->phone }}</td>
                        <td class="amount">{{ $item->message }}</td>
                        @if(false && $user->hasRole('admin'))
                        <td class="text-right">
                            <a href="{{ route('admin.transaction.view', encrypt($item->id)) }}"><i class="fa fa-2x fa-eye"></i></a>
                            <a title="Invoice" target="_blank" href="{{ route('admin.transactions.print', $item->id) }}"><i class="fa fa-2x fa-file-text"></i></a>
                        </td>
                        @endif
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
<script src="{{ static_file('js/plugins/bootstrap-datepicker-master/dist/js/bootstrap-datepicker.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('.input-daterange').datepicker({
            todayBtn: "linked",
            format: "dd/mm/yyyy"//"yyyy-mm-dd"
        });


    });
    $('body').on('click', '.post-delete', function(event) {
        event.preventDefault();

        var message = $(this).data('message'),
            url = $(this).attr('href');

        bootbox.dialog({
            message: message,
            buttons: {
                danger: {
                    label: "Yes",
                    //className: "red",
                    callback: function() {
                        $.ajax({
                            url: url,
                            //  type: 'delete',
                            //container: '#pjax-container'
                        }).done(function(data) {
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
