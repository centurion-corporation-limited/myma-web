<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Models\Pages;
use App\Models\PageLang;
use Illuminate\Http\Request;
use App\Models\Option;

use App\Http\Requests;
use App\Http\Requests\AddPageRequest;
use App\Http\Requests\EditPageRequest;
use App\Http\Controllers\Controller;
use Auth, Activity;

class PageController extends Controller
{
    public function getList(Request $request)
    {
      $auth_user = Auth::user();

      $items = Pages::where('id', '!=', '4');

      if ($name = $request->input('name')) {
          $name = strtolower($name);
          $items->whereHas('lang_content', function($q) use($name){
              $q->whereRaw('lower(`title`) like ?', array("%{$name}%"));
          });
      }
      $limit = 10;
      $items = $items->sortable(['id' => 'desc'])->paginate($limit);
      $paginate_data = $request->except('page');

      return view('admin.page.list', compact('items', 'auth_user','paginate_data'));
    }


    public function getAdd()
    {
        $auth_user = \Auth::user();
        $languages = array(
            'english' => 'English',
            'bengali' => 'Bengali',
            'mandarin' => 'Chinese',
            'tamil' => 'Tamil',
            'thai' => 'Thai'
        );
        return view('admin.page.add', compact('auth_user', 'languages'));
    }

