@extends('layouts.admin')

@section('content')

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>View Category</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <br />
            <form id="demo-form2" action="{{ route('admin.mom.category.edit', encrypt($item->id)) }}" method="POST" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left">
              {{ csrf_field() }}

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="type">Language</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    English
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Title</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    {{ $item->content('english')->first()?$item->content('english')->first()->title:'' }}
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="type">Language</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    Bengali
                </div>
              </div>
              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Title</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    {{ $item->content('bengali')->first()?$item->content('bengali')->first()->title:'' }}
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="type">Language</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    Chinese
                </div>
              </div>
              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Title</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    {{ $item->content('mandarin')->first()?$item->content('mandarin')->first()->title:'' }}
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="type">Language</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    Thai
                </div>
              </div>
              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Title</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    {{ $item->content('thai')->first()?$item->content('thai')->first()->title:'' }}
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="type">Language</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    Tamil
                </div>
              </div>
              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Title</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    {{ $item->content('tamil')->first()?$item->content('tamil')->first()->title:'' }}
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="image">Image</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    @if($item->image)
                    <img src= "{{ static_file($item->image) }}" height="410" width="640" />
                    @endif
                </div>
              </div>
              <div class="ln_solid"></div>
              <div class="form-group">
                <div class="col-md-9 col-sm-9 col-xs-9 col-md-offset-2">
                  <a href="{{ route('admin.mom.category.edit', encrypt($item->id)) }}" class="btn btn-success">Edit</a>
                </div>
              </div>

            </form>
          </div>
        </div>
      </div>
    </div>

@endsection
