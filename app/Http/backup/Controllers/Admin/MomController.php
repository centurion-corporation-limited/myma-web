<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Models\MomCategory as Category;
use App\Models\MomCategoryLang as CategoryLang;
use App\Models\MomTopic as Topic;
use App\Models\MomTopicLang as TopicLang;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\AddMomCategoryRequest;
use App\Http\Requests\EditMomCategoryRequest;
use App\Http\Requests\AddMomTopicRequest;
use App\Http\Requests\EditMomTopicRequest;
use App\Http\Controllers\Controller;
use Auth, Activity;

class MomController extends Controller
{
    public function getCategoryList(Request $request)
    {
      $auth_user = Auth::user();
      $items = Category::query();

      if ($name = $request->input('name')) {
          $name = strtolower($name);
          $items->whereHas('lang_content', function($q) use($name){
              $q->whereRaw('lower(`title`) like ?', array("%{$name}%"));
          });
      }
      // if(!$auth_user->hasRole('admin')){
      //     $items->where('created_by', $auth_user->id);
      // }
      $limit = 10;
      $items = $items->sortable(['id' => 'desc'])->paginate($limit);
      $paginate_data = $request->except('page');

      return view('admin.mom.category.list', compact('items', 'auth_user', 'paginate_data'));
    }


    public function getCategoryAdd()
    {
        $auth_user = \Auth::user();
        $languages = array(
            'english' => 'English',
            'bengali' => 'Bengali',
            'mandarin' => 'Chinese',
            'tamil' => 'Tamil',
            'thai' => 'Thai'
        );
        return view('admin.mom.category.add', compact('auth_user', 'languages'));
    }

    public function postCategoryAdd(AddMomCategoryRequest $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $data = $request->only('language');
        $data['created_by'] = $auth_user->id;

        if(isset($request['path']) && $request['path'] != "") {
          $file = $request->input('path');
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
              $folder = "files/mom";

              $path = savePhoto($file, $folder, $type);
              $data['image'] = $path;
          } else {
              throw new \Exception('did not match data URI with image data');
          }
        }
        $module = Category::create($data);

        Activity::log('Mom category created #'.@$module->id. ' by '.$auth_user->name);

