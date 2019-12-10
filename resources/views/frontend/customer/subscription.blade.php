@extends('layouts.customer')

@section('styles')
<style>
.subscription-grid{
    margin-top: 10 !important;
}
.fa-check{
          color: red;

        }
        .green_check .fa-check{
          color: green;
        }
</style>
@endsection
@section('header')
<header class="header">
  <h2>Subscription</h2>
  <span class="back-btn"><a href="javascript:;" onclick="window.history.go(-1); return false;"><img src="{{ static_file('images/icon-back-arrow.png ') }}'" alt=""></a></span>
</header>
<!-- End-Header -->
@endsection
@section('content')
<div class="content-pages page-content">
<div class="subscription">
  <table class="subscription-grid" width="100%%" cellspacing="0" cellpadding="0">
    <tr>
      <th>Date</th>
      <th>Brkfast</th>
      <th>Lunch</th>
      <th>Dinner</th>
    </tr>
    <?php $i = 0; ?>
    @while (strtotime($date) <= strtotime($end_date))
                <tr>
                    <td>{{ $date }}</td>
                    <td>
                        @if(isset($subs[$i]['breakfast']) && $subs[$i]['breakfast'] != '')
                          @if($subs[$i]['b_status'] > 10)
                            <span class="green_check">
                          @endif
                            <i class="fa fa-check"></i>
                          @if($subs[$i]['b_status'] > 10)
                            </span>
                          @endif
                        @endif
                    </td>
                    <td>
                        @if(isset($subs[$i]['lunch']) && $subs[$i]['lunch'] != '')
                          @if($subs[$i]['l_status'] > 10)
                            <span class="green_check">
                          @endif
                            <i class="fa fa-check"></i>
                          @if($subs[$i]['l_status'] > 10)
                            </span>
                          @endif
                        @endif
                    </td>
                    <td>
                        @if(isset($subs[$i]['dinner']) && $subs[$i]['dinner'] != '')
                          @if($subs[$i]['d_status'] > 10)
                            <span class="green_check">
                          @endif
                            <i class="fa fa-check"></i>
                          @if($subs[$i]['d_status'] > 10)
                            </span>
                          @endif
                        @endif
                    </td>
                </tr>
                <?php $date = date ("M d Y", strtotime("+1 day", strtotime($date)));
                    $i++;
                ?>
	@endWhile

  </table>
</div>
</div>

@endsection
@section('back-button')
<li><a href="javascript:;" onclick="window.history.go(-1); return false;"><span><img src="{{ static_file('images/icon-arrow-left.png') }}" alt=""></span>Back</a></li>
@endsection
@section('scripts')
@endsection
