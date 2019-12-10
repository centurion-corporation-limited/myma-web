<?php

namespace App\Http\Controllers\Admin;

use App\Events\MaintenanceStatus;
use App\User;
use App\Models\Dormitory;
use App\Models\Maintenance;
use App\Models\Status;
use App\Models\File;
use Illuminate\Http\Request;
use App\Models\Notification;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth, Activity;

class MaintenanceController extends Controller
{
    public function getList(Request $request)
    {
      $auth_user = Auth::user();

      $items = Maintenance::query();

      if(!$auth_user->hasRole('admin')){
          if($auth_user->hasRole('dorm-maintainer') && $auth_user->dormitory_id != ''){
              $items->where('dormitory_id', $auth_user->dormitory_id);
          }else{
              $dormitories = Dormitory::where('manager_id', $auth_user->id)->pluck('id')->toArray();
              $items->whereIn('dormitory_id', $dormitories);
          }

      }

      if ($id = $request->input('id')) {
          $items->where('id', $id);
      }

      if ($username = strtolower($request->input('username'))) {
            $users = User::where('name', 'like', '%'.$username.'%')->pluck('id');
            $items->whereIn('user_id', $users);
      }

      $items = $items->sortable(['id' => 'desc'])->paginate(10);
      $paginate_data = $request->except('page');

      return view('admin.maintenance.list', compact('items', 'auth_user', 'paginate_data'));
    }

    public function exportPDF($id, Request $request)
    {
        $id = decrypt($id);
        $data['item'] = Maintenance::findOrFail($id);

  	    // return view('pdf.maintenance', $data);
        $name = str_slug($data['item']->id);
  	    $pdf = \PDF::loadView('pdf.maintenance', $data);
  	    return $pdf->download($name.'.pdf');
    }

    public function getAdd()
    {
        $auth_user = \Auth::user();

        return view('admin.maintenance.add', compact('auth_user'));
    }

    public function postAdd(Request $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $data = $request->only('title');

        $module = Maintenance::create($data);

        return redirect()->route('admin.maintenance.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Maintenance added successfully.',
        ]);

    }

    public function getView($id, Request $request)
    {
        $id = decrypt($id);
    	$auth_user = \Auth::user();
    	$item = Maintenance::findOrFail($id);
        if($item){
            $loc = translateLang($item->location);
            $locc = json_decode($loc);
            if(isset($locc->error)){
                $item->location_lang = '';
            }else{
                $item->location_lang = '';
            }

            $com = translateLang($item->comments);
            $comc = json_decode($com);
            if(isset($comc->error)){
                $item->comments_lang = '';
            }else{
                $item->comments_lang = '';
            }

            $rem = translateLang($item->remarks);
            $remc = json_decode($rem);
            if(isset($remc->error)){
                $item->remarks_lang = '';
            }else{
                $item->remarks_lang = '';
            }
        }
        return view('admin.maintenance.view', compact('item'));
    }

    public function getEdit($id, Request $request)
    {
        $id = decrypt($id);
    	$auth_user = \Auth::user();
    	$item = Maintenance::findOrFail($id);
        $status = Status::where('type', 'maintenance')->where('id', '>=', $item->status_id)->pluck('name', 'id');

        return view('admin.maintenance.edit', compact('item', 'status'));
    }

    public function postEdit($id, Request $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $id = decrypt($id);
        $module = Maintenance::findOrFail($id);
        if(count($request->file('image')) > 0){
            $images = $request->file('image');
            foreach($images as $key => $image){
                $file = $image;
                $folder = "files/dormitory";

                $path = uploadPhoto($file, $folder);
                $dt['path'] = $path;
                $dt['type'] = 'maintenance';
                $dt['ref_id'] = $id;
                File::Create($dt);
            }
        }
        $from = $module->status->name;
        $data = $request->only('status_id', 'remarks');
        if($data['status_id'] == '3'){
            $data['completed_at'] = \Carbon\Carbon::now()->toDateTimeString();
        }
        $data['logged_by'] = $auth_user->id;
        // if($request->file('image'))
        $module->update($data);
        $module = Maintenance::find($module->id);
        $to = $module->status->name;

        if($from != $to){
            Activity::log('Updated maintenance #'.$id. ' status from '.$from.' to '.$to.' by '.$auth_user->name);
        }
        $message = 'Status of the maintenance #'.$module->id.' has be updated from '.$from.' to '.$to;
        Notification::create(['type' => 'maintenance', 'title' => 'Maintenance Status Update', 'message' => $message, 'user_id' => $module->user_id, 'created_by' => $auth_user->id]);
        event(new MaintenanceStatus($module->user_id, $id, $from, $to));
        // \Event::fire('maintenance.status', [$module->user_id, $id, $from, $to]);

        return redirect()->route('admin.maintenance.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Maintenance details updated successfully.',
        ]);

    }

    public function getDelete($id)
    {
        $auth_user = \Auth::user();
        Maintenance::destroy($id);

        Activity::log('Deleted maintenance #'.$id. ' by '.$auth_user->name);

        return redirect()->route('admin.maintenance.list')->with([
            'flash_level'   => 'danger',
            'flash_message' => 'Maintenance Deleted',
        ]);
    }

    public function postDelete($id)
    {
        Maintenance::delete($id);
        return redirect()->route('admin.maintenance.list')->with([
          'flash_level'   => 'danger',
          'flash_message' => 'Maintenance Deleted',
        ]);

    }
}
