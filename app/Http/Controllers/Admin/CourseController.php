<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Models\Course;
use App\Models\CourseLanguage;
use App\Models\CourseJoined;
use App\Models\Content;
use App\Models\ContentFile;
use App\Models\ContentLang;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Requests;
use App\Http\Requests\CourseRequest;
use App\Http\Requests\EditCourseRequest;
use App\Http\Requests\AddContentRequest;
use App\Http\Requests\EditContentRequest;
use App\Http\Controllers\Controller;
use Auth, Activity;
use Illuminate\Contracts\Encryption\DecryptException;

class CourseController extends Controller
{
    public function getList(Request $request)
    {
      $auth_user = Auth::user();
      $items = Course::query();
      if($auth_user->hasRole('admin')){

      }else{
        $items->where('vendor_id', $auth_user->id);
      }

      if ($title = $request->input('title')) {
          $items->whereHas('lang_content', function($q) use($title){
                $q->where('title', 'like', "%{$title}%");
          });
      }

      $type = $request->input('type');
      if ($type != '' && $type != '0') {
          $items->where('course_type', $type);
      }

      // if ($username = $request->input('username')) {
      //     $items->where(function ($q) use ($username) {
      //         $q->where('name', 'like', "%{$username}%")
      //             ->orWhere('email', 'like', "%{$username}%");
      //     });
      // }

      $limit = 10;
      $items = $items->sortable(['id' => 'desc'])->paginate($limit);
      $paginate_data = $request->except('page');

      $now = Carbon::now();
      foreach($items as $item){
          $lang = $item->language;
          $ser = CourseLanguage::where('course_id', $item->id)->where('language', $lang)->first();
          if($ser){
              $item->title = $ser->title;
          }
          $end = Carbon::parse($item->end_date);
          if($_SERVER['REMOTE_ADDR'] == '122.176.82.110'){
            // dd($now->diffInDays($end, false));
          }
          if($now->diffInDays($end, false) >= 0){
            $item->status = 'Active';
          }else{
            $item->status = 'Expired';
          }
      }

      return view('admin.course.list', compact('items', 'auth_user', 'paginate_data'));
    }


    public function getAdd()
    {
        $auth_user = \Auth::user();
        return view('admin.course.add', compact('auth_user'));
    }

