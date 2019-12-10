<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Validator;
use JWTAuth, DB;
use App\Models\JTC\Center;
use App\Models\JTC\CenterLang;
use App\Models\JTC\Category;
use App\Models\JTC\CategoryLang;
use App\Models\JTC\Event;
use App\Models\JTC\EventLang;
use App\Models\JTC\Detail;
use App\Models\JTC\DetailLang;
use App\Models\JTC\Comment;
use App\Models\JTC\Likes;
use App\User;
use Carbon\Carbon, Activity;

class JtcController extends Controller
{

    // protected function list_validator(array $data, $user)
    // {
    //   return Validator::make($data, [
    //         'type'       => 'required',
    //     ]);
    // }

    public function list_centers(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));

        try{
            $type = (isset($request->type) && $request->type != '')? $request->type:'jtc';
            $language = (isset($request->language) && $request->language != '')? $request->language:'english';
            $limit = (isset($request->limit) && $request->limit > 0)?$request->limit:10;
            $page_no = (isset($request->page_no) && $request->page_no > 1)?$request->page_no:1;
            $offset = ($page_no-1)*$limit;

            if($language == 'chinese'){
              $language = "mandarin";
            }
            $data = Center::where('type', $type)->whereHas('lang_content', function($q) use($language) {
                $q->where('language', $language);
            })->where('active', '1');
            $total = $data->count();

            $data = $data->orderBy('created_at', 'asc');
            $data = $data->offset($offset)->limit($limit)->get();
            foreach($data as $res){
                $ser = CenterLang::where('center_id', $res['id'])->where('language', $language)->first();
                if($ser){
                    $res['title'] = $ser->title;
                    // $res['content'] = $ser->content;
                }else{
                    $res['title'] = '';
                }
                if($res['image'] != ''){
                    $res['image'] = url($res['image']);
                }else{
                    $res['image'] = url('images/placeholder.png');
                }
            }
            
            $d = $data->sortBy('title');
            $data = [];
            foreach($d as $dd){
              $data[] = $dd->toArray();
            }
            return response()->json(['status' => 'success', 'data' => $data, 'total' => $total, 'message' => 'SUCCESS'], 200);

        }catch(Exception $e){
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function list_category(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));

        try{
            $center_id = (isset($request->center_id) && $request->center_id != '')?$request->center_id:'';
            $language = (isset($request->language) && $request->language != '')? $request->language:'english';
            $limit = (isset($request->limit) && $request->limit > 0)?$request->limit:10;
            $page_no = (isset($request->page_no) && $request->page_no > 1)?$request->page_no:1;
            $offset = ($page_no-1)*$limit;
            if($language == 'chinese'){
                $language = "mandarin";
            }
            $data = Category::whereHas('lang_content', function($q) use($language) {
                $q->where('language', $language);
            })->where('center_id', $center_id)->where('active', '1');
            $total = $data->count();

            $data = $data->orderBy('created_at', 'desc');
            $data = $data->offset($offset)->limit($limit)->get();
            foreach($data as $res){
                $ser = CategoryLang::where('category_id', $res['id'])->where('language', $language)->first();
                if($ser){
                    $res['title'] = $ser->title;
                }else{
                    $res['title'] = '';
                }

                if($res['image'] != ''){
                    $res['image'] = url($res['image']);
                }else{
                    $res['image'] = url('images/placeholder.png');
                }
            }
            
            $d = $data->sortBy('title');
            $data = [];
            foreach($d as $dd){
              $data[] = $dd->toArray();
            }
            if($total < 1){
                return response()->json(['status' => 'success', 'data' => $data, 'total' => $total, 'message' => 'No data available for this category'], 200);
            }else{
                return response()->json(['status' => 'success', 'data' => $data, 'total' => $total, 'message' => 'SUCCESS'], 200);
            }

        }catch(Exception $e){
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function list_events(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));

        try{
            $category_id = (isset($request->category_id) && $request->category_id != '')?$request->category_id:'';
            $language = (isset($request->language) && $request->language != '')? $request->language:'english';
            $limit = (isset($request->limit) && $request->limit > 0)?$request->limit:10;
            $page_no = (isset($request->page_no) && $request->page_no > 1)?$request->page_no:1;
            $offset = ($page_no-1)*$limit;

            if($language == 'chinese'){
              $language = "mandarin";
            }
            $data = Event::whereHas('lang_content', function($q) use($language) {
                $q->where('language', $language);
            })->where('active', '1')->where('category_id', $category_id);
            $total = $data->count();

            $data = $data->orderBy('created_at', 'asc');
            $data = $data->offset($offset)->limit($limit)->get();
            foreach($data as $res){
                $ser = EventLang::where('event_id', $res['id'])->where('language', $language)->first();
                if($ser){
                    $res['title'] = $ser->title;
                    // $res['content'] = $ser->content;
                }else{
                    $res['title'] = '';
                }
                if($res['image'] != ''){
                    $res['image'] = url($res['image']);
                }else{
                    $res['image'] = url('images/placeholder.png');
                }
            }
            
            $d = $data->sortBy('title');
            $data = [];
            foreach($d as $dd){
              $data[] = $dd->toArray();
            }
            return response()->json(['status' => 'success', 'data' => $data, 'total' => $total, 'message' => 'SUCCESS'], 200);

        }catch(Exception $e){
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    protected function view_validator(array $data, $user)
    {
      return Validator::make($data, [
            'event_id'       => 'required',
        ]);
    }

    public function detail(Request $request)
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
          $service_id = $request->input('event_id');

          $language = $request->language;
          if($language == 'bengali'){
              $label = 'bn';
          }elseif($language == 'chinese'){
              $language = 'mandarin';
              $label = 'mn';
          }elseif($language == 'tamil'){
              $label = 'ta';
          }elseif($language == 'thai'){
              $label = 'th';
          }else{
              $label = '';
          }

          $service = Detail::where('event_id', $service_id)->where('publish', '1')->withCount(['likes'])->with(['comments'])->first();

          if($service){
              $ser = DetailLang::where('topic_id', $service['id'])->where('language', $language)->first();
              if($ser){
                  $service['title'] = $ser->title;
                  $service['content'] = $ser->content;
                  $service['author'] = $ser->author == ''?'Unknown':$ser->author;
              }else{
                  $ser = DetailLang::where('topic_id', $service['id'])->where('language', 'english')->first();
                  if($ser){
                      $service['title'] = $ser->title;
                      $service['content'] = $ser->content;
                      $service['author'] = $ser->author == ''?'Unknown':$ser->author;
                  }else{
                      if($label != ''){
                          if($service['title_'.$label] != ''){
                              $service['title'] = $service['title_'.$label];
                          }
                          if($service['content_'.$label] != ''){
                              $service['content'] = $service['content_'.$label];
                          }
                          if($service['author_'.$label] != ''){
                              $service['author'] = $service['author_'.$label];
                          }
                      }
                  }
              }

              $service['content'] = $service['content'];
              foreach($service->comments as $comment){
                  $cuser = User::find($comment->user_id);
                  $comment['user_name'] = @$cuser->name;
                  if(@$cuser['profile']['profile_pic'] == ''){
                      $cprof = url('files/profile/user.jpg');
                  }else{
                      $cprof = url(@$cuser->profile->profile_pic);
                  }
                  $comment['user_image'] = $cprof;
              }
              $author = User::find($service->author_id);
              $service['user_name'] = isset($service->author) && $service->author != '' ?$service->author:'Admin';
              if($service['author_image'] == ''){
                  $prof = url('files/profile/user.jpg');
              }else{
                  $prof = url($service['author_image']);
              }
              $service['user_image'] = $prof;

              $liked = Likes::where('topic_id', $service['id'])->where('user_id', $user->id)->count();
              if($liked == 0){
                $service['is_liked'] = false;
              }else{
                $service['is_liked'] = true;
              }
              if($service->image != ''){
                  $service->image = url($service->image);
              }else{
                  $service->image = url('images/placeholder.png');
              }

            return response()->json(['status' => 'success', 'data' => $service, 'message' => 'SUCCESS'], 200);
          }else{
            return response()->json(['status' => 'success', 'data' => 'Detail does not exists.', 'message' => 'Detail does not exists.'], 200);
          }

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function like_topic(Request $request)
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
          $event_id = $request->event_id;
          $event = Detail::where('event_id', $event_id)->withCount(['likes'])->with(['comments'])->first();
          if($event){
            $data['topic_id'] = $event->id;
            $data['user_id'] = $user->id;

            $service = Likes::where($data)->first();
            if(!$service){
                $service = Likes::create($data);
                Activity::log('Liked JTC Topic #'.$service->id, $user->id);
                return response()->json(['status' => 'success', 'data' => [], 'message' => 'LIKED'], 200);
            }else{
                $service->delete();
                Activity::log('UnLiked JTC Topic #'.$service->id, $user->id);
                return response()->json(['status' => 'success', 'data' => [], 'message' => 'UNLIKED'], 200);
            }
          }else{
            return response()->json(['status' => 'error', 'data' => [], 'message' => 'Event Does not exist'], 200);

          }
        }catch(Exception $e){
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function share_topic(Request $request)
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
            $service_id = $request->event_id;

            $service = Detail::find($service_id);
            if(!empty($service)){
                $service->share = ++$service->share;
                $service->save();
                Activity::log('Shared JTC topic #'.$service->id, $user->id, $user->id);
                return response()->json(['status' => 'success', 'data' => 'Share count increased', 'message' => 'SUCCESS'], 200);
            }else{
                return response()->json(['status' => 'success', 'data' => 'JTC Event does not exist', 'message' => 'ERROR'], 200);
            }

        }catch(Exception $e){
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function addComment(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));

        try{
            $event_id = $request->event_id;
            $event = Detail::where('event_id', $event_id)->withCount(['likes'])->with(['comments'])->first();
            if($event){
              $data['topic_id'] = $event->id;
              $data['comment'] = $request->comment;
              $data['user_id'] = $user->id;

              $service = Comment::create($data);
              if(!empty($service)){
                  Activity::log('Added comment to JTC topic #'.$service->id, $user->id);
                  return response()->json(['status' => 'success', 'data' => $service, 'message' => 'SUCCESS'], 200);
              }else{
                  return response()->json(['status' => 'success', 'data' => 'Insertion error', 'message' => 'ERROR'], 200);
              }
            }else{
              return response()->json(['status' => 'error', 'data' => [], 'message' => 'Event Does not exist'], 200);

            }


        }catch(Exception $e){
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

}
