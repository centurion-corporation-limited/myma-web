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
            <h2>Edit Details</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <form id="demo-form2" action="{{ route('admin.role.edit', encrypt($item->id)) }}" method="POST" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left">
              {{ csrf_field() }}

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-3 col-xs-12" for="name">Name<span class="required">*</span></label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <input type="text" id="name" name="name" value="{{ old('name', $item->name) }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-3 col-xs-12" for="email">Description
                </label>
                <div class="col-md-6 col-sm-9 col-xs-12">
                  <textarea id="email" name="description" class="form-control">{{ old('description', $item->description) }}</textarea>
                </div>
              </div>

              <?php $item_perm = $item->getPermissions();
                $auth_user = Auth::user()->getPermissions();
                // dd($auth_user);
              ?>
              @if(isset($auth_user['assign-permission']['assign']) && $auth_user['assign-permission']['assign'])
              <div class="form-group">
                <label class="control-label col-md-2 col-sm-3 col-xs-12" for="email">Assign permissions</label>
                <div class="col-md-6 col-sm-9 col-xs-12">
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
                                  <div class="panel-body">
                                      <label class="col-md-6 col-xs-12">
                                          <input type="checkbox" @if(isset($item_perm[$permission->name])) checked @endif value="{{ $permission->id }}" name="permission[]">{{ $permission->title }}
                                      </label>
                        @else
                            <label class="col-md-6 col-xs-12">
                                <input type="checkbox" @if(isset($item_perm[$permission->name])) checked @endif value="{{ $permission->id }}" name="permission[]">{{ $permission->title }}
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
              @endif


              <div class="ln_solid"></div>
              <div class="form-group">
                <div class="col-md-2 col-sm-2 col-xs-12"></div>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <!-- <button class="btn btn-primary" type="button">Cancel</button> -->
                  <!-- <button class="btn btn-primary" type="reset">Reset</button> -->
                  <button type="submit" class="btn btn-success">Update</button>
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
