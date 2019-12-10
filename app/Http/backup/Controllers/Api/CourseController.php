<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Validator;
use JWTAuth, DB;
use App\Models\Course;
use App\Models\CourseLanguage;
use App\Models\ContentComplete;
use App\Models\Content;
use App\Models\ContentFile;

use App\Models\CourseJoined;
use App\Models\Likes;
use App\Models\Favourite;
use App\Models\Comment;
use App\Models\Attachment;
use App\Events\SendBrowserNotification;
use App\User;
use Carbon\Carbon, Activity;

class CourseController extends Controller
{
    public function getList(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        // $message = $user->name." made payment for spuul subscription.";
        // event(new SendBrowserNotification($message));
        try{
            $type = (isset($request->type) && $request->type != "")?$request->type:"course";
            $limit = (isset($request->limit) && $request->limit > 0)?$request->limit:10;
            $page_no = (isset($request->page_no) && $request->page_no > 1)?$request->page_no:1;
            $language = (isset($request->language) && $request->language != '')?$request->language:'english';

            $offset = ($page_no-1) * $limit;
            $joined = CourseJoined::where('user_id', $user->id)->distinct()->pluck('course_id');
            $data = Course::whereHas('lang_content', function($q) use($language) {
                $q->where('language', $language);
            })->whereNotIn('id', $joined)->where('start_date', '<=', Carbon::now())->where('end_date', '>', Carbon::now())->where('course_type', $type)->orderBy('created_at', 'desc');

            $total = $data->count();
            $data = $data->limit($limit)->offset($offset)->get();

            foreach($data as $key => $res){
                $cl = CourseLanguage::where('course_id', $res['id'])->where('language', $language)->first();
                if($cl){
                    $data[$key]['title'] = $cl['title'];
                    // $data[$key]['title'] = $res['description'];
                    // $data[$key]['title'] = $res['about'];
                    // $data[$key]['title'] = $res['help_text'];
                }
                // $res['user_name'] = $res['user']['name'];
                if($res['image'] == ''){
                    $img = url('images/img-elearning.jpg');
                }else{
                    $img = url($res['image']);
                }
                $res['image'] = $img;
                if($type == 'training'){
                    $res['medium'] = 'english';
                    $res['ratio'] = 20;
                }
                // unset($res['user']);
                // unset($res['profile']);
                // $fav = Favourite::where('ref_id', $res['id'])->where('type', 'topic')->count();
                // if($fav == 0){
                //     $res['is_fav'] = false;
                // }else{
                //     $res['is_fav'] = true;
                // }
            }
            return response()->json(['status' => 'success', 'data' => $data, 'total' => $total, 'message' => 'SUCCESS'], 200);

        }catch(Exception $e){
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function getOngoingList(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));

        try{
            $type = (isset($request->type) && $request->type != "")?$request->type:"course";
            $limit = (isset($request->limit) && $request->limit > 0)?$request->limit:10;
            $page_no = (isset($request->page_no) && $request->page_no > 1)?$request->page_no:1;
            $language = (isset($request->language) && $request->language != '')?$request->language:'english';

            $offset = ($page_no-1) * $limit;
            $joined = CourseJoined::where('user_id', $user->id)->pluck('course_id');//->distinct()
            $data = Course::whereHas('lang_content', function($q) use($language) {
                $q->where('language', $language);
            })->whereIn('id', $joined)->where('course_type', $type)
            ->where('start_date', '<=', Carbon::now())
            //->where('end_date', '>', Carbon::now())
            ->orderBy('created_at', 'desc');

            $total = $data->count();
            $data = $data->limit($limit)->offset($offset)->get();

            foreach($data as $key => $res){
                $cl = CourseLanguage::where('course_id', $res['id'])->where('language', $language)->first();
                if($cl){
                    $data[$key]['title'] = $cl['title'];
                    // $data[$key]['title'] = $res['description'];
                    // $data[$key]['title'] = $res['about'];
                    // $data[$key]['title'] = $res['help_text'];
                }
                // $res['user_name'] = $res['user']['name'];
                if($res['image'] == ''){
                    $img = url('images/img-elearning.jpg');
                }else{
                    $img = url($res['image']);
                }
                $res['image'] = $img;
                // unset($res['user']);
                // unset($res['profile']);
                // $fav = Favourite::where('ref_id', $res['id'])->where('type', 'topic')->count();
                // if($fav == 0){
                //     $res['is_fav'] = false;
                // }else{
                //     $res['is_fav'] = true;
                // }
            }
            return response()->json(['status' => 'success', 'data' => $data, 'total' => $total, 'message' => 'SUCCESS'], 200);

        }catch(Exception $e){
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function getUpcomingList(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));

        try{
            $type = (isset($request->type) && $request->type != "")?$request->type:"course";
            $limit = (isset($request->limit) && $request->limit > 0)?$request->limit:10;
            $page_no = (isset($request->page_no) && $request->page_no > 1)?$request->page_no:1;
            $language = (isset($request->language) && $request->language != '')?$request->language:'english';

            $offset = ($page_no-1) * $limit;
            $data = Course::whereHas('lang_content', function($q) use($language) {
                $q->where('language', $language);
            })->where('course_type', $type)->where('start_date', '>', Carbon::now() )->orderBy('created_at', 'desc');

            $total = $data->count();
            $data = $data->limit($limit)->offset($offset)->get();

            foreach($data as $key => $res){
                $cl = CourseLanguage::where('course_id', $res['id'])->where('language', $language)->first();
                if($cl){
                    $data[$key]['title'] = $cl['title'];
                    // $data[$key]['title'] = $res['description'];
                    // $data[$key]['title'] = $res['about'];
                    // $data[$key]['title'] = $res['help_text'];
                }
                // $res['user_name'] = $res['user']['name'];
                if($res['image'] == ''){
                    $img = url('images/img-elearning.jpg');
                }else{
                    $img = url($res['image']);
                }
                $res['image'] = $img;
                // unset($res['user']);
                // unset($res['profile']);
                // $fav = Favourite::where('ref_id', $res['id'])->where('type', 'topic')->count();
                // if($fav == 0){
                //     $res['is_fav'] = false;
                // }else{
                //     $res['is_fav'] = true;
                // }
            }

            return response()->json(['status' => 'success', 'data' => $data, 'total' => $total, 'message' => 'SUCCESS'], 200);

        }catch(Exception $e){
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function like_course(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $validator = $this->view_validator($request->all(), $user);

        if ($validator->fails()) {
              $errors = $validator->errors();
              $message = [];
              foreach($errors->messages() as $key => $error){
                  $message[$key] = $error[0];
              }
              return response()->json(['status' => 'error', 'data' => $message, 'message' => 'VALIDATION_ERROR'], 401);
        }
        try{
            $data['ref_id'] = $request->course_id;
            $data['user_id'] = $user->id;
            $data['type'] = 'course';

            $exist = Likes::where($data)->first();
            if(!$exist){
                $exist = Likes::create($data);
                Activity::log('Liked Course #'.$exist->id, $user->id);
                return response()->json(['status' => 'success', 'data' => [], 'message' => 'LIKED'], 200);
            }else{
                $exist->delete();
                Activity::log('UnLiked Course #'.$exist->id, $user->id);
                return response()->json(['status' => 'success', 'data' => [], 'message' => 'UNLIKED'], 200);
            }

        }catch(Exception $e){
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function fav_course(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $validator = $this->view_validator($request->all(), $user);

        if ($validator->fails()) {
              $errors = $validator->errors();
              $message = [];
              foreach($errors->messages() as $key => $error){
                  $message[$key] = $error[0];
              }
              return response()->json(['status' => 'error', 'data' => $message, 'message' => 'VALIDATION_ERROR'], 401);
        }
        try{
            $data['ref_id'] = $request->course_id;
            $data['user_id'] = $user->id;
            $data['type'] = 'course';

            $exist = Favourite::where($data)->first();
            if(!$exist){
                $exist = Favourite::create($data);
                Activity::log('Favourite Course #'.$exist->id, $user->id);
                return response()->json(['status' => 'success', 'data' => 'Added to favourite list.', 'message' => 'SUCCESS'], 200);
            }else{
                $exist->delete();
                Activity::log('Removed favourite course #'.$exist->id, $user->id);
                return response()->json(['status' => 'success', 'data' => 'Removed from favourite list.', 'message' => 'UNLIKED'], 200);
            }

        }catch(Exception $e){
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function report_course(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $validator = $this->view_validator($request->all(), $user);

        if ($validator->fails()) {
              $errors = $validator->errors();
              $message = [];
              foreach($errors->messages() as $key => $error){
                  $message[$key] = $error[0];
              }
              return response()->json(['status' => 'error', 'data' => $message, 'message' => 'VALIDATION_ERROR'], 401);
        }
        try{
            $course_id = $request->course_id;

            $course = Course::find($course_id);
            if(!empty($course)){
                $course->report = '1';
                $course->save();
                Activity::log('Reported course #'.$course->id, $user->id);
                return response()->json(['status' => 'success', 'data' => 'Course reported', 'message' => 'SUCCESS'], 200);
            }else{
                return response()->json(['status' => 'success', 'data' => 'Course does not exist', 'message' => 'ERROR'], 200);
            }

        }catch(Exception $e){
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function share_course(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $validator = $this->view_validator($request->all(), $user);

        if ($validator->fails()) {
              $errors = $validator->errors();
              $message = [];
              foreach($errors->messages() as $key => $error){
                  $message[$key] = $error[0];
              }
              return response()->json(['status' => 'error', 'data' => $message, 'message' => 'VALIDATION_ERROR'], 401);
        }
        try{
            $course_id = $request->course_id;

            $course = Course::find($course_id);
            if(!empty($course)){
                $course->share = ++$course->share;
                $course->save();
                Activity::log('Shared course #'.$course->id, $user->id);
                return response()->json(['status' => 'success', 'data' => 'Share count increased', 'message' => 'SUCCESS'], 200);
            }else{
                return response()->json(['status' => 'success', 'data' => 'Course does not exist', 'message' => 'ERROR'], 200);
            }

        }catch(Exception $e){
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    protected function view_validator(array $data, $user)
    {
      return Validator::make($data, [
            'course_id'       => 'required',
        ]);
    }

    public function view(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $validator = $this->view_validator($request->all(), $user);

        if ($validator->fails()) {
              $errors = $validator->errors();
              $message = [];
              foreach($errors->messages() as $key => $error){
                  $message[$key] = $error[0];
              }
              return response()->json(['status' => 'error', 'data' => $message, 'message' => 'VALIDATION_ERROR'], 401);
        }

        try{
          $course_id = $request->course_id;
          $course = Course::where('id', $course_id)->with('content')->withCount('likes')->first();
          $language = (isset($request->language) && $request->language != '')?$request->language:'english';
          // $liked = Likes::where('ref_id', $forum_id)->where('type', 'forum')->count();
          // if($liked == 0){
          //     $forum['is_liked'] = false;
          // }else{
          //     $forum['is_liked'] = true;
          // }
          // if($forum['user'] != null){
          //     $forum['user_name'] = $forum['user']['name'];
          //     $forum['user_image'] = isset($forum['user']['profile'])?$forum['user']['profile']['profile_pic']:null;
          //     unset($forum['user']);
          // }
          // foreach($forum->comments as $comment){
          //     $user = User::find($comment->user_id);
          //     $comment['user_name'] = $user->name;
          //     $comment['user_image'] = $user->profile->profile_pic;
          // }
          if($course){
              // $merchant = Merchant::where('user_id', $course->vendor_id)->first();
              // if($merchant){
              //   $course->mid
              // }
              $course_lang = CourseLanguage::where('course_id', $course->id)->where('language', $language)->first();
              if($course_lang){
                  $course->title = $course_lang->title;
                  $course->description = $course_lang->description;
                  $course->about = $course_lang->about;
                  $course->duration_label = $course_lang->duration_label;
                  $course->venue = $course_lang->venue;
                  $course->audience = $course_lang->audience;
                  $course->help_text = $course_lang->help_text;
              }
              if($course->course_type == 'training'){
                  $course->medium = $language;
                  $course->ratio = 20;
              }
              if($course->image == ''){
                  $course->image = url('images/img-elearning.jpg');
              }else{
                  $course->image = url($course->image);
              }
              $text = 'hour';
              if($course->duration > 1)
                $text = 'hours';

              $course->duration .= ' '.$text;

              if($course->duration == 0){
                $course->duration = '';
              }

              $textm = 'minute';
              if($course->duration_m > 1)
                $textm = 'minutes';

              if($course->duration_m){
                  $course->duration .= ' '.$course->duration_m.' '.$textm;
              }
              if($course->duration_breakage){
                  $course->duration_label = json_decode($course->duration_label);
                  if($course->duration_label){
                      $flag = 1;
                      foreach($course->duration_label as $val){
                          $val->label = strtolower($val->label);
                          if($val->label == ''){
                            $flag = 2;
                            continue;
                          }
                          $text = 'hour';
                          if($val->value > 1){
                              $text = 'hours';
                          }
                          if($val->value == 1){
                              $text = '';
                          }
                          if($val->value > 0){
                              $val->value .= ' '.$text;

                              $textm = 'minute';
                              if($val->minute > 1){
                                  $textm = 'minutes';
                              }
                              if($val->minute > 0){
                                  $val->value .= ' '.$val->minute.' '.$textm;
                              }

                          }
                      }
                      if($flag == 2){
                          $course->duration_label = '';
                      }
                  }
              }else{
                  $course->duration_label = '';
              }

             $course_joined = CourseJoined::where('course_id', $course_id)->where('user_id', $user->id)->count();
             $course->joined = false;
             if($course_joined > 0){
                 $course->joined = true;
             }

              $course->path = route('frontend.payment');
              if($course->content){
                  foreach($course->content as $val){
                      $content_files = ContentFile::where('content_id', $val->id)->get();
                      $no = 1;
                      foreach($content_files as $filess){
                          if($filess->file_type == 'upload'){
                             $filess->path =  url($filess->path);
                          }
                          if($filess->type == 'youtube'){
                             $filess->path =  $filess->video_id;
                          }
                          $filess->name = 'Lesson '.$no;
                          $no++;
                          unset($filess->file_type);
                      }
                      $val->files = $content_files;
                      unset($val->path);
                      unset($val->content_type);
                      $course_complete = ContentComplete::where('content_id', $val->id)->where('user_id', $user->id)->count();
                      $val->completed = false;
                      if($course_complete > 0){
                          $val->completed = true;
                      }
                      if($val->image == ''){
                          $val->image = url('images/img-elearning.jpg');
                      }else{
                          $val->image = url($val->image);
                      }
                  }
              }

            return response()->json(['status' => 'success', 'data' => $course, 'message' => 'SUCCESS'], 200);
          }else{
            return response()->json(['status' => 'error', 'data' => 'Does not exists.', 'message' => 'ERROR'], 404);
          }

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function addComment(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));

        try{
            $data['forum_id'] = $request->forum_id;
            $data['comment'] = $request->comment;
            $data['user_id'] = $user->id;

            $comment = Comment::create($data);
            if(!empty($comment)){
                return response()->json(['status' => 'success', 'data' => $comment, 'message' => 'SUCCESS'], 200);
            }else{
                return response()->json(['status' => 'success', 'data' => 'Insertion error', 'message' => 'ERROR'], 200);
            }

        }catch(Exception $e){
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    protected function complete_validator(array $data, $user)
    {
      return Validator::make($data, [
            'content_id'       => 'required',
        ]);
    }

    public function markComplete(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $validator = $this->complete_validator($request->all(), $user);

        if ($validator->fails()) {
              $errors = $validator->errors();
              $message = [];
              foreach($errors->messages() as $key => $error){
                  $message[$key] = $error[0];
              }
              return response()->json(['status' => 'error', 'data' => $message, 'message' => 'VALIDATION_ERROR'], 401);
        }
        try{
            $content_id = $request->content_id;

            $content = Content::find($content_id);
            if(!empty($content)){
                $data['content_id'] = $content_id;
                $data['user_id'] = $user->id;
                $exist = ContentComplete::where($data)->count();
                if($exist){
                    return response()->json(['status' => 'success', 'data' => 'You have already completed this lesson.', 'message' => 'ERROR'], 200);
                }else{
                    ContentComplete::create($data);
                    Activity::log('Completed lesson #'.$content->title. ' from course '.$content->course->title, $user->id);
                }

                return response()->json(['status' => 'success', 'data' => 'Lesson is marked complete', 'message' => 'SUCCESS'], 200);
            }else{
                return response()->json(['status' => 'success', 'data' => 'Lesson does not exist', 'message' => 'ERROR'], 200);
            }

        }catch(Exception $e){
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    protected function join_validator(array $data, $user)
    {
      return Validator::make($data, [
            'course_id'       => 'required',
        ]);
    }

    public function join_course(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $validator = $this->join_validator($request->all(), $user);

        if ($validator->fails()) {
              $errors = $validator->errors();
              $message = [];
              foreach($errors->messages() as $key => $error){
                  $message[$key] = $error[0];
              }
              return response()->json(['status' => 'error', 'data' => $message, 'message' => 'VALIDATION_ERROR'], 401);
        }
        try{
            $course_id = $request->course_id;

            $course = Course::find($course_id);
            if(!empty($course)){
                if($course->type == 'free'){
                    //join
                    $joined = CourseJoined::create([
                        'user_id' => $user->id,
                        'course_id' => $course_id
                    ]);
                    if(!empty($joined)){
                        Activity::log('Joined course #'.$course->id, $user->id);
                        return response()->json(['status' => 'success', 'data' => 'Course joined', 'message' => 'SUCCESS'], 200);
                    }else{
                        Activity::log('Not able to Join course #'.$course->id, $user->id);
                        return response()->json(['status' => 'success', 'data' => 'Something went wrong', 'message' => 'SUCCESS'], 200);
                    }
                }else{
                    // check if user wallet have that much amount if not throw error to add money to wallet and then buy the course
                    // if($user->wallet > $course->fee){
                        // $amount = $user->wallet-$course->fee;
                        // $update = $user->update(['wallet' => $amount]);

                        $joined = CourseJoined::create([
                            'user_id' => $user->id,
                            'course_id' => $course_id
                        ]);
                        if(!empty($joined)){
                            Activity::log('Joined course #'.$course->id, $user->id);
                            return response()->json(['status' => 'success', 'data' => 'Course joined', 'message' => 'SUCCESS'], 200);
                        }else{
                            Activity::log('Not able to Join course #'.$course->id, $user->id);
                            return response()->json(['status' => 'success', 'data' => 'Something went wrong', 'message' => 'SUCCESS'], 200);
                        }

                    // }else{
                    //
                    //     return response()->json(['status' => 'error', 'data' => 'Wallet does not have sufficient amount to join this course.', 'message' => 'ERROR'], 200);
                    // }
                }


            }else{
                return response()->json(['status' => 'error', 'data' => 'Course does not exist', 'message' => 'ERROR'], 200);
            }

        }catch(Exception $e){
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

}
