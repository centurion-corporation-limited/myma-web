<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Models\Advertisement;
use App\Models\Plans;
use App\Models\Adinvoices;
use App\Models\Sponsor;
use App\Models\FoodMenu;
use Illuminate\Http\Request;
use App\Models\Option;

use App\Http\Requests;
use App\Http\Requests\AddAdRequest;
use App\Http\Requests\EditAdRequest;
use App\Http\Controllers\Controller;
use Auth, Activity;
use Carbon\Carbon;

class AdController extends Controller
{
    public function getIndex()
    {
        $title = 'Bottom Ad Settings';
        return view('admin.advertisement.settings', compact('title'));
    }

    public function postIndex(Request $request)
    {
        $auth_user = Auth::user();
        $flag = false;
        foreach ($request->input('options') as $key => $value) {
            $flag = true;
            if (is_array($value)) {
                $value = serialize($value);
            }
            Option::setOption($key, $value);
        }

        return redirect()->back()->with([
            'flash_level'   => 'success',
            'flash_message' => 'Update success',
        ]);
    }
    
    public function getList(Request $request)
    {
      $auth_user = Auth::user();

      $items = Advertisement::query();
      if($auth_user->hasRole('admin')){
          $items->where('type', '!=','food');
      }elseif($auth_user->hasRole('food-admin')){
          $items->where('type', 'food');
      }else{
        $items->where('report_whom', $auth_user->id);
      }

      $type = $request->input('type');
      if ($type != '0' && $type != '') {
          $items->where('type', $type);
      }

      $ad_type = $request->input('adv_type');
      if ($ad_type != '0' && $ad_type != '') {
          $items->where('adv_type', $ad_type);
      }

      $status = $request->input('status');
      if ($status != '0' && $status != '') {
          $items->where('status', $status);
      }

      $limit = 10;
      // $sort = $request->input('sort');
      // if($sort == 'impression'){
      //     $order = $request->input('order', 'asc');
      //     $items->whereHas('impress', function($q) use($order){
      //       $q->orderBy('impressions', $order);
      //     });
      //     $items = $items->paginate($limit);
      // }else{
          //$items = $items->sortable(['id' => 'desc'])->paginate($limit);
          $items = $items->orderBy('id', 'desc')->paginate($limit);
      // }

      $paginate_data = $request->except('page');

      $now = Carbon::now();

      foreach($items as $item){
          if($item->status != ''){
              $item->status = ucfirst($item->status);
          }
          // if($item->adv_type == 1){//impression
          //     if($item->impress){
          //         if($item->plan->impressions >= $item->impress->impressions){
          //             $item->status = 'Running';
          //         }else{
          //             $item->status = 'Completed';
          //         }
          //     }else{
          //         $item->status = 'Inactive';
          //     }
          // }else{
          //     $start = Carbon::parse($item->start);
          //     $end = Carbon::parse($item->end);
          //     if($start->lte($now) && $end->gte($now)){
          //         $item->status = 'Running';
          //     }elseif($end->lt($now)){
          //         $item->status = 'Completed';
          //     }else{
          //         $item->status = 'Upcoming';
          //     }
          // }
      }
      return view('admin.advertisement.list', compact('items', 'auth_user', 'paginate_data'));
    }

    public function getAdd()
    {
        $auth_user = \Auth::user();

        if($auth_user->hasRole('food-admin')){
          $vendor = User::whereHas('roles', function($q){
              $q->where('slug', ['food-admin'])->
              whereHas('permissions', function($qq){
                  $qq->where('permissions.id',15)
                  ->orWhere('permissions.id',16)
                  ->orWhere('permissions.id',18);
              });
            })->orderBy('id', 'desc')->pluck('name','id')->toArray();
        }else{
          $vendor = User::whereHas('roles', function($q){
              $q->whereNotIn('slug', ['food-admin', 'restaurant-owner-single', 'restaurant-owner-catering'])->
              whereHas('permissions', function($qq){
                  $qq->where('permissions.id',15)
                  ->orWhere('permissions.id',16)
                  ->orWhere('permissions.id',18);
              });
            })->orderBy('id', 'desc')->pluck('name','id')->toArray();
        }


        $impressions = Plans::where('type', 'impression')->get();
        $date = Plans::where('type', 'date')->get();
        $sponsors = Sponsor::query();
        if($auth_user->hasRole('food-admin')){
          $sponsors->where('creator_id', $auth_user->id);
        }
        $sponsors = $sponsors->pluck('name', 'id')->toArray();
        $foods = FoodMenu::where('published', '1')->orderBy('id', 'desc')->pluck('name','id');//->get();

        $vendors = $sponsor = ['' => 'Please Select'];
        foreach($sponsors as $key => $ss){
            $sponsor[$key] = $ss;
        }

        foreach($vendor as $key => $ss){
            $vendors[$key] = $ss;
        }
        return view('admin.advertisement.add', compact('auth_user', 'vendors', 'impressions', 'date', 'sponsor', 'foods'));
    }

