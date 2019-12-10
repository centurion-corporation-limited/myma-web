<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Models\Topics;
use App\Models\TopicLang;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\AddTopicRequest;
use App\Http\Requests\EditTopicRequest;
use App\Http\Controllers\Controller;
use Auth, Activity;

class TopicController extends Controller
{
    public function getList(Request $request)
    {
      // echo $base = (base64_decode("B8K+QQceKsOFw5EHOlrCjcKxwqRTcA=="));
      // echo $hash = hash('sha256','f67f828219ea203982410e2accc6a228'.'c4cfbc8ca20f91faf4707670b88047965f0fab5b'.'beta-ih-flexm.mmvpay.com');
      // die();
      $auth_user = Auth::user();

      $items = Topics::query();

      if ($id = $request->input('id')) {
          $items->where('id', $id);
      }

      if ($name = $request->input('name')) {
          $name = strtolower($name);
          $items->whereHas('lang_content', function($q) use($name){
              $q->whereRaw('lower(`title`) like ?', array("%{$name}%"));
          });
      }

      $items = $items->withCount('likes','forum');

      $limit = 10;
      $items = $items->sortable(['id' => 'desc'])->paginate($limit);
      $paginate_data = $request->except('page');

      foreach($items as $item){
          $lang = $item->language;
          $ser = TopicLang::where('topic_id', $item->id)->where('language', $lang)->first();
          if($ser){
              $item->title = $ser->title;
          }
      }

      return view('admin.topic.list', compact('items', 'auth_user', 'paginate_data'));
    }


    public function getAdd()
    {
        $auth_user = \Auth::user();

        return view('admin.topic.add', compact('auth_user'));
    }

