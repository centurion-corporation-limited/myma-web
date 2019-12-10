@extends('layouts.admin')

@section('styles')

<style>
/*--- dashboard card panel ---*/
.cardbox {
    border: 0;
    -webkit-box-shadow: 0 2px 0 rgba(0,0,0,.07);
    box-shadow: 0 2px 0 rgba(0,0,0,.07);
    border-radius: 4px;
    min-height: 122px;
    overflow: hidden;
}
.cardbox {
    border: 0;
    border-radius: 0;
    min-height: 122px;
    overflow: hidden;
    -webkit-box-shadow: 0 2px 5px 0 rgba(0,0,0,0.16), 0 2px 10px 0 rgba(0,0,0,0.12);
    box-shadow: 0 2px 5px 0 rgba(0,0,0,0.16), 0 2px 10px 0 rgba(0,0,0,0.12);
}
.cardbox .panel-body {
    min-height: 130px;
    position: relative;
}
.refresh, .refresh2 {
    color: #000;
    opacity: .3;
    font-size: 15px;
}
.refresh-container {
    position:absolute;
    top:0;
    right:0;
    background:rgba(255,255,255,0.9);
    width:100%;
    height:100%;
    display: none;
    text-align:center;
    z-index:4;
}
.refresh-spinner {
    padding: 30px;
    opacity: 0.8;
}
.timer {
    margin: 0;
    font-size: 30px;
    font-weight: 700;
    color: #fff;
    position: absolute;
    right: 15px;
    top: 8px;
    font-family: 'Raleway', sans-serif;
}
.cardbox-icon {
    font-size: 96px;
    color: #000;
    opacity: .25;
    position: absolute;
    right: -20px;
    top: 35%;
    overflow: hidden;
}
.cardbox-icon i{
    font-size: 96px;
}
.card-details{
    margin-top: 30px;
}
.card-details h4 {
    font-size: 19px;
    font-weight: 600;
    color: #fff;
    margin: 0 0 5px;
}
.card-details span{
    color: #fff;
}

