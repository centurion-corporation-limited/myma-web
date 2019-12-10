<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Models\Category;
use App\Models\Type;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\AddCategoryRequest;
use App\Http\Requests\EditCategoryRequest;
use App\Http\Controllers\Controller;
use Auth, Activity;

class CategoryController extends Controller
{
    public function getList(Request $request)
    {
      $auth_user = Auth::user();
      $items = Category::query();

      if ($name = $request->input('name')) {
          $name = strtolower($name);
          $items->whereRaw('lower(`name`) like ?', array("%{$name}%"));
      }

      $type_id = $request->input('type_id');
      if ($type_id != '' && $type_id != '0') {
          $items->where('type_id', $type_id);
      }
      $types = Type::pluck('name', 'id')->toArray();

      array_unshift($types, 'Please select a type');

      $limit = 10;
      $items = $items->sortable(['id' => 'desc'])->paginate($limit);
      $paginate_data = $request->except('page');

      return view('admin.category.list', compact('items', 'auth_user', 'paginate_data', 'types', 'type_id'));
    }


    public function getAdd()
    {
        $auth_user = \Auth::user();
        $types = Type::pluck('name', 'id')->all();
        return view('admin.category.add', compact('auth_user', 'types'));
    }

    public function postAdd(AddCategoryRequest $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $data = $request->only('name','name_mn','name_bn','name_ta', 'name_th', 'type_id');

        $module = Category::create($data);
        // Activity::log('Deleted advertisement #'.$id. ' by '.$auth_user->name);

        return redirect()->route('admin.category.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Category added successfully.',
        ]);

    }
    public function getEdit($id, Request $request)
    {
    		$auth_user = \Auth::user();
            $id = decrypt($id);
    		$item = Category::findOrFail($id);
            $types = Type::pluck('name', 'id')->all();

        return view('admin.category.edit', compact('item', 'types'));
    }

    public function postEdit($id, EditCategoryRequest $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $id = decrypt($id);
        $module = Category::findOrFail($id);

        $data = $request->only('name','name_mn','name_bn','name_ta', 'name_th', 'type_id');

        $module->update($data);

        return redirect()->route('admin.category.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Category  details updated successfully.',
        ]);

    }

    public function getDelete($id)
    {
        Category::destroy($id);

        return redirect()->route('admin.category.list')->with([
            'flash_level'   => 'danger',
            'flash_message' => 'Category Deleted',
        ]);
    }

    public function postDelete($id)
    {
        Category::delete($id);
        return redirect()->route('admin.category.list')->with([
          'flash_level'   => 'danger',
          'flash_message' => 'Category Deleted',
        ]);

    }
}
