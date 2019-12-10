<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Models\UserProfile;
use App\Models\MenuCategory;
use App\Models\Menu;
use App\Models\Mpopular;
use App\Models\Dormitory;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\AddEmployeeRequest;
use App\Http\Requests\EditMenuRequest;
use App\Http\Controllers\Controller;
use Auth, Carbon\Carbon;
use Activity;

class MenuController extends Controller
{
    public function getList(Request $request)
    {
      $auth_user = Auth::user();

      $items = Menu::query();

      $name = $request->input('name');
      if ($name != '') {
          $items->where('name', 'like', "%{$name}%")
          ->orWhere('name_bn', 'like', "%{$name}%")
          ->orWhere('name_mn', 'like', "%{$name}%")
          ->orWhere('name_th', 'like', "%{$name}%")
          ->orWhere('name_ta', 'like', "%{$name}%");
      }

      $status = $request->input('status');
      if ($status != '') {
          $items->where('active', $status);
      }

      $access = $request->input('access');
      if ($access != '0' && $access != '') {
          $items->where('access', $access);
      }

      $limit = 10;
      $items = $items->sortable()->paginate($limit);
      $paginate_data = $request->except('page');

      return view('admin.menu.list', compact('items', 'auth_user', 'paginate_data'));
    }

    public function getAdd()
    {
        $categories = MenuCategory::pluck('name', 'id');
        return view('admin.menu.add', compact('categories'));
    }

