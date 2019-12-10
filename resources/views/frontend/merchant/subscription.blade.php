@extends('layouts.merchant')

@section('header')
<header class="header">
  <h2>Subscription</h2>
  <span class="back-btn"><a href="javascript:;" onclick="window.history.go(-1); return false;">
      <img src="{{ static_file('merchant/images/icon-back-arrow.png') }}" alt=""></a>
  </span>
</header>
@endsection

@section('content')
<div class="page-content">
<div class="subscription">
<div class="date-pic">
  <div class="form-group">
      <input type="text" class="form-control-md icon-cal" placeholder="22/03/2018">
      <input type="text" class="form-control-md icon-cal" placeholder="28/03/2018">
    </div>

</div>
<table class="subscription-grid" width="100%%" cellspacing="0" cellpadding="0">
  <tr>
    <th>Date</th>
    <th>Breakfast</th>
    <th>Lunch</th>
    <th>Dinner</th>
  </tr>
  <?php $index = 0; ?>
  @while (strtotime($date) <= strtotime($end_date))
              <tr>
                  <td>{{ $date }}</td>
                  <td>{{ $subs[$index]['breakfast'] or '' }}</td>
                  <td>{{ $subs[$index]['lunch'] or ''}}</td>
                  <td>{{ $subs[$index]['dinner'] or '' }}</td>
              </tr>
              <?php $date = date ("M d Y", strtotime("+1 day", strtotime($date)));
              $index++;
              ?>
  @endWhile
</table>
</div>
</div>
@endsection