    public function postAdd(CourseRequest $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $data = $request->only('fee', 'duration','duration_m', 'duration_breakage', 'type', 'start_date', 'end_date','course_type','language');

        $start = explode('/',$data['start_date']);
        $start = Carbon::create($start[2],$start[1],$start[0]);

        $end = explode('/',$data['end_date']);
        $end = Carbon::create($end[2],$end[1],$end[0]);

        $data['start_date'] = $start->toDateString();
        $data['end_date'] = $end->toDateString();

        $data['vendor_id'] = $auth_user->id;

        if(isset($request['path']) && $request['path'] != "") {
          $file = $request['path'];
          if (preg_match('/^data:image\/(\w+);base64,/', $file, $type)) {
              $file = substr($file, strpos($file, ',') + 1);
              $type = strtolower($type[1]); // jpg, png, gif

              if (!in_array($type, [ 'jpg', 'jpeg', 'gif', 'png' ])) {
                  throw new \Exception('invalid image type');
              }

              $decode = base64_decode($file);

              if ($decode === false) {
                  throw new \Exception('base64_decode failed');
              }
              $folder = "files/course";

              $path = savePhoto($file, $folder, $type);
              $data['image'] = $path;
          } else {
              throw new \Exception('did not match data URI with image data');
          }
        }
        // if($request->hasFile('image')){
        //   $file = $request->file('image');
        //   $folder = "files/course";
        //
        //   $path = uploadPhoto($file, $folder);
        //   $data['image'] = $path;
        // }

        if($data['type'] == 'free'){
            $data['fee'] = 0;
        }
        if($data['duration_breakage'] != 1){
            $data['duration_breakage'] = '0';
        }
        $data['vendor_id'] = $auth_user->id;
        $module = Course::create($data);

        if($request->input('title') != ''){
            $data_en['title'] = $request->input('title');
            $data_en['about'] = $request->input('about');
            $data_en['description'] = $request->input('description');
            $data_en['help_text'] = $request->input('help_text');
            $data_en['duration_label'] = $request->input('duration_label');
            $data_en['duration_value'] = $request->input('duration_value');
            $data_en['duration_m_value'] = $request->input('duration_m_value');
            $data_en['venue'] = $request->input('venue');
            $data_en['audience'] = $request->input('audience');
            $data_en['language'] = 'english';
            $arr = [];
            foreach($data_en['duration_label'] as $k => $label){
                $arr[$k]['label'] = $label;
                $arr[$k]['value'] = @$data_en['duration_value'][$k];
                $arr[$k]['minute'] = @$data_en['duration_m_value'][$k];
            }
            $data_en['duration_label'] = json_encode($arr, JSON_UNESCAPED_UNICODE);
            $data_en['course_id'] = $module->id;
            CourseLanguage::create($data_en);
        }

        if($request->input('title_mn') != ''){
            $data_mn['title'] = $request->input('title_mn');
            $data_mn['about'] = $request->input('about_mn');
            $data_mn['description'] = $request->input('description_mn');
            $data_mn['help_text'] = $request->input('help_text_mn');
            $data_mn['venue'] = $request->input('venue_mn');
            $data_mn['audience'] = $request->input('audience_mn');
            $data_mn['language'] = 'mandarin';
            $arr_mn = [];
            $data_mn['duration_label'] = $request->input('duration_label_mn');
            $data_mn['duration_value_mn'] = $request->input('duration_value_mn');
            $data_mn['duration_m_value_mn'] = $request->input('duration_m_value_mn');
            foreach($data_mn['duration_label'] as $k => $label){
                $arr_mn[$k]['label'] = $label;
                $arr_mn[$k]['value'] = @$data_mn['duration_value_mn'][$k];
                $arr_mn[$k]['minute'] = @$data_mn['duration_m_value_mn'][$k];
            }
            $data_mn['duration_label'] = json_encode($arr_mn, JSON_UNESCAPED_UNICODE);
            $data_mn['course_id'] = $module->id;
            CourseLanguage::create($data_mn);
        }

        if($request->input('title_bn') != ''){
            $data_bn['title'] = $request->input('title_bn');
            $data_bn['about'] = $request->input('about_bn');
            $data_bn['description'] = $request->input('description_bn');
            $data_bn['help_text'] = $request->input('help_text_bn');
            $data_bn['venue'] = $request->input('venue_bn');
            $data_bn['audience'] = $request->input('audience_bn');
            $data_bn['language'] = 'bengali';
            $arr_bn = [];
            $data_bn['duration_label'] = $request->input('duration_label_bn');
            $data_bn['duration_m_value_bn'] = $request->input('duration_m_value_bn');
            foreach($data_bn['duration_label'] as $k => $label){
                $arr_bn[$k]['label'] = $label;
                $arr_bn[$k]['value'] = @$data_bn['duration_value_bn'][$k];
                $arr_bn[$k]['minute'] = @$data_bn['duration_m_value_bn'][$k];
            }
            $data_bn['duration_label'] = json_encode($arr_bn, JSON_UNESCAPED_UNICODE);
            $data_bn['course_id'] = $module->id;
            CourseLanguage::create($data_bn);
        }

        if($request->input('title_ta') != ''){
            $data_ta['title'] = $request->input('title_ta');
            $data_ta['about'] = $request->input('about_ta');
            $data_ta['description'] = $request->input('description_ta');
            $data_ta['help_text'] = $request->input('help_text_ta');
            $data_ta['venue'] = $request->input('venue_ta');
            $data_ta['audience'] = $request->input('audience_ta');
            $data_ta['language'] = 'tamil';
            $arr_ta = [];
            $data_ta['duration_label'] = $request->input('duration_label_ta');
            $data_ta['duration_value_ta'] = $request->input('duration_value_ta');
            $data_ta['duration_m_value_ta'] = $request->input('duration_m_value_ta');
            foreach($data_ta['duration_label'] as $k => $label){
                $arr_ta[$k]['label'] = $label;
                $arr_ta[$k]['value'] = @$data_ta['duration_value_ta'][$k];
                $arr_ta[$k]['minute'] = @$data_ta['duration_m_value_ta'][$k];
            }
            $data_ta['duration_label'] = json_encode($arr_ta, JSON_UNESCAPED_UNICODE);
            $data_ta['course_id'] = $module->id;
            CourseLanguage::create($data_ta);
        }

        if($request->input('title_th') != ''){
            $data_th['title'] = $request->input('title_th');
            $data_th['about'] = $request->input('about_th');
            $data_th['description'] = $request->input('description_th');
            $data_th['help_text'] = $request->input('help_text_th');
            $data_th['venue'] = $request->input('venue_th');
            $data_th['audience'] = $request->input('audience_th');
            $data_th['language'] = 'thai';
            $arr_th = [];
            $data_th['duration_label'] = $request->input('duration_label_th');
            $data_th['duration_value_th'] = $request->input('duration_value_th');
            $data_th['duration_m_value_th'] = $request->input('duration_m_value_th');
            foreach($data_th['duration_label'] as $k => $label){
                $arr_th[$k]['label'] = $label;
                $arr_th[$k]['value'] = @$data_th['duration_value_th'][$k];
                $arr_th[$k]['minute'] = @$data_th['duration_m_value_th'][$k];
            }
            $data_th['duration_label'] = json_encode($arr_th, JSON_UNESCAPED_UNICODE);
            $data_th['course_id'] = $module->id;
            CourseLanguage::create($data_th);
        }

        Activity::log('Added new '.@$data['course_type'].' #'.@$module->id. ' by '.$auth_user->name);

        return redirect()->route('admin.course.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Course added successfully.',
        ]);

    }
    public function getEdit($id, Request $request)
    {
		$auth_user = \Auth::user();
        $id = decrypt($id);
		$item = Course::findOrFail($id);

        $label = $item->course_en?json_decode($item->course_en->duration_label):[];
        $label_mn = $item->course_mn?json_decode($item->course_mn->duration_label):[];
        $label_bn = $item->course_bn?json_decode($item->course_bn->duration_label):[];
        $label_ta = $item->course_ta?json_decode($item->course_ta->duration_label):[];
        $label_th = $item->course_th?json_decode($item->course_th->duration_label):[];

        $item->start_date = date('d/m/Y', strtotime($item->start_date));
        $item->end_date = date('d/m/Y', strtotime($item->end_date));

        return view('admin.course.edit', compact('item', 'label', 'label_mn', 'label_bn', 'label_ta', 'label_th'));
    }

    public function postEdit($id, EditCourseRequest $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $id = decrypt($id);
        $module = Course::findOrFail($id);

        $data = $request->only('fee', 'duration', 'duration_m', 'duration_breakage' ,'type', 'start_date', 'end_date','course_type');

        $start = explode('/',$data['start_date']);
        $start = Carbon::create($start[2],$start[1],$start[0]);

        $end = explode('/',$data['end_date']);
        $end = Carbon::create($end[2],$end[1],$end[0]);

        $data['start_date'] = $start->toDateString();
        $data['end_date'] = $end->toDateString();

        // $data['vendor_id'] = $auth_user->id;

        if(isset($request['path']) && $request['path'] != "") {
          $file = $request['path'];
          if (preg_match('/^data:image\/(\w+);base64,/', $file, $type)) {
              $file = substr($file, strpos($file, ',') + 1);
              $type = strtolower($type[1]); // jpg, png, gif

              if (!in_array($type, [ 'jpg', 'jpeg', 'gif', 'png' ])) {
                  throw new \Exception('invalid image type');
              }

              $decode = base64_decode($file);

              if ($decode === false) {
                  throw new \Exception('base64_decode failed');
              }
              $folder = "files/course";

              $path = savePhoto($file, $folder, $type);
              $data['image'] = $path;
          } else {
              throw new \Exception('did not match data URI with image data');
          }
        }
        // if($request->hasFile('image')){
        //   $file = $request->file('image');
        //   $folder = "files/course";
        //
        //   $path = uploadPhoto($file, $folder);
        //   $data['image'] = $path;
        // }

        if($data['type'] == 'free'){
            $data['fee'] = 0;
        }
        if($data['duration_breakage'] != 1){
            $data['duration_breakage'] = '0';
        }

        $module->update($data);

        $data_en['language'] = 'english';
        $data_mn['language'] = 'mandarin';
        $data_ta['language'] = 'tamil';
        $data_bn['language'] = 'bengali';
        $data_th['language'] = 'thai';

        $data_en['course_id'] = $data_th['course_id'] = $data_mn['course_id'] = $data_bn['course_id'] = $data_ta['course_id'] = $id;

        // english start
        $exist = CourseLanguage::where($data_en)->first();
        if($request->input('title') != '' || ($exist && $exist->exists)){

            $arr_en = [];
            $data_en['duration_label'] = $request->input('duration_label');
            $data_en['duration_value'] = $request->input('duration_value');
            $data_en['duration_m_value'] = $request->input('duration_m_value');
            foreach($data_en['duration_label'] as $k => $label){
                $arr_en[$k]['label'] = $label;
                $arr_en[$k]['value'] = $data_en['duration_value'][$k];
                $arr_en[$k]['minute'] = $data_en['duration_m_value'][$k];
            }
            $data_en['title'] = $request->input('title');
            $data_en['about'] = $request->input('about');
            $data_en['description'] = $request->input('description');
            $data_en['help_text'] = $request->input('help_text');
            $data_en['duration_label'] = json_encode($arr_en);
            $data_en['venue'] = $request->input('venue');
            $data_en['audience'] = $request->input('audience');

            if($exist && $exist->exists){
                $exist->update($data_en);
            }else{
                CourseLanguage::create($data_en);
            }
        }
        // english end

        // mandarin start
        $exist = CourseLanguage::where($data_mn)->first();
        if($request->input('title_mn') != '' || ($exist && $exist->exists)){

            $arr_mn = [];
            $data_mn['duration_label'] = $request->input('duration_label_mn');
            $data_mn['duration_value_mn'] = $request->input('duration_value_mn');
            $data_mn['duration_m_value_mn'] = $request->input('duration_m_value_mn');

            foreach($data_mn['duration_label'] as $k => $label){
                $arr_mn[$k]['label'] = $label;
                $arr_mn[$k]['value'] = @$data_mn['duration_value_mn'][$k];
                $arr_mn[$k]['minute'] = @$data_mn['duration_m_value_mn'][$k];
            }
            $data_mn['title'] = $request->input('title_mn');
            $data_mn['about'] = $request->input('about_mn');
            $data_mn['description'] = $request->input('description_mn');
            $data_mn['help_text'] = $request->input('help_text_mn');
            $data_mn['duration_label'] = json_encode($arr_mn);
            $data_mn['venue'] = $request->input('venue_mn');
            $data_mn['audience'] = $request->input('audience_mn');

            if($exist && $exist->exists){
                $exist->update($data_mn);
            }else{
                CourseLanguage::create($data_mn);
            }
        }
        // mandarin end

        // tamil start
        $exist = CourseLanguage::where($data_ta)->first();
        if($request->input('title_ta') != '' || ($exist && $exist->exists)){

            $arr_ta = [];
            $data_ta['duration_label'] = $request->input('duration_label_ta');
            $data_ta['duration_value_ta'] = $request->input('duration_value_ta');
            $data_ta['duration_m_value_ta'] = $request->input('duration_m_value_ta');
            foreach($data_ta['duration_label'] as $k => $label){
                $arr_ta[$k]['label'] = $label;
                $arr_ta[$k]['value'] = @$data_ta['duration_value_ta'][$k];
                $arr_ta[$k]['minute'] = @$data_ta['duration_m_value_ta'][$k];
            }
            $data_ta['title'] = $request->input('title_ta');
            $data_ta['about'] = $request->input('about_ta');
            $data_ta['description'] = $request->input('description_ta');
            $data_ta['help_text'] = $request->input('help_text_ta');
            $data_ta['duration_label'] = json_encode($arr_ta);
            $data_ta['venue'] = $request->input('venue_ta');
            $data_ta['audience'] = $request->input('audience_ta');

            if($exist && $exist->exists){
                $exist->update($data_ta);
            }else{
                CourseLanguage::create($data_ta);
            }
        }
        // tamil end

        // bengali start
        $exist = CourseLanguage::where($data_bn)->first();
        if($request->input('title_bn') != '' || ($exist && $exist->exists)){

            $arr_bn = [];
            $data_bn['duration_label'] = $request->input('duration_label_bn');
            $data_bn['duration_value_bn'] = $request->input('duration_value_bn');
            $data_bn['duration_m_value_bn'] = $request->input('duration_m_value_bn');
            foreach($data_bn['duration_label'] as $k => $label){
                $arr_bn[$k]['label'] = $label;
                $arr_bn[$k]['value'] = @$data_bn['duration_value_bn'][$k];
                $arr_bn[$k]['minute'] = @$data_bn['duration_m_value_bn'][$k];
            }

            $data_bn['title'] = $request->input('title_bn');
            $data_bn['about'] = $request->input('about_bn');
            $data_bn['description'] = $request->input('description_bn');
            $data_bn['help_text'] = $request->input('help_text_bn');
            $data_bn['duration_label'] = json_encode($arr_bn);
            $data_bn['venue'] = $request->input('venue_bn');
            $data_bn['audience'] = $request->input('audience_bn');

            if($exist && $exist->exists){
                $exist->update($data_bn);
            }else{
                CourseLanguage::create($data_bn);
            }
        }
        // bengali end

        // thai start
        $exist = CourseLanguage::where($data_th)->first();
        if($request->input('title_th') != '' || ($exist && $exist->exists)){

            $arr_th = [];
            $data_th['duration_label'] = $request->input('duration_label_th');
            $data_th['duration_value_th'] = $request->input('duration_value_th');
            $data_th['duration_m_value_th'] = $request->input('duration_m_value_th');
            foreach($data_th['duration_label'] as $k => $label){
                $arr_th[$k]['label'] = $label;
                $arr_th[$k]['value'] = @$data_th['duration_value_th'][$k];
                $arr_th[$k]['minute'] = @$data_th['duration_m_value_th'][$k];
            }
            $data_th['title'] = $request->input('title_th');
            $data_th['about'] = $request->input('about_th');
            $data_th['description'] = $request->input('description_th');
            $data_th['help_text'] = $request->input('help_text_th');
            $data_th['duration_label'] = json_encode($arr_th);
            $data_th['venue'] = $request->input('venue_th');
            $data_th['audience'] = $request->input('audience_th');

            if($exist && $exist->exists){
                $exist->update($data_th);
            }else{
                CourseLanguage::create($data_th);
            }
        }
        // thai end

        Activity::log('Updated '.@$data['course_type'].'#'.$id. ' by '.$auth_user->name);

        return redirect()->route('admin.course.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Course details updated successfully.',
        ]);

    }

    public function getDelete($id)
    {
        $course = Course::find($id);
        Course::destroy($id);
        $auth_user = Auth::user();
        Activity::log('Deleted '.@$course->course_type.'#'.$id. ' by '.$auth_user->name);

        return redirect()->route('admin.course.list')->with([
            'flash_level'   => 'danger',
            'flash_message' => 'CourseDeleted',
        ]);
    }

    public function postDelete($id)
    {
        Course::delete($id);
        return redirect()->route('admin.course.list')->with([
          'flash_level'   => 'danger',
          'flash_message' => 'Course Deleted',
        ]);

    }

    public function getCList(Request $request)
    {
      $auth_user = Auth::user();

      $items = Content::query();
      // $courses = Course::pluck('title', 'id');

      // if ($id = $request->input('id')) {
      //     $items->where('id', $id);
      // }
      if ($title = $request->input('title')) {
          $items->whereHas('lang_content', function($q) use($title){
                $q->where('title', 'like', "%{$title}%");
          });
      }

      $course_id = $request->input('course_id');
      $sel_val = '';
      if ($course_id != '0' && $course_id != '') {
          try{
              $course_id = decrypt($course_id);
          }catch(DecryptException $e){}
          $items->where('course_id', $course_id);
          $selected = CourseLanguage::where('course_id', $course_id)->distinct('course_id')->first();
          if($selected){
              $sel_val = $selected->title;
          }
      }

      if ($username = $request->input('username')) {
          $items->where(function ($q) use ($username) {
              $q->where('name', 'like', "%{$username}%")
                  ->orWhere('email', 'like', "%{$username}%");
          });
      }


      $limit = 10;
      $items = $items->sortable()->paginate($limit);
      $paginate_data = $request->except('page');
      $cour = CourseLanguage::distinct('course_id')->get()->pluck('title', 'course_id');
      foreach($cour as $key => $cou){
          $courses[encrypt($key)] = $cou;
      }

      return view('admin.content.list', compact('items', 'auth_user', 'courses', 'course_id','paginate_data', 'courses', 'sel_val'));
    }


    public function getCAdd()
    {
        $auth_user = \Auth::user();
        $course_id = \Input::get('course_id');
        $course = '';
        if($course_id){
            $course = CourseLanguage::where('course_id', $course_id)->first();
        }
        $courses = CourseLanguage::where('language', 'english')->get()->pluck('title', 'course_id');
        $courses_bn = CourseLanguage::where('language', 'bengali')->get()->pluck('title', 'course_id');
        $courses_mn = CourseLanguage::where('language', 'mandarin')->get()->pluck('title', 'course_id');
        $courses_ta = CourseLanguage::where('language', 'tamil')->get()->pluck('title', 'course_id');
        $courses_th = CourseLanguage::where('language', 'thai')->get()->pluck('title', 'course_id');

        return view('admin.content.add', compact('auth_user', 'courses', 'courses_bn', 'courses_mn', 'courses_ta', 'courses_th', 'course'));
    }

    public function postCAdd(AddContentRequest $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();

        $data = $request->only('title', 'course_id', 'type', 'order', 'language');
        $data['language'] = 'english';

        if($data['language'] == 'bengali'){
            $data['course_id'] = $request->course_id_bn;
        }
        if($data['language'] == 'mandarin'){
            $data['course_id'] = $request->course_id_mn;
        }
        if($data['language'] == 'tamil'){
            $data['course_id'] = $request->course_id_ta;
        }
        if($data['language'] == 'thai'){
            $data['course_id'] = $request->course_id_th;
        }

        $module = Content::create($data);
        $files = $request->only('file_type', 'path');

        foreach($files['file_type'] as $key => $file){
            $dd['content_id'] = $module->id;
            $dd['language'] = 'english';
            $dd['file_type'] = $file;
            if($file == 'url'){
                $dd['path'] = $files['path'][$key-1];

                $video_id = getYoutubeIdFromUrl($dd['path']);
                $dd['video_id'] = $video_id;

                $dd['type'] = 'youtube';
            }
            else{
                if($request->hasFile('path.'.($key-1))){
                  $up = $request->file('path.'.($key-1));
                  $folder = "files/course/content";
                  $mimeType = $up->extension();

                  $path = uploadPhoto($up, $folder);
                  $dd['path'] = $path;
                  $type = 'video';
                  if(in_array($mimeType, array('pdf'))) {
                      $type = 'pdf';
                  }
                  // elseif(in_array($up->mimeType, array('application/pdf'))){
                  //
                  // }
                  $dd['type'] = $type;
                }
            }
            ContentFile::create($dd);
        }

        $data_mnn = $request->only('title_mn');
        $data_taa = $request->only('title_ta');
        $data_bnn = $request->only('title_bn');
        $data_thh = $request->only('title_th');

        $data_mn['title'] = $data_mnn['title_mn'];
        $data_ta['title'] = $data_taa['title_ta'];
        $data_bn['title'] = $data_bnn['title_bn'];
        $data_th['title'] = $data_thh['title_th'];

        $data_th['content_id'] = $data_mn['content_id'] = $data_ta['content_id'] = $data_bn['content_id'] = $module->id;
        $data_mn['language'] = 'mandarin';
        $data_bn['language'] = 'bengali';
        $data_ta['language'] = 'tamil';
        $data_th['language'] = 'thai';

        ContentLang::create($data_mn);
        ContentLang::create($data_ta);
        ContentLang::create($data_bn);
        ContentLang::create($data_th);

        Activity::log('Added content #'.$module->id. ' by '.$auth_user->name);

        return redirect()->route('admin.content.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Content added successfully.',
        ]);

    }
    public function getCEdit($id, Request $request)
    {
            $id = decrypt($id);
    		$auth_user = \Auth::user();
            $courses = Course::pluck('title', 'id');
    		$item = Content::findOrFail($id);

            $empty_lang = array(
                'title' => '',
                'path' => '',
            );

            $lang['english'] = array(
                'title' => $item->title,
                'path' => $item->path,
            );

            if($item->content_mn){
                $lang['mandarin'] = $item->content_mn->toArray();
            }else{
                $lang['mandarin'] = $empty_lang;
            }

            if($item->content_bn){
                $lang['bengali'] = $item->content_bn->toArray();
            }else{
                $lang['bengali'] = $empty_lang;
            }

            if($item->content_ta){
                $lang['tamil'] = $item->content_ta->toArray();
            }else{
                $lang['tamil'] = $empty_lang;
            }

            $courses = CourseLanguage::where('language', 'english')->get()->pluck('title', 'course_id');
            $courses_bn = CourseLanguage::where('language', 'bengali')->get()->pluck('title', 'course_id');
            $courses_mn = CourseLanguage::where('language', 'mandarin')->get()->pluck('title', 'course_id');
            $courses_ta = CourseLanguage::where('language', 'tamil')->get()->pluck('title', 'course_id');
            $courses_th = CourseLanguage::where('language', 'thai')->get()->pluck('title', 'course_id');

        // $profile = $user->profile;

        return view('admin.content.edit', compact('item', 'courses','courses_bn','courses_mn','courses_ta','courses_th', 'lang'));
    }

    public function postCEdit($id, EditContentRequest $request)
    {
        $id = decrypt($id);
        /** @var User $item */
        $auth_user = \Auth::user();

        $module = Content::findOrFail($id);

        $data_lang = $request->only('title', 'language');
        $data = $request->only('course_id', 'type', 'order');

        if($data_lang['language'] == 'english'){
            $data = array_merge($data, $data_lang);
        }
        if($data_lang['language'] == 'bengali'){
            $data['course_id'] = $request->course_id_bn;
        }
        if($data_lang['language'] == 'mandarin'){
            $data['course_id'] = $request->course_id_mn;
        }
        if($data_lang['language'] == 'tamil'){
            $data['course_id'] = $request->course_id_ta;
        }
        if($data_lang['language'] == 'thai'){
            $data['course_id'] = $request->course_id_th;
        }

        $module->update($data);

        Activity::log('Updated content #'.$id. ' by '.$auth_user->name);

        $files = $request->only('file_type', 'path', 'id');

        $i = 0;
        foreach($files['file_type'] as $file){

            $dd['content_id'] = $module->id;
            $dd['language'] = 'english';
            $dd['file_type'] = $file;

            if($file == 'url'){
                $dd['path'] = $files['path'][$i];

                $video_id = getYoutubeIdFromUrl($dd['path']);
                $dd['video_id'] = $video_id;

                $dd['type'] = 'youtube';
            }
            else{
                if($request->hasFile('path.'.($i))){
                  $up = $request->file('path.'.($i));
                  $folder = "files/course/content";
                  $mimeType = $up->extension();

                  $path = uploadPhoto($up, $folder);
                  $dd['path'] = $path;
                  $type = 'video';
                  if(in_array($mimeType, array('pdf'))) {
                      $type = 'pdf';
                  }
                  $dd['type'] = $type;
              }else{
                  unset($dd['path']);
                  unset($dd['type']);
              }
            }
            $content = [];
            if(isset($files['id'][$i+1])){
                $content = ContentFile::find($files['id'][$i+1]);
            }
            // echo "<pre>";print_r($dd);
            if(!empty($content)){
                $content->update($dd);
            }else{
                $content = ContentFile::create($dd);
            }
            $i++;
        }
        // die();

        $data_mnn = $request->only('title_mn');
        $data_taa = $request->only('title_ta');
        $data_bnn = $request->only('title_bn');
        $data_thh = $request->only('title_th');

        $data_mn['title'] = $data_mnn['title_mn'];
        $data_ta['title'] = $data_taa['title_ta'];
        $data_bn['title'] = $data_bnn['title_bn'];
        $data_th['title'] = $data_thh['title_th'];

        $data_mn['language'] = 'mandarin';
        $data_mn['content_id'] = $id;
        $exist = ContentLang::where($data_mn)->first();
        if($exist){
            $exist->update($data_mn);
        }else{
            ContentLang::create($data_mn);
        }

        $data_ta['language'] = 'tamil';
        $data_ta['content_id'] = $id;
        $exist = ContentLang::where($data_ta)->first();
        if($exist){
            $exist->update($data_ta);
        }else{
            ContentLang::create($data_ta);
        }

        $data_bn['language'] = 'bengali';
        $data_bn['content_id'] = $id;
        $exist = ContentLang::where($data_bn)->first();
        if($exist){
            $exist->update($data_bn);
        }else{
            ContentLang::create($data_bn);
        }

        $data_th['language'] = 'thai';
        $data_th['content_id'] = $id;
        $exist = ContentLang::where($data_th)->first();
        if($exist){
            $exist->update($data_th);
        }else{
            ContentLang::create($data_th);
        }

        return redirect()->route('admin.content.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Content details updated successfully.',
        ]);

    }

    public function removeFile(Request $request)
    {
        $id = $request->input('id');
        ContentFile::destroy($id);

        return response()->json([
            'status'   => 'success',
            'message' => 'Deleted',
        ]);
    }

    public function getCDelete($id)
    {
        Content::destroy($id);
        $auth_user = Auth::user();
        Activity::log('Deleted Content #'.$id. ' by '.$auth_user->name);

        return redirect()->route('admin.content.list')->with([
            'flash_level'   => 'danger',
            'flash_message' => 'content Deleted',
        ]);
    }

    public function postCDelete($id)
    {
        Content::delete($id);
        return redirect()->route('admin.content.list')->with([
          'flash_level'   => 'danger',
          'flash_message' => 'Course Deleted',
        ]);

    }

    public function getJoinees(Request $request)
    {
      $auth_user = Auth::user();

      $items = CourseJoined::query();

      if ($course_id = $request->input('course_id')) {
          $course_id = decrypt($course_id);
          $items->where('course_id', $course_id);
      }

      // if ($username = $request->input('username')) {
      //     $items->where(function ($q) use ($username) {
      //         $q->where('name', 'like', "%{$username}%")
      //             ->orWhere('email', 'like', "%{$username}%");
      //     });
      // }

      $limit = 10;
      $items = $items->sortable()->paginate($limit);
      $paginate_data = $request->except('page');

      return view('admin.course.joined', compact('items', 'auth_user', 'course_id','paginate_data'));
    }
}