    public function postAdd(AddTopicRequest $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $data = $request->only('language');
        // $data = $request->only('title','description','title_bn', 'description_bn','title_mn', 'description_mn','title_ta', 'description_ta',
        // 'title_th', 'description_th');
        //
        // if($data['title'] == '' && $data['title_bn'] == '' && $data['title_mn'] == '' && $data['title_ta'] == '' && $data['title_th'] == ''){
        //     return back()->withErrors('The title field is required.');
        //
        // }else if($data['title'] == '' && $data['title_mn'] != ''){
        //     $data['title'] = $data['title_mn'];
        // }
        // else if($data['title'] == '' && $data['title_ta'] != '' ){
        //     $data['title'] = $data['title_ta'];
        // }
        // else if($data['title'] == '' && $data['title_bn'] != '' ){
        //     $data['title'] = $data['title_bn'];
        // }
        // else if($data['title'] == '' && $data['title_th'] != '' ){
        //     $data['title'] = $data['title_th'];
        // }
        //
        // if($data['description'] == '' && $data['description_bn'] == '' && $data['description_mn'] == '' && $data['description_ta'] == '' && $data['description_th']){
        //     return back()->withErrors('The description field is required.');
        //
        // }else if($data['description'] == '' && $data['description_mn'] != ''){
        //     $data['description'] = $data['description_mn'];
        // }
        // else if($data['description'] == '' && $data['description_ta'] != '' ){
        //     $data['description'] = $data['description_ta'];
        // }
        // else if($data['description'] == '' && $data['description_bn'] != '' ){
        //     $data['description'] = $data['description_bn'];
        // }
        // else if($data['description'] == '' && $data['description_th'] != '' ){
        //     $data['description'] = $data['description_th'];
        // }
        //
        // $data['description'] = strip_tags($data['description']);
        // $data['description_mn'] = strip_tags($data['description_mn']);
        // $data['description_ta'] = strip_tags($data['description_ta']);
        // $data['description_bn'] = strip_tags($data['description_bn']);
        // $data['description_th'] = strip_tags($data['description_th']);

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
              $folder = "files/topic";

              $path = savePhoto($file, $folder, $type);
              $data['image'] = $path;
          } else {
              throw new \Exception('did not match data URI with image data');
          }
        }
        $module = Topics::create($data);

        Activity::log('Created a new topic #'.@$module->id. ' by '.$auth_user->name);

        if($request->input('title') != ''){
            $data_en['title'] = $request->input('title');
            $data_en['description'] = $request->input('description');
            $data_en['language'] = 'english';
            $data_en['topic_id'] = $module->id;
            TopicLang::create($data_en);
        }

        if($request->input('title_mn') != ''){
            $data_en['title'] = $request->input('title_mn');
            $data_en['description'] = $request->input('description_mn');
            $data_en['language'] = 'mandarin';
            $data_en['topic_id'] = $module->id;
            TopicLang::create($data_en);
        }

        if($request->input('title_bn') != ''){
            $data_en['title'] = $request->input('title_bn');
            $data_en['description'] = $request->input('description_bn');
            $data_en['language'] = 'bengali';
            $data_en['topic_id'] = $module->id;
            TopicLang::create($data_en);
        }

        if($request->input('title_ta') != ''){
            $data_en['title'] = $request->input('title_ta');
            $data_en['description'] = $request->input('description_ta');
            $data_en['language'] = 'tamil';
            $data_en['topic_id'] = $module->id;
            TopicLang::create($data_en);
        }

        if($request->input('title_th') != ''){
            $data_en['title'] = $request->input('title_th');
            $data_en['description'] = $request->input('description_th');
            $data_en['language'] = 'thai';
            $data_en['topic_id'] = $module->id;
            TopicLang::create($data_en);
        }

        return redirect()->route('admin.topic.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Topic added successfully.',
        ]);

    }
    public function getEdit($id, Request $request)
    {
        $id = decrypt($id);
    	$auth_user = \Auth::user();
    	$item = Topics::findOrFail($id);

        return view('admin.topic.edit', compact('item'));
    }

    public function postEdit($id, EditTopicRequest $request)
    {
        $id = decrypt($id);
        /** @var User $item */
        $auth_user = \Auth::user();

        $module = Topics::findOrFail($id);

        // if($data['title'] == '' && $data['title_bn'] == '' && $data['title_mn'] == '' && $data['title_ta'] == '' && $data['title_th'] == ''){
        //     return back()->withErrors('The title field is required.');
        // }else if($data['title'] == '' && $data['title_mn'] != ''){
        //     $data['title'] = $data['title_mn'];
        // }
        // else if($data['title'] == '' && $data['title_ta'] != '' ){
        //     $data['title'] = $data['title_ta'];
        // }
        // else if($data['title'] == '' && $data['title_bn'] != '' ){
        //     $data['title'] = $data['title_bn'];
        // }
        // else if($data['title'] == '' && $data['title_th'] != '' ){
        //     $data['title'] = $data['title_th'];
        // }
        //
        // if($data['description'] == '' && $data['description_bn'] == '' && $data['description_mn'] == '' && $data['description_ta'] == '' && $data['description_th']){
        //     return back()->withErrors('The description field is required.');
        //
        // }else if($data['description'] == '' && $data['description_mn'] != ''){
        //     $data['description'] = $data['description_mn'];
        // }
        // else if($data['description'] == '' && $data['description_ta'] != '' ){
        //     $data['description'] = $data['description_ta'];
        // }
        // else if($data['description'] == '' && $data['description_bn'] != '' ){
        //     $data['description'] = $data['description_bn'];
        // }
        // else if($data['description'] == '' && $data['description_th'] != '' ){
        //     $data['description'] = $data['description_th'];
        // }
        //
        // $data['description'] = strip_tags($data['description']);
        // $data['description_mn'] = strip_tags($data['description_mn']);
        // $data['description_ta'] = strip_tags($data['description_ta']);
        // $data['description_bn'] = strip_tags($data['description_bn']);
        // $data['description_th'] = strip_tags($data['description_th']);

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
              $folder = "files/topic";

              $path = savePhoto($file, $folder, $type);
              $data['image'] = $path;
              $module->update($data);
          } else {
              throw new \Exception('did not match data URI with image data');
          }
        }


        $data_en['language'] = 'english';
        $data_mn['language'] = 'mandarin';
        $data_ta['language'] = 'tamil';
        $data_bn['language'] = 'bengali';
        $data_th['language'] = 'thai';

        $data_en['topic_id'] = $data_th['topic_id'] = $data_mn['topic_id'] = $data_bn['topic_id'] = $data_ta['topic_id'] = $id;

        // english start
        $exist = TopicLang::where($data_en)->first();
        if($request->input('title') != '' || ($exist && $exist->exists)){

            $data_en['title'] = $request->input('title');
            $data_en['description'] = $request->input('description');
            if($exist && $exist->exists){
                $exist->update($data_en);
            }else{
                TopicLang::create($data_en);
            }
        }
        // english end

        // mandarin start
        $exist = TopicLang::where($data_mn)->first();
        if($request->input('title_mn') != '' || ($exist && $exist->exists)){

            $data_mn['title'] = $request->input('title_mn');
            $data_mn['description'] = $request->input('description_mn');
            if($exist && $exist->exists){
                $exist->update($data_mn);
            }else{
                TopicLang::create($data_mn);
            }
        }
        // mandarin end

        // bengali start
        $exist = TopicLang::where($data_bn)->first();
        if($request->input('title_bn') != '' || ($exist && $exist->exists)){

            $data_bn['title'] = $request->input('title_bn');
            $data_bn['description'] = $request->input('description_bn');
            if($exist && $exist->exists){
                $exist->update($data_bn);
            }else{
                TopicLang::create($data_bn);
            }
        }
        // bengali end

        // tamil start
        $exist = TopicLang::where($data_ta)->first();
        if($request->input('title_ta') != '' || ($exist && $exist->exists)){

            $data_ta['title'] = $request->input('title_ta');
            $data_ta['description'] = $request->input('description_ta');
            if($exist && $exist->exists){
                $exist->update($data_ta);
            }else{
                TopicLang::create($data_ta);
            }
        }
        // tamil end

        // thai start
        $exist = TopicLang::where($data_th)->first();
        if($request->input('title_th') != '' || ($exist && $exist->exists)){

            $data_th['title'] = $request->input('title_th');
            $data_th['description'] = $request->input('description_th');
            if($exist && $exist->exists){
                $exist->update($data_th);
            }else{
                TopicLang::create($data_th);
            }
        }
        // thai end

        Activity::log('Updated topic #'.@$id. ' by '.$auth_user->name);

        return redirect()->route('admin.topic.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Topic details updated successfully.',
        ]);

    }

    public function getDelete($id)
    {
        $items = Topics::where('id', $id);
        $items = $items->withCount('likes','forum')->first();
        if($items->forum_count){
            return redirect()->route('admin.topic.list')->with([
                'flash_level'   => 'danger',
                'flash_message' => 'Can not delete as topic have forums in it',
            ]);
        }
        Topics::destroy($id);

        $auth_user = Auth::user();
        Activity::log('Deleted topic #'.@$id. ' by '.$auth_user->name);

        return redirect()->route('admin.topic.list')->with([
            'flash_level'   => 'danger',
            'flash_message' => 'Topic Deleted',
        ]);
    }

    public function postDelete($id)
    {
        Topics::delete($id);
        return redirect()->route('admin.topic.list')->with([
          'flash_level'   => 'danger',
          'flash_message' => 'Topic Deleted',
        ]);

    }
}
