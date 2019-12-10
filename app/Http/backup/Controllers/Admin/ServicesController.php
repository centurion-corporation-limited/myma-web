<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Models\Servicess;
use App\Models\Dormitory;
use App\Models\ServicesLang;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\ServiceRequest;
use App\Http\Requests\AddServiceRequest;
use App\Http\Requests\EditServiceRequest;
use App\Http\Controllers\Controller;
use Auth, Activity;

class ServicesController extends Controller
{
    public function getList(Request $request)
    {
      $auth_user = Auth::user();

      $items = Servicess::withCount('comments');

      if ($name = $request->input('name')) {
          $name = strtolower($name);
          $items->whereHas('lang_content', function($q) use($name){
              $q->whereRaw('lower(`title`) like ?', array("%{$name}%"));
          });
      }
      $items->where('type', '!=', 'mom');

      if ($type = $request->input('type')) {
          $type = strtolower($type);
          if($type == 'embassy')
            $items->where('type', 'custom3');
          else if($type == 'non-dorm')
            $items->where(function($q){
              $q->whereNull('dormitory_id')
              ->orWhere('dormitory_id', '0');
              })->where('type', 'event-news');
          else
            $items->where(function($q){
              $q->whereNotNull('dormitory_id')
              ->where('dormitory_id', '!=', '0');
            })->where('type', 'event-news');
      }

      $limit = 10;
      $items = $items->sortable(['id' => 'desc'])->paginate($limit);
      $paginate_data = $request->except('page');

      foreach($items as $item){
          $lang = $item->language;
          $ser = ServicesLang::where('services_id', $item->id)->where('language', $lang)->first();
          if($ser){
              $item->title = $ser->title;
              $item->author = $ser->author;
          }
      }

      return view('admin.services.list', compact('items', 'auth_user', 'paginate_data'));
    }


    public function getAdd()
    {
        $auth_user = \Auth::user();
        $users = User::pluck('name','id');

        $dorm = Dormitory::pluck('name' ,'id')->toArray();
        $dorm[0] = 'Select Dormitory';
        ksort($dorm);

        return view('admin.services.add', compact('auth_user', 'users', 'dorm'));
    }

    public function postAdd(Request $request)
    {
        $auth_user = \Auth::user();

        /** @var User $item */
        $data_main = $request->only('type', 'publish', 'language', 'dormitory_id');

        $data = $request->only('title','content','title_bn', 'content_bn','title_mn', 'content_mn',
        'title_th', 'content_th', 'title_ta', 'content_ta','author','author_bn','author_mn','author_ta', 'author_th');

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
        $data_main['user_id'] = $auth_user->id;

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
              $folder = "files/services/";

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
          $folder = "files/services/author";

          $path = uploadPhoto($file, $folder);
          $data_main['author_image'] = $path;
        }

        $module = Servicess::create($data_main);

        Activity::log('Created new service of type - '.@$data_main['type'].' #'.@$module->id. ' by '.$auth_user->name);
        //english
        if($request->input('title') != ''){
            $data_en['title'] = $request->input('title');
            $data_en['content'] = trim(preg_replace('#<script(.*?)>(.*?)</script>#is', '', $request->input('content')));
            $data_en['author'] = $request->input('author');
            $data_en['services_id'] = $module->id;
            $data_en['language'] = 'english';
            ServicesLang::create($data_en);
        }

        //bengali
        if($request->input('title_bn') != ''){
            $data_bn['title'] = $request->input('title_bn');
            $data_bn['content'] = trim(preg_replace('#<script(.*?)>(.*?)</script>#is', '', $request->input('content_bn')));
            $data_bn['author'] = $request->input('author_bn');
            $data_bn['services_id'] = $module->id;
            $data_bn['language'] = 'bengali';
            ServicesLang::create($data_bn);
        }

        //chinese
        if($request->input('title_mn') != ''){
            $data_mn['title'] = $request->input('title_mn');
            $data_mn['content'] = trim(preg_replace('#<script(.*?)>(.*?)</script>#is', '', $request->input('content_mn')));
            $data_mn['author'] = $request->input('author_mn');
            $data_mn['services_id'] = $module->id;
            $data_mn['language'] = 'mandarin';
            ServicesLang::create($data_mn);
        }

        //tamil
        if($request->input('title_ta') != ''){
            $data_ta['title'] = $request->input('title_ta');
            $data_ta['content'] = trim(preg_replace('#<script(.*?)>(.*?)</script>#is', '', $request->input('content_ta')));
            $data_ta['author'] = $request->input('author_ta');
            $data_ta['services_id'] = $module->id;
            $data_ta['language'] = 'tamil';
            ServicesLang::create($data_ta);
        }

        //thai
        if($request->input('title_th') != ''){
            $data_th['title'] = $request->input('title_th');
            $data_th['content'] = trim(preg_replace('#<script(.*?)>(.*?)</script>#is', '', $request->input('content_th')));
            $data_th['author'] = $request->input('author_th');
            $data_th['services_id'] = $module->id;
            $data_th['language'] = 'thai';
            ServicesLang::create($data_th);
        }

