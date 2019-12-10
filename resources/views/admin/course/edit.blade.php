@extends('layouts.admin')

@section('styles')
<link href="{{  static_file('js/plugins/cropper/css/style.css') }}" rel="stylesheet">
<link href="{{  static_file('js/plugins/cropper/css/style-example.css') }}" rel="stylesheet">
<link href="{{  static_file('js/plugins/cropper/css/jquery.Jcrop.css') }}" rel="stylesheet">
<link rel="stylesheet" href="{{ static_file('js/plugins/jquery.tagsinput/src/jquery.tagsinput.css')}}">

<style>
table th, table tr{
      text-align: center;
}
</style>
@endsection
@section('content')

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Edit Course</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <br />
            <form id="demo-form2" action="{{ route('admin.course.edit', encrypt($item->id)) }}" method="POST" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left">
              {{ csrf_field() }}

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="language">Language <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <select id="language" class="form-control" name="language">
                      <option data-short="" @if(old('language', $item->language) == 'english') selected="selected" @endif value="english">English</option>
                      <option data-short="bn" @if(old('language', $item->language) == 'bengali') selected="selected" @endif value="bengali">Bengali</option>
                      <option data-short="mn" @if(old('language', $item->language) == 'mandarin') selected="selected" @endif value="mandarin">Chinese</option>
                      <option data-short="ta" @if(old('language', $item->language) == 'tamil') selected="selected" @endif value="tamil">Tamil</option>
                      <option data-short="th" @if(old('language', $item->language) == 'thai') selected="selected" @endif value="thai">Thai</option>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="full-name">Course Type <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <select id="course_type" class="form-control" name="course_type">
                      <option value="training" @if(old('course_type', $item->course_type) == 'training') selected="selected" @endif>Training</option>
                      <option value="course" @if(old('course_type', $item->course_type) == 'course') selected="selected" @endif>E-learning(Course)</option>
                  </select>
                </div>
              </div>

              <div class="form-group language english">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title">Title <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="title" name="title" value="{{ old('title', $item->course_en ? $item->course_en->title : '') }}" class="form-control">
                </div>
              </div>

              <div class="form-group language tamil hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title_ta">Title <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="title_ta" name="title_ta" value="{{ old('title_ta', $item->course_ta ? $item->course_ta->title : '') }}" class="form-control">
                </div>
              </div>

              <div class="form-group language bengali hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title_bn">Title <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="title_bn" name="title_bn" value="{{ old('title_bn', $item->course_bn?$item->course_bn->title : '') }}" class="form-control">
                </div>
              </div>

              <div class="form-group language mandarin hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title_mn">Title <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="title_mn" name="title_mn" value="{{ old('title_mn', $item->course_mn?$item->course_mn->title : '') }}" class="form-control">
                </div>
              </div>

              <div class="form-group language thai hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="title_th">Title <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="title_th" name="title_th" value="{{ old('title_th', $item->course_th?$item->course_th->title : '') }}" class="form-control">
                </div>
              </div>

              <div class="form-group language english">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="about">About <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <textarea id="about" name="about" class="form-control">{{ old('about', $item->course_en ? $item->course_en->about : '') }}</textarea>
                </div>
              </div>

              <div class="form-group language english">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="description">Description <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <textarea id="description" name="description" class="form-control">{{ old('description', $item->course_en ? $item->course_en->description : '') }}</textarea>
                </div>
              </div>

              <div class="training hide">
                  <div class="form-group language english">
                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="venue">Training Venue</label>
                    <div class="col-md-6 col-sm-10 col-xs-12">
                      <textarea id="venue" name="venue" class="form-control">{{ old('venue', $item->course_en ? $item->course_en->venue : '') }}</textarea>
                    </div>
                  </div>

                  <div class="form-group language english">
                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="audience">Target Audience</label>
                    <div class="col-md-6 col-sm-10 col-xs-12">
                      <textarea id="audience" name="audience" class="form-control">{{ old('audience', $item->course_en ? $item->course_en->audience : '') }}</textarea>
                    </div>
                  </div>
              </div>

              <div class="form-group language mandarin hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="about_mn">About <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <textarea id="about_mn" name="about_mn" class="form-control">{{ old('about_mn', $item->course_mn ? $item->course_mn->about : '') }}</textarea>
                </div>
              </div>

              <div class="form-group language mandarin hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="description_mn">Description <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <textarea id="description_mn" name="description_mn" class="form-control">{{ old('description_mn', $item->course_mn ? $item->course_mn->description:'') }}</textarea>
                </div>
              </div>

              <div class="training hide">
                  <div class="form-group language mandarin hide">
                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="venue_mn">Training Venue</label>
                    <div class="col-md-6 col-sm-10 col-xs-12">
                      <textarea id="venue_mn" name="venue_mn" class="form-control">{{ old('venue_mn', $item->course_mn ? $item->course_mn->venue : '' ) }}</textarea>
                    </div>
                  </div>

                  <div class="form-group language mandarin hide">
                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="audience_mn">Target Audience</label>
                    <div class="col-md-6 col-sm-10 col-xs-12">
                      <textarea id="audience_mn" name="audience_mn" class="form-control">{{ old('audience_mn', $item->course_mn ? $item->course_mn->audience:'') }}</textarea>
                    </div>
                  </div>
              </div>

              <div class="form-group language tamil hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="about_ta">About <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <textarea id="about_ta" name="about_ta" class="form-control">{{ old('about_mn', $item->course_ta ? $item->course_ta->about : '') }}</textarea>
                </div>
              </div>

              <div class="form-group language tamil hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="description_ta">Description <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <textarea id="description_ta" name="description_ta" class="form-control">{{ old('description_ta', $item->course_ta ? $item->course_ta->description : '') }}</textarea>
                </div>
              </div>

              <div class="training hide">
                  <div class="form-group language tamil hide">
                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="venue_ta">Training Venue</label>
                    <div class="col-md-6 col-sm-10 col-xs-12">
                      <textarea id="venue_ta" name="venue_ta" class="form-control">{{ old('venue_ta', $item->course_ta ? $item->course_ta->venue : '') }}</textarea>
                    </div>
                  </div>

                  <div class="form-group language tamil hide">
                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="audience_ta">Target Audience</label>
                    <div class="col-md-6 col-sm-10 col-xs-12">
                      <textarea id="audience_ta" name="audience_ta" class="form-control">{{ old('audience_ta', $item->course_ta ? $item->course_ta->audience : '') }}</textarea>
                    </div>
                  </div>
              </div>

              <div class="form-group language bengali hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="about_bn">About <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <textarea id="about_bn" name="about_bn" class="form-control">{{ old('about_bn', $item->course_bn ? $item->course_bn->about : '') }}</textarea>
                </div>
              </div>

              <div class="form-group language bengali hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="description_bn">Description <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <textarea id="description_bn" name="description_bn" class="form-control">{{ old('description_bn', $item->course_bn ? $item->course_bn->description : '') }}</textarea>
                </div>
              </div>

              <div class="training hide">
                  <div class="form-group language bengali hide">
                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="venue_bn">Training Venue</label>
                    <div class="col-md-6 col-sm-10 col-xs-12">
                      <textarea id="venue_bn" name="venue_bn" class="form-control">{{ old('venue_bn', $item->course_bn ? $item->course_bn->venue : '') }}</textarea>
                    </div>
                  </div>

                  <div class="form-group language bengali hide">
                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="audience_bn">Target Audience</label>
                    <div class="col-md-6 col-sm-10 col-xs-12">
                      <textarea id="audience_bn" name="audience_bn" class="form-control">{{ old('audience_bn', $item->course_bn ? $item->course_bn->audience : '') }}</textarea>
                    </div>
                  </div>
              </div>

              <div class="form-group language thai hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="about_th">About <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <textarea id="about_th" name="about_th" class="form-control">{{ old('about_th', $item->course_th ? $item->course_th->about : '') }}</textarea>
                </div>
              </div>

              <div class="form-group language thai hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="description_th">Description <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <textarea id="description_th" name="description_th" class="form-control">{{ old('description_th', $item->course_th ? $item->course_th->description : '') }}</textarea>
                </div>
              </div>

              <div class="training hide">
                  <div class="form-group language thai hide">
                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="venue_th">Training Venue</label>
                    <div class="col-md-6 col-sm-10 col-xs-12">
                      <textarea id="venue_th" name="venue_th" class="form-control">{{ old('venue_th', $item->course_th ? $item->course_th->venue : '') }}</textarea>
                    </div>
                  </div>

                  <div class="form-group language thai hide">
                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="audience_th">Target Audience</label>
                    <div class="col-md-6 col-sm-10 col-xs-12">
                      <textarea id="audience_th" name="audience_th" class="form-control">{{ old('audience_th', $item->course_th ? $item->course_th->audience : '') }}</textarea>
                    </div>
                  </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="type">Type <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <select id="type" class="form-control" name="type">
                      <option value="free" @if(old('type', $item->type) == 'free') selected="selected" @endif>Free</option>
                      <option value="paid" @if(old('type', $item->type) == 'paid') selected="selected" @endif>Paid</option>
                  </select>
                </div>
              </div>

              <div class="form-group fee hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="fee">Fee (Including GST) <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="text" id="fee" name="fee" value="{{ old('fee', $item->fee) }}" class="number_only form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12">Start Date <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input id="start_date" name="start_date" class="date-picker form-control" value="{{ old('start_date', $item->start_date) }}" type="text">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12">End Date <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input id="end_date" name="end_date" class="date-picker form-control" value="{{ old('end_date', $item->end_date) }}" type="text">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="duration">Duration (In Hours) <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="number" min="0" id="duration" name="duration" value="{{ old('duration', $item->duration) }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="duration">Duration (In Minutes)</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="number" min="0" id="duration_m" name="duration_m" value="{{ old('duration_m', $item->duration_m) }}" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="duration_breakage">Duration Breakage</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="checkbox" id="duration_breakage" name="duration_breakage" value="1" @if(old('duration_breakage', $item->duration_breakage) == 1) checked @endif>
                </div>
              </div>

              <div class="placeholder_div">
                <div class="duration_breakage @if(old('duration_breakage', $item->duration_breakage) != 1) hide @endif form-group">
                  <div class="english language">
                      <label class="control-label col-md-2 col-sm-2 col-xs-12" for="duration_breakage">Duration Label</label>
                      <div class="col-md-6 col-sm-10 col-xs-12 add-icon">
                        <div class="form-control" style="height: auto;padding: 0;border: 0;background: transparent;">
                        <input type="text" name="duration_label[]" value="{{ $label[0]->label or '' }}" placeholder="Ex. Training" class="form-control">
                        <input type="number" min="0" name="duration_value[]" value="{{ $label[0]->value or '' }}" placeholder="Ex. How many hours" value="" class="form-control">
                        <input type="number" min="0" name="duration_m_value[]" value="{{ $label[0]->minute or '' }}" placeholder="Ex. Minutes leave empty if its just hours" value="" class="form-control">
                      </div>
                      <a href="javascript:;" class="add_label"> <i class="fa fa-2x fa-plus"></i></a></div>
                  </div>

                  <div class="bengali language hide">
                      <label class="control-label col-md-2 col-sm-2 col-xs-12" for="duration_breakage">Duration Label</label>
                      <div class="col-md-6 col-sm-10 col-xs-12 add-icon">
                        <div class="form-control" style="height: auto;padding: 0;border: 0;background: transparent;">
                        <input type="text" name="duration_label_bn[]" value="{{ $label_bn[0]->label or '' }}" placeholder="Ex. Training" class="form-control">
                        <input type="number" min="0" name="duration_value_bn[]" value="{{ $label_bn[0]->value or '' }}" placeholder="Ex. How many hours" value="" class="form-control">
                        <input type="number" min="0" name="duration_m_value_bn[]" value="{{ $label_bn[0]->minute or '' }}" placeholder="Ex. Minutes leave empty if its just hours" value="" class="form-control">
                      </div>
                      <a href="javascript:;" class="add_label"> <i class="fa fa-2x fa-plus"></i></a></div>
                  </div>

                  <div class="mandarin language hide">
                      <label class="control-label col-md-2 col-sm-2 col-xs-12" for="duration_breakage">Duration Label</label>
                      <div class="col-md-6 col-sm-10 col-xs-12 add-icon">
                        <div class="form-control" style="height: auto;padding: 0;border: 0;background: transparent;">
                        <input type="text" name="duration_label_mn[]" value="{{ $label_mn[0]->label or '' }}" placeholder="Ex. Training" class="form-control">
                        <input type="number" min="0" name="duration_value_mn[]" value="{{ $label_mn[0]->value or '' }}" placeholder="Ex. How many hours" value="" class="form-control">
                        <input type="number" min="0" name="duration_m_value_mn[]" value="{{ $label_mn[0]->minute or '' }}" placeholder="Ex. Minutes leave empty if its just hours" value="" class="form-control">
                      </div>
                    <a href="javascript:;" class="add_label"> <i class="fa fa-2x fa-plus"></i></a></div>
                  </div>

                  <div class="tamil language hide">
                      <label class="control-label col-md-2 col-sm-2 col-xs-12" for="duration_breakage">Duration Label</label>
                      <div class="col-md-6 col-sm-10 col-xs-12 add-icon">
                        <div class="form-control" style="height: auto;padding: 0;border: 0;background: transparent;">
                        <input type="text" name="duration_label_ta[]" value="{{ $label_ta[0]->label or '' }}" placeholder="Ex. Training" class="form-control">
                        <input type="number" min="0" name="duration_value_ta[]" value="{{ $label_ta[0]->value or '' }}" placeholder="Ex. How many hours" value="" class="form-control">
                        <input type="number" min="0" name="duration_m_value_ta[]" value="{{ $label_ta[0]->minute or '' }}" placeholder="Ex. Minutes leave empty if its just hours" value="" class="form-control">
                      </div>
                      <a href="javascript:;" class="add_label"> <i class="fa fa-2x fa-plus"></i></a></div>
                  </div>

                  <div class="thai language hide">
                      <label class="control-label col-md-2 col-sm-2 col-xs-12" for="duration_breakage">Duration Label</label>
                      <div class="col-md-6 col-sm-10 col-xs-12 add-icon">
                        <div class="form-control" style="height: auto;padding: 0;border: 0;background: transparent;">
                        <input type="text" name="duration_label_th[]" value="{{ $label_th[0]->label or '' }}" placeholder="Ex. Training" class="form-control">
                        <input type="number" min="0" name="duration_value_th[]" value="{{ $label_Th[0]->value or '' }}" placeholder="Ex. How many hours" value="" class="form-control">
                        <input type="number" min="0" name="duration_m_value_th[]" value="{{ $label_Th[0]->minute or '' }}" placeholder="Ex. Minutes leave empty if its just hours" value="" class="form-control">
                      </div>
                      <a href="javascript:;" class="add_label"> <i class="fa fa-2x fa-plus"></i></a></div>
                  </div>
                </div>
              </div>

             @if(count($label) > 0)
              @foreach($label as $k => $val)
                @if($k == 0)
                    @continue
                @else
                  <div class="language english duration_breakage_part form-group">
                    <div class="col-md-6 col-sm-10 col-xs-12 col-md-offset-2 col-sm-offset-2 add-icon">
                      <div class="form-control" style="height: auto;padding: 0;border: 0;background: transparent;">
                      <input type="text" name="duration_label[]" value="{{ $val->label }}" placeholder="Ex. Training" class="form-control">
                      <input type="number" min="0" name="duration_value[]" value="{{ $val->value or '0' }}" placeholder="Ex. How many hours" value="" class="form-control">
                      <input type="number" min="0" name="duration_m_value[]" value="{{ $val->minute or '0' }}" placeholder="Ex. Minutes leave empty if its just hours" value="" class="form-control">
                    </div>
                    <a href="javascript:;" class="remove_label"> <i class="fa fa-2x fa-times-circle"></i></a></div>
                  </div>
                 @endif
              @endforeach
             @endif

             @if(count($label_mn) > 0)
              @foreach($label_mn as $k => $val)
                @if($k == 0)
                    @continue
                @else
                  <div class="language mandarin hide duration_breakage_part form-group">
                    <div class="col-md-6 col-sm-10 col-xs-12 col-md-offset-2 col-sm-offset-2 add-icon">
                      <div class="form-control" style="height: auto;padding: 0;border: 0;background: transparent;">
                      <input type="text" name="duration_label_mn[]" value="{{ $val->label }}" placeholder="Ex. Training" class="form-control">
                      <input type="number" min="0" name="duration_value_mn[]" value="{{ $val->value or '0' }}" placeholder="Ex. How many hours" value="" class="form-control">
                      <input type="number" min="0" name="duration_m_value_mn[]" value="{{ $val->minute or '0' }}" placeholder="Ex. Minutes leave empty if its just hours" value="" class="form-control">
                    </div>
                    <a href="javascript:;" class="remove_label"> <i class="fa fa-2x fa-times-circle"></i></a></div>
                  </div>
                 @endif
              @endforeach
             @endif

             @if(count($label_bn) > 0)
              @foreach($label_bn as $k => $val)
                @if($k == 0)
                    @continue
                @else
                  <div class="language bengali hide duration_breakage_part form-group">
                    <div class="col-md-6 col-sm-10 col-xs-12 col-md-offset-2 col-sm-offset-2 add-icon">
                      <div class="form-control" style="height: auto;padding: 0;border: 0;background: transparent;">
                      <input type="text" name="duration_label_bn[]" value="{{ $val->label }}" placeholder="Ex. Training" class="form-control">
                      <input type="number" min="0" name="duration_value_bn[]" value="{{ $val->value }}" placeholder="Ex. How many hours" value="" class="form-control">
                      <input type="number" min="0" name="duration_m_value_bn[]" value="{{ $val->minute }}" placeholder="Ex. Minutes leave empty if its just hours" value="" class="form-control">
                    </div>
                    <a href="javascript:;" class="remove_label"> <i class="fa fa-2x fa-times-circle"></i></a></div>
                  </div>
                 @endif
              @endforeach
             @endif

             @if(count($label_ta) > 0)
              @foreach($label_ta as $k => $val)
                @if($k == 0)
                    @continue
                @else
                  <div class="language tamil hide duration_breakage_part form-group">
                    <div class="col-md-6 col-sm-10 col-xs-12 col-md-offset-2 col-sm-offset-2 add-icon">
                      <div class="form-control" style="height: auto;padding: 0;border: 0;background: transparent;">
                      <input type="text" name="duration_label_ta[]" value="{{ $val->label }}" placeholder="Ex. Training" class="form-control">
                      <input type="number" min="0" name="duration_value_ta[]" value="{{ $val->value }}" placeholder="Ex. How many hours" value="" class="form-control">
                      <input type="number" min="0" name="duration_m_value_ta[]" value="{{ $val->minute }}" placeholder="Ex. Minutes leave empty if its just hours" value="" class="form-control">
                    </div>
                    <a href="javascript:;" class="remove_label"> <i class="fa fa-2x fa-times-circle"></i></a></div>
                  </div>
                 @endif
              @endforeach
             @endif

             @if(count($label_th) > 0)
              @foreach($label_th as $k => $val)
                @if($k == 0)
                    @continue
                @else
                  <div class="language thai hide duration_breakage_part form-group">
                    <div class="col-md-6 col-sm-10 col-xs-12 col-md-offset-2 col-sm-offset-2 add-icon">
                      <div class="form-control" style="height: auto;padding: 0;border: 0;background: transparent;">
                      <input type="text" name="duration_label_th[]" value="{{ $val->label }}" placeholder="Ex. Training" class="form-control">
                      <input type="number" min="0" name="duration_value_th[]" value="{{ $val->value }}" placeholder="Ex. How many hours" value="" class="form-control">
                      <input type="number" min="0" name="duration_m_value_th[]" value="{{ $val->minute }}" placeholder="Ex. Minutes leave empty if its just hours" value="" class="form-control">
                    </div>
                    <a href="javascript:;" class="remove_label"> <i class="fa fa-2x fa-times-circle"></i></a></div>
                  </div>
                 @endif
              @endforeach
             @endif

              <div class="form-group language english">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="help_text">Help Text <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <textarea id="help_text" name="help_text" class="form-control">{{ old('help_text', $item->course_en ? $item->course_en->help_text : '') }}</textarea>
                </div>
              </div>

              <div class="form-group language tamil hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="help_text_ta">Help Text <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <textarea id="help_text_Ta" name="help_text_ta" class="form-control">{{ old('help_text_ta', $item->course_ta ? $item->course_ta->help_text : '') }}</textarea>
                </div>
              </div>

              <div class="form-group language bengali hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="help_text_bn">Help Text <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <textarea id="help_text_bn" name="help_text_bn" class="form-control">{{ old('help_text_bn', $item->course_bn ? $item->course_bn->help_text : '') }}</textarea>
                </div>
              </div>

              <div class="form-group language mandarin hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="help_text_mn">Help Text <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <textarea id="help_text_mn" name="help_text_mn" class="form-control">{{ old('help_text_mn', $item->course_mn ? $item->course_mn->help_text : '') }}</textarea>
                </div>
              </div>

              <div class="form-group language thai hide">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="help_text_th">Help Text <span class="required">*</span></label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <textarea id="help_text_th" name="help_text_th" class="form-control">{{ old('help_text_th', $item->course_th ? $item->course_th->help_text : '') }}</textarea>
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12">Image <span class="required">*</span></label>
                <div class="col-md-9 col-sm-9 col-xs-12">
                  <div class="colapsImg">
                    <div class="placehoderImg">
                      <div class="cropme" style="width: 567px; height: 330px;"></div>
                      <input type="hidden" class="form-control" name="path">
                    </div>
                  @if($item->image)
                  <div class="orgImg">
                    <input type="hidden" name="have_image" value="1">
                    <img src= "{{ static_file($item->image) }}" height="330" width="567" />
                  </div>
                  @endif
                  </div>
                </div>
              </div>

              {{-- <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12">Image</label>
                <div class="col-md-6 col-sm-10 col-xs-12">
                  <input type="file" class="form-control" name="path">
                  @if($item->image)
                  <img src="{{ static_file($item->image) }}" height="100" width="100" >
                  @endif
                </div>
            </div> --}}



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
<script src="{{ static_file('js/plugins/cropper/scripts/jquery.Jcrop.js') }}"></script>
<script src="{{ static_file('js/plugins/cropper/scripts/jquery.SimpleCropper.js') }}"></script>
<script src="{{ static_file('js/plugins/jquery.tagsinput/src/jquery.tagsinput.js')}}"></script>
<script>
$(document).ready(function(){
    $('[name=type]').on('change', function(){
        if($(this).val() == 'free'){
            $('.fee').addClass('hide');
        }else{
            $('.fee').removeClass('hide');
        }
    });
    $('[name=type]').trigger('change');
    $('.tags').tagsInput({
      width: '100%',
      height: '50px'
      //tagClass: 'label label-info'
    });
    $('.cropme').simpleCropper();
    // $('#start_date').datepicker({
    //     format:"yyyy-mm-dd"
    // });
    // $('#end_date').datepicker({
    //     format:"yyyy-mm-dd"
    // });

    $('#start_date').datepicker({
       format:"dd/mm/yyyy",
       startDate: '1'
     }).on('changeDate', function (selected) {
     if(selected.date != undefined)
       var minDate = new Date(selected.date.valueOf());
     else
       var minDate = new Date(this.value.valueOf());
       $('#end_date').datepicker('setStartDate', minDate);
   });

   $('#end_date').datepicker({
      format:"dd/mm/yyyy",
      startDate: '1'
    }).on('changeDate', function (selected) {
    if(selected.date != undefined)
      var minDate = new Date(selected.date.valueOf());
    else
      var minDate = new Date(this.value.valueOf());
      $('#start_date').datepicker('setEndDate', minDate);
   });

});
$(document).on('ready',function(){

    $('[name=language]').on('change', function(){
        var value = $(this).val();
        $('.language').addClass('hide');
        $('.'+value).removeClass('hide');

        // var lang = $(this).val();
        // var content = <?php //print_r(json_encode($lang)); ?>;
        //
        // $('#title').val(content[lang]['title']);
        // $('#about').val(content[lang]['about']);
        // $('#description').val(content[lang]['description']);
        // $('#venue').val(content[lang]['venue']);
        // $('#audience').val(content[lang]['audience']);
        // $('#help_text').val(content[lang]['help_text']);

    });
    $('[name=language]').trigger('change');

    $('[name=course_type]').on('change', function(){
        if($(this).val() == 'training'){
            $('.training').removeClass('hide');
        }else{
            $('.training').addClass('hide');
        }
    });
    $('[name=course_type]').trigger('change');

    $('[name=duration_breakage]').on('change', function(){
        if($(this).is(':checked')){
            $('.duration_breakage').removeClass('hide');
        }else{
            $('.duration_breakage').addClass('hide');
        }
    });

    $('.add_label').on('click',function(){
        var lang = $('[name=language]').val();
        var key = $('[name=language] option:selected').attr("data-short");

        var html = '<div class="duration_breakage_part form-group"><div class="language '+lang+'">'+

          '<div class="col-md-6 col-sm-10 col-xs-12 col-md-offset-2 col-sm-offset-2 add-icon">'+
          '<div class="form-control" style="height: auto;padding: 0;border: 0;background: transparent;">'+
            '<input type="text" name="duration_label';
            if(key != ''){
                html += '_'+key;
            }
            html+='[]" placeholder="Ex. Training" class="form-control">'+
            '<input type="number" min="0" name="duration_value';
            if(key != ''){
                html += '_'+key;
            }
            html +='[]" placeholder="Ex. How many hours" value="" class="form-control">'+
            '<input type="number" min="0" name="duration_m_value';
            if(key != ''){
                html += '_'+key;
            }
            html +='[]" placeholder="Ex. Minutes leave empty if its just hours" value="" class="form-control">'+
          '</div>'+
          '<a href="javascript:;" class="remove_label"> <i class="fa fa-2x fa-times-circle"></i></a></div>'+
        '</div></div>';
        $('.placeholder_div').append(html);
    });

    $(document).on('click','.remove_label',function(){
        $(this).closest('.duration_breakage_part').remove();
    });
});
</script>
@endsection
