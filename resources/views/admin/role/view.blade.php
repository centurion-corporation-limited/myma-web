@extends('layouts.admin')

@section('styles')
<style>
.panel-default>.panel-heading{
    cursor:pointer;
}
</style>
@endsection
@section('content')

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>View Details</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <form class="colord-form">
              <div class="form-group row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Name</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{ $item->name }}
                </div>
              </div>

              <div class="form-group row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="email">Description
                </label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  {{ $item->description }}
                </div>
              </div>

              <?php $item_perm = $item->getPermissions();
                $auth_user = Auth::user()->getPermissions();
                // dd($auth_user);
              ?>
              <div class="form-group row">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="email">Permissions</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    <div class="panel-group" id="accordion">
                    <?php $arr = []; ?>
                      @foreach($permissions as $key => $permission)
                        @if(!in_array($permission->type, $arr))
                            @if(count($arr) != 0)
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <?php $arr[] = $permission->type;  ?>
                              <div class="panel panel-default">

                                    <div class="panel-heading" data-toggle="collapse" data-parent="#accordion" href="#collapse{{$key}}">
                                      <h4 class="panel-title">
                                        {{ ucwords($permission->type) }}
                                      </h4>
                                    </div>

                                <div id="collapse{{ $key }}" class="panel-collapse collapse">
                                  <div class="panel-body list-body">
                                      <label class="col-md-6 col-xs-12">
                                          <input disabled type="checkbox" @if(isset($item_perm[$permission->name])) checked @endif value="{{ $permission->id }}" name="permission[]">{{ $permission->title }}
                                      </label>
                        @else
                            <label class="col-md-6 col-xs-12">
                                <input disabled type="checkbox" @if(isset($item_perm[$permission->name])) checked @endif value="{{ $permission->id }}" name="permission[]">{{ $permission->title }}
                            </label>
                        @endif

                      @endforeach
                      <!-- last accordian div end start -->
                                  </div>
                              </div>
                          </div>
                      <!-- accordian div end -->
                      </div>
                </div>
              </div>


              <div class="ln_solid"></div>
              <div class="form-group">
                <div class="col-md-2 col-sm-2 col-xs-12"></div>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <a class="btn btn-success" href="{{ route('admin.role.edit', encrypt($item->id)) }}">Edit</a>
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
