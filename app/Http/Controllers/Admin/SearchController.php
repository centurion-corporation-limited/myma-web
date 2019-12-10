<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Models\Search;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;

class SearchController extends Controller
{
    public function getList(Request $request)
    {
      $auth_user = Auth::user();

      $items = Search::query();

      if ($name = $request->input('word')) {
          $name = strtolower($name);
          $items->whereRaw('lower(`word`) like ?', array("%{$name}%"));
      }

      $limit = 10;
      $items = $items->paginate($limit);
      $paginate_data = $request->except('page');

      return view('admin.search.list', compact('items', 'auth_user', 'paginate_data'));
    }

    public function getEdit($id, Request $request)
    {
    		$auth_user = \Auth::user();
    		$item = Search::findOrFail($id);

        return view('admin.search.edit', compact('item'));
    }

    public function postEdit($id, Request $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();

        $module = Search::findOrFail($id);

        $data = $request->only('title');

        $module->update($data);

        return redirect()->route('admin.search.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Topic details updated successfully.',
        ]);

    }

    public function getDelete($id)
    {
        Search::destroy($id);

        return redirect()->route('admin.search.list')->with([
            'flash_level'   => 'danger',
            'flash_message' => 'Search Item Deleted',
        ]);
    }

    public function postDelete($id)
    {
        Search::delete($id);
        return redirect()->route('admin.search.list')->with([
          'flash_level'   => 'danger',
          'flash_message' => 'Search Item Deleted',
        ]);

    }
}