/*--- dashboard card ---*/
/*background color*/
.bg-primary{background: #2184DA;}
.bg-success {background: #17B6A4;}
.bg-grey, .bg-secondary {background: #8a8f94;}
.bg-dark, .bg-inverse {background: #3C454D;}
.bg-warning {background: #EF6C00;}
</style>
@endsection
@section('content')
<!-- top tiles -->
@if($user->hasRole('admin') || $user->hasRole('sub-admin'))
<div class="row">
                            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                <div class="panel cardbox bg-primary">
                                    <div class="panel-body card-item panel-refresh">
                                        <a class="refresh" href="#">
                                            <span class="fa fa-refresh"></span>
                                        </a>
                                        <div class="refresh-container" style="display: none;"><i class="refresh-spinner fa fa-spinner fa-spin fa-5x"></i></div>
                                        <div class="timer" data-to="{{ $data['total_users'] }}" data-speed="1500">{{ $data['total_users'] }}</div>
                                        <div class="cardbox-icon">
                                            <i class="fa fa-user"></i>
                                        </div>
                                        <div class="card-details">
                                            <h4><a href="{{ route('admin.user.list', ['role' => 'app-user']) }}">Total Users</a></h4>
                                            <span>{{ $data['total_users_text'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                <div class="panel cardbox bg-success">
                                    <div class="panel-body card-item panel-refresh">
                                        <a class="refresh" href="#">
                                            <span class="fa fa-refresh"></span>
                                        </a>
                                        <div class="refresh-container" style="display: none;"><i class="refresh-spinner fa fa-spinner fa-spin fa-5x"></i></div>
                                        <div class="timer" data-to="{{ $data['logged_in'] }}" data-speed="1500">{{ $data['logged_in'] }}</div>
                                        <div class="cardbox-icon">
                                            <i class="fa fa-clock-o"></i>
                                        </div>
                                        <div class="card-details">
                                            <h4><a href="{{ route('admin.logged.in') }}">Total Logged-In Today</a></h4>
                                            <!-- <span>10% Higher than last week</span> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                <div class="panel cardbox bg-warning">
                                    <div class="panel-body card-item panel-refresh">
                                        <a class="refresh" href="#">
                                            <span class="fa fa-refresh"></span>
                                        </a>
                                        <div class="refresh-container" style="display: none;"><i class="refresh-spinner fa fa-spinner fa-spin fa-5x"></i></div>
                                        <div class="timer" data-to="{{ $data['logged_out'] }}" data-speed="1500">{{ $data['logged_out'] }}</div>
                                        <div class="cardbox-icon">
                                            <i class="fa fa-sign-out"></i>

                                        </div>
                                        <div class="card-details">
                                            <h4><a href="{{ route('admin.logged.out') }}">Total Logged-Out Today</a></h4>
                                            <!-- <span>10% Higher than last week</span> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                <div class="panel cardbox bg-dark">
                                    <div class="panel-body card-item panel-refresh">
                                        <a class="refresh" href="#">
                                            <span class="fa fa-refresh"></span>
                                        </a>
                                        <div class="refresh-container" style="display: none;"><i class="refresh-spinner fa fa-spinner fa-spin fa-5x"></i></div>
                                        <div class="timer" data-to="{{ $data['not_verified'] }}" data-speed="1500">{{ $data['not_verified'] }}</div>
                                        <div class="cardbox-icon">
                                            <i class="fa fa-user-secret"></i>
                                        </div>
                                        <div class="card-details">
                                            <h4><a href="{{ route('admin.user.list', ['role' => 'app-user', 'verified' => 'false']) }}">Not-Verified User</a></h4>
                                            <!-- <span>10% Higher than last week</span> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
</div>

<div class="row">
                            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                <div class="panel cardbox bg-primary">
                                    <div class="panel-body card-item panel-refresh">
                                        <a class="refresh" href="#">
                                            <span class="fa fa-refresh"></span>
                                        </a>
                                        <div class="refresh-container" style="display: none;"><i class="refresh-spinner fa fa-spinner fa-spin fa-5x"></i></div>
                                        <div class="timer" data-to="{{ $data['total_ads'] }}" data-speed="1500">{{ $data['total_ads'] }}</div>
                                        <div class="cardbox-icon">
                                            <i class="fa fa-dollar"></i>
                                        </div>
                                        <div class="card-details">
                                            <h4><a href="{{ route('admin.advertisement.list', ['status' => 'running']) }}">Active advertisement</a></h4>
                                            <!-- <span>10% Higher than last week</span> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                <div class="panel cardbox bg-success">
                                    <div class="panel-body card-item panel-refresh">
                                        <a class="refresh" href="#">
                                            <span class="fa fa-refresh"></span>
                                        </a>
                                        <div class="refresh-container" style="display: none;"><i class="refresh-spinner fa fa-spinner fa-spin fa-5x"></i></div>
                                        <div class="timer" data-to="{{ $data['forum_reported'] }}" data-speed="1500">{{ $data['forum_reported'] }}</div>
                                        <div class="cardbox-icon">
                                            <i class="fa fa-book"></i>
                                        </div>
                                        <div class="card-details">
                                            <h4><a href="{{ route('admin.forum.list', ['reported' => 'true']) }}">Reported Forums</a></h4>
                                            <span>{{ $data['forum_reported_text'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                <div class="panel cardbox bg-warning">
                                    <div class="panel-body card-item panel-refresh">
                                        <a class="refresh" href="#">
                                            <span class="fa fa-refresh"></span>
                                        </a>
                                        <div class="refresh-container" style="display: none;"><i class="refresh-spinner fa fa-spinner fa-spin fa-5x"></i></div>
                                        <div class="timer" data-to="{{ $data['maintenance'] }}" data-speed="1500">{{ $data['maintenance'] }}</div>
                                        <div class="cardbox-icon">
                                            <i class="fa fa-wrench"></i>
                                        </div>
                                        <div class="card-details">
                                            <h4><a href="{{ route('admin.maintenance.list', ['reported' => 'today']) }}">Maintenance</h4>
                                            <span>{{ $data['maintenance_text'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                <div class="panel cardbox bg-dark">
                                    <div class="panel-body card-item panel-refresh">
                                        <a class="refresh" href="#">
                                            <span class="fa fa-refresh"></span>
                                        </a>
                                        <div class="refresh-container" style="display: none;"><i class="refresh-spinner fa fa-spinner fa-spin fa-5x"></i></div>
                                        <div class="timer" data-to="{{ $data['feedback_count'] }}" data-speed="1500">{{ $data['feedback_count'] }}</div>
                                        <div class="cardbox-icon">
                                            <i class="fa fa-commenting"></i>
                                        </div>
                                        <div class="card-details">
                                            <h4><a href="{{ route('admin.feedback.list', ['reported' => 'today']) }}">Todays Feedbacks</a></h4>
                                            <span>{{ $data['feedback_text'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
</div>
<div class="row tile_count">
        {{--    <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
              <span class="count_top"><i class="fa fa-user"></i> Total Users</span>
              <div class="count">{{ $data['total_users'] }}</div>
              <!-- <span class="count_bottom"><i class="green">4% </i> From last Week</span> -->
            </div>
            <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
              <span class="count_top"><i class="fa fa-clock-o"></i> Total Logged-In Today</span>
              <div class="count">{{ $data['logged_in'] }}</div>
              <!-- <span class="count_bottom"><i class="green"><i class="fa fa-sort-asc"></i>3% </i> From last Week</span> -->
            </div>
            <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
              <span class="count_top"><i class="fa fa-user"></i> Total Logged-Out Today</span>
              <div class="count green">{{ $data['logged_out'] }}</div>
              <!-- <span class="count_bottom"><i class="green"><i class="fa fa-sort-asc"></i>34% </i> From last Week</span> -->
            </div>
            <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
              <span class="count_top"><i class="fa fa-user"></i> Not-Verified User</span>
              <div class="count">{{ $data['not_verified'] }}</div>
              <!-- <span class="count_bottom"><i class="red"><i class="fa fa-sort-desc"></i>12% </i> From last Week</span> -->
            </div>
            <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
              <span class="count_top"><i class="fa fa-user"></i> Total Collections</span>
              <div class="count">2,315</div>
              <!-- <span class="count_bottom"><i class="green"><i class="fa fa-sort-asc"></i>34% </i> From last Week</span> -->
            </div>
            <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
              <span class="count_top"><i class="fa fa-user"></i> Total Connections</span>
              <div class="count">7,325</div>
              <!-- <span class="count_bottom"><i class="green"><i class="fa fa-sort-asc"></i>34% </i> From last Week</span> -->
            </div>
        </div> --}}
          <!-- /top tiles -->

          {{-- <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
              <div class="dashboard_graph">

                <div class="row x_title">
                  <div class="col-md-6">
                    <h3>Network Activities <small>Graph title sub-title</small></h3>
                  </div>
                  <div class="col-md-6">
                    <div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc">
                      <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                      <span>December 30, 2014 - January 28, 2015</span> <b class="caret"></b>
                    </div>
                  </div>
                </div>

                <div class="col-md-9 col-sm-9 col-xs-12">
                  <div id="chart_plot_01" class="demo-placeholder"></div>
                </div>
                <div class="col-md-3 col-sm-3 col-xs-12 bg-white">
                  <div class="x_title">
                    <h2>Top Campaign Performance</h2>
                    <div class="clearfix"></div>
                  </div>

                  <div class="col-md-12 col-sm-12 col-xs-6">
                    <div>
                      <p>Facebook Campaign</p>
                      <div class="">
                        <div class="progress progress_sm" style="width: 76%;">
                          <div class="progress-bar bg-green" role="progressbar" data-transitiongoal="80"></div>
                        </div>
                      </div>
                    </div>
                    <div>
                      <p>Twitter Campaign</p>
                      <div class="">
                        <div class="progress progress_sm" style="width: 76%;">
                          <div class="progress-bar bg-green" role="progressbar" data-transitiongoal="60"></div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-12 col-sm-12 col-xs-6">
                    <div>
                      <p>Conventional Media</p>
                      <div class="">
                        <div class="progress progress_sm" style="width: 76%;">
                          <div class="progress-bar bg-green" role="progressbar" data-transitiongoal="40"></div>
                        </div>
                      </div>
                    </div>
                    <div>
                      <p>Bill boards</p>
                      <div class="">
                        <div class="progress progress_sm" style="width: 76%;">
                          <div class="progress-bar bg-green" role="progressbar" data-transitiongoal="50"></div>
                        </div>
                      </div>
                    </div>
                  </div>

                </div>

                <div class="clearfix"></div>
              </div>
            </div>

        </div> --}}
          <br />

          {{--<div class="row">


            <div class="col-md-4 col-sm-4 col-xs-12">
              <div class="x_panel tile fixed_height_320">
                <div class="x_title">
                  <h2>App Versions</h2>
                  <ul class="nav navbar-right panel_toolbox">
                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </li>
                    <li class="dropdown">
                      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                      <ul class="dropdown-menu" role="menu">
                        <li><a href="#">Settings 1</a>
                        </li>
                        <li><a href="#">Settings 2</a>
                        </li>
                      </ul>
                    </li>
                    <li><a class="close-link"><i class="fa fa-close"></i></a>
                    </li>
                  </ul>
                  <div class="clearfix"></div>
                </div>
                <div class="x_content">
                  <h4>App Usage across versions</h4>
                  <div class="widget_summary">
                    <div class="w_left w_25">
                      <span>0.1.5.2</span>
                    </div>
                    <div class="w_center w_55">
                      <div class="progress">
                        <div class="progress-bar bg-green" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 66%;">
                          <span class="sr-only">60% Complete</span>
                        </div>
                      </div>
                    </div>
                    <div class="w_right w_20">
                      <span>123k</span>
                    </div>
                    <div class="clearfix"></div>
                  </div>

                  <div class="widget_summary">
                    <div class="w_left w_25">
                      <span>0.1.5.3</span>
                    </div>
                    <div class="w_center w_55">
                      <div class="progress">
                        <div class="progress-bar bg-green" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 45%;">
                          <span class="sr-only">60% Complete</span>
                        </div>
                      </div>
                    </div>
                    <div class="w_right w_20">
                      <span>53k</span>
                    </div>
                    <div class="clearfix"></div>
                  </div>
                  <div class="widget_summary">
                    <div class="w_left w_25">
                      <span>0.1.5.4</span>
                    </div>
                    <div class="w_center w_55">
                      <div class="progress">
                        <div class="progress-bar bg-green" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 25%;">
                          <span class="sr-only">60% Complete</span>
                        </div>
                      </div>
                    </div>
                    <div class="w_right w_20">
                      <span>23k</span>
                    </div>
                    <div class="clearfix"></div>
                  </div>
                  <div class="widget_summary">
                    <div class="w_left w_25">
                      <span>0.1.5.5</span>
                    </div>
                    <div class="w_center w_55">
                      <div class="progress">
                        <div class="progress-bar bg-green" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 5%;">
                          <span class="sr-only">60% Complete</span>
                        </div>
                      </div>
                    </div>
                    <div class="w_right w_20">
                      <span>3k</span>
                    </div>
                    <div class="clearfix"></div>
                  </div>
                  <div class="widget_summary">
                    <div class="w_left w_25">
                      <span>0.1.5.6</span>
                    </div>
                    <div class="w_center w_55">
                      <div class="progress">
                        <div class="progress-bar bg-green" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 2%;">
                          <span class="sr-only">60% Complete</span>
                        </div>
                      </div>
                    </div>
                    <div class="w_right w_20">
                      <span>1k</span>
                    </div>
                    <div class="clearfix"></div>
                  </div>

                </div>
              </div>
            </div>

            <div class="col-md-4 col-sm-4 col-xs-12">
              <div class="x_panel tile fixed_height_320 overflow_hidden">
                <div class="x_title">
                  <h2>Device Usage</h2>
                  <ul class="nav navbar-right panel_toolbox">
                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </li>
                    <li class="dropdown">
                      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                      <ul class="dropdown-menu" role="menu">
                        <li><a href="#">Settings 1</a>
                        </li>
                        <li><a href="#">Settings 2</a>
                        </li>
                      </ul>
                    </li>
                    <li><a class="close-link"><i class="fa fa-close"></i></a>
                    </li>
                  </ul>
                  <div class="clearfix"></div>
                </div>
                <div class="x_content">
                  <table class="" style="width:100%">
                    <tr>
                      <th style="width:37%;">
                        <p>Top 5</p>
                      </th>
                      <th>
                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7">
                          <p class="">Device</p>
                        </div>
                        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">
                          <p class="">Progress</p>
                        </div>
                      </th>
                    </tr>
                    <tr>
                      <td>
                        <canvas class="canvasDoughnut" height="140" width="140" style="margin: 15px 10px 10px 0"></canvas>
                      </td>
                      <td>
                        <table class="tile_info">
                          <tr>
                            <td>
                              <p><i class="fa fa-square blue"></i>IOS </p>
                            </td>
                            <td>30%</td>
                          </tr>
                          <tr>
                            <td>
                              <p><i class="fa fa-square green"></i>Android </p>
                            </td>
                            <td>10%</td>
                          </tr>
                          <tr>
                            <td>
                              <p><i class="fa fa-square purple"></i>Blackberry </p>
                            </td>
                            <td>20%</td>
                          </tr>
                          <tr>
                            <td>
                              <p><i class="fa fa-square aero"></i>Symbian </p>
                            </td>
                            <td>15%</td>
                          </tr>
                          <tr>
                            <td>
                              <p><i class="fa fa-square red"></i>Others </p>
                            </td>
                            <td>30%</td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                  </table>
                </div>
              </div>
            </div>


            <div class="col-md-4 col-sm-4 col-xs-12">

            </div>

        </div>--}}


          <div class="row">
            <div class="col-md-4 col-sm-4 col-xs-12">
              <div class="x_panel">
                <div class="x_title">
                  <h2>Recent Activities</h2>
                  <ul class="nav navbar-right panel_toolbox">
                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </li>
                  </ul>
                  <div class="clearfix"></div>
                </div>
                <div class="x_content">
                  <div class="dashboard-widget-content">

                    <ul class="list-unstyled timeline widget">
                    @foreach($data['logs'] as $log)
                      <li>
                        <div class="block">
                          <div class="block_content">
                            <h2 class="title">
                              <a>{{ $log->text }}</a>
                            </h2>
                            <div class="byline">
                              <span>{{ $log->created_at->diffForHumans() }}</span> by <a>{{ $log->user->name or 'Anonymous'}}</a>
                            </div>
                            <!-- <p class="excerpt">Film festivals used to be do-or-die moments for movie makers. They were where you met the producers that could fund your project, and if the buyers liked your flick, they’d pay to Fast-forward and… <a>Read&nbsp;More</a> -->
                            </p>
                          </div>
                        </div>
                      </li>
                      @endforeach
                    </ul>
                  </div>
                </div>
              </div>
            </div>


            <div class="col-md-8 col-sm-8 col-xs-12">
              <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel tile">
                      <div class="x_title">
                        <h2>Analytics - Visitor's count </h2>
                        <ul class="nav navbar-right panel_toolbox">
                          <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                          </li>
                        </ul>
                        <div class="clearfix"></div>
                      </div>
                      <div class="x_content">
                        <div class="dashboard-widget-content">
                            <table class="table table-striped table-bordered dt-responsive nowrap table-hover foo_table" cellspacing="0" width="100%" data-paging="false" >
                              <thead>
                                <tr>
                                  <th>ID</th>
                                  <th class="no-sort">Date</th>
                                  <th class="no-sort">Visitor Count</th>
                                  <!-- <th>Status</th> -->
                                </tr>
                              </thead>
                              <tbody>
                                @foreach($analyticsData as $key => $item)
                                <tr>
                                  <td>{{ ++$key }}</td>
                                  <td>{{ $item['date'] }}</td>
                                  <td>{{ $item['visitors'] }}</td>
                                  <!-- <td><span class="label label-default">{{-- $item->status->name --}}</span></td> -->
                                </tr>
                                @endforeach
                              </tbody>
                            </table>
                        </div>
                      </div>
                    </div>
                </div>

                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel tile">
                      <div class="x_title">
                        <h2>Analytics - Mobile device wise visitor's count </h2>
                        <ul class="nav navbar-right panel_toolbox">
                          <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                          </li>
                        </ul>
                        <div class="clearfix"></div>
                      </div>
                      <div class="x_content">
                        <div class="dashboard-widget-content">
                            <table class="table table-striped table-bordered dt-responsive nowrap table-hover foo_table" cellspacing="0" width="100%" data-paging="false">
                              <thead>
                                <tr>
                                  <th>ID</th>
                                  <th class="no-sort">Mobile device model</th>
                                  <th class="no-sort">Visitor Count</th>
                                  <!-- <th>Status</th> -->
                                </tr>
                              </thead>
                              <tbody>
                                @foreach($modelWise as $key => $item)
                                <tr>
                                  <td>{{ ++$key }}</td>
                                  <td>{{ $item['model'] }}</td>
                                  <td>{{ $item['visitors'] }}</td>
                                  <!-- <td><span class="label label-default">{{-- $item->status->name --}}</span></td> -->
                                </tr>
                                @endforeach
                              </tbody>
                            </table>
                        </div>
                      </div>
                    </div>
                </div>

              </div>
              {{-- <div class="row">


                <!-- Start to do list -->
                <div class="col-md-6 col-sm-6 col-xs-12">
                  <div class="x_panel">
                    <div class="x_title">
                      <h2>To Do List <small>Sample tasks</small></h2>
                      <ul class="nav navbar-right panel_toolbox">
                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                        </li>
                      </ul>
                      <div class="clearfix"></div>
                    </div>
                    <div class="x_content">

                      <div class="">
                        <ul class="to_do">
                          <li>
                            <p>
                              <input type="checkbox" class="flat"> Schedule meeting with new client </p>
                          </li>
                          <li>
                            <p>
                              <input type="checkbox" class="flat"> Create email address for new intern</p>
                          </li>
                          <li>
                            <p>
                              <input type="checkbox" class="flat"> Have IT fix the network printer</p>
                          </li>
                          <li>
                            <p>
                              <input type="checkbox" class="flat"> Copy backups to offsite location</p>
                          </li>
                          <li>
                            <p>
                              <input type="checkbox" class="flat"> Food truck fixie locavors mcsweeney</p>
                          </li>
                          <li>
                            <p>
                              <input type="checkbox" class="flat"> Food truck fixie locavors mcsweeney</p>
                          </li>
                          <li>
                            <p>
                              <input type="checkbox" class="flat"> Create email address for new intern</p>
                          </li>
                          <li>
                            <p>
                              <input type="checkbox" class="flat"> Have IT fix the network printer</p>
                          </li>
                          <li>
                            <p>
                              <input type="checkbox" class="flat"> Copy backups to offsite location</p>
                          </li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- End to do list -->

                <!-- start of weather widget -->
                <div class="col-md-6 col-sm-6 col-xs-12">
                  <div class="x_panel">
                    <div class="x_title">
                      <h2>Daily active users <small>Sessions</small></h2>
                      <ul class="nav navbar-right panel_toolbox">
                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                        </li>
                        <li class="dropdown">
                          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                          <ul class="dropdown-menu" role="menu">
                            <li><a href="#">Settings 1</a>
                            </li>
                            <li><a href="#">Settings 2</a>
                            </li>
                          </ul>
                        </li>
                        <li><a class="close-link"><i class="fa fa-close"></i></a>
                        </li>
                      </ul>
                      <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                      <div class="row">
                        <div class="col-sm-12">
                          <div class="temperature"><b>Monday</b>, 07:30 AM
                            <span>F</span>
                            <span><b>C</b></span>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-4">
                          <div class="weather-icon">
                            <canvas height="84" width="84" id="partly-cloudy-day"></canvas>
                          </div>
                        </div>
                        <div class="col-sm-8">
                          <div class="weather-text">
                            <h2>Texas <br><i>Partly Cloudy Day</i></h2>
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-12">
                        <div class="weather-text pull-right">
                          <h3 class="degrees">23</h3>
                        </div>
                      </div>

                      <div class="clearfix"></div>

                      <div class="row weather-days">
                        <div class="col-sm-2">
                          <div class="daily-weather">
                            <h2 class="day">Mon</h2>
                            <h3 class="degrees">25</h3>
                            <canvas id="clear-day" width="32" height="32"></canvas>
                            <h5>15 <i>km/h</i></h5>
                          </div>
                        </div>
                        <div class="col-sm-2">
                          <div class="daily-weather">
                            <h2 class="day">Tue</h2>
                            <h3 class="degrees">25</h3>
                            <canvas height="32" width="32" id="rain"></canvas>
                            <h5>12 <i>km/h</i></h5>
                          </div>
                        </div>
                        <div class="col-sm-2">
                          <div class="daily-weather">
                            <h2 class="day">Wed</h2>
                            <h3 class="degrees">27</h3>
                            <canvas height="32" width="32" id="snow"></canvas>
                            <h5>14 <i>km/h</i></h5>
                          </div>
                        </div>
                        <div class="col-sm-2">
                          <div class="daily-weather">
                            <h2 class="day">Thu</h2>
                            <h3 class="degrees">28</h3>
                            <canvas height="32" width="32" id="sleet"></canvas>
                            <h5>15 <i>km/h</i></h5>
                          </div>
                        </div>
                        <div class="col-sm-2">
                          <div class="daily-weather">
                            <h2 class="day">Fri</h2>
                            <h3 class="degrees">28</h3>
                            <canvas height="32" width="32" id="wind"></canvas>
                            <h5>11 <i>km/h</i></h5>
                          </div>
                        </div>
                        <div class="col-sm-2">
                          <div class="daily-weather">
                            <h2 class="day">Sat</h2>
                            <h3 class="degrees">26</h3>
                            <canvas height="32" width="32" id="cloudy"></canvas>
                            <h5>10 <i>km/h</i></h5>
                          </div>
                        </div>
                        <div class="clearfix"></div>
                      </div>
                    </div>
                  </div>

                </div>
                <!-- end of weather widget -->
              </div>--}}
            </div>
          </div>
<!-- /top tiles -->
@else
  @if($user->hasRole('food-admin') || $user->hasRole('restaurant-owner-single') || $user->hasRole('restaurant-owner-catering'))
                            <div class="row">
                              <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                  <div class="panel cardbox bg-primary">
                                      <div class="panel-body card-item panel-refresh">
                                          <a class="refresh" href="#">
                                              <span class="fa fa-refresh"></span>
                                          </a>
                                          <div class="refresh-container" style="display: none;"><i class="refresh-spinner fa fa-spinner fa-spin fa-5x"></i></div>
                                          <div class="timer" data-to="{{ @$data['total_items'] }}" data-speed="1500">{{ @$data['total_items'] }}</div>
                                          <div class="cardbox-icon">
                                              <i class="fa fa-user"></i>
                                          </div>
                                          <div class="card-details">
                                              <h4><a href="{{ route('admin.food_menu.list') }}">Total Items</a></h4>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                              @if($user->hasRole('food-admin'))
                              <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                  <div class="panel cardbox bg-success">
                                      <div class="panel-body card-item panel-refresh">
                                          <a class="refresh" href="#">
                                              <span class="fa fa-refresh"></span>
                                          </a>
                                          <div class="refresh-container" style="display: none;"><i class="refresh-spinner fa fa-spinner fa-spin fa-5x"></i></div>
                                          <div class="timer" data-to="{{ @$data['total_restaurant'] }}" data-speed="1500">{{ @$data['total_restaurant'] }}</div>
                                          <div class="cardbox-icon">
                                              <i class="fa fa-clock-o"></i>
                                          </div>
                                          <div class="card-details">
                                              <h4><a href="{{ route('admin.restaurant.list') }}">Total Restaurant</a></h4>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                              <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                  <div class="panel cardbox bg-warning">
                                      <div class="panel-body card-item panel-refresh">
                                          <a class="refresh" href="#">
                                              <span class="fa fa-refresh"></span>
                                          </a>
                                          <div class="refresh-container" style="display: none;"><i class="refresh-spinner fa fa-spinner fa-spin fa-5x"></i></div>
                                          <div class="timer" data-to="{{ @$data['total_coupons'] }}" data-speed="1500">{{ @$data['total_coupons'] }}</div>
                                          <div class="cardbox-icon">
                                              <i class="fa fa-user"></i>
                                          </div>
                                          <div class="card-details">
                                              <h4><a href="{{ route('admin.coupon.list', ['type' => 'active']) }}">Total Active Coupons</a></h4>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                              @endif
                              <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                  <div class="panel cardbox bg-dark">
                                      <div class="panel-body card-item panel-refresh">
                                          <a class="refresh" href="#">
                                              <span class="fa fa-refresh"></span>
                                          </a>
                                          <div class="refresh-container" style="display: none;"><i class="refresh-spinner fa fa-spinner fa-spin fa-5x"></i></div>
                                          <div class="timer" data-to="{{ @$data['total_new_orders'] }}" data-speed="1500">{{ @$data['total_new_orders'] }}</div>
                                          <div class="cardbox-icon">
                                              <i class="fa fa-user"></i>
                                          </div>
                                          <div class="card-details">
                                              <h4><a href="{{ route('admin.order.list', ['type' => 'today']) }}">New Orders</a></h4>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                              @if($user->hasRole('food-admin'))
                              <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                  <div class="panel cardbox bg-primary">
                                      <div class="panel-body card-item panel-refresh">
                                          <a class="refresh" href="#">
                                              <span class="fa fa-refresh"></span>
                                          </a>
                                          <div class="refresh-container" style="display: none;"><i class="refresh-spinner fa fa-spinner fa-spin fa-5x"></i></div>
                                          <div class="timer" data-to="{{ @$data['total_new_items'] }}" data-speed="1500">{{ @$data['total_new_items'] }}</div>
                                          <div class="cardbox-icon">
                                              <i class="fa fa-user"></i>
                                          </div>
                                          <div class="card-details">
                                              <h4><a href="{{ route('admin.food_menu.list', ['status' => 'pending']) }}">Food item waiting approval</a></h4>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                            </div>
                            @endif

  @else
    <div class="row">
        <h2>Welcome {{ $user->name }}</h2>
    </div>
  @endif
@endif
@endsection
