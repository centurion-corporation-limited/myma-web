@extends('layouts.admin')

@section('content')

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Subscription detail</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <br />
            <form id="demo-form2" >

              <table class="table table-striped table-bordered nowrap table-hover" cellspacing="0" width="100%">
                <thead>
                  <tr>
                    <th>Date</th>
                    <th>Breakfast</th>
                    <th>Lunch</th>
                    <th>Dinner</th>
                  </tr>
                </thead>
                <tbody>
                    <?php $i = 0; ?>
                    @while (strtotime($date) <= strtotime($end_date))
                        <tr>
                            <td>{{ $date }}</td>
                            <td>
                                @if(isset($subs[$i]['breakfast']) && $subs[$i]['breakfast'] != '')
                                    <i class="fa fa-check"></i>
                                @endif
                            </td>
                            <td>
                                        @if(isset($subs[$i]['lunch']) && $subs[$i]['lunch'] != '')
                                            <i class="fa fa-check"></i>
                                        @endif
                            </td>
                            <td>
                                        @if(isset($subs[$i]['dinner']) && $subs[$i]['dinner'] != '')
                                            <i class="fa fa-check"></i>
                                        @endif
                            </td>
                        </tr>
                        <?php $date = date ("M d Y", strtotime("+1 day", strtotime($date)));
                            $i++;
                        ?>
                	@endWhile
                </tbody>
              </table>
              <div class="form-group">
                <div class="col-md-2 col-sm-2 col-xs-12"></div>
                <div class="col-md-6 col-sm-10 col-xs-12">

                </div>
              </div>

            </form>
          </div>
        </div>
      </div>
    </div>

@endsection
@section('scripts')
<script>

</script>
@endsection
