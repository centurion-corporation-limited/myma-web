<?php

namespace App\Http\Controllers\Admin\JTC;

use App\User;
use App\Models\Menu;
use App\Models\JTC\Center;
use App\Models\JTC\CenterLang;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\JTC\AddCenterRequest;
use App\Http\Requests\JTC\EditCenterRequest;
use App\Http\Controllers\Controller;
use Auth, Activity;

class CenterController extends Controller
{
    public function getCategoryList(Request $request)
    {
      $auth_user = Auth::user();
      $items = Center::query();

      $type = $request->input('type');
      if($type == ''){
        $type = 'jtc';
      }

      $main = '';
      $main = Menu::where('slug', $type)->first();

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
      // if(!$auth_user->hasRole('admin')){
      //     $items->where('created_by', $auth_user->id);
      // }
      $limit = 10;
      $items = $items->sortable(['id' => 'desc'])->paginate($limit);
      $paginate_data = $request->except('page');

      return view('admin.jtc.center.list', compact('items', 'auth_user', 'paginate_data', 'main'));
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

        $type = request('type');
        if($type == ''){
          $type = 'jtc';
        }
        $main = '';
        $main = Menu::where('slug', $type)->first();

        return view('admin.jtc.center.add', compact('auth_user', 'languages', 'main'));
    }

    public function postCategoryAdd(AddCenterRequest $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $data = $request->only('language', 'type');

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
              $folder = "files/jtc/center/";

              $path = savePhoto($file, $folder, $type);
              $data['image'] = $path;
          } else {
              throw new \Exception('did not match data URI with image data');
          }
        }
        $module = Center::create($data);

        Activity::log('CMS Main category created #'.@$module->id. ' by '.$auth_user->name);

        if($request->title != ''){
            $dd_en['language'] = 'english';
            $dd_en['title'] = $request->title;
            $dd_en['center_id'] = $module->id;

            CenterLang::create($dd_en);
        }
        if($request->title_bn != ''){
            $dd_bn['language'] = 'bengali';
            $dd_bn['title'] = $request->title_bn;
            $dd_bn['center_id'] = $module->id;

            CenterLang::create($dd_bn);
        }
        if($request->title_mn != ''){
            $dd_en['language'] = 'mandarin';
            $dd_en['title'] = $request->title_mn;
            $dd_en['center_id'] = $module->id;

            CenterLang::create($dd_en);
        }
        if($request->title_th != ''){
            $dd_en['language'] = 'thai';
            $dd_en['title'] = $request->title_th;
            $dd_en['center_id'] = $module->id;

            CenterLang::create($dd_en);
        }
        if($request->title_ta != ''){
            $dd_en['language'] = 'tamil';
            $dd_en['title'] = $request->title_ta;
            $dd_en['center_id'] = $module->id;

            CenterLang::create($dd_en);
        }
        return redirect()->route('admin.jtc.centers.list', ['type' => $data['type']])->with([
            'flash_level'   => 'success',
            'flash_message' => 'Main Category added successfully.',
        ]);

    }
    public function getCategoryEdit($id, Request $request)
    {
      	$auth_user = \Auth::user();
        $id = decrypt($id);
      	$item = Center::findOrFail($id);
        $languages = array(
            'english' => 'English',
            'bengali' => 'Bengali',
            'mandarin' => 'Chinese',
            'tamil' => 'Tamil',
            'thai' => 'Thai'
        );

        $type = $request->input('type');
        if($type == ''){
          $type = 'jtc';
        }
        $main = '';
        $main = Menu::where('slug', $type)->first();

        return view('admin.jtc.center.edit', compact('item', 'languages', 'main'));
    }

    public function postCategoryEdit($id, EditCenterRequest $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $id = decrypt($id);
        $module = Center::findOrFail($id);
        $data = $request->only('language', 'active', 'type');

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
              $folder = "files/jtc/center/";

              $path = savePhoto($file, $folder, $type);
              $data['image'] = $path;
          } else {
              throw new \Exception('did not match data URI with image data');
          }
        }

        $module->update($data);
        Activity::log('CMS Main category updated #'.@$id. ' by '.$auth_user->name);

        $dd_en['language'] = 'english';
        $dd_bn['language'] = 'bengali';
        $dd_mn['language'] = 'mandarin';
        $dd_th['language'] = 'thai';
        $dd_ta['language'] = 'tamil';

        $dd_en['center_id'] = $module->id;
        $dd_bn['center_id'] = $module->id;
        $dd_mn['center_id'] = $module->id;
        $dd_th['center_id'] = $module->id;
        $dd_ta['center_id'] = $module->id;

        $exist = CenterLang::where($dd_en)->first();
        if($request->title != ''){
            $dd_en['title'] = $request->title;
            if($exist && $exist->exists){
                $exist->update($dd_en);
            }else{
                CenterLang::create($dd_en);
            }
        }else{
            if($exist && $exist->exists && $request->title == ''){
                $exist->delete();
            }
        }
        $exist = CenterLang::where($dd_bn)->first();
        if($request->title_bn != ''){
            $dd_bn['title'] = $request->title_bn;
            if($exist && $exist->exists){
                $exist->update($dd_bn);
            }else{
                CenterLang::create($dd_bn);
            }
        }else{
            if($exist && $exist->exists && $request->title_bn == ''){
                $exist->delete();
            }
        }
        $exist = CenterLang::where($dd_mn)->first();
        if($request->title_mn != ''){
            $dd_mn['title'] = $request->title_mn;
            if($exist && $exist->exists){
                $exist->update($dd_mn);
            }else{
                CenterLang::create($dd_mn);
            }
        }else{
            if($exist && $exist->exists && $request->title_mn == ''){
                $exist->delete();
            }
        }
        $exist = CenterLang::where($dd_th)->first();
        if($request->title_th != ''){
            $dd_th['title'] = $request->title_th;
            if($exist && $exist->exists){
                $exist->update($dd_th);
            }else{
                CenterLang::create($dd_th);
            }
        }else{
            if($exist && $exist->exists && $request->title_th == ''){
                $exist->delete();
            }
        }
        $exist = CenterLang::where($dd_ta)->first();
        if($request->title_ta != ''){
            $dd_ta['title'] = $request->title_ta;
            if($exist && $exist->exists){
                $exist->update($dd_ta);
            }else{
                CenterLang::create($dd_ta);
            }
        }else{
            if($exist && $exist->exists && $request->title_ta == ''){
                $exist->delete();
            }
        }

        return redirect()->route('admin.jtc.centers.list', ['type' => $data['type']])->with([
            'flash_level'   => 'success',
            'flash_message' => 'Main Category details updated successfully.',
        ]);

    }

    public function getCategoryDelete($id)
    {
        Center::destroy($id);

        $auth_user = Auth::user();
        Activity::log('Deleted JTC Center #'.@$id. ' by '.$auth_user->name);

        return redirect()->route('admin.jtc.centers.list')->with([
            'flash_level'   => 'danger',
            'flash_message' => 'Main Category Deleted',
        ]);
    }

    public function postCategoryDelete($id)
    {
        Center::delete($id);
        return redirect()->route('admin.jtc.centers.list')->with([
          'flash_level'   => 'danger',
          'flash_message' => 'Main Category  Deleted',
        ]);

    }
}
