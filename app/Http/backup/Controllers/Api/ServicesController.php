<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Validator;
use JWTAuth, DB;
use App\Models\MomFeedbackCategory;
use App\Models\MomCategory;
use App\Models\MomCategoryLang;
use App\Models\MomTopic;
use App\Models\MomTopicLang;
use App\Models\Servicess;
use App\Models\ServicesLang;
use App\Models\ServicesLike;
use App\Models\ServicesComment;
use App\Models\Attachment;
use App\User;
use Carbon\Carbon, Activity;

class ServicesController extends Controller
{

    // protected function list_validator(array $data, $user)
    // {
    //   return Validator::make($data, [
    //         'type'       => 'required',
    //     ]);
    // }

    public function list_mom_category(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));

        try{
            $language = (isset($request->language) && $request->language != '')? $request->language:'english';
            $limit = (isset($request->limit) && $request->limit > 0)?$request->limit:10;
            $page_no = (isset($request->page_no) && $request->page_no > 1)?$request->page_no:1;
            $offset = ($page_no-1)*$limit;

                if($language == 'chinese'){
                    $language = "mandarin";
                }
            $data = MomCategory::whereHas('lang_content', function($q) use($language) {
                $q->where('language', $language);
            })->where('active', '1');
            $total = $data->count();

            $data = $data->orderBy('created_at', 'asc');
            $data = $data->offset($offset)->limit($limit)->get();
            foreach($data as $res){
                $ser = MomCategoryLang::where('category_id', $res['id'])->where('language', $language)->first();
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
                unset($res['author']);
                unset($res['author_image']);
                unset($res['profile']);
            }
            return response()->json(['status' => 'success', 'data' => $data, 'total' => $total, 'message' => 'SUCCESS'], 200);

        }catch(Exception $e){
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function list_mom_topic(Request $request)
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
            $data = MomTopic::whereHas('lang_content', function($q) use($language) {
                $q->where('language', $language);
            })->where('category_id', $category_id);
            $total = $data->count();

            $data = $data->orderBy('created_at', 'desc');
            $data = $data->offset($offset)->limit($limit)->get();
            foreach($data as $res){
                $ser = MomTopicLang::where('topic_id', $res['id'])->where('language', $language)->first();
                if($ser){
                    $res['title'] = $ser->title;
                    $res['content'] = $ser->content;
                    if($res['type'] == 'youtube'){
                        $res['content'] = $ser->video_id;
                    }
                }else{
                    $res['title'] = '';
                    $res['content'] = '';
                }

                if($res['type'] == 'file' || $res['type'] == 'image' || $res['type'] == 'video'){
                    $res['content'] = url($res['content']);
                }

                if($res['image'] != ''){
                    $res['image'] = url($res['image']);
                }else{
                    $res['image'] = url('images/13481508821533457544.png');
                }

                unset($res['author']);
                unset($res['author_image']);
                unset($res['profile']);
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

    public function list_services(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));

        try{

            $type = (isset($request->type) && $request->type != '')?$request->type:'mom';
            $language = (isset($request->language) && $request->language != '')? $request->language:'english';
            $limit = (isset($request->limit) && $request->limit > 0)?$request->limit:10;
            $page_no = (isset($request->page_no) && $request->page_no > 1)?$request->page_no:1;
            $offset = ($page_no-1)*$limit;

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

            $data = Servicess::whereHas('lang_content', function($q) use($language) {
                $q->where('language', $language);
            })->where('publish', '1')->where('type' , $type);
            if($type == 'event-news'){
                $data->where(function($q) use($user){
                        $q->where('dormitory_id', $user->dormitory_id)
                        ->orWhereNull('dormitory_id')
                        ->orWhere('dormitory_id', 0);
                });
            }
            $total = $data->count();

            $data = $data->orderBy('created_at', 'desc');
            $data = $data->offset($offset)->limit($limit)->withCount('likes')->get();
            foreach($data as $res){
                $ser = ServicesLang::where('services_id', $res['id'])->where('language', $language)->first();
                if($ser){
                    $res['title'] = $ser->title;
                    // $res['content'] = $ser->content;
                    $res['author'] = $ser->author == ''?'Unknown':$ser->author;
                }else{
                    if($label != ''){
                        if($res['title_'.$label] != ''){
                            $res['title'] = $res['title_'.$label];
                        }
                        if($res['content_'.$label] != ''){
                            $res['content'] = $res['content_'.$label];
                        }
                        if($res['author_'.$label] != ''){
                            $res['author'] = $res['author_'.$label];
                        }
                    }
                }
                $liked = ServicesLike::where('service_id', $res['id'])->where('user_id', $user->id)->count();
                if($liked == 0){
                  $res['is_liked'] = false;
                }else{
                  $res['is_liked'] = true;
                }
                if($res['image'] != ''){
                    $res['image'] = url($res['image']);
                }else{
                    $res['image'] = url('images/placeholder.png');
                }
                $res['user_name'] = isset($res['author']) && $res['author'] != ''?$res['author']:'Admin';
                if($res['author_image'] == ''){
                    $prof = url('files/profile/user.jpg');
                }else{
                    $prof = url($res['author_image']);
                }
                $res['user_image'] = $prof;
                unset($res['author']);
                unset($res['author_image']);
                unset($res['profile']);
            }
            if($total < 1){
                return response()->json(['status' => 'success', 'data' => $data, 'total' => $total, 'message' => 'No data available'], 200);
            }else{
                return response()->json(['status' => 'success', 'data' => $data, 'total' => $total, 'message' => 'SUCCESS'], 200);
            }

        }catch(Exception $e){
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function like_services(Request $request)
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
            $data['service_id'] = $request->service_id;
            $data['user_id'] = $user->id;

            $service = ServicesLike::where($data)->first();
            if(!$service){
                $service = ServicesLike::create($data);
                Activity::log('Liked '.$service->type .'services #'.$service->id, $user->id);
                return response()->json(['status' => 'success', 'data' => [], 'message' => 'LIKED'], 200);
            }else{
                $service->delete();
                Activity::log('UnLiked Service #'.$service->id, $user->id);
                return response()->json(['status' => 'success', 'data' => [], 'message' => 'UNLIKED'], 200);
            }

        }catch(Exception $e){
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function share_services(Request $request)
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
            $service_id = $request->service_id;

            $service = Servicess::find($service_id);
            if(!empty($service)){
                $service->share = ++$service->share;
                $service->save();
                Activity::log('Shared '.$service->type .'services #'.$service->id, $user->id, $user->id);
                return response()->json(['status' => 'success', 'data' => 'Share count increased', 'message' => 'SUCCESS'], 200);
            }else{
                return response()->json(['status' => 'success', 'data' => 'Service does not exist', 'message' => 'ERROR'], 200);
            }

        }catch(Exception $e){
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    protected function view_validator(array $data, $user)
    {
      return Validator::make($data, [
            'service_id'       => 'required',
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
          $service_id = $request->service_id;
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

          $service = Servicess::where('id', $service_id)->withCount(['likes'])->with(['comments'])->first();

          if($service){
              $ser = ServicesLang::where('services_id', $service['id'])->where('language', $language)->first();
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
              // $author = User::find($service->author_id);
              $service['user_name'] = isset($service->author) && $service->author != '' ?$service->author:'Admin';
              if($service['author_image'] == ''){
                  $prof = url('files/profile/user.jpg');
              }else{
                  $prof = url($service['author_image']);
              }
              $service['user_image'] = $prof;

              $liked = ServicesLike::where('service_id', $service_id)->where('user_id', $user->id)->count();
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
            $data['service_id'] = $request->service_id;
            $data['comment'] = $request->comment;
            $data['user_id'] = $user->id;

            $service = ServicesComment::create($data);
            if(!empty($service)){
                Activity::log('Added comment to '.$service->type .'service #'.$service->id, $user->id);
                return response()->json(['status' => 'success', 'data' => $service, 'message' => 'SUCCESS'], 200);
            }else{
                return response()->json(['status' => 'success', 'data' => 'Insertion error', 'message' => 'ERROR'], 200);
            }

        }catch(Exception $e){
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function list_feedback_mom_category(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));

        try{
            // $language = (isset($request->language) && $request->language != '')? $request->language:'english';
            // $limit = (isset($request->limit) && $request->limit > 0)?$request->limit:10;
            // $page_no = (isset($request->page_no) && $request->page_no > 1)?$request->page_no:1;
            // $offset = ($page_no-1)*$limit;
            //
            //     if($language == 'chinese'){
            //         $language = "mandarin";
            //     }
            $data = MomFeedbackCategory::all();

            return response()->json(['status' => 'success', 'data' => $data, 'message' => 'SUCCESS'], 200);

        }catch(Exception $e){
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

}
