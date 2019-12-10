{{-- @extends('frontend.master') --}}
<meta name="viewport" content="width=device-width, initial-scale=1.0">
{{--@section('content')--}}
<style>

.sucess-msg {
	height: 100vh;
	display: inline-block;
	margin: 0 auto;
	padding: 0;
	max-width: 100%;
	text-align: center;
	width: 100%;
}
.sucess-msg img {
	max-height: 150px;
	margin: 25px 0 0;
}
.sucess-msg h1 {
	font-size: 21px;
	letter-spacing: 0.2px;
	padding: 0 15px;
}
@media only screen and (max-width: 767px){
	.sucess-msg img {
	    max-height: 110px;
	    margin: 25px 0 0;
	}
	.sucess-msg h1 {
    font-size: 18px;
    padding: 0 5%;
}
}
@media only screen and (max-width: 640px){

}
</style>
    <section class="content">
        <div class="container">
          <div class="sucess-msg">
            <img src="{{  static_file('images/check-verified.png') }}">
            <h1>You have successfully registered your account.</h1>
          </div>
        </div>
    </section>
{{--@endsection--}}
