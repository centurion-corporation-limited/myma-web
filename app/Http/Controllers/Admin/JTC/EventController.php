<?php

namespace App\Http\Controllers\Admin\JTC;

use App\User;
use App\Models\JTC\Category;
use App\Models\JTC\Event;
use App\Models\JTC\EventLang;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\JTC\AddEventRequest;
use App\Http\Requests\JTC\EditEventRequest;
use App\Http\Controllers\Controller;
use Auth, Activity;

class EventController extends Controller
{
    public function getCategoryList(Request $request)
    {
      $auth_user = Auth::user();
      $items = Event::query();

      $type = $request->input('type');
      if($type == ''){
        $type = 'jtc';
      }

      if ($type = $request->input('type')) {
          $type = strtolower($type);
          $items->where('type', $type);
      }

      if ($name = $request->input('name')) {
          $name = strtolower($name);
          $items->whereHas('lang_content', function($q) use($name){
              $q->whereRaw('lower(`title`) like ?', array("%{$name}%"));
          });
      }
      $category = '';
      if ($category_id = $request->input('category_id')) {
          $category_id = decrypt($category_id);
          $category = Category::find($category_id);
          $items->where('category_id', $category_id);
      }

      $limit = 10;
      $items = $items->sortable(['id' => 'desc'])->paginate($limit);
      $paginate_data = $request->except('page');

      return view('admin.jtc.event.list', compact('items', 'auth_user', 'paginate_data', 'category'));
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

        $category = [];
        $jtc_centers = Category::where('active', '1')->get();
        foreach($jtc_centers as $cent){
            if($cent->content($cent->language)->first()){
                $title = $cent->content($cent->language)->first()->title;
            }else{
                $title = $cent->lang_content()->first()->title;
            }
            if(@$cent->main->content($cent->language)->first()){
              $title .= ' ('.$cent->main->content($cent->language)->first()->title.')';
            }
            $category[$cent->id] = $title;
        }

        return view('admin.jtc.event.add', compact('auth_user', 'languages', 'category'));
    }

    public function postCategoryAdd(AddEventRequest $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $data = $request->only('language','type', 'category_id');
        $data['created_by'] = $auth_user->id;
        if($data['type'] == ''){
          $data['type'] = 'jtc';
        }
        $cat = Category::find($data['category_id']);
        if($cat){
          $data['type'] = $cat->type;
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
              $folder = "files/jtc/event";

              $path = savePhoto($file, $folder, $type);
              $data['image'] = $path;
          } else {
              throw new \Exception('did not match data URI with image data');
          }
        }
        $module = Event::create($data);

        Activity::log('CMS Sub category 2 created #'.@$module->id. ' by '.$auth_user->name);

