<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Models\Permission;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;

class PermissionController extends Controller
{
    public function getList(Request $request)
    {
      $auth_user = Auth::user();

      $items = Permission::orderBy('type', 'asc');

      if ($id = $request->input('id')) {
          $items->where('id', $id);
      }

      $items = $items->get();

      return view('admin.permission.list', compact('items', 'auth_user'));
    }


    public function getAdd()
    {
        $auth_user = \Auth::user();

        return view('admin.permission.add', compact('auth_user'));
    }

    public function postAdd(Request $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $data = $request->only('name', 'description', 'permissions');
        $data['slug'] = [          // pass an array of permissions.
            'create'     => isset($data['permissions']['add'])?true:false,
            'view'       => isset($data['permissions']['view'])?true:false,
            'update'     => isset($data['permissions']['update'])?true:false,
            'delete'     => isset($data['permissions']['delete'])?true:false,

        ];
        $data['title'] = $data['name'];
        $data['name'] = str_slug($data['name']);

        $module = Permission::create($data);

        return redirect()->route('admin.permission.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Permission added successfully.',
        ]);

    }
    public function getEdit($id, Request $request)
    {
    		$auth_user = \Auth::user();

    		$item = Permission::findOrFail($id);
        return view('admin.permission.edit', compact('item'));
    }

    public function postEdit($id, Request $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();

        $module = Permission::findOrFail($id);
        $data = $request->only('name', 'description', 'permissions');
        $data['slug'] = [          // pass an array of permissions.
            'create'     => isset($data['permissions']['create'])?true:false,
            'view'       => isset($data['permissions']['view'])?true:false,
            'update'     => isset($data['permissions']['update'])?true:false,
            'delete'     => isset($data['permissions']['delete'])?true:false,

        ];
        $data['title'] = $data['name'];
        $data['name'] = str_slug($data['name']);
        $module->update($data);

        return redirect()->route('admin.permission.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Permission details updated successfully.',
        ]);

    }

    public function getDelete($id)
    {
        Permission::destroy($id);

        return redirect()->route('admin.permission.list')->with([
            'flash_level'   => 'danger',
            'flash_message' => 'Permission Deleted',
        ]);
    }

    public function postDelete($id)
    {
        Permission::delete($id);
        return redirect()->route('admin.permission.list')->with([
          'flash_level'   => 'danger',
          'flash_message' => 'Permission Deleted',
        ]);

    }
}
