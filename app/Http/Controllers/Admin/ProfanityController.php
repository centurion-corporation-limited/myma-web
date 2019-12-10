<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Models\Badwords;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\AddWordRequest;
use App\Http\Requests\EditWordRequest;
use App\Http\Controllers\Controller;
use Auth;

class ProfanityController extends Controller
{
    public function getList(Request $request)
    {
      $auth_user = Auth::user();

      $items = Badwords::query();

      if ($name = $request->input('word')) {
          $name = strtolower($name);
          $items->whereRaw('lower(`word`) like ?', array("%{$name}%"));
      }
      $limit = 10;
      $items = $items->sortable(['id' => 'desc'])->paginate($limit);
      $paginate_data = $request->except('page');

      return view('admin.words.list', compact('items', 'auth_user', 'paginate_data'));
    }


    public function getAdd()
    {
        $auth_user = \Auth::user();
        // $vendors = User::whereHas('roles', function($q){
    	// 			$q->where('slug', 'admin');
    	// 		})->orderBy('id', 'desc')->pluck('name','id');

        return view('admin.words.add', compact('auth_user'));
    }

    public function postAdd(AddWordRequest $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $data = $request->only('word', 'language');
        $data['word'] = strtolower($data['word']);
        $module = Badwords::create($data);

        return redirect()->route('admin.words.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Word added successfully.',
        ]);

    }
    public function getEdit($id, Request $request)
    {
        $id = decrypt($id);
    	$auth_user = \Auth::user();
    	$item = Badwords::findOrFail($id);

        return view('admin.words.edit', compact('item'));
    }

    public function postEdit($id, EditWordRequest $request)
    {
        $id = decrypt($id);
        /** @var User $item */
        $auth_user = \Auth::user();

        $module = Badwords::findOrFail($id);

        $data = $request->only('word', 'langugae');
        $data['word'] = strtolower($data['word']);
        // dd($data);
        $module->update($data);

        return redirect()->route('admin.words.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Word updated successfully.',
        ]);

    }

    public function getDelete($id)
    {
        Badwords::destroy($id);

        return redirect()->route('admin.words.list')->with([
            'flash_level'   => 'danger',
            'flash_message' => 'Word Deleted',
        ]);
    }

    public function postDelete($id)
    {
        Badwords::delete($id);
        return redirect()->route('admin.words.list')->with([
          'flash_level'   => 'danger',
          'flash_message' => 'Word Deleted',
        ]);

    }
}