        if($request->title != ''){
            $dd_en['language'] = 'english';
            $dd_en['title'] = $request->title;
            $dd_en['event_id'] = $module->id;

            EventLang::create($dd_en);
        }
        if($request->title_bn != ''){
            $dd_bn['language'] = 'bengali';
            $dd_bn['title'] = $request->title_bn;
            $dd_bn['event_id'] = $module->id;

            EventLang::create($dd_bn);
        }
        if($request->title_mn != ''){
            $dd_en['language'] = 'mandarin';
            $dd_en['title'] = $request->title_mn;
            $dd_en['event_id'] = $module->id;

            EventLang::create($dd_en);
        }
        if($request->title_th != ''){
            $dd_en['language'] = 'thai';
            $dd_en['title'] = $request->title_th;
            $dd_en['event_id'] = $module->id;

            EventLang::create($dd_en);
        }
        if($request->title_ta != ''){
            $dd_en['language'] = 'tamil';
            $dd_en['title'] = $request->title_ta;
            $dd_en['event_id'] = $module->id;

            EventLang::create($dd_en);
        }
        return redirect()->route('admin.jtc.event.list', ['category_id' => encrypt(@$data['category_id'])])->with([
            'flash_level'   => 'success',
            'flash_message' => 'Sub Category added successfully.',
        ]);

    }
    public function getCategoryEdit($id, Request $request)
    {
      	$auth_user = \Auth::user();
        $id = decrypt($id);
      	$item = Event::findOrFail($id);
        $languages = array(
            'english' => 'English',
            'bengali' => 'Bengali',
            'mandarin' => 'Chinese',
            'tamil' => 'Tamil',
            'thai' => 'Thai'
        );
        $category = [];
        $jtc_centers = Category::where('active', '1')->get();
        foreach($jtc_centers as $cent){
            if($cent->content($cent->language)->first()){
                $title = $cent->content($cent->language)->first()->title;
            }else{
                $title = $cent->lang_content()->first()->title;
            }
            if(@$cent->main->content($cent->language)->first()){
              $title .= ' ('.$cent->main->content($cent->language)->first()->title.')';
            }
            $category[$cent->id] = $title;
        }
        return view('admin.jtc.event.edit', compact('item', 'languages', 'category'));
    }

    public function postCategoryEdit($id, EditEventRequest $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $id = decrypt($id);
        $module = Event::findOrFail($id);
        $data = $request->only('language', 'active', 'type', 'category_id');

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
              $folder = "files/jtc/event";

              $path = savePhoto($file, $folder, $type);
              $data['image'] = $path;
          } else {
              throw new \Exception('did not match data URI with image data');
          }
        }

        $module->update($data);
        Activity::log('CMS Sub category 2 updated #'.@$id. ' by '.$auth_user->name);

        $dd_en['language'] = 'english';
        $dd_bn['language'] = 'bengali';
        $dd_mn['language'] = 'mandarin';
        $dd_th['language'] = 'thai';
        $dd_ta['language'] = 'tamil';

        $dd_en['event_id'] = $module->id;
        $dd_bn['event_id'] = $module->id;
        $dd_mn['event_id'] = $module->id;
        $dd_th['event_id'] = $module->id;
        $dd_ta['event_id'] = $module->id;

        $exist = EventLang::where($dd_en)->first();
        if($request->title != ''){
            $dd_en['title'] = $request->title;
            if($exist && $exist->exists){
                $exist->update($dd_en);
            }else{
                EventLang::create($dd_en);
            }
        }else{
            if($exist && $exist->exists && $request->title == ''){
                $exist->delete();
            }
        }
        $exist = EventLang::where($dd_bn)->first();
        if($request->title_bn != ''){
            $dd_bn['title'] = $request->title_bn;
            if($exist && $exist->exists){
                $exist->update($dd_bn);
            }else{
                EventLang::create($dd_bn);
            }
        }else{
            if($exist && $exist->exists && $request->title_bn == ''){
                $exist->delete();
            }
        }
        $exist = EventLang::where($dd_mn)->first();
        if($request->title_mn != ''){
            $dd_mn['title'] = $request->title_mn;
            if($exist && $exist->exists){
                $exist->update($dd_mn);
            }else{
                EventLang::create($dd_mn);
            }
        }else{
            if($exist && $exist->exists && $request->title_mn == ''){
                $exist->delete();
            }
        }
        $exist = EventLang::where($dd_th)->first();
        if($request->title_th != ''){
            $dd_th['title'] = $request->title_th;
            if($exist && $exist->exists){
                $exist->update($dd_th);
            }else{
                EventLang::create($dd_th);
            }
        }else{
            if($exist && $exist->exists && $request->title_th == ''){
                $exist->delete();
            }
        }
        $exist = EventLang::where($dd_ta)->first();
        if($request->title_ta != ''){
            $dd_ta['title'] = $request->title_ta;
            if($exist && $exist->exists){
                $exist->update($dd_ta);
            }else{
                EventLang::create($dd_ta);
            }
        }else{
            if($exist && $exist->exists && $request->title_ta == ''){
                $exist->delete();
            }
        }

        return redirect()->route('admin.jtc.event.list', ['category_id' => encrypt(@$data['category_id'])])->with([
            'flash_level'   => 'success',
            'flash_message' => 'Sub category updated successfully.',
        ]);

    }

    public function getCategoryDelete($id)
    {
        Event::destroy($id);

        $auth_user = Auth::user();
        Activity::log('Deleted Sub category 2 #'.@$id. ' by '.$auth_user->name);

        return redirect()->route('admin.jtc.event.list')->with([
            'flash_level'   => 'danger',
            'flash_message' => 'Sub category Deleted',
        ]);
    }

    public function postCategoryDelete($id)
    {
        Event::delete($id);
        return redirect()->route('admin.jtc.event.list')->with([
          'flash_level'   => 'danger',
          'flash_message' => 'Sub category Deleted',
        ]);

    }
}
