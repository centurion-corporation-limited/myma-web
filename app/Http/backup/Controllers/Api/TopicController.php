<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Validator;
use JWTAuth, DB;
use App\Models\Topics;
use App\Models\TopicLang;
use App\Models\Forum;
use App\Models\Likes;
use App\Models\Favourite;
use App\Models\Comment;
use App\Models\Attachment;
use App\User;
use Carbon\Carbon, Activity;

class TopicController extends Controller
{
    protected function validator(array $data, $user)
    {
      return Validator::make($data, [
            'title'      => 'required',
            // 'topic_id' => 'required',
            'category_id' => 'required',
            'desc'      => 'required',
        ]);
    }

    public function add(Request $request)
    {
      $user = JWTAuth::toUser($request->input('token'));
      $validator = $this->validator($request->all(), $user);

      if ($validator->fails()) {
          $errors = $validator->errors();
          $message = [];
          foreach($errors->messages() as $key => $error){
              $message[$key] = $error[0];
          }
          return response()->json(['status' => 'error', 'data' => $message, 'message' => 'VALIDATION_ERROR'], 401);
      }

      try{

          $forum = Forum::create([
              'user_id'        => $user->id,
              'title'          => $request->title,
              'topic_id'       => $request->category_id,
              'content'        => $request->desc,
              // 'category_id'    => $request->category_id,
          ]);

          if($forum){
            return response()->json(['status' => 'success', 'data' => $forum, 'message' => 'CREATED.'], 200);
          }else{
            return response()->json(['status' => 'error', 'data' => 'Their was a problem while creating new data. Try Later.', 'message' => 'INSERTION_ERROR'], 404);
          }

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    protected function list_validator(array $data, $user)
    {
      return Validator::make($data, [
            'topic_id'      => 'required',
        ]);
    }

    public function list_topics(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));

        try{
            $limit = (isset($request->limit) && $request->limit > 0)?$request->limit:10;
            $page_no = (isset($request->page_no) && $request->page_no > 1)?$request->page_no:1;
            $keyword = (isset($request->keyword) && $request->keyword != '')?$request->keyword:'';
            $order = (isset($request->order) && $request->order != "")?$request->order:'recent';
            $language = (isset($request->language) && $request->language != '')? $request->language:'english';

            if($language == 'bengali'){
                $label = 'bn';
            }elseif($language == 'mandarin' || $language == 'chinese'){
                $language = 'mandarin';
                $label = 'mn';
            }elseif($language == 'tamil'){
                $label = 'ta';
            }elseif($language == 'thai'){
                $label = 'th';
            }else{
                $label = '';
            }

            $data = Topics::withCount('likes', 'forum');

            if($keyword != ""){
                $keyword = strtolower($keyword);
                $data->whereHas('lang_content', function($q) use($keyword, $language){
                    $q->whereRaw('lower(title) like ?', ['%'.$keyword.'%'])->orWhereRaw('lower(description) like ?', ['%' . $keyword . '%'])
                    ->where('language', $language);
                });
            }else{
                $data->whereHas('lang_content', function($q) use($language){
                    $q->where('language', $language);
                });
            }
            switch($order){
                case 'my':
                    // $data->where('user_id', $user->id);
                break;
                case 'fav':
                    $ids = Favourite::select('ref_id')->where('type', 'topic')->where('user_id', $user->id)->get();
                    $data->whereIn('id',$ids);
                break;
                // default:

            }
            $total = $data->count();
            $offset = ($page_no-1)*$limit;
            if($order == 'recent'){
                $data = $data->orderBy('created_at', 'desc')->offset($offset)->limit($limit)->get();
            }else{
                $data = $data->orderBy('created_at', 'asc')->offset($offset)->limit($limit)->get();
            }


            foreach($data as $res){
                $ser = TopicLang::where('topic_id', $res['id'])->where('language', $language)->first();
                if($ser){
                    $res['title'] = $ser->title;
                    $res['description'] = $ser->description;
                }else{
                    $res['title'] = '';
                    $res['description'] = '';
                    // if($label != ''){
                    //     if($res['title_'.$label] != ''){
                    //         $res['title'] = $res['title_'.$label];
                    //     }
                    //     if($res['description_'.$label] != ''){
                    //         $res['description'] = $res['description_'.$label];
                    //     }
                    // }
                }
                // $res['user_name'] = $res['user']['name'];
                // $res['user_image'] = $res['profile']['profile_pic'];
                // unset($res['user']);
                // unset($res['profile']);
                $liked = Likes::where('ref_id', $res['id'])->where('type', 'topic')->count();
                if($liked == 0){
                  $res['is_liked'] = false;
                }else{
                  $res['is_liked'] = true;
                }
                $fav = Favourite::where('ref_id', $res['id'])->where('type', 'topic')->count();
                if($fav == 0){
                    $res['is_fav'] = false;
                }else{
                    $res['is_fav'] = true;
                }
            }
            return response()->json(['status' => 'success', 'data' => $data, 'total' => $total, 'message' => 'SUCCESS'], 200);

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
            $data['ref_id'] = $request->topic_id;
            $data['user_id'] = $user->id;
            $data['type'] = 'topic';

            $exist = Likes::where($data)->first();
            if(!$exist){
                $exist = Likes::create($data);
                Activity::log('Liked Topic #'.$exist->id);
                $count = Likes::where('ref_id', $data['ref_id'])->where('type', 'topic')->count();
                return response()->json(['status' => 'success', 'data' => ['count' => $count], 'message' => 'LIKED'], 200);
            }else{
                $exist->delete();
                Activity::log('UnLiked Topic #'.$exist->id);
                $count = Likes::where('ref_id', $data['ref_id'])->where('type', 'topic')->count();
                return response()->json(['status' => 'success', 'data' => ['count' => $count], 'message' => 'UNLIKED'], 200);
            }

        }catch(Exception $e){
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function fav_topic(Request $request)
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
            $data['ref_id'] = $request->topic_id;
            $data['user_id'] = $user->id;
            $data['type'] = 'topic';

            $exist = Favourite::where($data)->first();
            if(!$exist){
                $exist = Favourite::create($data);
                Activity::log('Favourite Topic #'.$exist->id);
                return response()->json(['status' => 'success', 'data' => 'Added to favourite list.', 'message' => 'SUCCESS'], 200);
            }else{
                $exist->delete();
                Activity::log('Removed Favourite Topic #'.$exist->id);
                return response()->json(['status' => 'success', 'data' => 'Removed from favourite list.', 'message' => 'UNLIKED'], 200);
            }

        }catch(Exception $e){
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function report_topic(Request $request)
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
            $topic_id = $request->topic_id;

            $topic = Topics::find($topic_id);
            if(!empty($topic)){
                $topic->report = '1';
                $topic->save();
                Activity::log('Reported Topic #'.$topic->id);
                return response()->json(['status' => 'success', 'data' => 'Topic reported', 'message' => 'SUCCESS'], 200);
            }else{
                return response()->json(['status' => 'success', 'data' => 'Topic does not exist', 'message' => 'ERROR'], 200);
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
            $topic_id = $request->topic_id;

            $topic = Topics::find($topic_id);
            if(!empty($topic)){
                $topic->share = ++$topic->share;
                $topic->save();
                Activity::log('Shared Topic #'.$topic->id);
                return response()->json(['status' => 'success', 'data' => 'Share count increased', 'message' => 'SUCCESS'], 200);
            }else{
                return response()->json(['status' => 'success', 'data' => 'Topic does not exist', 'message' => 'ERROR'], 200);
            }

        }catch(Exception $e){
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    protected function view_validator(array $data, $user)
    {
      return Validator::make($data, [
            'topic_id'       => 'required',
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
          $forum_id = $request->forum_id;
          $forum = Forum::where('id', $forum_id)->with(['user','comments'])->withCount('likes')->first();
          $language = (isset($request->language) && $request->language != '')? $request->language:'';

          if($language == 'bengali'){
              $label = 'bn';
          }elseif($language == 'chinese'){
              $language == 'mandarin';
              $label = 'mn';
          }elseif($language == 'tamil'){
              $label = 'ta';
          }elseif($language == 'thai'){
              $label = 'th';
          }else{
              $label = '';
          }

          if($forum){
              if($label != ''){
                  if($forum['title_'.$label] != ''){
                      $forum['title'] = $res['title_'.$label];
                  }
                  if($forum['description_'.$label] != ''){
                      $forum['description'] = $res['description_'.$label];
                  }
              }

              $liked = Likes::where('ref_id', $forum_id)->where('type', 'forum')->count();
              if($liked == 0){
                  $forum['is_liked'] = false;
              }else{
                  $forum['is_liked'] = true;
              }
              if($forum['user'] != null){
                  $forum['user_name'] = $forum['user']['name'];
                  if(isset($forum['user']['profile']) && $forum['user']['profile']['profile_pic'] != ''){
                      $img = url($forum['user']['profile']['profile_pic']);
                  }else{
                      $img = url('files/profile/user.jpg');
                  }
                  $forum['user_image'] = $img;
                  unset($forum['user']);
              }
              foreach($forum->comments as $comment){
                  $user = User::find($comment->user_id);
                  $comment['user_name'] = $user->name;

                  if(isset($user->profile->profile_pic) && $user->profile->profile_pic != ''){
                      $img = url($user->profile->profile_pic);
                  }else{
                      $img = url('files/profile/user.jpg');
                  }
                  $comment['user_image'] = $img;
              }
            return response()->json(['status' => 'success', 'data' => $forum, 'message' => 'SUCCESS'], 200);
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

}
