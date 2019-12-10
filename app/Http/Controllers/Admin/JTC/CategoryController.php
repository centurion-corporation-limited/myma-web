<?php

namespace App\Http\Controllers\Admin\JTC;

use App\User;
use App\Models\JTC\Center;
use App\Models\JTC\Category;
use App\Models\JTC\CategoryLang as CategoryLang;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\JTC\AddCategoryRequest;
use App\Http\Requests\JTC\EditCategoryRequest;
use App\Http\Controllers\Controller;
use Auth, Activity;

class CategoryController extends Controller
{
    public function getCategoryList(Request $request)
    {
      $auth_user = Auth::user();
      $items = Category::query();

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
      $center = '';
      if ($center_id = $request->input('center_id')) {
          $center_id = decrypt($center_id);
          $center = Center::find($center_id);
          $items->where('center_id', $center_id);
      }
      // if(!$auth_user->hasRole('admin')){
      //     $items->where('created_by', $auth_user->id);
      // }
      $limit = 10;
      $items = $items->sortable(['id' => 'desc'])->paginate($limit);
      $paginate_data = $request->except('page');

      return view('admin.jtc.category.list', compact('items', 'auth_user', 'paginate_data', 'center'));
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

        $centers = [];
        $jtc_centers = Center::where('active', '1')->get();
        foreach($jtc_centers as $cent){
            if($cent->content($cent->language)->first()){
                $centers[$cent->id] = $cent->content($cent->language)->first()->title;
            }else{
                $centers[$cent->id] = $cent->lang_content()->first()->title;
            }
        }

        return view('admin.jtc.category.add', compact('auth_user', 'languages', 'centers'));
    }

    public function postCategoryAdd(AddCategoryRequest $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $data = $request->only('language', 'type', 'center_id');
        $data['created_by'] = $auth_user->id;
        if($data['type'] == ''){
          $data['type'] = 'jtc';
        }
        $center = Center::find($data['center_id']);
        if($center){
          $data['type'] = $center->type;
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
              $folder = "files/jtc/category";

              $path = savePhoto($file, $folder, $type);
              $data['image'] = $path;
          } else {
              throw new \Exception('did not match data URI with image data');
          }
        }
        $module = Category::create($data);

        Activity::log('CMS Sub category created #'.@$module->id. ' by '.$auth_user->name);

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
        return redirect()->route('admin.jtc.category.list', ['center_id' => encrypt($data['center_id'])])->with([
            'flash_level'   => 'success',
            'flash_message' => 'Sub Category added successfully.',
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

        $centers = [];
        $jtc_centers = Center::where('active', '1')->get();
        foreach($jtc_centers as $cent){
            if($cent->content($cent->language)->first()){
                $centers[$cent->id] = $cent->content($cent->language)->first()->title;
            }else{
                $centers[$cent->id] = $cent->lang_content()->first()->title;
            }
        }

        return view('admin.jtc.category.edit', compact('item', 'languages', 'centers'));
    }

    public function postCategoryEdit($id, EditCategoryRequest $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $id = decrypt($id);
        $module = Category::findOrFail($id);
        $data = $request->only('language', 'active', 'type', 'center_id');

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
              $folder = "files/jtc/category";

              $path = savePhoto($file, $folder, $type);
              $data['image'] = $path;
          } else {
              throw new \Exception('did not match data URI with image data');
          }
        }

        $module->update($data);
        Activity::log('CMS Sub Category updated #'.@$id. ' by '.$auth_user->name);

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

        return redirect()->route('admin.jtc.category.list', ['center_id' => encrypt($data['center_id'])])->with([
            'flash_level'   => 'success',
            'flash_message' => 'Sub Category details updated successfully.',
        ]);

    }

    public function getCategoryDelete($id)
    {
        Category::destroy($id);

        $auth_user = Auth::user();
        Activity::log('Deleted JTC Category #'.@$id. ' by '.$auth_user->name);

        return redirect()->route('admin.jtc.category.list')->with([
            'flash_level'   => 'danger',
            'flash_message' => 'Sub Category Deleted',
        ]);
    }

    public function postCategoryDelete($id)
    {
        Category::delete($id);
        return redirect()->route('admin.jtc.category.list')->with([
          'flash_level'   => 'danger',
          'flash_message' => 'Sub Category Deleted',
        ]);

    }
}
