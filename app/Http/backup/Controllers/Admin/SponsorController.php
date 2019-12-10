<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Models\Sponsor;

use App\Models\Type;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\AddSponsorRequest;
use App\Http\Requests\EditSponsorRequest;
use App\Http\Controllers\Controller;
use Auth, Activity;

class SponsorController extends Controller
{
    public function getList(Request $request)
    {
      $auth_user = Auth::user();
      $items = Sponsor::query();

      if ($name = $request->input('sponsor_id')) {
          $items->where('id', $name);
      }
      $sponsors = Sponsor::pluck('name','id');
      $limit = 10;
      $items = $items->sortable(['id' => 'desc'])->paginate($limit);
      $paginate_data = $request->except('page');

      return view('admin.sponsor.list', compact('items', 'auth_user', 'paginate_data', 'sponsors'));
    }


    public function getAdd()
    {
        $auth_user = \Auth::user();
        return view('admin.sponsor.add', compact('auth_user'));
    }

    public function postAdd(AddSponsorRequest $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $data = $request->only('name','phone','email','address');

        $module = Sponsor::create($data);
        // Activity::log('Deleted advertisement #'.$id. ' by '.$auth_user->name);

        return redirect()->route('admin.sponsor.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Sponsor added successfully.',
        ]);

    }

    public function getView($id, Request $request)
    {
    		$auth_user = \Auth::user();
        $id = decrypt($id);
    		$item = Sponsor::findOrFail($id);

        return view('admin.sponsor.view', compact('item'));
    }

    public function getEdit($id, Request $request)
    {
    		$auth_user = \Auth::user();
        $id = decrypt($id);
    		$item = Sponsor::findOrFail($id);

        return view('admin.sponsor.edit', compact('item'));
    }

    public function postEdit($id, EditSponsorRequest $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $id = decrypt($id);
        $module = Sponsor::findOrFail($id);

        $data = $request->only('name','phone','email','address');

        $module->update($data);
        return redirect()->route('admin.sponsor.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Sponsor details updated successfully.',
        ]);

    }

    public function getDelete($id)
    {
        Sponsor::destroy($id);

        return redirect()->route('admin.sponsor.list')->with([
            'flash_level'   => 'danger',
            'flash_message' => 'Sponsor Deleted',
        ]);
    }

    public function postDelete($id)
    {
        Sponsor::delete($id);
        return redirect()->route('admin.sponsor.list')->with([
          'flash_level'   => 'danger',
          'flash_message' => 'Sponsor Deleted',
        ]);

    }
}