    public function postAdd(AddAdRequest $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        if($request->input('adv_type') == 1){
            $data = $request->only('sponsor_id', 'type', 'plan_id', 'report_whom', 'description', 'adv_type', 'link', 'slider_order');
            $data['slider_order'] = @$request->input('slider_order');
        }else{
            $data = $request->only('sponsor_id', 'type', 'plan_id', 'start', 'end', 'report_whom', 'description', 'adv_type', 'link', 'slider_order');
            $start = explode('/',$data['start']);
            $start = Carbon::create($start[2],$start[1],$start[0]);

            $end = explode('/',$data['end']);
            $end = Carbon::create($end[2],$end[1],$end[0]);

            $data['start'] = $start->toDateString();
            $data['end'] = $end->toDateString();
        }

        if($auth_user->hasRole('food-admin')){
            $data['food_item'] = $request->input('food_item');
        }
        if(isset($request['path']) && $request['path'] != "") {
          $file = $request->input('path');
          if (preg_match('/^data:image\/(\w+);base64,/', $file, $type)) {
              $file = substr($file, strpos($file, ',') + 1);
              $type = strtolower($type[1]); // jpg, png, gif

              if (!in_array($type, [ 'jpg', 'jpeg', 'gif', 'png' ])) {
                  throw new \Exception('invalid image type');
              }

              $decode = base64_decode($file);

              if ($decode === false) {
                  throw new \Exception('base64_decode failed');
              }
              $folder = "files/ad";

              $path = savePhoto($file, $folder, $type);
              $data['path'] = $path;
          } else {
              throw new \Exception('did not match data URI with image data');
          }
        }

        if($data['link'] != ''){
          $data['link'] = addhttp($data['link']);
        }
        // if($request->hasFile('path')){
        //   $file = $request->file('path');
        //   $folder = "files/ad";
        //
        //   $path = uploadPhoto($file, $folder);
        //   $data['path'] = $path;
        // }
        $module = Advertisement::create($data);
        if($module){
            Activity::log('New advertisement #'.$module->id. ' added by '.$auth_user->name);
            if($module['adv_type'] == 1)
                $type = 'impression';
            else
                $type = 'date';

            $plan = Plans::findOrFail($module['plan_id']);

            $impressions = $plan->impressions;
            $price = $plan->price;

            $invoice = [
                'ad_id' => $module['id'],
                'user_id' => $module['report_whom'],
                'type' => $type,
                'impressions' => $impressions,
                'price' => $price,
                'status' => 'pending'
            ];
            Adinvoices::create($invoice);
        }

        return redirect()->route('admin.advertisement.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Advertisement added successfully.',
        ]);

    }

    public function postSponsor(Request $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $data = $request->only('name');

        $module = Sponsor::create($data);

        return redirect()->back()->with([
            'flash_level'   => 'success',
            'flash_message' => 'Sponsor added.',
        ]);

    }
    public function getEdit($id, Request $request)
    {
    		$auth_user = \Auth::user();
        $id = decrypt($id);
        if($auth_user->hasRole('food-admin')){
          $vendor = User::whereHas('roles', function($q){
              $q->where('slug', ['food-admin'])->
              whereHas('permissions', function($qq){
                  $qq->where('permissions.id',15)
                  ->orWhere('permissions.id',16)
                  ->orWhere('permissions.id',18);
              });
            })->orderBy('id', 'desc')->pluck('name','id')->toArray();
        }else{
          $vendor = User::whereHas('roles', function($q){
              $q->whereNotIn('slug', ['food-admin', 'restaurant-owner-single', 'restaurant-owner-catering'])->
              whereHas('permissions', function($qq){
                  $qq->where('permissions.id',15)
                  ->orWhere('permissions.id',16)
                  ->orWhere('permissions.id',18);
              });
            })->orderBy('id', 'desc')->pluck('name','id')->toArray();
        }

    		$item = Advertisement::findOrFail($id);

            $item->start = date('d/m/Y', strtotime($item->start));
            $item->end = date('d/m/Y', strtotime($item->end));
            $impressions = Plans::where('type', 'impression')->get();
            $date = Plans::where('type', 'date')->get();
            $sponsors = Sponsor::query();
            if($auth_user->hasRole('food-admin')){
              $sponsors->where('creator_id', $auth_user->id);
            }
            $sponsors = $sponsors->pluck('name', 'id')->toArray();
            $vendors = $sponsor = ['' => 'Please Select'];
            foreach($sponsors as $key => $ss){
                $sponsor[$key] = $ss;
            }

            foreach($vendor as $key => $ss){
                $vendors[$key] = $ss;
            }

            $foods = FoodMenu::where('published', '1')->pluck('name','id');//->get();

        return view('admin.advertisement.edit', compact('item', 'vendors', 'impressions', 'date', 'sponsor', 'auth_user', 'foods'));
    }

    public function postEdit($id, EditAdRequest $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $id = decrypt($id);
        $module = Advertisement::findOrFail($id);

        if($request->input('adv_type') == 1){
            $data = $request->only('sponsor_id', 'type', 'description', 'report_whom', 'link','slider_order');//, 'plan_id', 'adv_type'
        }else{
            $data = $request->only('sponsor_id', 'type', 'description', 'report_whom', 'link','slider_order');//, 'plan_id', 'start', 'end', 'adv_type'
        }

        if($auth_user->hasRole('food-admin')){
            $data['food_item'] = $request->input('food_item');
        }
        // $data = $request->only('title', 'type', 'start', 'end', 'plan_id',  'report_whom', 'description', 'adv_type', 'slider_order');
        // if($request->hasFile('path')){
        //   $file = $request->file('path');
        //   $folder = "files/ad";
        //
        //   $path = uploadPhoto($file, $folder);
        //   $data['path'] = $path;
        // }

        if($data['link'] != ''){
          $data['link'] = addhttp($data['link']);
        }

        if(isset($request['path']) && $request['path'] != "") {
          $file = $request->input('path');
          if (preg_match('/^data:image\/(\w+);base64,/', $file, $type)) {
              $file = substr($file, strpos($file, ',') + 1);
              $type = strtolower($type[1]); // jpg, png, gif

              if (!in_array($type, [ 'jpg', 'jpeg', 'gif', 'png' ])) {
                  throw new \Exception('invalid image type');
              }

              $decode = base64_decode($file);

              if ($decode === false) {
                  throw new \Exception('base64_decode failed');
              }
              $folder = "files/ad";

              $path = savePhoto($file, $folder, $type);
              $data['path'] = $path;
          } else {
              throw new \Exception('did not match data URI with image data');
          }
        }
        $module->update($data);
        Activity::log('Updated advertisement #'.$module->id. ' by '.$auth_user->name);
        return redirect()->route('admin.advertisement.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Advertisement details updated successfully.',
        ]);

    }

    public function getView($id, Request $request)
    {
    		$auth_user = \Auth::user();
        $id = decrypt($id);

    		$item = Advertisement::findOrFail($id);

        $item->start = date('d/m/Y', strtotime($item->start));
        $item->end = date('d/m/Y', strtotime($item->end));

        if($item->adv_type == '1'){//impression based
          $impressions = @$item->plan->impressions.' Impressions';
          $price = @$item->plan->price;
        }else{
          $impressions = @$item->plan->impressions;
          if($impressions == 7){
            $impressions = 'A Week';
          }elseif($impressions == 31){
            $impressions = 'A Month';
          }else{
            $impressions = 'A Year';
          }
          $price = @$item->plan->price;
        }
        return view('admin.advertisement.view', compact('item', 'auth_user', 'impressions', 'price'));
    }

    public function getDelete($id)
    {

        Advertisement::destroy($id);
        $auth_user = \Auth::user();
        Activity::log('Deleted advertisement #'.$id. ' by '.$auth_user->name);
        return redirect()->route('admin.advertisement.list')->with([
            'flash_level'   => 'danger',
            'flash_message' => 'Advertisement Deleted',
        ]);
    }

    public function postDelete($id)
    {
        Advertisement::destroy($id);
        $auth_user = \Auth::user();
        Activity::log('Deleted advertisement #'.$id. ' by '.$auth_user->name);
        return redirect()->route('admin.advertisement.list')->with([
          'flash_level'   => 'danger',
          'flash_message' => 'Advertisement Deleted',
        ]);

    }
}
