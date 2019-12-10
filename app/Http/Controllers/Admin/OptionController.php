<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Models\Option;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;

class OptionController extends Controller
{
    public function getList(Request $request)
    {
      $auth_user = Auth::user();

      $items = Option::orderBy('id');

      if ($id = $request->input('id')) {
          $items->where('id', $id);
      }

      if ($username = $request->input('username')) {
          $items->where(function ($q) use ($username) {
              $q->where('name', 'like', "%{$username}%")
                  ->orWhere('email', 'like', "%{$username}%");
          });
      }

      $items = $items->get();

      return view('admin.option.list', compact('items', 'auth_user'));
    }


    public function getAdd()
    {
        $auth_user = \Auth::user();

        return view('admin.option.add', compact('auth_user'));
    }

    public function postAdd(Request $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $data = $request->only('name', 'value');

        $module = Option::create($data);

        return redirect()->route('admin.option.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Number added successfully.',
        ]);

    }
    public function getEdit($id, Request $request)
    {
    		$auth_user = \Auth::user();

    		$item = Option::findOrFail($id);
        // $profile = $user->profile;

        return view('admin.option.edit', compact('item'));
    }

    public function postEdit($id, Request $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();

        $module = Option::findOrFail($id);

        $data = $request->only('name', 'value');

        $module->update($data);

        return redirect()->route('admin.option.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Number updated successfully.',
        ]);

    }

    public function getDelete($id)
    {
        Option::destroy($id);

        return redirect()->route('admin.option.list')->with([
            'flash_level'   => 'danger',
            'flash_message' => 'Number Deleted',
        ]);
    }

    public function postDelete($id)
    {
        Option::delete($id);
        return redirect()->route('admin.option.list')->with([
          'flash_level'   => 'danger',
          'flash_message' => 'Number Deleted',
        ]);

    }
}
