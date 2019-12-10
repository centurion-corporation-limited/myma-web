<?php

namespace App\Http\Controllers\Admin\JTC;

use App\User;
use App\Models\JTC\Event;
use App\Models\JTC\Detail;
use App\Models\JTC\DetailLang;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\AddDetailRequest;
use App\Http\Requests\JTC\EditDetailRequest;
use App\Http\Controllers\Controller;
use Auth, Activity;

class DetailController extends Controller
{
    public function getTopicList(Request $request)
    {
      $auth_user = Auth::user();

      $items = Detail::withCount('comments', 'likes');

      // $menu_type = $request->input('menu_type');
      // if($menu_type == ''){
      //   $menu_type = 'jtc';
      // }
      //
      // if ($menu_type = $request->input('menu_type')) {
      //     $menu_type = strtolower($menu_type);
      //     $items->where('menu_type', $menu_type);
      // }

      if ($name = $request->input('name')) {
          $name = strtolower($name);
          $items->whereHas('lang_content', function($q) use($name){
              $q->whereRaw('lower(`title`) like ?', array("%{$name}%"));
          });
      }

      $event = '';
      if ($event_id = $request->input('event_id')) {
          $event_id = decrypt($event_id);
          $event = Event::find($event_id);
          $items->where('event_id', $event_id);
      }

      $limit = 10;
      $items = $items->sortable(['id' => 'desc'])->paginate($limit);
      $paginate_data = $request->except('page');

      foreach($items as $item){
          $lang = $item->language;
          $ser = DetailLang::where('topic_id', $item->id)->where('language', $lang)->first();
          if($ser){
              $item->title = $ser->title;
              $item->author = $ser->author;
          }
      }

      return view('admin.jtc.detail.list', compact('items', 'auth_user', 'paginate_data', 'event'));
    }


    public function getTopicAdd()
    {
        $auth_user = \Auth::user();
        $users = User::pluck('name','id');

        $events = [];
        $jtc_centers = Event::where('active', '1')->get();
        foreach($jtc_centers as $cent){
            if($cent->detail){
              continue;
            }
            if($cent->content($cent->language)->first()){
                $events[$cent->id] = $cent->content($cent->language)->first()->title;
            }else{
                $events[$cent->id] = $cent->lang_content()->first()->title;
            }
        }
        return view('admin.jtc.detail.add', compact('auth_user', 'users', 'events'));
    }

