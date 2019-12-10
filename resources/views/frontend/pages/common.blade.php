@extends('layouts.frontend')

@section('styles')
@endsection
@section('content')


<div class="forum-sec info-sec">
 <h2>{!! $page->title !!}</h2>
 {!! $page->content !!}

</div>


@endsection

@section('scripts')
@endsection
