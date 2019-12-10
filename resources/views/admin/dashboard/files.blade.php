@extends('layouts.admin')

@section('content')
<div class="col-md-12 col-sm-12 col-xs-12">

    <div class="x_panel">
        <div class="x_title">
            <h2>Flexm Files </h2>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">

            <table class="table table-striped table-bordered dt-responsive nowrap table-hover foo_table" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <!-- <th>ID</th> -->
                        <th>Id</th>
                        <th>Date</th>
                        <th>File</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $key => $item)
                    <tr>
                        <td>{{ ++$key }}</td>
                        <td>{{ $item['time'] }}</td>
                        <td><a href="{{ static_file('files/uploaded/'.$item['name']) }}" >{{ $item['name'] }}</a></td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@stop
