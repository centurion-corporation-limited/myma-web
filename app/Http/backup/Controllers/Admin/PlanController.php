<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Models\Advertisement;
use App\Models\Plans;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\AddPlanRequest;
use App\Http\Requests\EditPlanRequest;
use App\Http\Controllers\Controller;
use Auth;

class PlanController extends Controller
{
    public function getList(Request $request)
    {
      $auth_user = Auth::user();

      $items = Plans::orderBy('type', 'desc');

      // if ($id = $request->input('id')) {
      //     $items->where('id', $id);
      // }

      $limit = 10;
      $items = $items->paginate($limit);
      $paginate_data = $request->except('page');

      return view('admin.plans.list', compact('items', 'auth_user', 'paginate_data'));
    }


    public function getAdd()
    {
        $auth_user = \Auth::user();
        // $vendors = User::whereHas('roles', function($q){
    	// 			$q->where('slug', 'admin');
    	// 		})->orderBy('id', 'desc')->pluck('name','id');

        return view('admin.plans.add', compact('auth_user', 'vendors'));
    }

    public function postAdd(AddPlanRequest $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $data = $request->only('type', 'price', 'impressions');

        $module = Plans::create($data);

        return redirect()->route('admin.advertisement.plan.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Plan added successfully.',
        ]);

    }
    public function getEdit($id, Request $request)
    {
    	$auth_user = \Auth::user();
        $id = decrypt($id);
        $vendors = User::whereHas('roles', function($q){
        				$q->where('slug', 'admin');
        			})->orderBy('id', 'desc')->pluck('name','id');

    	$item = Plans::findOrFail($id);

        return view('admin.plans.edit', compact('item', 'vendors'));
    }

    public function postEdit($id, EditPlanRequest $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $id = decrypt($id);
        $module = Plans::findOrFail($id);

        if($module->ads->count() == 0){
            $data = $request->only('type', 'price', 'impressions');
            $module->update($data);
            return redirect()->route('admin.advertisement.plan.list')->with([
                'flash_level'   => 'success',
                'flash_message' => 'Plan details updated successfully.',
            ]);
        }else{
            return redirect()->route('admin.advertisement.plan.list')->with([
                'flash_level'   => 'error',
                'flash_message' => "Plan can't be updated as this has been used by other advertisements.",
            ]);
        }


    }

    public function getDelete($id)
    {
        Plans::destroy($id);

        return redirect()->route('admin.advertisement.plan.list')->with([
            'flash_level'   => 'danger',
            'flash_message' => 'Plan Deleted',
        ]);
    }

    public function postDelete($id)
    {
        Plans::delete($id);
        return redirect()->route('admin.advertisement.plan.list')->with([
          'flash_level'   => 'danger',
          'flash_message' => 'Plan Deleted',
        ]);

    }
}
