@extends('layouts.admin')

@section('styles')
<link href="{{  static_file('css/lightbox.min.css') }}" rel="stylesheet">
<style>
.control-label + div{
  padding-top: 0px;
}
.aud_vid{
    top: -11px;
    position: relative;
}
audio, canvas, progress, video{
    margin-bottom: 10px;
}
</style>
@endsection
@section('content')
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Incident Detail (ID - {{ $item->id }}) </h2>
                    <a class="btn btn-primary pull-right" href="{{ route('admin.incident.export', encrypt($item->id)) }}">Export</a>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                  <form class="form-horizontal colord-form">
                    <!-- <div class="x_title form-group">
                      <h2>Key Points</h2>
                      <div class="clearfix"></div>
                    </div> -->

                      <div class="form-group">

                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="full-name">Incident Date :
                        </label>
                        <div class="col-md-9 col-sm-10 col-xs-12">
                          {{ Carbon\Carbon::parse($item->date)->format('d/m/Y') }}
                        </div>
                      </div>
                      <div class="form-group">

                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="full-name">Incident Time :
                        </label>
                        <div class="col-md-9 col-sm-10 col-xs-12">
                          {{ Carbon\Carbon::parse($item->time)->format('h:i A') }}
                        </div>
                      </div>

                      <div class="form-group">

                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="full-name">Dormitory :
                        </label>
                        <div class="col-md-9 col-sm-10 col-xs-12">
                          {{ $item->dormitory->name or '--' }}
                        </div>
                      </div>

                      <div class="form-group">

                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="full-name">Location :
                        </label>
                        <div class="col-md-9 col-sm-10 col-xs-12">
                          {{ $item->location }}
                        </div>
                      </div>

                      <div class="form-group">

                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="full-name">Details :
                        </label>
                        <div class="col-md-9 col-sm-10 col-xs-12">
                          {{ $item->details }}
                        </div>
                      </div>


                      <div class="form-group">

                        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="full-name">Photo :
                        </label>
                        <div class="col-md-9 col-sm-10 col-xs-12">
                        @foreach($item_photos as $photo)
                            <a href="{{ ($photo->path) }}" data-lightbox="image-1">
                              <img height="150" src="{{ ($photo->path) }}" />
                            </a>
                        @endforeach
                        </div>
                      </div>

                    <div class="form-group">
                      <label class="control-label col-md-2 col-sm-2 col-xs-12" for="full-name">Video :
                      </label>
                      <div class="col-md-9 col-sm-10 col-xs-12">
                          @foreach($item_videos as $photo)
                              <video width="320" height="240" controls>
                                <source src="{{ $photo->path }}">
                                Your browser does not support the video tag.
                              </video>
                          @endforeach
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="control-label col-md-2 col-sm-2 col-xs-12" for="full-name">Audio :
                      </label>
                      <div class="col-md-9 col-sm-10 col-xs-12">
                          @foreach($item_audios as $photo)
                              <audio controls>
                                  <source src="{{ ($photo->path) }}">
                                      Your browser does not support the audio tag.
                              </audio>
                          @endforeach
                      </div>
                    </div>

                    {{-- <div class="form-group">

                      <label class="control-label col-md-2 col-sm-2 col-xs-12" for="full-name">Reporting Officer
                      </label>
                      <div class="col-md-9 col-sm-10 col-xs-12">
                        {{ $item->name }}
                      </div>
                    </div>

                    <div class="form-group">

                      <label class="control-label col-md-2 col-sm-2 col-xs-12" for="full-name">NRIC
                      </label>
                      <div class="col-md-9 col-sm-10 col-xs-12">
                        {{ $item->nric }}
                      </div>
                    </div>

                    <div class="form-group">

                      <label class="control-label col-md-2 col-sm-2 col-xs-12" for="full-name">Grade
                      </label>
                      <div class="col-md-9 col-sm-10 col-xs-12">
                        {{ $item->grade }}
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="control-label col-md-2 col-sm-2 col-xs-12" for="full-name">ID Photo</label>
                      <div class="col-md-9 col-sm-10 col-xs-12">
                        @if($item->id_photo)
                        <a href="{{ static_file($item->id_photo) }}" data-lightbox="image-3">
                          <img width="100" height="100" src="{{ static_file($item->id_photo) }}" />
                        </a>
                        @endif
                      </div>
                    </div>

                    <div class="form-group">

                      <label class="control-label col-md-2 col-sm-2 col-xs-12" for="full-name">Signature
                      </label>
                      <div class="col-md-9 col-sm-10 col-xs-12">
                        @if($item->sign)
                        <a href="{{ static_file($item->sign) }}" data-lightbox="image-3">
                          <img width="100" height="100" src="{{ static_file($item->sign) }}" />
                        </a>
                        @endif
                      </div>
                    </div>--}}

                    <!-- <div class="x_title form-group">
                      <h2>Reporting</h2>
                      <div class="clearfix"></div>
                    </div> -->


                </form>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="{{  static_file('js/lightbox.min.js') }}"></script>
@endsection
