@extends('layouts.frontend')

@section('styles')
@endsection
@section('content')

{{-- <div class="top-head">
  <h2>Contact Us</h2>
  <span class="back-btn"><a href="#"><img src="{{ static_file('images/icon-back-arrow.png') }}" alt=""></a></span></div> --}}
<div class="feedback">

<form action="{{ route('frontend.contact_us') }}" method="post">
    {{ csrf_field() }}
  <div class="forum-sec">

    <div class="form-topic">
      <input type="text" class="form-control" placeholder="Name" name="name" value="{{ $user->name or '' }}" required>
    </div>
    <div class="form-topic">
      <input type="email" class="form-control" placeholder="Email" name="email" value="{{ $user->email or '' }}" required>
    </div>
    <div class="form-topic">
      <input type="text" class="form-control" placeholder="Phone" name="phone" value="{{ $user->profile->phone or '' }}" required>
    </div>
    <div class="form-topic">
      <textarea class="form-control form-control-topic" placeholder="Text Here .." name="description" required> </textarea>
    </div>

    <div class="top-up-row">
      <button type="submit" class="btn top-btn">Submit</button>
    </div>
</form>

  </div>
</div>


@endsection

@section('scripts')
@endsection
