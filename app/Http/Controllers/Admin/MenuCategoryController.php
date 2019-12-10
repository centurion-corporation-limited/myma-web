<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Models\MenuCategory;
use App\Models\Type;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\AddCategoryRequest;
use App\Http\Requests\EditCategoryRequest;
use App\Http\Controllers\Controller;
use Auth, Activity;

class MenuCategoryController extends Controller
{
    public function getList(Request $request)
    {
      $auth_user = Auth::user();
      $items = MenuCategory::query();

      if ($name = $request->input('name')) {
          $name = strtolower($name);
          $items->whereRaw('lower(`name`) like ?', array("%{$name}%"));
      }

      $limit = 10;
      $items = $items->sortable(['id' => 'desc'])->paginate($limit);
      $paginate_data = $request->except('page');

      return view('admin.menu.category.list', compact('items', 'auth_user', 'paginate_data'));
    }


    public function getAdd()
    {
        $auth_user = \Auth::user();
        return view('admin.menu.category.add', compact('auth_user'));
    }

    public function postAdd(Request $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $data = $request->only('name','name_mn','name_bn','name_ta', 'name_th');

        $module = MenuCategory::create($data);
        // Activity::log('Deleted advertisement #'.$id. ' by '.$auth_user->name);

        return redirect()->route('admin.menu.category.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Menu Category added successfully.',
        ]);

    }
    public function getEdit($id, Request $request)
    {
    		$auth_user = \Auth::user();
            $id = decrypt($id);
    		$item = MenuCategory::findOrFail($id);
            
            return view('admin.menu.category.edit', compact('item'));
    }

    public function postEdit($id, Request $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $id = decrypt($id);
        $module = MenuCategory::findOrFail($id);

        $data = $request->only('name','name_mn','name_bn','name_ta', 'name_th');

        $module->update($data);

        return redirect()->route('admin.menu.category.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Menu Category updated successfully.',
        ]);

    }

    public function getDelete($id)
    {
        MenuCategory::destroy($id);

        return redirect()->route('admin.menu.category.list')->with([
            'flash_level'   => 'danger',
            'flash_message' => 'Category Deleted',
        ]);
    }

    public function postDelete($id)
    {
        MenuCategory::delete($id);
        return redirect()->route('admin.menu.category.list')->with([
          'flash_level'   => 'danger',
          'flash_message' => 'Category Deleted',
        ]);

    }
}