    public function postAdd(Request $request)
    {
        $data = $request->only('name', 'order', 'name_ta','name_bn','name_mn', 'name_th', 'access', 'category_id');
        $data['slug'] = str_slug($data['name']);
        if($data['order'] == ''){
            $data['order'] = Menu::get()->count()+1;
        }
        if($request->hasFile('icon')){
          $file = $request->file('icon');
          $folder = "files/icon";

          $path = uploadPhoto($file, $folder);
          $data['icon'] = $path;
        }
        $item = Menu::create($data);

        $auth_user = Auth::user();
        Activity::log('Added new Menu - '.@$data['name']. ' by '.$auth_user->name);

        return redirect()->route('admin.menu.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Menu added successfully.',
        ]);

    }
    public function getEdit($id, Request $request)
    {
        $id = decrypt($id);
    	  $item = Menu::findOrFail($id);
        $categories = MenuCategory::pluck('name', 'id');

        return view('admin.menu.edit', compact('item', 'categories'));
    }

    public function postEdit($id, EditMenuRequest $request)
    {
        $id = decrypt($id);
        $menu = Menu::findOrFail($id);
        $data = $request->only('name', 'active', 'order', 'name_ta','name_bn','name_mn', 'name_th', 'access', 'category_id');
        // $data['slug'] = str_slug($data['name']);
        if($data['order'] == ''){
            $data['order'] = Menu::get()->count()+1;
        }
        if($request->hasFile('icon')){
          $file = $request->file('icon');
          $folder = "files/icon";

          $path = uploadPhoto($file, $folder);
          $data['icon'] = $path;
        }
        $menu->update($data);
        $auth_user = Auth::user();
        Activity::log('Updated Menu information - '.@$data['name']. ' by '.$auth_user->name);

        return redirect()->route('admin.menu.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Menu updated successfully.',
        ]);

    }

    public function getUser(Request $request)
    {
        $from = $request->input('start');
        $to = $request->input('end');
        $user_id = $request->input('user_id');

        $menus = Menu::all();
        $menu_total = [];
        $menu_icons = [];
        foreach($menus as $menu){
          $total = Mpopular::where('menu_id', $menu->id);
          if($from != '' && $to != ''){
            $from = Carbon::parse($from)->toDateString();
            $to = Carbon::parse($to)->toDateString();
            $total->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to);
          }
          if($user_id != ''){
            $total->where('user_id', $user_id);
          }
          $total = $total->sum('count');
          $total_menu[$menu->id] = $total;

          if($menu->slug == 'notifications' && $menu->icon == '')
              $menu->icon = 'images/a2.png';
          elseif($menu->slug == 'home' && $menu->icon == ''){
            $menu->icon = 'images/icon-home.png';
          }
          elseif($menu->slug == 'training' && $menu->icon == ''){
            $menu->icon = 'images/icon-training.png';
          }
          elseif($menu->slug == 'topup' && $menu->icon == ''){
            $menu->icon = 'images/icon-topp.png';
          }
          elseif($menu->slug == 'transport' && $menu->icon == ''){
            $menu->icon = 'images/icon-transport-r.png';
          }
          elseif($menu->slug == 'banking' && $menu->icon == ''){
            $menu->icon = 'images/icon-bankings.png';
          }
          elseif($menu->slug == 'remittance' && $menu->icon == ''){
            $menu->icon = 'images/icon-remitance.png';
          }
          elseif($menu->slug == 'mwc' && $menu->icon == ''){
            $menu->icon = 'images/icon-mwc-menu.png';
          }
          elseif($menu->slug == 'forums' && $menu->icon == ''){
            $menu->icon = 'images/icon-forum.png';
          }
          elseif($menu->slug == 'e-learning' && $menu->icon == ''){
            $menu->icon = 'images/icon-elearning.png';
          }
          elseif($menu->slug == 'dormitory-maintenance' && $menu->icon == ''){
            $menu->icon = 'images/icon-demotiry.png';
          }
          elseif($menu->slug == 'incident-reporting' && $menu->icon == ''){
            $menu->icon = 'images/icon-incident.png';
          }
          elseif($menu->slug == 'feedback' && $menu->icon == ''){
            $menu->icon = 'images/icon-feedback-r.png';
          }
          elseif($menu->slug == 'emergency' && $menu->icon == ''){
            $menu->icon = 'images/icon-emergency.png';
          }
          elseif($menu->slug == 'information' && $menu->icon == ''){
            $menu->icon = 'images/icon-information.png';
          }
          elseif($menu->slug == 'games' && $menu->icon == ''){
            $menu->icon = 'images/icon-game.png';
          }
          elseif($menu->slug == 'movies' && $menu->icon == ''){
            $menu->icon = 'images/icon-movie.png';
          }
          elseif($menu->slug == 'aspri' && $menu->icon == ''){
            $menu->icon = 'images/icon-aspri.png';
          }
          elseif($menu->slug == 'event-news' && $menu->icon == ''){
            $menu->icon = 'images/icon-bankings.png';
          }
          elseif($menu->slug == 'logout' && $menu->icon == ''){
            $menu->icon = 'images/icon-logout.png';
          }
          elseif($menu->slug == 'mom' && $menu->icon == '')
              $menu->icon = 'images/img-m.jpg';
          elseif($menu->slug == 'free-wifi' && $menu->icon == '')
              $menu->icon = 'images/icon-mom.png';
          elseif($menu->slug == 'food' && $menu->icon == '')
              $menu->icon = 'images/naan.png';
          else{
              if($menu->icon == ''){
                  $menu->icon = 'images/icon-mom.png';
              }
          }
        }
        return view('admin.menu.user', compact('menus', 'total_menu'));
    }

    public function getUserList(Request $request)
    {
        $name = $request->input('q');

        $users = User::whereHas('roles', function($q){
          $q->where('slug', 'app-user');
        });
        if($name != ''){
          $users->where('name', 'like', "%{$name}%");
        }
        $users = $users->select('name', 'id')->get()->toArray();
        return response()->json(['items' => $users]);
    }

    public function getDormitoryList(Request $request)
    {
        $name = $request->input('q');

        $users = Dormitory::query();
        if($name != ''){
          $users->where('name', 'like', "%{$name}%");
        }
        $users = $users->select('name', 'id')->get()->toArray();
        return response()->json(['items' => $users]);
    }

    public function getDormitory(Request $request)
    {
        $from = $request->input('start');
        $to = $request->input('end');
        $dormitory_id = $request->input('dormitory_id');

        $menus = Menu::all();
        $menu_total = [];
        $menu_icons = [];
        foreach($menus as $menu){
          $total = Mpopular::where('menu_id', $menu->id);
          if($from != '' && $to != ''){
            $from = Carbon::parse($from)->toDateString();
            $to = Carbon::parse($to)->toDateString();
            $total->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to);
          }
          if($dormitory_id != ''){
            $user_ids = UserProfile::where('dormitory_id', $dormitory_id)->pluck('user_id');
            $total->where('user_id', $user_ids);
          }
          $total = $total->sum('count');
          $total_menu[$menu->id] = $total;

          if($menu->slug == 'notifications' && $menu->icon == '')
              $menu->icon = 'images/a2.png';
          elseif($menu->slug == 'home' && $menu->icon == ''){
            $menu->icon = 'images/icon-home.png';
          }
          elseif($menu->slug == 'training' && $menu->icon == ''){
            $menu->icon = 'images/icon-training.png';
          }
          elseif($menu->slug == 'topup' && $menu->icon == ''){
            $menu->icon = 'images/icon-topp.png';
          }
          elseif($menu->slug == 'transport' && $menu->icon == ''){
            $menu->icon = 'images/icon-transport-r.png';
          }
          elseif($menu->slug == 'banking' && $menu->icon == ''){
            $menu->icon = 'images/icon-bankings.png';
          }
          elseif($menu->slug == 'remittance' && $menu->icon == ''){
            $menu->icon = 'images/icon-remitance.png';
          }
          elseif($menu->slug == 'mwc' && $menu->icon == ''){
            $menu->icon = 'images/icon-mwc-menu.png';
          }
          elseif($menu->slug == 'forums' && $menu->icon == ''){
            $menu->icon = 'images/icon-forum.png';
          }
          elseif($menu->slug == 'e-learning' && $menu->icon == ''){
            $menu->icon = 'images/icon-elearning.png';
          }
          elseif($menu->slug == 'dormitory-maintenance' && $menu->icon == ''){
            $menu->icon = 'images/icon-demotiry.png';
          }
          elseif($menu->slug == 'incident-reporting' && $menu->icon == ''){
            $menu->icon = 'images/icon-incident.png';
          }
          elseif($menu->slug == 'feedback' && $menu->icon == ''){
            $menu->icon = 'images/icon-feedback-r.png';
          }
          elseif($menu->slug == 'emergency' && $menu->icon == ''){
            $menu->icon = 'images/icon-emergency.png';
          }
          elseif($menu->slug == 'information' && $menu->icon == ''){
            $menu->icon = 'images/icon-information.png';
          }
          elseif($menu->slug == 'games' && $menu->icon == ''){
            $menu->icon = 'images/icon-game.png';
          }
          elseif($menu->slug == 'movies' && $menu->icon == ''){
            $menu->icon = 'images/icon-movie.png';
          }
          elseif($menu->slug == 'aspri' && $menu->icon == ''){
            $menu->icon = 'images/icon-aspri.png';
          }
          elseif($menu->slug == 'event-news' && $menu->icon == ''){
            $menu->icon = 'images/icon-bankings.png';
          }
          elseif($menu->slug == 'logout' && $menu->icon == ''){
            $menu->icon = 'images/icon-logout.png';
          }
          elseif($menu->slug == 'mom' && $menu->icon == '')
              $menu->icon = 'images/img-m.jpg';
          elseif($menu->slug == 'free-wifi' && $menu->icon == '')
              $menu->icon = 'images/icon-mom.png';
          elseif($menu->slug == 'food' && $menu->icon == '')
              $menu->icon = 'images/naan.png';
          else{
              if($menu->icon == ''){
                  $menu->icon = 'images/icon-mom.png';
              }
          }
        }
        return view('admin.menu.dormitory', compact('menus', 'total_menu'));

    }

    public function getDelete($id)
    {
        $auth_user = Auth::user();
        Menu::destroy($id);
        Activity::log('Menu deleted - '.$id.' by '.$auth_user->name);

        return redirect()->route('admin.menu.list')->with([
            'flash_level'   => 'danger',
            'flash_message' => 'Menu Deleted',
        ]);
    }

    public function postDelete($id)
    {
        $auth_user = Auth::user();
        Menu::delete($id);
        Activity::log('Menu deleted - '.$id.' by '.$auth_user->name);

        return redirect()->route('admin.menu.list')->with([
          'flash_level'   => 'danger',
          'flash_message' => 'Menu Deleted',
        ]);

    }
}
