@extends('layouts.admin')

@section('content')

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>View Topic</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <br />
            <form id="demo-form2" action="{{ route('admin.mom.category.edit', encrypt($item->id)) }}" method="POST" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left">
              {{ csrf_field() }}

              @if($item->content('english') && $item->content('english')->first())
              <div class="form-group">
                  <label class="control-label col-md-2 col-sm-2 col-xs-12" for="type">Category</label>
                  <div class="col-md-6 col-sm-10 col-xs-12">
                      @if($item->category('english') && $item->category('english')->first())
                      {{ $item->category('english')->first()->title }}
                      @endif
                  </div>
              </div>
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
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="type">Content</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    @if($item->type =='text')
                        {{ $item->content('english')->first()->content }}
                    @elseif(in_array($item->type, $file_array))
                        <a href="{{ static_file($item->content('english')->first()->content) }}">Click to view File</a>
                    @else
                        Youtube - {{ $item->content('english')->first()->content }}
                    @endif
                </div>
              </div>
              @endif

              @if($item->content('bengali') && $item->content('bengali')->first())
              <div class="form-group">
                  <label class="control-label col-md-2 col-sm-2 col-xs-12" for="type">Category</label>
                  <div class="col-md-6 col-sm-10 col-xs-12">
                      @if($item->category('bengali') && $item->category('bengali')->first())
                      {{ $item->category('bengali')->first()->title }}
                      @endif
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
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="type">Content</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    @if($item->type =='text')
                        {{ $item->content('bengali')->first()->content }}
                    @elseif(in_array($item->type, $file_array))
                        <a href="{{ static_file($item->content('bengali')->first()->content) }}">Click to view File</a>
                    @else
                        Youtube - {{ $item->content('bengali')->first()->content }}
                    @endif
                </div>
              </div>
              @endif

              @if($item->content('mandarin') && $item->content('mandarin')->first())
              <div class="form-group">
                  <label class="control-label col-md-2 col-sm-2 col-xs-12" for="type">Category</label>
                  <div class="col-md-6 col-sm-10 col-xs-12">
                      @if($item->category('mandarin') && $item->category('mandarin')->first())
                      {{ $item->category('mandarin')->first()->title }}
                      @endif
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
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="type">Content</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    @if($item->type =='text')
                        {{ $item->content('mandarin')->first()->content }}
                    @elseif(in_array($item->type, $file_array))
                        <a href="{{ static_file($item->content('mandarin')->first()->content) }}">Click to view File</a>
                    @else
                        Youtube - {{ $item->content('mandarin')->first()->content }}
                    @endif
                </div>
              </div>
              @endif

              @if($item->content('thai') && $item->content('thai')->first())
              <div class="form-group">
                  <label class="control-label col-md-2 col-sm-2 col-xs-12" for="type">Category</label>
                  <div class="col-md-6 col-sm-10 col-xs-12">
                      @if($item->category('thai') && $item->category('thai')->first())
                      {{ $item->category('thai')->first()->title }}
                      @endif
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
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="type">Content</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    @if($item->type =='text')
                        {{ $item->content('thai')->first()->content }}
                    @elseif(in_array($item->type, $file_array))
                        <a href="{{ static_file($item->content('thai')->first()->content) }}">Click to view File</a>
                    @else
                        Youtube - {{ $item->content('thai')->first()->content }}
                    @endif
                </div>
              </div>
              @endif

              @if($item->content('tamil') && $item->content('tamil')->first())
              <div class="form-group">
                  <label class="control-label col-md-2 col-sm-2 col-xs-12" for="type">Category</label>
                  <div class="col-md-6 col-sm-10 col-xs-12">
                      @if($item->category('tamil') && $item->category('tamil')->first())
                      {{ $item->category('tamil')->first()->title }}
                      @endif
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
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="type">Content</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                    @if($item->type =='text')
                        {{ $item->content('tamil')->first()->content }}
                    @elseif(in_array($item->type, $file_array))
                        <a href="{{ static_file($item->content('tamil')->first()->content) }}">Click to view File</a>
                    @else
                        Youtube - {{ $item->content('tamil')->first()->content }}
                    @endif
                </div>
              </div>
              @endif

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
                  <!-- <button class="btn btn-primary" type="button">Cancel</button> -->
                  <!-- <button class="btn btn-primary" type="reset">Reset</button> -->
                  <a class="btn btn-success" href="{{ route('admin.mom.topic.edit', encrypt($item->id)) }}">Edit</a>
                </div>
              </div>

            </form>
          </div>
        </div>
      </div>
    </div>

@endsection
