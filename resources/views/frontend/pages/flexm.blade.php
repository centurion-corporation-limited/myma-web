@extends('layouts.frontend')

@section('styles')
<style>
.nav-pills > li{
  background-color: inherit;
}
</style>

@endsection
@section('content')


<div class="forum-sec info-sec">
 <h2>Terms & Conditions</h2>
 <ul class="nav nav-pills">
   <li class="active"><a data-toggle="pill" href="#menu2">Flexm</a></li>
   <li><a data-toggle="pill" href="#menu1">Remittance</a></li>
 </ul>

 <div class="tab-content">
   <div id="menu2" class="tab-pane fade in active">
     <h3>Flexm</h3>
     <p>{!! $flexm_content !!}</p>
   </div>
   <div id="menu1" class="tab-pane fade">
     <h3>Remmittance</h3>
     <p>{!! $remittance_content !!}</p>
   </div>
 </div>

</div>


@endsection

@section('scripts')
@endsection
