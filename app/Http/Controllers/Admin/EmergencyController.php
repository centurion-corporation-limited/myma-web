<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Models\Emergency;
use App\Models\Category;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\AddEmergencyRequest;
use App\Http\Requests\EditEmergencyRequest;
use App\Http\Controllers\Controller;
use Auth,Activity;
use Illuminate\Contracts\Encryption\DecryptException;

class EmergencyController extends Controller
{
    public function getList(Request $request)
    {
      $auth_user = Auth::user();

      $items = Emergency::with('category');

      if ($name = $request->input('name')) {
          $name = strtolower($name);
          $items->whereRaw('lower(`name`) like ?', array("%{$name}%"))
          ->orWhereRaw('lower(`name_bn`) like ?', array("%{$name}%"))
          ->orWhereRaw('lower(`name_mn`) like ?', array("%{$name}%"))
          ->orWhereRaw('lower(`name_ta`) like ?', array("%{$name}%"))
          ->orWhereRaw('lower(`name_th`) like ?', array("%{$name}%"))
          ->orWhereRaw('lower(`value`) like ?', array("%{$name}%"))
          ;
      }

      $category_id = 0;
      if ($id = $request->input('category_id')) {
          try{
            $category_id = $id = decrypt($id);
          }catch(DecryptException $e){

          }
          $items->where('category_id', $id);
      }

      $categories = Category::pluck('name', 'id')->toArray();

      array_unshift($categories, 'Please select a category');

      $limit = 10;
      $items = $items->sortable(['id' => 'desc'])->paginate($limit);
      $paginate_data = $request->except('page');

      return view('admin.emergency.list', compact('items', 'auth_user', 'paginate_data', 'categories', 'category_id'));
    }


    public function getAdd()
    {
        $auth_user = \Auth::user();
        $categories = Category::pluck('name','id');
        return view('admin.emergency.add', compact('auth_user', 'categories'));
    }

    public function postAdd(AddEmergencyRequest $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $data = $request->only('name','name_mn','name_bn','name_ta','name_th', 'value','category_id');

        $module = Emergency::create($data);

        return redirect()->route('admin.emergency.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Emergency added successfully.',
        ]);

    }
    public function getEdit($id, Request $request)
    {
        $id = decrypt($id);
    		$auth_user = \Auth::user();
    		$item = Emergency::findOrFail($id);
            $categories = Category::pluck('name', 'id');

        return view('admin.emergency.edit', compact('item', 'categories'));
    }

    public function postEdit($id, EditEmergencyRequest $request)
    {
        $id = decrypt($id);
        /** @var User $item */
        $auth_user = \Auth::user();

        $module = Emergency::findOrFail($id);

        $data = $request->only('name','name_mn','name_bn','name_ta','name_th', 'value','category_id');

        $module->update($data);

        return redirect()->route('admin.emergency.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Emergency details updated successfully.',
        ]);

    }

    public function getDelete($id)
    {
        Emergency::destroy($id);
        $auth_user = Auth::user();
        Activity::log('Deleted emergency no #'.$id. ' by '.$auth_user->name);

        return redirect()->route('admin.emergency.list')->with([
            'flash_level'   => 'danger',
            'flash_message' => 'Emergency Deleted',
        ]);
    }

    public function postDelete($id)
    {
        Emergency::delete($id);
        return redirect()->route('admin.emergency.list')->with([
          'flash_level'   => 'danger',
          'flash_message' => 'Emergency Deleted',
        ]);

    }
}
