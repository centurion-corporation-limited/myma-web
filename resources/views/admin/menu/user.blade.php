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
    /* color: #fff; */
    position: absolute;
    right: 15px;
    top: 8px;
    font-family: 'Raleway', sans-serif;
}
.cardbox-icon {
    font-size: 96px;
    color: #000;
    opacity: .8;
    position: absolute;
    right: 2px;
    top: 19%;
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
.card-details a{color: #5A738E;}
.card-details a:hover{color: #5A738E;}
/*--- dashboard card ---*/
/*background color*/
.bg-primary{background: #2184DA;}
.bg-success {background: #17B6A4;}
.bg-grey, .bg-secondary {background: #8a8f94;}
.bg-dark, .bg-inverse {background: #3C454D;}
.bg-warning {background: #EF6C00;}
.row.flex-row.button-tran {
	width: auto !important;
	padding-right: 10px !important;
}
.transaction-form .select2 {
	width: 100% !important;
}
@media only screen and (max-width: 991px){
  .row.flex-row {
  	width: 100% !important;
  }
}
</style>
@endsection
@section('content')

<div class="row">
  <div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
      <div class="x_title">
        <h2>Menu Statistics</h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content x_content_form">
                      <form action="{{ route('admin.menu.user') }}" method="GET" class="form-inline">
                          <div class="row flex-row transaction-form">
                            <label class="control-label" for="">User</label>
                            <div class="input-group">
                              <select class="user_list input-group" name="user_id">
                              </select>
                                <!-- <input type="text" placeholder="User ID" name="user_id" value="{{ Request::input('user_id') }}" class="user_list form-control input-small"> -->
                            </div>
                          </div>

                            <div class="row flex-row transaction-form">
    													<label class="control-label" for="dormitory_id">Date range </label>
    													<div class="input-group input-daterange">
                                  <input autocomplete="off" type="text" placeholder="From" class="form-control" name="start" value="{{ Request::input('start') }}">
                                  <div class="input-group-addon">to</div>
                                  <input autocomplete="off" type="text" placeholder="To" class="form-control" name="end" value="{{ Request::input('end') }}">
                              </div>
                            </div>
                            <div class="row flex-row button-tran">
                              <button class="btn btn-success" type="submit">Search</button>
                              <a href="{{ route('admin.menu.user') }}" class="btn btn-success">Reset</a>
                            </div>
                        </form>
      </div>
    </div>
  </div>
  @foreach($menus as $menu)
                            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                <div class="panel cardbox bg-default">
                                    <div class="panel-body card-item panel-refresh">

                                        <div class="refresh-container" style="display: none;"><i class="refresh-spinner fa fa-spinner fa-spin fa-5x"></i></div>
                                        <div class="timer" data-to="{{ @$total_menu[$menu->id] }}" data-speed="1500">{{ @$total_menu[$menu->id] }}</div>
                                        <div class="cardbox-icon">
                                          <img src="{{ static_file($menu->icon) }}" width="50" height="50">
                                            <!-- <i class="fa fa-user"></i> -->
                                        </div>
                                        <div class="card-details">
                                            <h4><a href="javascript:;">{{ $menu->name }}</a></h4>
                                            <!-- <span>{{ @$total_menu[$key] }}</span> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
  @endforeach
</div>

@endsection
@section('scripts')
<script>
$(document).ready(function(){
  $('.input-daterange').datepicker({
    todayBtn: "linked",
    format: "yyyy-mm-dd"
  });
});
$(".user_list").select2({
  ajax: {
    url: "{{ route('admin.get.app_user') }}",
    dataType: 'json',
    delay: 250,
    data: function (params) {
      return {
        q: params.term, // search term
        page: params.page
      };
    },
    processResults: function (data, params) {
      params.page = params.page || 1;

      return {
        results: data.items,
        pagination: {
          more: (params.page * 30) < data.total_count
        }
      };
    },
    cache: true
  },
  placeholder: 'Select a User',
  escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
  minimumInputLength: 1,
  templateResult: formatRepo,
  templateSelection: formatRepoSelection
});

function formatRepo (repo) {
  if (repo.loading) {
    return repo.text;
  }

  var markup = "<div class='select2-result-repository clearfix'>" +
    "<div class='select2-result-repository__meta'>" +
      "<div class='select2-result-repository__title'>" + repo.name + "</div>";

  markup += "</div></div>";

  return markup;
}

function formatRepoSelection (repo) {
  return repo.name || repo.text;
}
</script>
@endsection