        return redirect()->route('admin.services.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Service added successfully.',
        ]);

    }
    public function getEdit($id, Request $request)
    {
        $id = decrypt($id);
    	$auth_user = \Auth::user();
    	$item = Servicess::findOrFail($id);
        $users = User::pluck('name','id');

        $dorm = Dormitory::pluck('name' ,'id')->toArray();
        $dorm[0] = 'Select Dormitory';
        ksort($dorm);

        return view('admin.services.edit', compact('item', 'users', 'dorm'));
    }

    public function getView($id, Request $request)
    {
        $id = decrypt($id);
    	$auth_user = \Auth::user();
    	$item = Servicess::findOrFail($id);
        $lang = ServicesLang::where('language', $item->language)->where('services_id', $id)->first();
        $users = User::pluck('name','id');

        return view('admin.services.view', compact('item', 'users', 'lang'));
    }

    public function postEdit($id, EditServiceRequest $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $id = decrypt($id);
        $module = Servicess::findOrFail($id);

        $data = $request->only('publish', 'type', 'dormitory_id', 'language');
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
              $folder = "files/services";

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
          $folder = "files/services";

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

        Activity::log('Updated service of type '.@$data['type'].' #'.@$id. ' by '.$auth_user->name);

        //english
        $data_en['language'] = 'english';
        $data_en['services_id'] = $module->id;
        $exist = ServicesLang::where($data_en)->first();
        if($request->input('title') != '' || ($exist && $exist->exists)){
            $data_en['title'] = $request->input('title');
            $data_en['content'] = trim(preg_replace('#<script(.*?)>(.*?)</script>#is', '', $request->input('content')));
            $data_en['author'] = $request->input('author');
            if($exist && $exist->exists){
                $exist->update($data_en);
            }else{
                ServicesLang::create($data_en);
            }
        }

        //bengali
        $data_bn['language'] = 'bengali';
        $data_bn['services_id'] = $module->id;
        $exist = ServicesLang::where($data_bn)->first();
        if($request->input('title_bn') != '' || ($exist && $exist->exists)){
            $data_bn['title'] = $request->input('title_bn');
            $data_bn['content'] = trim(preg_replace('#<script(.*?)>(.*?)</script>#is', '', $request->input('content_bn')));
            $data_bn['author'] = $request->input('author_bn');
            if($exist && $exist->exists){
                $exist->update($data_bn);
            }else{
                ServicesLang::create($data_bn);
            }
        }

        //chinese
        $data_mn['language'] = 'mandarin';
        $data_mn['services_id'] = $module->id;
        $exist = ServicesLang::where($data_mn)->first();
        if($request->input('title_mn') != '' || ($exist && $exist->exists)){
            $data_mn['title'] = $request->input('title_mn');
            $data_mn['content'] = trim(preg_replace('#<script(.*?)>(.*?)</script>#is', '', $request->input('content_mn')));
            $data_mn['author'] = $request->input('author_mn');
            if($exist && $exist->exists){
                $exist->update($data_mn);
            }else{
                ServicesLang::create($data_mn);
            }
        }

        //tamil
        $data_ta['language'] = 'tamil';
        $data_ta['services_id'] = $module->id;
        $exist = ServicesLang::where($data_ta)->first();
        if($request->input('title_ta') != '' || ($exist && $exist->exists)){
            $data_ta['title'] = $request->input('title_ta');
            $data_ta['content'] = trim(preg_replace('#<script(.*?)>(.*?)</script>#is', '', $request->input('content_ta')));
            $data_ta['author'] = $request->input('author_ta');
            if($exist && $exist->exists){
                $exist->update($data_ta);
            }else{
                ServicesLang::create($data_ta);
            }
        }

        //thai
        $data_th['language'] = 'thai';
        $data_th['services_id'] = $module->id;
        $exist = ServicesLang::where($data_th)->first();
        if($request->input('title_th') != '' || ($exist && $exist->exists)){
            $data_th['title'] = $request->input('title_th');
            $data_th['content'] = trim(preg_replace('#<script(.*?)>(.*?)</script>#is', '', $request->input('content_th')));
            $data_th['author'] = $request->input('author_th');
            if($exist && $exist->exists){
                $exist->update($data_th);
            }else{
                ServicesLang::create($data_th);
            }
        }

        return redirect()->route('admin.services.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Service details updated successfully.',
        ]);

    }

    public function getDelete($id)
    {
        Servicess::destroy($id);

        $auth_user = Auth::user();
        Activity::log('Deleted a service #'.@$id. ' by '.$auth_user->name);

        return redirect()->route('admin.services.list')->with([
            'flash_level'   => 'danger',
            'flash_message' => 'Service Deleted',
        ]);
    }

    public function postDelete($id)
    {
        Servicess::delete($id);
        return redirect()->route('admin.services.list')->with([
          'flash_level'   => 'danger',
          'flash_message' => 'Service Deleted',
        ]);

    }
}