        if($request->title != ''){
            $dd_en['language'] = 'english';
            $dd_en['title'] = $request->title;
            $dd_en['category_id'] = $module->id;

            CategoryLang::create($dd_en);
        }
        if($request->title_bn != ''){
            $dd_bn['language'] = 'bengali';
            $dd_bn['title'] = $request->title_bn;
            $dd_bn['category_id'] = $module->id;

            CategoryLang::create($dd_bn);
        }
        if($request->title_mn != ''){
            $dd_en['language'] = 'mandarin';
            $dd_en['title'] = $request->title_mn;
            $dd_en['category_id'] = $module->id;

            CategoryLang::create($dd_en);
        }
        if($request->title_th != ''){
            $dd_en['language'] = 'thai';
            $dd_en['title'] = $request->title_th;
            $dd_en['category_id'] = $module->id;

            CategoryLang::create($dd_en);
        }
        if($request->title_ta != ''){
            $dd_en['language'] = 'tamil';
            $dd_en['title'] = $request->title_ta;
            $dd_en['category_id'] = $module->id;

            CategoryLang::create($dd_en);
        }
        return redirect()->route('admin.mom.category.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Category added successfully.',
        ]);

    }
    public function getCategoryEdit($id, Request $request)
    {
    	$auth_user = \Auth::user();
        $id = decrypt($id);
    	$item = Category::findOrFail($id);
        $languages = array(
            'english' => 'English',
            'bengali' => 'Bengali',
            'mandarin' => 'Chinese',
            'tamil' => 'Tamil',
            'thai' => 'Thai'
        );
        return view('admin.mom.category.edit', compact('item', 'languages'));
    }

    public function postCategoryEdit($id, EditMomCategoryRequest $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $id = decrypt($id);
        $module = Category::findOrFail($id);
        $data = $request->only('language', 'active');

        if(isset($request['path']) && $request['path'] != "") {
          $file = $request->input('path');
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
              $folder = "files/mom";

              $path = savePhoto($file, $folder, $type);
              $data['image'] = $path;
          } else {
              throw new \Exception('did not match data URI with image data');
          }
        }

        $module->update($data);
        Activity::log('Mom category updated #'.@$id. ' by '.$auth_user->name);

        $dd_en['language'] = 'english';
        $dd_bn['language'] = 'bengali';
        $dd_mn['language'] = 'mandarin';
        $dd_th['language'] = 'thai';
        $dd_ta['language'] = 'tamil';

        $dd_en['category_id'] = $module->id;
        $dd_bn['category_id'] = $module->id;
        $dd_mn['category_id'] = $module->id;
        $dd_th['category_id'] = $module->id;
        $dd_ta['category_id'] = $module->id;

        $exist = CategoryLang::where($dd_en)->first();
        if($request->title != ''){
            $dd_en['title'] = $request->title;
            if($exist && $exist->exists){
                $exist->update($dd_en);
            }else{
                CategoryLang::create($dd_en);
            }
        }else{
            if($exist && $exist->exists && $request->title == ''){
                $exist->delete();
            }
        }
        $exist = CategoryLang::where($dd_bn)->first();
        if($request->title_bn != ''){
            $dd_bn['title'] = $request->title_bn;
            if($exist && $exist->exists){
                $exist->update($dd_bn);
            }else{
                CategoryLang::create($dd_bn);
            }
        }else{
            if($exist && $exist->exists && $request->title_bn == ''){
                $exist->delete();
            }
        }
        $exist = CategoryLang::where($dd_mn)->first();
        if($request->title_mn != ''){
            $dd_mn['title'] = $request->title_mn;
            if($exist && $exist->exists){
                $exist->update($dd_mn);
            }else{
                CategoryLang::create($dd_mn);
            }
        }else{
            if($exist && $exist->exists && $request->title_mn == ''){
                $exist->delete();
            }
        }
        $exist = CategoryLang::where($dd_th)->first();
        if($request->title_th != ''){
            $dd_th['title'] = $request->title_th;
            if($exist && $exist->exists){
                $exist->update($dd_th);
            }else{
                CategoryLang::create($dd_th);
            }
        }else{
            if($exist && $exist->exists && $request->title_th == ''){
                $exist->delete();
            }
        }
        $exist = CategoryLang::where($dd_ta)->first();
        if($request->title_ta != ''){
            $dd_ta['title'] = $request->title_ta;
            if($exist && $exist->exists){
                $exist->update($dd_ta);
            }else{
                CategoryLang::create($dd_ta);
            }
        }else{
            if($exist && $exist->exists && $request->title_ta == ''){
                $exist->delete();
            }
        }

        return redirect()->route('admin.mom.category.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Category details updated successfully.',
        ]);

    }

    public function getCategoryDelete($id)
    {
        Category::destroy($id);

        $auth_user = Auth::user();
        Activity::log('Deleted Mom Category #'.@$id. ' by '.$auth_user->name);

        return redirect()->route('admin.mom.category.list')->with([
            'flash_level'   => 'danger',
            'flash_message' => 'Category Deleted',
        ]);
    }

    public function postCategoryDelete($id)
    {
        Category::delete($id);
        return redirect()->route('admin.mom.category.list')->with([
          'flash_level'   => 'danger',
          'flash_message' => 'Category Deleted',
        ]);

    }

    public function getTopicList(Request $request)
    {
      $auth_user = Auth::user();
      $items = Topic::query();

      if ($id = $request->input('category_id')) {
          $id = decrypt($id);
          $items->where('category_id', $id);
      }
      if ($name = $request->input('name')) {
          $name = strtolower($name);
          $items->whereHas('lang_content', function($q) use($name){
              $q->whereRaw('lower(`title`) like ?', array("%{$name}%"));
          });
      }
      // if(!$auth_user->hasRole('admin')){
      //     $items->where('created_by', $auth_user->id);
      // }
      $limit = 10;
      $items = $items->sortable()->paginate($limit);
      $paginate_data = $request->except('page');

      return view('admin.mom.topic.list', compact('items', 'auth_user', 'paginate_data'));
    }


    public function getTopicAdd()
    {
        $auth_user = \Auth::user();
        $categories = Category::select('language', 'id');
        // if(!$auth_user->hasRole('admin')){
        //     $categories->where('created_by', $auth_user->id);
        // }
        $categories = $categories->get();
        $cats = array();
        foreach($categories as $cat){
            $cats[$cat->id] = $cat->content($cat->language)->first()?$cat->content($cat->language)->first()->title:$cat->content()->first();
        }

        $languages = array(
            'english' => 'English',
            'bengali' => 'Bengali',
            'mandarin' => 'Chinese',
            'tamil' => 'Tamil',
            'thai' => 'Thai'
        );
        $file_array = ['file','image', 'video'];
        return view('admin.mom.topic.add', compact('auth_user', 'languages','cats', 'file_array'));
    }

    public function postTopicAdd(AddMomTopicRequest $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $video_mime_types = array('application/annodex','application/mp4','application/ogg','application/vnd.rn-realmedia',
            'application/x-matroska','video/3gpp','video/3gpp2','video/annodex','video/divx','video/flv','video/h264',
            'video/mp4','video/mp4v-es','video/mpeg','video/mpeg-2','video/mpeg4','video/ogg','video/ogm','video/quicktime',
            'video/ty','video/vdo','video/vivo','video/vnd.rn-realvideo','video/vnd.vivo','video/webm','video/x-bin',
            'video/x-cdg','video/x-divx','video/x-dv','video/x-flv','video/x-la-asf','video/x-m4v','video/x-matroska',
            'video/x-motion-jpeg','video/x-ms-asf','video/x-ms-dvr','video/x-ms-wm','video/x-ms-wmv','video/x-msvideo',
            'video/x-sgi-movie','video/x-tivo','video/avi','video/x-ms-asx','video/x-ms-wvx','video/x-ms-wmx',
            );
        $data = $request->only('language', 'type', 'category_id');
        $data['created_by'] = $auth_user->id;
        if(isset($request['path']) && $request['path'] != "") {
          $file = $request->input('path');
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
              $folder = "files/mom/";

              $path = savePhoto($file, $folder, $type);
              $data['image'] = $path;
          } else {
              throw new \Exception('did not match data URI with image data');
          }
        }
        $module = Topic::create($data);

        Activity::log('Added Mom topic #'.@$module->id. ' by '.$auth_user->name);

        if($request->title != ''){
            $dd_en['language'] = 'english';
            $dd_en['title'] = $request->title;
            if($data['type'] == 'file'){
                if($request->hasFile('content')) {
                  $up = $request->file('content');
                  $folder = "files/mom/";
                  $mimeType = $up->getClientMimeType();
                  if(in_array($mimeType, $video_mime_types)){
                      $top = Topic::find($module->id);
                      $top->update(['type' => 'video']);
                  }
                  if (strpos($mimeType, 'image') !== false) {
                      $top = Topic::find($module->id);
                      $top->update(['type' => 'image']);
                  }
                  $path = uploadPhoto($up, $folder);
                  $dd_en['content'] = $path;
                }else{
                  $dd_en['content'] = '';
                }
            }else{
                if($data['type'] == 'youtube'){
                    $url = $request->content;
                    $video_id = getYoutubeIdFromUrl($url);
                    $dd_en['video_id'] = $video_id;
                }
                $dd_en['content'] = $request->content;
            }
            $dd_en['topic_id'] = $module->id;

            TopicLang::create($dd_en);
        }
        if($request->title_bn != ''){
            $dd_bn['language'] = 'bengali';
            $dd_bn['title'] = $request->title_bn;
            if($data['type'] == 'file'){
                if($request->hasFile('content_bn')) {
                  $up = $request->file('content_bn');
                  $folder = "files/mom/";
                  $mimeType = $up->getClientMimeType();
                  if(in_array($mimeType, $video_mime_types)){
                      $top = Topic::find($module->id);
                      $top->update(['type' => 'video']);
                  }
                  if (strpos($mimeType, 'image') !== false) {
                      $top = Topic::find($module->id);
                      $top->update(['type' => 'image']);
                  }

                  $path = uploadPhoto($up, $folder);
                  $dd_bn['content'] = $path;
                }else{
                  $dd_bn['content'] = '';
                }
            }else{
                if($data['type'] == 'youtube'){
                    $url = $request->content_bn;
                    $video_id = getYoutubeIdFromUrl($url);
                    $dd_en['video_id'] = $video_id;
                }
                $dd_bn['content'] = $request->content_bn;
            }
            $dd_bn['topic_id'] = $module->id;

            TopicLang::create($dd_bn);
        }
        if($request->title_mn != ''){
            $dd_en['language'] = 'mandarin';
            $dd_en['title'] = $request->title_mn;
            if($data['type'] == 'file'){
                if($request->hasFile('content_mn')) {
                  $up = $request->file('content_mn');
                  $folder = "files/mom/";
                  $mimeType = $up->getClientMimeType();
                  if(in_array($mimeType, $video_mime_types)){
                      $top = Topic::find($module->id);
                      $top->update(['type' => 'video']);
                  }
                  if (strpos($mimeType, 'image') !== false) {
                      $top = Topic::find($module->id);
                      $top->update(['type' => 'image']);
                  }

                  $path = uploadPhoto($up, $folder);
                  $dd_en['content'] = $path;
                }else{
                  $dd_en['content'] = '';
                }
            }else{
                if($data['type'] == 'youtube'){
                    $url = $request->content_mn;
                    $video_id = getYoutubeIdFromUrl($url);
                    $dd_en['video_id'] = $video_id;
                }
                $dd_en['content'] = $request->content_mn;
            }
            $dd_en['topic_id'] = $module->id;

            TopicLang::create($dd_en);
        }
        if($request->title_th != ''){
            $dd_en['language'] = 'thai';
            $dd_en['title'] = $request->title_th;
            if($data['type'] == 'file'){
                if($request->hasFile('content_th')) {
                  $up = $request->file('content_th');
                  $folder = "files/mom/";
                  $mimeType = $up->getClientMimeType();
                  if(in_array($mimeType, $video_mime_types)){
                      $top = Topic::find($module->id);
                      $top->update(['type' => 'video']);
                  }
                  if (strpos($mimeType, 'image') !== false) {
                      $top = Topic::find($module->id);
                      $top->update(['type' => 'image']);
                  }

                  $path = uploadPhoto($up, $folder);
                  $dd_en['content'] = $path;
                }else{
                  $dd_en['content'] = '';
                }
            }else{
                if($data['type'] == 'youtube'){
                    $url = $request->content_th;
                    $video_id = getYoutubeIdFromUrl($url);
                    $dd_en['video_id'] = $video_id;
                }
                $dd_en['content'] = $request->content_th;
            }
            $dd_en['topic_id'] = $module->id;

            TopicLang::create($dd_en);
        }
        if($request->title_ta != ''){
            $dd_en['language'] = 'tamil';
            $dd_en['title'] = $request->title_ta;
            if($data['type'] == 'file'){
                if($request->hasFile('content_ta')) {
                  $up = $request->file('content_ta');
                  $folder = "files/mom/";
                  $mimeType = $up->getClientMimeType();
                  if(in_array($mimeType, $video_mime_types)){
                      $top = Topic::find($module->id);
                      $top->update(['type' => 'video']);
                  }
                  if (strpos($mimeType, 'image') !== false) {
                      $top = Topic::find($module->id);
                      $top->update(['type' => 'image']);
                  }

                  $path = uploadPhoto($up, $folder);
                  $dd_en['content'] = $path;
                }else{
                  $dd_en['content'] = '';
                }
            }else{
                if($data['type'] == 'youtube'){
                    $url = $request->content_ta;
                    $video_id = getYoutubeIdFromUrl($url);
                    $dd_en['video_id'] = $video_id;
                }
                $dd_en['content'] = $request->content_ta;
            }
            $dd_en['topic_id'] = $module->id;

            TopicLang::create($dd_en);
        }
        return redirect()->route('admin.mom.topic.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Topic added successfully.',
        ]);

    }

    public function getTopicView($id, Request $request)
    {
    	$auth_user = \Auth::user();
        $id = decrypt($id);
    	$item = Topic::findOrFail($id);

        // $categories = Category::select('language', 'id');
        // if(!$auth_user->hasRole('admin')){
        //     $categories->where('created_by', $auth_user->id);
        // }
        // $categories = $categories->get();
        $cats = array();
        $file_array = ['file','image', 'video'];
        return view('admin.mom.topic.view', compact('item', 'cats', 'file_array'));
    }

    public function getTopicEdit($id, Request $request)
    {
    	$auth_user = \Auth::user();
        $id = decrypt($id);
    	$item = Topic::findOrFail($id);
        $file_array = ['file','image', 'video'];
        if(in_array($item->type, $file_array)){
            $item->type = 'file';
        }
        $categories = Category::select('language', 'id')->get();
        $cats = array();
        foreach($categories as $cat){
            $cats[$cat->id] = $cat->content($cat->language)->first()?$cat->content($cat->language)->first()->title:$cat->content()->first();
        }

        $languages = array(
            'english' => 'English',
            'bengali' => 'Bengali',
            'mandarin' => 'Chinese',
            'tamil' => 'Tamil',
            'thai' => 'Thai'
        );
        return view('admin.mom.topic.edit', compact('item', 'languages', 'cats', 'file_array'));
    }

    public function postTopicEdit($id, EditMomTopicRequest $request)
    {
        /** @var User $item */
        $video_mime_types = array('application/annodex','application/mp4','application/ogg','application/vnd.rn-realmedia',
            'application/x-matroska','video/3gpp','video/3gpp2','video/annodex','video/divx','video/flv','video/h264',
            'video/mp4','video/mp4v-es','video/mpeg','video/mpeg-2','video/mpeg4','video/ogg','video/ogm','video/quicktime',
            'video/ty','video/vdo','video/vivo','video/vnd.rn-realvideo','video/vnd.vivo','video/webm','video/x-bin',
            'video/x-cdg','video/x-divx','video/x-dv','video/x-flv','video/x-la-asf','video/x-m4v','video/x-matroska',
            'video/x-motion-jpeg','video/x-ms-asf','video/x-ms-dvr','video/x-ms-wm','video/x-ms-wmv','video/x-msvideo',
            'video/x-sgi-movie','video/x-tivo','video/avi','video/x-ms-asx','video/x-ms-wvx','video/x-ms-wmx',
            );
        $auth_user = \Auth::user();
        $id = decrypt($id);
        $module = Topic::findOrFail($id);

        $data = $request->only('type', 'category_id');

        if(isset($request['path']) && $request['path'] != "") {
          $file = $request->input('path');
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
              $folder = "files/mom/";

              $path = savePhoto($file, $folder, $type);
              $data['image'] = $path;
          } else {
              throw new \Exception('did not match data URI with image data');
          }
        }
        $module = $module->update($data);

        Activity::log('Updated Mom topic #'.@$id. ' by '.$auth_user->name);

        $dd_en['language'] = 'english';
        $dd_en['topic_id'] = $id;
        $exist = TopicLang::where($dd_en)->first();

        if($request->title != ''){
            $dd_en['title'] = $request->title;
            if($data['type'] == 'file'){
                if($request->hasFile('content')) {
                  $up = $request->file('content');
                  $folder = "files/mom/";
                  $mimeType = $up->getClientMimeType();
                  if(in_array($mimeType, $video_mime_types)){
                      $top = Topic::find($id);
                      $top->update(['type' => 'video']);
                  }
                  if (strpos($mimeType, 'image') !== false) {
                      $top = Topic::find($id);
                      $top->update(['type' => 'image']);
                  }

                  $path = uploadPhoto($up, $folder);
                  $dd_en['content'] = $path;
                }
            }else{
                if($data['type'] == 'youtube'){
                    $url = $request->content;
                    $video_id = getYoutubeIdFromUrl($url);
                    $dd_en['video_id'] = $video_id;
                }else{
                    $dd_en['video_id'] = '';
                }
                $dd_en['content'] = $request->content;
            }
            if($exist && $exist->exists){
                $exist->update($dd_en);
            }else{
                TopicLang::create($dd_en);
            }
        }else{
            if($exist && $exist->exists){
                $exist->delete();
            }
        }
        $dd_bn['language'] = 'bengali';
        $dd_bn['topic_id'] = $id;
        $exist = TopicLang::where($dd_bn)->first();

        if($request->title_bn != ''){
            $dd_bn['title'] = $request->title_bn;
            if($data['type'] == 'file'){
                if($request->hasFile('content_bn')) {
                  $up = $request->file('content_bn');
                  $folder = "files/mom/";
                  $mimeType = $up->getClientMimeType();
                  if(in_array($mimeType, $video_mime_types)){
                      $top = Topic::find($id);
                      $top->update(['type' => 'video']);
                  }
                  if (strpos($mimeType, 'image') !== false) {
                      $top = Topic::find($id);
                      $top->update(['type' => 'image']);
                  }

                  $path = uploadPhoto($up, $folder);
                  $dd_bn['content'] = $path;
                }
            }else{
                if($data['type'] == 'youtube'){
                    $url = $request->content_bn;
                    $video_id = getYoutubeIdFromUrl($url);
                    $dd_bn['video_id'] = $video_id;
                }else{
                    $dd_bn['video_id'] = '';
                }
                $dd_bn['content'] = $request->content_bn;
            }
            if($exist && $exist->exists){
                $exist->update($dd_bn);
            }else{
                TopicLang::create($dd_bn);
            }
        }else{
            if($exist && $exist->exists){
                $exist->delete();
            }
        }
        $dd_en['language'] = 'mandarin';
        $dd_en['topic_id'] = $id;
        $exist = TopicLang::where($dd_en)->first();

        if($request->title_mn != ''){
            $dd_en['title'] = $request->title_mn;
            if($data['type'] == 'file'){
                if($request->hasFile('content_mn')) {
                  $up = $request->file('content_mn');
                  $folder = "files/mom/";
                  $mimeType = $up->getClientMimeType();
                  if(in_array($mimeType, $video_mime_types)){
                      $top = Topic::find($id);
                      $top->update(['type' => 'video']);
                  }
                  if (strpos($mimeType, 'image') !== false) {
                      $top = Topic::find($id);
                      $top->update(['type' => 'image']);
                  }

                  $path = uploadPhoto($up, $folder);
                  $dd_en['content'] = $path;
                }
            }else{
                if($data['type'] == 'youtube'){
                    $url = $request->content_mn;
                    $video_id = getYoutubeIdFromUrl($url);
                    $dd_en['video_id'] = $video_id;
                }else{
                    $dd_en['video_id'] = '';
                }
                $dd_en['content'] = $request->content_mn;
            }
            if($exist && $exist->exists){
                $exist->update($dd_en);
            }else{
                TopicLang::create($dd_en);
            }
        }else{
            if($exist && $exist->exists){
                $exist->delete();
            }
        }
        $dd_en['language'] = 'thai';
        $dd_en['topic_id'] = $id;
        $exist = TopicLang::where($dd_en)->first();

        if($request->title_th != ''){
            $dd_en['title'] = $request->title_th;
            if($data['type'] == 'file'){
                if($request->hasFile('content_th')) {
                  $up = $request->file('content_th');
                  $folder = "files/mom/";
                  $mimeType = $up->getClientMimeType();
                  if(in_array($mimeType, $video_mime_types)){
                      $top = Topic::find($id);
                      $top->update(['type' => 'video']);
                  }
                  if (strpos($mimeType, 'image') !== false) {
                      $top = Topic::find($id);
                      $top->update(['type' => 'image']);
                  }

                  $path = uploadPhoto($up, $folder);
                  $dd_en['content'] = $path;
                }
            }else{
                if($data['type'] == 'youtube'){
                    $url = $request->content_th;
                    $video_id = getYoutubeIdFromUrl($url);
                    $dd_en['video_id'] = $video_id;
                }else{
                    $dd_en['video_id'] = '';
                }
                $dd_en['content'] = $request->content_th;
            }
            if($exist && $exist->exists){
                $exist->update($dd_en);
            }else{
                TopicLang::create($dd_en);
            }
        }else{
            if($exist && $exist->exists){
                $exist->delete();
            }
        }

        $dd_en['language'] = 'tamil';
        $dd_en['topic_id'] = $id;
        $exist = TopicLang::where($dd_en)->first();

        if($request->title_ta != ''){
            $dd_en['title'] = $request->title_ta;
            if($data['type'] == 'file'){
                if($request->hasFile('content_ta')) {
                  $up = $request->file('content_ta');
                  $folder = "files/mom/";
                  $mimeType = $up->getClientMimeType();
                  if(in_array($mimeType, $video_mime_types)){
                      $top = Topic::find($id);
                      $top->update(['type' => 'video']);
                  }
                  if (strpos($mimeType, 'image') !== false) {
                      $top = Topic::find($id);
                      $top->update(['type' => 'image']);
                  }

                  $path = uploadPhoto($up, $folder);
                  $dd_en['content'] = $path;
                }
            }else{
                if($data['type'] == 'youtube'){
                    $url = $request->content_ta;
                    $video_id = getYoutubeIdFromUrl($url);
                    $dd_en['video_id'] = $video_id;
                }else{
                    $dd_en['video_id'] = '';
                }
                $dd_en['content'] = $request->content_ta;
            }
            if($exist && $exist->exists){
                $exist->update($dd_en);
            }else{
                TopicLang::create($dd_en);
            }
        }else{
            if($exist && $exist->exists){
                $exist->delete();
            }
        }

        return redirect()->route('admin.mom.topic.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Topic details updated successfully.',
        ]);

    }

    public function getTopicDelete($id)
    {
        Topic::destroy($id);

        $auth_user = Auth::user();
        Activity::log('Deleted Mom Topic #'.@$id. ' by '.$auth_user->name);

        return redirect()->route('admin.mom.topic.list')->with([
            'flash_level'   => 'danger',
            'flash_message' => 'Topic Deleted',
        ]);
    }

    public function postTopicDelete($id)
    {
        Topic::delete($id);
        return redirect()->route('admin.mom.topic.list')->with([
            'flash_level'   => 'danger',
            'flash_message' => 'Topic Deleted',
        ]);

    }
}