    public function postTopicAdd(Request $request)
    {
        $auth_user = \Auth::user();

        /** @var User $item */
        $data_main = $request->only('publish', 'language' ,'event_id');

        $data = $request->only('title','content','title_bn', 'content_bn','title_mn', 'content_mn',
        'title_th', 'content_th', 'title_ta', 'content_ta','author','author_bn','author_mn','author_ta', 'author_th');

        if($data_main['event_id'] == ''){
            return back()->withErrors('The sub category field is required.')->withInput();
        }

        if($data['title'] == '' && $data['title_bn'] == '' && $data['title_mn'] == '' && $data['title_ta'] == '' && $data['title_th'] == ''){
                return back()->withErrors('The title field is required.')->withInput();

            }else if($data['title'] == '' && $data['title_mn'] != ''){
                $data['title'] = $data['title_mn'];
            }
            else if($data['title'] == '' && $data['title_ta'] != '' ){
                $data['title'] = $data['title_ta'];
            }
            else if($data['title'] == '' && $data['title_bn'] != '' ){
                $data['title'] = $data['title_bn'];
            }
            else if($data['title'] == '' && $data['title_th'] != '' ){
                $data['title'] = $data['title_th'];
        }

        if($data['content'] == '' && $data['content_bn'] == '' && $data['content_mn'] == '' && $data['content_ta'] == ''){
            return back()->withErrors('The content field is required.')->withInput();
        }else if($data['content'] == '' && $data['content_mn'] != ''){
            $data['content'] = $data['content_mn'];
        }
        else if($data['content'] == '' && $data['content_mn'] == '' && $data['content_ta'] != '' ){
            $data['content'] = $data['content_ta'];
        }
        else if($data['content'] == '' && $data['content_mn'] == '' && $data['content_bn'] != '' ){
            $data['content'] = $data['content_bn'];
        }
        else if($data['content'] == '' && $data['content_th'] == '' && $data['content_th'] != '' ){
            $data['content'] = $data['content_th'];
        }

        // $data['content'] = strip_tags($data['content']);
        // $data['content_mn'] = strip_tags($data['content_mn']);
        // $data['content_ta'] = strip_tags($data['content_ta']);
        // $data['content_bn'] = strip_tags($data['content_bn']);
        // $data['content_th'] = strip_tags($data['content_th']);

        if($data['author'] == '' && $data['author_bn'] == '' && $data['author_mn'] == '' && $data['author_ta'] == ''){
                return back()->withErrors('The author field is required.')->withInput();
        }else if($data['author'] == '' && $data['author_mn'] != ''){
                $data['author'] = $data['author_mn'];
        }
        else if($data['author'] == '' && $data['author_mn'] == '' && $data['author_ta'] != '' ){
                $data['author'] = $data['author_ta'];
        }
        else if($data['author'] == '' && $data['author_mn'] == '' && $data['author_bn'] != '' ){
                $data['author'] = $data['author_bn'];
        }

        if($data_main['publish'] == ''){
                return back()->withErrors('The publish field is required.')->withInput();
        }
        $auth_user = \Auth::user();
        $data_main['created_by'] = $auth_user->id;

        // if(isset($request['image']) && $request['image'] != "") {
        //   $file = $request->input('image');
        //   if (preg_match('/^data:image\/(\w+);base64,/', $file, $type)) {
        //       $file = substr($file, strpos($file, ',') + 1);
        //       $type = strtolower($type[1]); // jpg, png, gif
        //
        //       if (!in_array($type, [ 'jpg', 'jpeg', 'gif', 'png' ])) {
        //           throw new \Exception('invalid image type');
        //       }
        //
        //       $decode = base64_decode($file);
        //
        //       if ($decode === false) {
        //           throw new \Exception('base64_decode failed');
        //       }
        //       $folder = "files/services";
        //
        //       $path = savePhoto($file, $folder, $type);
        //       $data['image'] = $path;
        //   } else {
        //       throw new \Exception('did not match data URI with image data');
        //   }
        // }

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
              $folder = "files/jtc/detail/";

              $path = savePhoto($file, $folder, $type);
              $data_main['image'] = $path;
          } else {
              throw new \Exception('did not match data URI with image data');
          }
        }

        // if($request->hasFile('image')){
        //   $file = $request->file('image');
        //   $folder = "files/services";
        //
        //   $path = uploadPhoto($file, $folder);
        //   $data_main['image'] = $path;
        // }
        if($request->hasFile('author_image')){
          $file = $request->file('author_image');
          $folder = "files/jtc/detail/author/";

          $path = uploadPhoto($file, $folder);
          $data_main['author_image'] = $path;
        }

        $module = Detail::create($data_main);

        Activity::log('Created new detail entry #'.@$module->id. ' by '.$auth_user->name);
        //english
        if($request->input('title') != ''){
            $data_en['title'] = $request->input('title');
            $data_en['content'] = trim(preg_replace('#<script(.*?)>(.*?)</script>#is', '', $request->input('content')));
            $data_en['author'] = $request->input('author');
            $data_en['topic_id'] = $module->id;
            $data_en['language'] = 'english';
            DetailLang::create($data_en);
        }

        //bengali
        if($request->input('title_bn') != ''){
            $data_bn['title'] = $request->input('title_bn');
            $data_bn['content'] = trim(preg_replace('#<script(.*?)>(.*?)</script>#is', '', $request->input('content_bn')));
            $data_bn['author'] = $request->input('author_bn');
            $data_bn['topic_id'] = $module->id;
            $data_bn['language'] = 'bengali';
            DetailLang::create($data_bn);
        }

        //chinese
        if($request->input('title_mn') != ''){
            $data_mn['title'] = $request->input('title_mn');
            $data_mn['content'] = trim(preg_replace('#<script(.*?)>(.*?)</script>#is', '', $request->input('content_mn')));
            $data_mn['author'] = $request->input('author_mn');
            $data_mn['topic_id'] = $module->id;
            $data_mn['language'] = 'mandarin';
            DetailLang::create($data_mn);
        }

        //tamil
        if($request->input('title_ta') != ''){
            $data_ta['title'] = $request->input('title_ta');
            $data_ta['content'] = trim(preg_replace('#<script(.*?)>(.*?)</script>#is', '', $request->input('content_ta')));
            $data_ta['author'] = $request->input('author_ta');
            $data_ta['topic_id'] = $module->id;
            $data_ta['language'] = 'tamil';
            DetailLang::create($data_ta);
        }

        //thai
        if($request->input('title_th') != ''){
            $data_th['title'] = $request->input('title_th');
            $data_th['content'] = trim(preg_replace('#<script(.*?)>(.*?)</script>#is', '', $request->input('content_th')));
            $data_th['author'] = $request->input('author_th');
            $data_th['topic_id'] = $module->id;
            $data_th['language'] = 'thai';
            DetailLang::create($data_th);
        }

        return redirect()->route('admin.jtc.detail.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Detail added successfully.',
        ]);

    }
    public function getTopicEdit($id, Request $request)
    {
        $id = decrypt($id);
    	  $auth_user = \Auth::user();
    	  $item = Detail::findOrFail($id);
        $users = User::pluck('name','id');
        $events = [];
        $jtc_centers = Event::where('active', '1')->get();
        foreach($jtc_centers as $cent){
            if($cent->detail && $cent->id != $item->event_id){
              continue;
            }
            if($cent->content($cent->language)->first()){
                $events[$cent->id] = $cent->content($cent->language)->first()->title;
            }else{
                $events[$cent->id] = $cent->lang_content()->first()->title;
            }
        }

        return view('admin.jtc.detail.edit', compact('item', 'users', 'events'));
    }

    public function getTopicView($id, Request $request)
    {
        $id = decrypt($id);
    	  $auth_user = \Auth::user();
    	  $item = Detail::findOrFail($id);
        $lang = DetailLang::where('language', $item->language)->where('topic_id', $id)->first();
        $users = User::pluck('name','id');

        return view('admin.jtc.detail.view', compact('item', 'users', 'lang'));
    }

    public function postTopicEdit($id, EditDetailRequest $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $id = decrypt($id);
        $module = Detail::findOrFail($id);

        $data = $request->only('publish', 'language', 'event_id');
        // dd($data);
        if($data['publish'] == null){
            $data['publish'] = 0;
        }

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
              $folder = "files/jtc/detail/";

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
        if($request->hasFile('author_image')){
          $file = $request->file('author_image');
          $folder = "files/jtc/detail/author/";

          $path = uploadPhoto($file, $folder);
          $data['author_image'] = $path;
        }
        // if(isset($request['image']) && $request['image'] != "") {
        //   $file = $request->input('image');
        //   if (preg_match('/^data:image\/(\w+);base64,/', $file, $type)) {
        //       $file = substr($file, strpos($file, ',') + 1);
        //       $type = strtolower($type[1]); // jpg, png, gif
        //
        //       if (!in_array($type, [ 'jpg', 'jpeg', 'gif', 'png' ])) {
        //           throw new \Exception('invalid image type');
        //       }
        //
        //       $decode = base64_decode($file);
        //
        //       if ($decode === false) {
        //           throw new \Exception('base64_decode failed');
        //       }
        //       $folder = "files/services";
        //
        //       $path = savePhoto($file, $folder, $type);
        //       $data['image'] = $path;
        //   } else {
        //       throw new \Exception('did not match data URI with image data');
        //   }
        // }
        $module->update($data);

        Activity::log('Updated detail entry #'.@$id. ' by '.$auth_user->name);

        //english
        $data_en['language'] = 'english';
        $data_en['topic_id'] = $module->id;
        $exist = DetailLang::where($data_en)->first();
        if($request->input('title') != '' || ($exist && $exist->exists)){
            $data_en['title'] = $request->input('title');
            $data_en['content'] = trim(preg_replace('#<script(.*?)>(.*?)</script>#is', '', $request->input('content')));
            $data_en['author'] = $request->input('author');
            if($exist && $exist->exists){
                $exist->update($data_en);
            }else{
                DetailLang::create($data_en);
            }
        }

        //bengali
        $data_bn['language'] = 'bengali';
        $data_bn['topic_id'] = $module->id;
        $exist = DetailLang::where($data_bn)->first();
        if($request->input('title_bn') != '' || ($exist && $exist->exists)){
            $data_bn['title'] = $request->input('title_bn');
            $data_bn['content'] = trim(preg_replace('#<script(.*?)>(.*?)</script>#is', '', $request->input('content_bn')));
            $data_bn['author'] = $request->input('author_bn');
            if($exist && $exist->exists){
                $exist->update($data_bn);
            }else{
                DetailLang::create($data_bn);
            }
        }

        //chinese
        $data_mn['language'] = 'mandarin';
        $data_mn['topic_id'] = $module->id;
        $exist = DetailLang::where($data_mn)->first();
        if($request->input('title_mn') != '' || ($exist && $exist->exists)){
            $data_mn['title'] = $request->input('title_mn');
            $data_mn['content'] = trim(preg_replace('#<script(.*?)>(.*?)</script>#is', '', $request->input('content_mn')));
            $data_mn['author'] = $request->input('author_mn');
            if($exist && $exist->exists){
                $exist->update($data_mn);
            }else{
                DetailLang::create($data_mn);
            }
        }

        //tamil
        $data_ta['language'] = 'tamil';
        $data_ta['topic_id'] = $module->id;
        $exist = DetailLang::where($data_ta)->first();
        if($request->input('title_ta') != '' || ($exist && $exist->exists)){
            $data_ta['title'] = $request->input('title_ta');
            $data_ta['content'] = trim(preg_replace('#<script(.*?)>(.*?)</script>#is', '', $request->input('content_ta')));
            $data_ta['author'] = $request->input('author_ta');
            if($exist && $exist->exists){
                $exist->update($data_ta);
            }else{
                DetailLang::create($data_ta);
            }
        }

        //thai
        $data_th['language'] = 'thai';
        $data_th['topic_id'] = $module->id;
        $exist = DetailLang::where($data_th)->first();
        if($request->input('title_th') != '' || ($exist && $exist->exists)){
            $data_th['title'] = $request->input('title_th');
            $data_th['content'] = trim(preg_replace('#<script(.*?)>(.*?)</script>#is', '', $request->input('content_th')));
            $data_th['author'] = $request->input('author_th');
            if($exist && $exist->exists){
                $exist->update($data_th);
            }else{
                DetailLang::create($data_th);
            }
        }

        return redirect()->route('admin.jtc.detail.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Detail entry updated successfully.',
        ]);

    }

    public function getTopicDelete($id)
    {
        Detail::destroy($id);

        $auth_user = Auth::user();
        Activity::log('Deleted a detail entry #'.@$id. ' by '.$auth_user->name);

        return redirect()->route('admin.jtc.detail.list')->with([
            'flash_level'   => 'danger',
            'flash_message' => 'Detail entry deleted',
        ]);
    }

    public function postTopicDelete($id)
    {
        Detail::delete($id);
        return redirect()->route('admin.jtc.detail.list')->with([
          'flash_level'   => 'danger',
          'flash_message' => 'Detail entry deleted',
        ]);

    }
}
