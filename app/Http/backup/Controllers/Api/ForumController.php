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
use App\Models\Badwords;
use App\User;
use Carbon\Carbon, Activity;
use ConsoleTVs\Profanity\Facades\Profanity;

class ForumController extends Controller
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

          $obj = Badwords::select('language', 'word')->get()->toArray();
          $clean_words = \ConsoleTVs\Profanity\Builder::blocker(strtolower($request->desc))->dictionary($obj)->clean();

          if($clean_words)
            $clean_words = \ConsoleTVs\Profanity\Builder::blocker(strtolower($request->title))->dictionary($obj)->clean();

          if($clean_words){
              $forum = Forum::create([
                  'user_id'        => $user->id,
                  'title'          => $request->title,
                  'topic_id'       => $request->category_id,
                  'content'        => $request->desc,
                  'bad_word'       => $clean_words?'0':'1',
                  // 'category_id'    => $request->category_id,
              ]);

          }else{
              return response()->json(['status' => 'validation', 'data' => [], 'message' => 'Post with bad words are not allowed.'], 200);
          }

          if($forum){
              Activity::log('Created '.$forum->title.' Forum #'.$forum->id, $user->id);

            return response()->json(['status' => 'success', 'data' => $forum, 'message' => 'CREATED.'], 200);
          }else{
            return response()->json(['status' => 'error', 'data' => 'Their was a problem while creating new data. Try Later.', 'message' => 'INSERTION_ERROR'], 404);
          }

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function category_list(Request $request)
    {
        // $user = JWTAuth::toUser($request->input('token'));
        \Log::debug($request->all());
        try{
          $list = Topics::select('id','title')->orderBy('title', 'asc')->get();
          foreach($list as $res){
            //->where('language', $language)
            $ser = TopicLang::where('topic_id', $res->id)->first();
            if($ser){
                $res['title'] = $ser->title;
            }else{
                $res['title'] = '';
            }
          }
          return response()->json(['status' => 'success', 'data' => $list, 'message' => ''], 200);

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

    public function list_forums(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $validator = $this->list_validator($request->all(), $user);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $message = [];
            foreach($errors->messages() as $key => $error){
                $message[$key] = $error[0];
            }
            return response()->json(['status' => 'error', 'data' => $message, 'message' => 'VALIDATION_ERROR'], 401);
        }
        try{
            $keyword = (isset($request->keyword) && $request->keyword != '')?$request->keyword:'';
            $limit = (isset($request->limit) && $request->limit > 0)?$request->limit:10;
            $page_no = (isset($request->page_no) && $request->page_no > 1)?$request->page_no:1;
            $order = (isset($request->order) && $request->order != "")?$request->order:'recent';
            $language = (isset($request->language) && $request->language != '')? $request->language:'english';

            if($language == 'chinese'){
                $language = 'mandarin';
            }
            $data = Forum::where('topic_id', $request->topic_id)->with('user')->withCount('likes', 'comments');
            if($keyword != ""){
                $data->where(function($q) use($keyword){
                    $q->where('title', 'like', '%'.$keyword.'%')->orWhere('content', 'like', '%' . $keyword . '%');
                });
            }

            switch($order){
                case 'my':
                    $data->where('user_id', $user->id);
                break;
                case 'fav':
                    $ids = Favourite::select('ref_id')->where('type', 'forum')->where('user_id', $user->id)->get();
                    $data->whereIn('id',$ids);
                break;
                // default:

            }
            $total = $data->count();
            $offset = ($page_no-1)*$limit;
            $data = $data->orderBy('created_at', 'desc')->offset($offset)->limit($limit)->get();
            foreach($data as $res){
                $res['user_name'] = $res['user']['name'];
                // $res['user_image'] = $res['profile']['profile_pic'];
                unset($res['user']);
                // unset($res['profile']);
                $liked = Likes::where('ref_id', $res['id'])->where('type', 'forum')->count();
                if($liked == 0){
                    $res['is_liked'] = false;
                }else{
                    $res['is_liked'] = true;
                }
                $fav = Favourite::where('ref_id', $res['id'])->where('type', 'forum')->count();
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

    public function like_forum(Request $request)
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
            $data['ref_id'] = $request->forum_id;
            $data['user_id'] = $user->id;
            $data['type'] = 'forum';

            $exist = Likes::where($data)->first();
            if(!$exist){
                $exist = Likes::create($data);
                Activity::log('Liked '.$exist->title.' Forum #'.$exist->id, $user->id);
                $count = Likes::where('ref_id', $data['ref_id'])->where('type', 'forum')->count();
                return response()->json(['status' => 'success', 'data' => ['count' => $count], 'message' => 'LIKED'], 200);
            }else{
                $exist->delete();
                Activity::log('UnLiked '.$exist->title.' Forum #'.$exist->id, $user->id);
                $count = Likes::where('ref_id', $data['ref_id'])->where('type', 'forum')->count();
                return response()->json(['status' => 'success', 'data' => ['count' => $count], 'message' => 'UNLIKED'], 200);
            }

        }catch(Exception $e){
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function fav_forum(Request $request)
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
            $data['ref_id'] = $request->forum_id;
            $data['user_id'] = $user->id;
            $data['type'] = 'forum';

            $exist = Favourite::where($data)->first();
            if(!$exist){
                Favourite::create($data);
                $exist = Forum::find($request->forum_id);
                $count = Favourite::where('ref_id', $data['ref_id'])->where('type', 'forum')->count();
                Activity::log('Favourite '.$exist->title.' Forum #'.$exist->id, $user->id);
                return response()->json(['status' => 'success', 'data' => 'Added to favourite list.', 'message' => 'FAVOURITE'], 200);
            }else{
                $exist->delete();
                Activity::log('Removed Favourite '.$exist->title.' Forum #'.$exist->id, $user->id);
                $count = Favourite::where('ref_id', $data['ref_id'])->where('type', 'forum')->count();
                return response()->json(['status' => 'success', 'data' => 'Removed from favourite list.', 'message' => 'UN-FAVOURITE'], 200);
            }

        }catch(Exception $e){
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function report_forum(Request $request)
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

            $forum = Forum::find($forum_id);
            if(!empty($forum)){
                $forum->report = '1';
                $forum->reported_at = date('Y-m-d h:i:s');
                $forum->save();
                Activity::log('Reported '.$forum->title.' Forum #'.$forum->id, $user->id);
                return response()->json(['status' => 'success', 'data' => 'Forum reported', 'message' => 'SUCCESS'], 200);
            }else{
                return response()->json(['status' => 'success', 'data' => 'Forum does not exist', 'message' => 'ERROR'], 200);
            }

        }catch(Exception $e){
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function share_forum(Request $request)
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

            $forum = Forum::find($forum_id);
            if(!empty($forum)){
                $forum->share = ++$forum->share;
                $forum->save();
                Activity::log('Shared '.$forum->title.' Forum #'.$forum->id, $user->id);

                return response()->json(['status' => 'success', 'data' => 'Share count increased', 'message' => 'SUCCESS'], 200);
            }else{
                return response()->json(['status' => 'success', 'data' => 'Forum does not exist', 'message' => 'ERROR'], 200);
            }

        }catch(Exception $e){
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    protected function view_validator(array $data, $user)
    {
      return Validator::make($data, [
            'forum_id'       => 'required',
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

          $liked = Likes::where('ref_id', $forum_id)->where('type', 'forum')->count();
          if($liked == 0){
              $forum['is_liked'] = false;
          }else{
              $forum['is_liked'] = true;
          }
          $fav = Favourite::where('ref_id', $forum_id)->where('type', 'forum')->count();
          if($fav == 0){
              $forum['is_fav'] = false;
          }else{
              $forum['is_fav'] = true;
          }
          if($forum['user'] != null){
              $forum['user_name'] = $forum['user']['name'];
              if(isset($forum['user']['profile']) && $forum['user']['profile']['profile_pic'] != ''){
                  $img = url($forum['user']['profile']['profile_pic']);
              }else{
                  $img = url('files/profile/user.jpg');
              }
              $forum['user_image'] = $img;
              // $forum['user_image'] = isset($forum['user']['profile'])?url($forum['user']['profile']['profile_pic']):null;
              unset($forum['user']);
          }
          foreach($forum->comments as $comment){
              $comment['comment'] = Profanity::blocker($comment['comment'])->filter();
              $user = User::find($comment->user_id);
              $comment['user_name'] = @$user->name;
              // $comment['user_image'] = url(@$user->profile->profile_pic);
              if(isset($user->profile) && @$user->profile->profile_pic != ''){
                  $img = url($user->profile->profile_pic);
              }else{
                  $img = url('files/profile/user.jpg');
              }
              $comment['user_image'] = $img;
          }
          if($forum){
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

            $obj = Badwords::select('language', 'word')->get()->toArray();
            $clean_words = \ConsoleTVs\Profanity\Builder::blocker(strtolower($request->comment))->dictionary($obj)->clean();

            if($clean_words){
                $comment = Comment::create($data);
            }else{
                return response()->json(['status' => 'validation', 'data' => [], 'message' => 'Comment with bad words are not allowed.'], 200);
            }
            if(!empty($comment)){
                $forum = Forum::find($request->forum_id);
                Activity::log('Commented on '.$forum->title.' Forum #'.$forum->id, $user->id);

                return response()->json(['status' => 'success', 'data' => $comment, 'message' => 'SUCCESS'], 200);
            }else{
                return response()->json(['status' => 'success', 'data' => 'Insertion error', 'message' => 'ERROR'], 200);
            }

        }catch(Exception $e){
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

}
