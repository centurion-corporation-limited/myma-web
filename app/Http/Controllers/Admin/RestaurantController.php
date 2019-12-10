<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Models\Restaurant;
use App\Models\FoodCategory;
use App\Models\FoodCategoryLang;
use App\Models\FoodCourse;
use App\Models\FoodMerchant;
use App\Models\Role;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\AddRestraRequest;
use App\Http\Requests\AddFoodCatRequest;
use App\Http\Requests\AddFoodCourseRequest;
use App\Http\Requests\EditRestraRequest;
use App\Http\Requests\EditFoodCatRequest;
use App\Http\Requests\EditFoodCourseRequest;
use App\Http\Controllers\Controller;
use Auth, Activity;
use Illuminate\Support\Facades\Crypt;

use Illuminate\Contracts\Encryption\DecryptException;

class RestaurantController extends Controller
{
    public function getList(Request $request)
    {
      $auth_user = Auth::user();

      $items = Restaurant::query();
      if ($id = $request->input('id')) {
          $items->where('id', $id);
      }
      $limit = 50;
      $items = $items->sortable(['id' => 'desc'])->paginate($limit);
      $paginate_data = $request->except('page');

      return view('admin.food.restaurant.list', compact('items', 'auth_user', 'paginate_data', 'limit'));
    }


    public function getAdd()
    {
        $auth_user = \Auth::user();
        $users = User::whereHas('roles', function($q){
            $q->whereIn('slug', ['restaurant-owner-package', 'restaurant-owner-single'] );
        })->where('blocked', '0')->pluck('email','id');

        // $roles = Role::whereIn('id', ['5','8'])->pluck('name', 'id');
        $roles = [
          '5' => 'Catering',
          '8' => 'Food Outlet'
        ];

        return view('admin.food.restaurant.add', compact('auth_user', 'users', 'roles'));
    }

