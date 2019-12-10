<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Models\Notification;
use App\Models\Dormitory;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\NotificationRequest;
use App\Http\Controllers\Controller;
use Auth;
use Activity, Carbon\Carbon;

class NotificationController extends Controller
{
    public function getList(Request $request)
    {
      $auth_user = Auth::user();

      $items = Menu::orderBy('id', 'asc')->get();
      return view('admin.menu.list', compact('items', 'auth_user'));
    }

    public function getAdd()
    {
        $users = User::whereNotNull('fcm_token')->get()->pluck('email', 'id')->toArray();
        $dormitories = Dormitory::pluck('name', 'id')->toArray();
        $type = [
            'Mom',
            'Training',
            'Transport',
            'Forum',
            'E-learning'
        ];
        return view('admin.notification.add', compact('users', 'type', 'dormitories'));
    }

    public function postAdd(NotificationRequest $request)
    {
        $auth_user = Auth::user();
        $data = $request->only('sendto', 'user_id', 'dormitory_id', 'message', 'send_at');
        $message = html_entity_decode($data['message']);
        
        if($data['sendto'] == 'specific'){
            $users = User::whereIn('id', $data['user_id'])->get();
        }elseif($data['sendto'] == 'dormitory'){
            $users = User::whereHas('profile', function($q) use($data){
                $q->whereIn('dormitory_id', $data['dormitory_id']);
            })->get();
        }elseif($data['sendto'] == 'all'){
            $users = User::whereHas('roles', function($q){
                $q->where('role_id', 3);
            })->get();
        }

        $now = Carbon::now();
        $send_at = Carbon::parse($data['send_at']);
        $seconds = $now->diffInSeconds($send_at, false);

        // $type = (isset($request->type) && $request->type != '')? $request->type:'general';
        // $id = (isset($request->id) && $request->id != '')? $request->id:'';
        foreach ($users as $key => $user) {
            if($user->fcm_token && $seconds <= 0){
              sendSingle($user, $data['message']);
            }
            Notification::create(['type' => 'general', 'title' => 'Notification', 'message' => $data['message'], 'user_id' => $user->id, 'created_by' => $auth_user->id, 'send_at' => $send_at->toDateTimeString()]);
        }
        return redirect()->route('admin.notification.add')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Notification Sent.',
        ]);

    }
    public function getEdit($id, Request $request)
    {
        $id = decrypt($id);
    	$item = Menu::findOrFail($id);

        return view('admin.menu.edit', compact('item'));
    }

    public function postEdit($id, EditMenuRequest $request)
    {
        $id = decrypt($id);
        $menu = Menu::findOrFail($id);
        $data = $request->only('name', 'active', 'order', 'name_ta','name_bn','name_mn', 'name_th', 'access');
        // $data['slug'] = str_slug($data['name']);
        if($data['order'] == ''){
            $data['order'] = Menu::get()->count()+1;
        }
        $menu->update($data);
        // Activity::log('User details updated - '.$user->id);

        return redirect()->route('admin.menu.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Notification updated successfully.',
        ]);

    }


    public function getDelete($id)
    {
        Notification::destroy($id);
        $auth_user = Auth::user();
        Activity::log('Deleted notification #'.@$id. ' by '.$auth_user->name);

        return redirect()->route('admin.user.list')->with([
            'flash_level'   => 'danger',
            'flash_message' => 'Notification Deleted',
        ]);
    }
}
