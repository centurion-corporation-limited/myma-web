<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\AddRoleRequest;
use App\Http\Requests\EditRoleRequest;
use App\Http\Controllers\Controller;
use Auth;

class RoleController extends Controller
{
    public function getList(Request $request)
    {
      $auth_user = Auth::user();
      // $user = Auth::user();
      // $perm = $user->getPermissions();
      // echo "<pre>";print_r($user->can('user-add'));die();
      if ($request->input('sort') != '') {
        $sort_by = $request->input('sort');
      }else{
        $sort_by = 'id';
      }

      if ($request->input('order') != '') {
        $order = $request->input('order');
      }else{
        $order = 'asc';
      }

      $not_in = ['restaurant-owner-catering', 'restaurant-owner-single'];

      $items = Role::whereNotIn('slug', $not_in)->orderBy($sort_by, $order);

      if ($name = $request->input('name')) {
          $items->where('name', 'like',"%{$name}%");
      }

      $limit = 10;
      $items = $items->paginate($limit);
      $paginate_data = $request->except('page');

      return view('admin.role.list', compact('items', 'auth_user', 'paginate_data'));
    }


    public function getAdd()
    {
        $auth_user = \Auth::user();
        $permissions = Permission::orderBy('type', 'asc')->get();
        return view('admin.role.add', compact('auth_user', 'permissions'));
    }

    public function postAdd(AddRoleRequest $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $data = $request->only('name', 'description');
        $data['slug'] = str_slug($data['name']);
        $role = Role::create($data);

        $permissions = $request->only('permission');

        if(count($permissions['permission'])){
            $role->syncPermissions($permissions['permission']);
        }
        return redirect()->route('admin.role.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Role added successfully.',
        ]);

    }
    public function getEdit($id, Request $request)
    {
    	$auth_user = \Auth::user();
        $id = decrypt($id);
    	$item = Role::findOrFail($id);
        $permissions = Permission::orderBy('type', 'asc')->get();

        return view('admin.role.edit', compact('item','permissions', 'auth_user'));
    }

    public function getView($id, Request $request)
    {
    	$auth_user = \Auth::user();
        $id = decrypt($id);
    	$item = Role::findOrFail($id);
        $permissions = Permission::orderBy('name', 'asc')->get();

        return view('admin.role.view', compact('item','permissions', 'auth_user'));
    }

    public function postEdit($id, EditRoleRequest $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $id = decrypt($id);
        $role = Role::findOrFail($id);
        $data = $request->only('name', 'description');
        // $data['slug'] = str_slug($data['name']);

        $role->update($data);

        // $role = Role::first();

        $permissions = $request->only('permission');

        if(count($permissions['permission'])){
            // $permissions = $request->only('permission');
            // $val = array_values($permissions['permission']);
            // echo "<pre>";print_r($permissions['permission']);die();
            $role->syncPermissions($permissions['permission']);
        }

        return redirect()->route('admin.role.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Role details updated successfully.',
        ]);

    }

    public function getDelete($id)
    {
        Role::destroy($id);

        return redirect()->route('admin.role.list')->with([
            'flash_level'   => 'danger',
            'flash_message' => 'Role Deleted',
        ]);
    }

    public function postDelete($id)
    {
        Role::delete($id);
        return redirect()->route('admin.role.list')->with([
          'flash_level'   => 'danger',
          'flash_message' => 'Role Deleted',
        ]);

    }
}
