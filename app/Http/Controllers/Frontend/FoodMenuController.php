<?php

namespace App\Http\Controllers\Frontend;

use App\User;
use App\Models\FoodMenu;
use App\Models\FoodCourse;
use App\Models\FoodPackage;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;

class FoodMenuController extends Controller
{
    public function getList(Request $request)
    {
      $auth_user = Auth::user();

      $items = FoodMenu::orderBy('id');

      if ($id = $request->input('id')) {
          $items->where('id', $id);
      }

      $items = $items->get();

      return view('frontend.food.menu.list', compact('items', 'auth_user'));
    }


    public function getAdd()
    {
        $auth_user = \Auth::user();
        $users = User::where('blocked', '0')->pluck('email','id');

        return view('frontend.food.menu.add', compact('auth_user', 'users'));
    }

    public function postAdd(Request $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $data = $request->only('name', 'merchant_id', 'open_at', 'closes_at');
        
        $module = FoodMenu::create($data);
        // if($module){
        //     $user = User::findOrFail($data['manager_id']);
        //     $user->addPermission('view.maintenance-list');
        //
        // }

        return redirect()->route('frontend.food_menu.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Item added successfully.',
        ]);

    }

    public function getView($id, Request $request)
    {
		$auth_user = \Auth::user();
		$item = FoodMenu::findOrFail($id);
        $users = User::where('blocked', '0')->pluck('email','id');

        return view('frontend.food.menu.view', compact('item', 'users'));
    }

    public function getEdit($id, Request $request)
    {
    	$auth_user = \Auth::user();
    	$item = FoodMenu::findOrFail($id);
        // $status = Status::pluck('name', 'id');
        $users = User::where('blocked', '0')->pluck('email','id');

        return view('frontend.food.menu.edit', compact('item', 'users'));
    }

    public function postEdit($id, Request $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $module = FoodMenu::findOrFail($id);

        $data = $request->only('name', 'merchant_id', 'open_at', 'closes_at');

        $up = $module->update($data);

        // if($up){
        //     $user = User::findOrFail($data['manager_id']);
        //     $user->addPermission('view.maintenance-list');
        // }

        return redirect()->route('frontend.food_menu.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Item updated successfully.',
        ]);

    }

    public function getDelete($id)
    {
        FoodMenu::destroy($id);

        return redirect()->route('frontend.food_menu.list')->with([
            'flash_level'   => 'danger',
            'flash_message' => 'Item Deleted',
        ]);
    }


    public function getPackageList(Request $request)
    {
      $auth_user = Auth::user();

      $items = FoodPackage::orderBy('id');

      if ($id = $request->input('id')) {
          $items->where('id', $id);
      }

      $items = $items->get();

      return view('frontend.food.package.list', compact('items', 'auth_user'));
    }


    public function getCategoryAdd()
    {
        $auth_user = \Auth::user();
        $users = User::where('blocked', '0')->pluck('email','id');

        return view('frontend.food.package.add', compact('auth_user', 'users'));
    }

    public function postCategoryAdd(Request $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $data = $request->only('name');

        $module = FoodPackage::create($data);
        // if($module){
        //     $user = User::findOrFail($data['manager_id']);
        //     $user->addPermission('view.maintenance-list');
        //
        // }

        return redirect()->route('frontend.food_category.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Package created successfully.',
        ]);

    }

    public function getCategoryView($id, Request $request)
    {
		$auth_user = \Auth::user();
		$item = FoodPackage::findOrFail($id);
        $users = User::where('blocked', '0')->pluck('email','id');

        return view('frontend.food.package.view', compact('item', 'users'));
    }

    public function getCategoryEdit($id, Request $request)
    {
    	$auth_user = \Auth::user();
    	$item = FoodPackage::findOrFail($id);
        // $status = Status::pluck('name', 'id');
        $users = User::where('blocked', '0')->pluck('email','id');

        return view('frontend.food.package.edit', compact('item', 'users'));
    }

    public function postCategoryEdit($id, Request $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $module = FoodPackage::findOrFail($id);

        $data = $request->only('name');

        $up = $module->update($data);

        // if($up){
        //     $user = User::findOrFail($data['manager_id']);
        //     $user->addPermission('view.maintenance-list');
        // }

        return redirect()->route('frontend.food_category.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Package updated successfully.',
        ]);

    }

    public function getCategoryDelete($id)
    {
        FoodPackage::destroy($id);

        return redirect()->route('frontend.food_category.list')->with([
            'flash_level'   => 'danger',
            'flash_message' => 'Package Deleted',
        ]);
    }
}