    public function postAdd(AddRestraRequest $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $data = $request->only('name', 'open_at', 'closes_at', 'gst_no', 'phone_no', 'address', 'longitude', 'latitude'
        ,'nea_number','bank_name','bank_number');
        $data['open_at'] = date('H:i:s', strtotime($data['open_at']));
        $data['closes_at'] = date('H:i:s', strtotime($data['closes_at']));

        $data_user = $request->only('user_name', 'email', 'password');

        $searchValue = $data_user['email'];
        $items = User::all()->filter(function($record) use($searchValue) {
                    $email = $record->email;
                    try{
                        $email = Crypt::decrypt($email);
                    }catch(DecryptException $e){

                    }
                    if(($email) == $searchValue) {
                        return $record;
                    }
        });
        if(count($items)){
            return redirect()->back()->withInput($request->input())->withErrors([
                'email' => 'The email has already been taken.',
            ]);
        }

        // $exist = User::where('email', $data_user['email'])->get();
        // if($exist->count()){
        //     $user = $exist->first();
        //     if($role = $request->input('role_id')){
        //       $user->assignRole($role);
        //     }
        // }else{
            $data_user['name'] = $data_user['user_name'];
            $data_user['password'] = bcrypt($data_user['password']);
            $data['register_by'] = 'food_admin';
            $user = User::create($data_user);
            // Activity::log('New user created by food admin for restaurant - #'.$user->id.' by '. $auth_user->name );

            if($role = $request->input('role_id')){
              $user->assignRole($role);
            }
        // }
        $data['merchant_id'] = $user->id;
        $module = Restaurant::create($data);

        FoodMerchant::create(['user_id' => $user->id]);

        return redirect()->route('admin.restaurant.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Restaurant added successfully.',
        ]);

    }

    public function getView($id, Request $request)
    {
		    $auth_user = \Auth::user();
        $id = decrypt($id);
        $item = Restaurant::findOrFail($id);
        $users = User::whereHas('roles', function($q){
            $q->whereIn('slug', ['restaurant-owner-package', 'restaurant-owner-single'] );
        })->where('blocked', '0')->pluck('email','id');

        return view('admin.food.restaurant.view', compact('item', 'users'));
    }

    public function getEdit($id, Request $request)
    {
    	  $auth_user = \Auth::user();
        $id = decrypt($id);
    	  $item = Restaurant::findOrFail($id);
        // $status = Status::pluck('name', 'id');
        $users = User::whereHas('roles', function($q){
            $q->whereIn('slug', ['restaurant-owner-package', 'restaurant-owner-single'] );
        })->where('blocked', '0')->pluck('email','id');
        // $roles = Role::whereIn('id', ['5','8'])->pluck('name', 'id');

        $roles = [
          '5' => 'Catering',
          '8' => 'Food Outlet'
        ];

        if($item){
            $item->role_id = current(array_keys($item->merchant->getRoles()));
        }
        return view('admin.food.restaurant.edit', compact('item', 'users', 'roles'));
    }

    public function postEdit($id, EditRestraRequest $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $id = decrypt($id);
        $module = Restaurant::findOrFail($id);

        $data = $request->only('name', 'open_at', 'closes_at', 'gst_no', 'phone_no', 'address', 'longitude', 'latitude', 'blocked'
        ,'nea_number','bank_name','bank_number');

        $data['open_at'] = date('H:i:s', strtotime($data['open_at']));
        $data['closes_at'] = date('H:i:s', strtotime($data['closes_at']));

        $up = $module->update($data);

        $data_user = $request->only('user_name', 'password');
        $exist = User::where('id', $module->merchant_id)->get();
        if($exist->count()){
            $user = $exist->first();
            $data_user['name'] = $data_user['user_name'];
            if($data_user['password'] != ''){
                $data_user['password'] = bcrypt($data_user['password']);
            }else{
                unset($data_user['password']);
            }
            $user->update($data_user);
            Activity::log('User detail updated by food admin for restaurant - #'.$user->id.' by '. $auth_user->name );
        }else{

        }
        // if($up){
        //     $user = User::findOrFail($data['manager_id']);
        //     $user->addPermission('view.maintenance-list');
        // }

        return redirect()->route('admin.restaurant.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Restaurant details updated successfully.',
        ]);

    }

    public function getDelete($id)
    {
        $id = decrypt($id);
        Restaurant::destroy($id);

        return redirect()->route('admin.restaurant.list')->with([
            'flash_level'   => 'danger',
            'flash_message' => 'Restaurant Deleted',
        ]);
    }


    public function getCategoryList(Request $request)
    {
      $auth_user = Auth::user();

      $items = FoodCategory::query();

      if ($id = $request->input('id')) {
          $items->where('id', $id);
      }

      $limit = 50;
      $items = $items->sortable(['id' => 'desc'])->paginate($limit);
      $paginate_data = $request->except('page');

      return view('admin.food.category.list', compact('items', 'auth_user', 'paginate_data', 'limit'));
    }


    public function getCategoryAdd()
    {
        $auth_user = \Auth::user();
        $users = User::where('blocked', '0')->pluck('email','id');

        return view('admin.food.category.add', compact('auth_user', 'users'));
    }

    public function postCategoryAdd(AddFoodCatRequest $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $data = $request->only('name', 'approved');
        $data['slug'] = str_slug($data['name']);
        $module = FoodCategory::create($data);

        if($request->name_mn != ''){
            $dd['name'] = $request->name_mn;
            $dd['category_id'] = $module->id;
            $dd['language'] = 'mandarin';
            FoodCategoryLang::create($dd);
        }

        if($request->name_bn != ''){
            $dd['name'] = $request->name_bn;
            $dd['category_id'] = $module->id;
            $dd['language'] = 'bengali';
            FoodCategoryLang::create($dd);
        }

        if($request->name_ta != ''){
            $dd['name'] = $request->name_ta;
            $dd['category_id'] = $module->id;
            $dd['language'] = 'tamil';
            FoodCategoryLang::create($dd);
        }

        if($request->name_th != ''){
            $dd['name'] = $request->name_th;
            $dd['category_id'] = $module->id;
            $dd['language'] = 'thai';
            FoodCategoryLang::create($dd);
        }

        return redirect()->route('admin.food_category.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Category added successfully.',
        ]);

    }

    public function getCategoryView($id, Request $request)
    {
        $id = decrypt($id);
		$auth_user = \Auth::user();
		$item = FoodCategory::findOrFail($id);
        $users = User::where('blocked', '0')->pluck('email','id');

        return view('admin.food.category.view', compact('item', 'users'));
    }

    public function getCategoryEdit($id, Request $request)
    {
        $id = decrypt($id);
    	$auth_user = \Auth::user();
    	$item = FoodCategory::findOrFail($id);
        // $status = Status::pluck('name', 'id');
        $users = User::where('blocked', '0')->pluck('email','id');

        return view('admin.food.category.edit', compact('item', 'users'));
    }

    public function postCategoryEdit($id, EditFoodCatRequest $request)
    {
        $id = decrypt($id);
        /** @var User $item */
        $auth_user = \Auth::user();
        $module = FoodCategory::findOrFail($id);

        $data = $request->only('name', 'approved');
        $data['slug'] = str_slug($data['name']);
        $up = $module->update($data);

        $dd['category_id'] = $id;
        $dd['language'] = 'mandarin';
        $exist = FoodCategoryLang::where($dd)->first();

        if($request->name_mn != '' || ($exist && $exist->exists)){
            $dd['name'] = $request->name_mn;
            if($exist && $exist->exists){
                $exist->update($dd);
            }else{
                FoodCategoryLang::create($dd);
            }
        }
        unset($dd['name']);
        $dd['category_id'] = $id;
        $dd['language'] = 'bengali';
        $exist = FoodCategoryLang::where($dd)->first();

        if($request->name_bn != '' || ($exist && $exist->exists)){
            $dd['name'] = $request->name_bn;
            if($exist && $exist->exists){
                $exist->update($dd);
            }else{
                FoodCategoryLang::create($dd);
            }
        }
        unset($dd['name']);
        $dd['category_id'] = $id;
        $dd['language'] = 'tamil';
        $exist = FoodCategoryLang::where($dd)->first();
        if($request->name_ta != '' || ($exist && $exist->exists)){
            $dd['name'] = $request->name_ta;
            if($exist && $exist->exists){
                $exist->update($dd);
            }else{
                FoodCategoryLang::create($dd);
            }
        }
        unset($dd['name']);
        $dd['category_id'] = $id;
        $dd['language'] = 'thai';
        $exist = FoodCategoryLang::where($dd)->first();
        if($request->name_th != '' || ($exist && $exist->exists)){
            $dd['name'] = $request->name_th;
            if($exist && $exist->exists){
                $exist->update($dd);
            }else{
                FoodCategoryLang::create($dd);
            }
        }

        return redirect()->route('admin.food_category.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Category updated successfully.',
        ]);

    }

    public function getCategoryDelete($id)
    {
        $id = decrypt($id);
        FoodCategory::destroy($id);

        return redirect()->route('admin.food_category.list')->with([
            'flash_level'   => 'danger',
            'flash_message' => 'Category Deleted',
        ]);
    }


    public function getCourseList(Request $request)
    {
      $auth_user = Auth::user();

      $items = FoodCourse::query();

      if ($id = $request->input('id')) {
          $items->where('id', $id);
      }

      $limit = 50;
      $items = $items->sortable(['id' => 'desc'])->paginate($limit);
      $paginate_data = $request->except('page');

      return view('admin.food.course.list', compact('items', 'auth_user', 'paginate_data', 'limit'));
    }


    public function getCourseAdd()
    {
        $auth_user = \Auth::user();
        $users = User::where('blocked', '0')->pluck('email','id');

        return view('admin.food.course.add', compact('auth_user', 'users'));
    }

    public function postCourseAdd(AddFoodCourseRequest $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $data = $request->only('name');

        $module = FoodCourse::create($data);
        // if($module){
        //     $user = User::findOrFail($data['manager_id']);
        //     $user->addPermission('view.maintenance-list');
        //
        // }

        return redirect()->route('admin.food_course.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Course added successfully.',
        ]);

    }

    public function getCourseView($id, Request $request)
    {
        $id = decrypt($id);
		$auth_user = \Auth::user();
		$item = FoodCourse::findOrFail($id);
        $users = User::where('blocked', '0')->pluck('email','id');

        return view('admin.food.course.view', compact('item', 'users'));
    }

    public function getCourseEdit($id, Request $request)
    {
        $id = decrypt($id);
    	$auth_user = \Auth::user();
    	$item = FoodCourse::findOrFail($id);
        // $status = Status::pluck('name', 'id');
        $users = User::where('blocked', '0')->pluck('email','id');

        return view('admin.food.course.edit', compact('item', 'users'));
    }

    public function postCourseEdit($id, EditFoodCourseRequest $request)
    {
        $id = decrypt($id);
        /** @var User $item */
        $auth_user = \Auth::user();
        $module = FoodCourse::findOrFail($id);

        $data = $request->only('name');

        $up = $module->update($data);

        // if($up){
        //     $user = User::findOrFail($data['manager_id']);
        //     $user->addPermission('view.maintenance-list');
        // }

        return redirect()->route('admin.food_course.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Course updated successfully.',
        ]);

    }

    public function getCourseDelete($id)
    {
        $id = decrypt($id);
        FoodCourse::destroy($id);

        return redirect()->route('admin.food_course.list')->with([
            'flash_level'   => 'danger',
            'flash_message' => 'Course Deleted',
        ]);
    }


}