    public function postAdd(AddPageRequest $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $data = $request->only('language', 'style', 'script');

        $module = Pages::create($data);

        Activity::log('Created new Page #'.@$module->id. ' by '.$auth_user->name);

        if($request->title != ''){
            $dd_bn['language'] = 'english';
            $dd_bn['title'] = $request->title;
            $dd_bn['page_id'] = $module->id;
            $dd_bn['content'] = $request->content;

            PageLang::create($dd_bn);
        }

        if($request->title_bn != ''){
            $dd_bn['language'] = 'bengali';
            $dd_bn['title'] = $request->title_bn;
            $dd_bn['page_id'] = $module->id;
            $dd_bn['content'] = $request->content_bn;

            PageLang::create($dd_bn);
        }
        if($request->title_mn != ''){
            $dd_en['language'] = 'mandarin';
            $dd_en['title'] = $request->title_mn;
            $dd_en['page_id'] = $module->id;
            $dd_en['content'] = $request->content_mn;

            PageLang::create($dd_en);
        }
        if($request->title_th != ''){
            $dd_en['language'] = 'thai';
            $dd_en['title'] = $request->title_th;
            $dd_en['page_id'] = $module->id;
            $dd_en['content'] = $request->content_th;

            PageLang::create($dd_en);
        }
        if($request->title_ta != ''){
            $dd_en['language'] = 'tamil';
            $dd_en['title'] = $request->title_ta;
            $dd_en['page_id'] = $module->id;
            $dd_en['content'] = $request->content_ta;

            PageLang::create($dd_en);
        }

        return redirect()->route('admin.page.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Page added successfully.',
        ]);

    }
    public function getEdit($id, Request $request)
    {
        $id = decrypt($id);
		$auth_user = \Auth::user();
		$item = Pages::findOrFail($id);
        // $profile = $user->profile;
        $languages = array(
            'english' => 'English',
            'bengali' => 'Bengali',
            'mandarin' => 'Chinese',
            'tamil' => 'Tamil',
            'thai' => 'Thai'
        );

        return view('admin.page.edit', compact('item', 'languages'));
    }

    public function postEdit($id, EditPageRequest $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $id = decrypt($id);
        $module = Pages::findOrFail($id);

        $data = $request->only('language', 'style', 'script');

        $module->update($data);
        Activity::log('Updated page information #'.@$id. ' by '.$auth_user->name);

        $dd_en['language'] = 'english';
        $dd_bn['language'] = 'bengali';
        $dd_mn['language'] = 'mandarin';
        $dd_th['language'] = 'thai';
        $dd_ta['language'] = 'tamil';

        $dd_en['page_id'] = $module->id;
        $dd_bn['page_id'] = $module->id;
        $dd_mn['page_id'] = $module->id;
        $dd_th['page_id'] = $module->id;
        $dd_ta['page_id'] = $module->id;

        $exist = PageLang::where($dd_en)->first();
        if($request->title != ''){
            $dd_en['title'] = $request->title;
            $dd_en['content'] = $request->content;
            if($exist && $exist->exists){
                $exist->update($dd_en);
            }else{
                PageLang::create($dd_en);
            }
        }else{
            if($exist && $exist->exists && $request->title == '' && $request->content == ''){
                $exist->delete();
            }
        }

        $exist = PageLang::where($dd_bn)->first();
        if($request->title_bn != ''){
            $dd_bn['title'] = $request->title_bn;
            $dd_bn['content'] = $request->content_bn;
            if($exist && $exist->exists){
                $exist->update($dd_bn);
            }else{
                PageLang::create($dd_bn);
            }
        }else{
            if($exist && $exist->exists && $request->title_bn == '' && $request->content_bn == ''){
                $exist->delete();
            }
        }

        $exist = PageLang::where($dd_mn)->first();
        if($request->title_mn != ''){
            $dd_bn['title'] = $request->title_mn;
            $dd_bn['content'] = $request->content_mn;
            if($exist && $exist->exists){
                $exist->update($dd_mn);
            }else{
                PageLang::create($dd_mn);
            }
        }else{
            if($exist && $exist->exists && $request->title_mn == '' && $request->content_mn == ''){
                $exist->delete();
            }
        }

        $exist = PageLang::where($dd_mn)->first();
        if($request->title_mn != ''){
            $dd_bn['title'] = $request->title_mn;
            $dd_bn['content'] = $request->content_mn;
            if($exist && $exist->exists){
                $exist->update($dd_mn);
            }else{
                PageLang::create($dd_mn);
            }
        }else{
            if($exist && $exist->exists && $request->title_mn == '' && $request->content_mn == ''){
                $exist->delete();
            }
        }

        $exist = PageLang::where($dd_ta)->first();
        if($request->title_ta != ''){
            $dd_bn['title'] = $request->title_ta;
            $dd_bn['content'] = $request->content_ta;
            if($exist && $exist->exists){
                $exist->update($dd_ta);
            }else{
                PageLang::create($dd_ta);
            }
        }else{
            if($exist && $exist->exists && $request->title_ta == '' && $request->content_ta == ''){
                $exist->delete();
            }
        }

        $exist = PageLang::where($dd_th)->first();
        if($request->title_th != ''){
            $dd_bn['title'] = $request->title_th;
            $dd_bn['content'] = $request->content_th;
            if($exist && $exist->exists){
                $exist->update($dd_th);
            }else{
                PageLang::create($dd_th);
            }
        }else{
            if($exist && $exist->exists && $request->title_th == '' && $request->content_th == ''){
                $exist->delete();
            }
        }

        return redirect()->route('admin.page.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Page details updated successfully.',
        ]);

    }

    public function getFlexm()
    {
        $auth_user = \Auth::user();
        return view('admin.page.flexm', compact('auth_user'));
    }

    public function getMWC()
    {
        $auth_user = \Auth::user();
        return view('admin.page.mwc', compact('auth_user'));
    }

    public function postMWC(Request $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();

        $flag = false;
        foreach ($request->input('options') as $key => $value) {
            $flag = true;
            if (is_array($value)) {
                $value = serialize($value);
            }
            if($value != '')
                $value = addhttp($value);

            Option::setOption($key, $value);
        }
        if($flag)
            Activity::log('Updated MWC links by '.$auth_user->name);

        return redirect()->back()->with([
            'flash_level'   => 'success',
            'flash_message' => 'Links updated successfully.',
        ]);

    }

    public function getLinks()
    {
        $auth_user = \Auth::user();
        return view('admin.page.links', compact('auth_user'));
    }

    public function postLinks(Request $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();

        $flag = false;
        foreach ($request->input('options') as $key => $value) {
            $flag = true;
            if (is_array($value)) {
                $value = serialize($value);
            }
            if($value != '')
                $value = addhttp($value);

            Option::setOption($key, $value);
        }
        if($flag)
            Activity::log('Updated ASPRI link '.$auth_user->name);

        return redirect()->back()->with([
            'flash_level'   => 'success',
            'flash_message' => 'Links updated successfully.',
        ]);

    }

    public function getDelete($id)
    {
        Pages::destroy($id);
        $auth_user = Auth::user();
        Activity::log('Deleted page #'.@$id. ' by '.$auth_user->name);

        return redirect()->route('admin.page.list')->with([
            'flash_level'   => 'danger',
            'flash_message' => 'Page Deleted',
        ]);
    }

    public function postDelete($id)
    {
        Pages::delete($id);
        return redirect()->route('admin.page.list')->with([
          'flash_level'   => 'danger',
          'flash_message' => 'Page Deleted',
        ]);

    }
}
