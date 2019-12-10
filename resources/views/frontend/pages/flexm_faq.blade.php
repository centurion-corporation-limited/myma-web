@extends('layouts.frontend')

@section('styles')
<style>
.info-sec li {
    list-style: none;
    border-bottom: none;
    font-size: 14px;
    padding: 8px 0;
}
</style>
@endsection
@section('content')

<div class="forum-sec info-sec">
 <h2>{!! $page->title !!}</h2>
 {!! $page->content !!}

</div>


@endsection

@section('scripts')
@endsection
