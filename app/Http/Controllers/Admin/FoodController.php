<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Models\FoodMenu;
use App\Models\FoodCourse;
use App\Models\FoodCategory;
use App\Models\FoodPackage;
use App\Models\FoodTag;
use App\Models\Restaurant;
use App\Models\FoodMerchant;
use App\Models\Merchant;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\AddItemRequest;
use App\Http\Requests\EditItemRequest;
use App\Http\Controllers\Controller;
use Auth;

class FoodController extends Controller
{
    public function getList(Request $request)
    {
      $auth_user = Auth::user();

      $users = User::whereHas('roles', function($q){
          $q->whereIn('slug', ['restaurant-owner-single','restaurant-owner-catering'] );
      })->where('blocked', '0')->pluck('name', 'id');

      $items = FoodMenu::query();

      $flag = false;
      if($auth_user->hasRole('restaurant-owner-single|restaurant-owner-catering')){
          $flag = true;
          $items->whereHas('restaurant', function($q) use ($auth_user){
            $q->where('merchant_id', $auth_user->id);
          });
      }

      if ($name = $request->input('name')) {
          $name = strtolower($name);
          $items->whereRaw('lower(`name`) like ?', "%{$name}%");
      }

      if ($merchant_id = $request->input('merchant_id')) {
          $items->whereHas('restaurant', function($q) use ($merchant_id){
            $q->where('merchant_id', $merchant_id);
          });
      }
      $status = $request->input('status');
      if ($status == 'pending') {
          $items->where('published', '0');
      }

      $merchants[''] = 'Please Select a merchant';

      foreach($users as $id => $user){

            $merchants[$id] = $user;

      }
      $limit = 50;
      $items = $items->sortable(['id' => 'desc'])->paginate($limit);
      $paginate_data = $request->except('page');

      return view('admin.food.menu.list', compact('items', 'auth_user', 'paginate_data','merchants', 'flag', 'limit'));
    }


    public function getAdd()
    {
        $auth_user = \Auth::user();

        $single_users = User::whereHas('roles', function($q){
            $q->whereIn('slug', ['restaurant-owner-single'] );
        })->where('blocked', '0')->pluck('id');

        $caterer_users = User::whereHas('roles', function($q){
            $q->whereIn('slug', ['restaurant-owner-catering']);
        })->where('blocked', '0')->pluck('id');

        $courses = FoodCourse::pluck('name', 'id')->all();
        $category = FoodCategory::pluck('name', 'id')->all();

        $single_restra = Restaurant::whereIn('merchant_id', $single_users)->pluck('name','id');

        $catering_restra = Restaurant::whereIn('merchant_id', $caterer_users)->pluck('name','id');

        $flag = '';
        if($auth_user->hasRole('restaurant-owner-single')){
            $flag = 'single';
            $single_restra = Restaurant::where('merchant_id', $auth_user->id)->pluck('name','id');
        }
        if($auth_user->hasRole('restaurant-owner-catering')){
            $flag = "catering";
            $catering_restra = Restaurant::where('merchant_id', $auth_user->id)->pluck('name','id');
        }
        
        $list[] = 'Please Select';
        $list['7'] = '7';
        $list['14'] = '14';
        $list['21'] = '21';
        $list['28'] = '28';
        
        return view('admin.food.menu.add', compact('auth_user', 'users', 'courses', 'category', 'single_restra', 'catering_restra', 'flag', 'list'));
    }

    public function postAdd(AddItemRequest $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();

        $data = $request->only('name', 'description', 'is_veg', 'is_halal', 'course_id', 'price', 'type',
         'breakfast', 'lunch', 'dinner', 'restaurant_id', 'published', 'base_price');

        $restaurant = Restaurant::where('merchant_id', $auth_user->id)->first();
        if($restaurant){
            $data['restaurant_id'] = $restaurant->id;
        }
        if($auth_user->hasRole('restaurant-owner-single')){
            $data['type'] = 'single';
        }
        if($auth_user->hasRole('restaurant-owner-catering')){
            $data['type'] = "package";
        }

        if(isset($request['image']) && $request['image'] != "") {
           $images = $request->input('image');
           $dd = [];

           foreach($images as $file){
             if($file == ''){
               continue;
             }
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
                 $folder = "files/food/";

                 $path = savePhoto($file, $folder, $type);
                 $dd[] = $path;
             } else {
                 throw new \Exception('did not match data URI with image data');
             }
           }
           $data['image'] = implode(',', $dd);
        }
        // $restaurant = Restaurant::where('merchant_id', $data['merchant_id'])->select('id')->first();
        // $data['restaurant_id'] = $restaurant->id;
        if($data['type'] == 'package'){
            $data['price'] = $data['base_price'];
        }
        $food = FoodMenu::create($data);

        if($food){
            $tags = $request->input('tags');
            if(count($tags)){
                foreach($tags as $tag){

                    $cats = FoodCategory::where('id', $tag)->get();
                    if($cats->count()){
                        $cat_tag = $tag;
                    }else{
                        $cats = FoodCategory::create(['name' => ucfirst($tag), 'slug' => str_slug($tag), 'approved' => 1]);
                        $cat_tag = $cats->id;
                    }
                    FoodTag::create([
                        'food_id' => $food->id,
                        'category_id' => $cat_tag
                    ]);
                }
            }
        }

        return redirect()->route('admin.food_menu.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Item added successfully.',
        ]);

    }

    public function recommend(Request $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $item_id = $request->input('item_id');
        $item_id = decrypt($item_id);
        $item = FoodMenu::find($item_id);
        if($item->recommended){
            $item->update(['recommended' => '0']);
        }else{
            $item->update(['recommended' => '1']);
        }

        return redirect()->route('admin.food_menu.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Status updated.',
        ]);

    }

    public function getView($id, Request $request)
    {
        $id = decrypt($id);
		    $auth_user = \Auth::user();
		    $item = FoodMenu::findOrFail($id);
        $users = User::where('blocked', '0')->pluck('email','id');

        return view('admin.food.menu.view', compact('item', 'users'));
    }

    public function getEdit($id, Request $request)
    {
        $id = decrypt($id);
    	  $auth_user = \Auth::user();
    	  $item = FoodMenu::findOrFail($id);
        // $status = Status::pluck('name', 'id');
        $users = User::whereHas('roles', function($q){
            $q->whereIn('slug', ['restaurant-owner-package', 'restaurant-owner-single'] );
        })->where('blocked', '0')->pluck('email','id');

        $single_users = User::whereHas('roles', function($q){
            $q->whereIn('slug', ['restaurant-owner-single'] );
        })->where('blocked', '0')->pluck('id');

        $caterer_users = User::whereHas('roles', function($q){
            $q->whereIn('slug', ['restaurant-owner-catering']);
        })->where('blocked', '0')->pluck('id');

        $single_restra = Restaurant::whereIn('merchant_id', $single_users)->pluck('name','id');
        $catering_restra = Restaurant::whereIn('merchant_id', $caterer_users)->pluck('name','id');

        $courses = FoodCourse::pluck('name', 'id')->all();
        $category = FoodCategory::pluck('name', 'id')->all();

        $tags = [];
        foreach($item->tags as $tag){
            $tags[] = $tag->category_id;
        }

        $item->tags = $tags;

        $restra = Restaurant::where('id', $item->restaurant_id)->first();
        if(!$restra){
          return response()->json(['status' => false, 'message' => 'Invalid restaurant.']);
        }
        $html = '';
        $gst = 7;
        $actual_price = $price = $item->base_price;
        $foodmerchant = User::where('id', $restra->merchant_id)->first();
        if($foodmerchant->hasRole('restaurant-owner-catering')){
          $merchant = Merchant::find(13);

          $wlc_share = $merchant->merchant_share != ""?$merchant->merchant_share:'3.75';
          $naanstap_share_per = '1.25';

          $flexm_per = $merchant->myma_transaction_charges != ""?$merchant->myma_transaction_charges:'2.5';

          $gst_val = (($price*($gst/100)));
          $naanstap_share = 0;
          $wlc_share = 0;
          $flexm_share = (($price*$flexm_per)/100);
          // $price += $gst_val;

          $html = "<div>Cost Price : ".$actual_price."</div>";
          $html .= "<div>Naanstap Fee : 0</div>";
          $html .= "<div>WLC Fee : 0</div>";
          $html .= "<div>Flexm Fee : ".$flexm_share."</div>";

          $html = '';
        }
        else{
            $foodmerchant = FoodMerchant::where('user_id', $restra->merchant_id)->first();    
          $merchant = Merchant::find(12);
          $wlc_share_per = $merchant->merchant_share != ""?$merchant->merchant_share:8;
          $naanstap_share_per = $foodmerchant->naanstap_share != ""?$foodmerchant->naanstap_share:10;
          $flexm_per = $merchant->myma_transaction_charges != ""?$merchant->myma_transaction_charges:'2.5';


          $naanstap_share = (($price*$naanstap_share_per)/100);

          $selling_price = ((($price+$naanstap_share)/(1-($wlc_share_per/100)-($flexm_per/100)-($gst/100*($wlc_share_per/100)))));
          $wlc_share = (($selling_price*$wlc_share_per/100));
          $flexm_share = (($selling_price*$flexm_per)/100);
          $gst_val = (($wlc_share*($gst/100)));

          $html = "<div>Cost Price : ".($actual_price)."</div>";
          $html .= "<div>Naanstap Fee (".$naanstap_share_per."% of ".$price.") : ".$naanstap_share."</div>";
          $html .= "<div>WLC Fee (".$wlc_share_per."% of ".($selling_price)."): ".$wlc_share."</div>";
          $html .= "<div>Flexm Fee (".$flexm_per."% of ".($selling_price)."): ".$flexm_share."</div>";
          $html .= "<div>GST (".$gst."% of ".($wlc_share)."): ".$gst_val."</div>";
        }


        $flag = '';
        $restaurant_id = "";
        if($auth_user->hasRole('restaurant-owner-single')){
            $flag = 'single';
            $restaurant = Restaurant::where('merchant_id', $auth_user->id)->first();
            if($restaurant){
              $restaurant_id = $restaurant->id;
            }
        }
        if($auth_user->hasRole('restaurant-owner-catering')){
            $flag = "catering";
            $restaurant = Restaurant::where('merchant_id', $auth_user->id)->first();
            if($restaurant){
              $restaurant_id = $restaurant->id;
            }
        }
        
        $list[] = 'Please Select';
        $list['7'] = '7';
        $list['14'] = '14';
        $list['21'] = '21';
        $list['28'] = '28';
        
        return view('admin.food.menu.edit', compact('auth_user','item', 'users', 'courses', 'category', 'single_restra', 'catering_restra', 'html', 'flag', 'restaurant_id', 'list'));
    }

    public function postEdit($id, EditItemRequest $request)
    {
        $id = decrypt($id);
        /** @var User $item */
        $auth_user = \Auth::user();
        $food = FoodMenu::findOrFail($id);

        $data = $request->only('name', 'description', 'is_veg', 'is_halal', 'course_id', 'price', 'type',
         'breakfast', 'lunch', 'dinner', 'published', 'restaurant_id', 'base_price');

        $restaurant = Restaurant::where('merchant_id', $auth_user->id)->first();
        if($restaurant){
          $data['restaurant_id'] = $restaurant->id;
        }
        if($auth_user->hasRole('restaurant-owner-single')){
             $data['type'] = 'single';
        }
        if($auth_user->hasRole('restaurant-owner-catering')){
             $data['type'] = "package";
        }
        if(isset($request['image']) && $request['image'] != "") {
            $images = $request->input('image');
            $dd = explode(',',$food->image);

            foreach($images as $file){
              if($file == ''){
                continue;
              }
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
                  $folder = "files/food/";

                  $path = savePhoto($file, $folder, $type);
                  $dd[] = $path;
              } else {
                  throw new \Exception('did not match data URI with image data');
              }
            }
            $data['image'] = implode(',', $dd);
        }
        if($data['type'] == 'package'){
            $data['price'] = $data['base_price'];
        }
        $up = $food->update($data);

        if($up){
            $tags = $request->input('tags');
            if(count($tags)){
                foreach($tags as $tag){
                    $cats = FoodCategory::where('id', $tag)->get();
                    if($cats->count()){
                        $cat_tag = $tag;
                    }else{
                        $cats = FoodCategory::create(['name' => ucfirst($tag), 'slug' => str_slug($tag), 'approved' => '1']);
                        $cat_tag = $cats->id;
                    }
                    $exist = FoodTag::where('food_id', $id)->where('category_id', $cat_tag)->first();
                    if(!$exist){
                        FoodTag::create([
                            'food_id' => $id,
                            'category_id' => $cat_tag
                        ]);
                    }
                }
            }
        }

        return redirect()->route('admin.food_menu.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Item updated successfully.',
        ]);

    }

    public function getDelete($id)
    {
        $id = decrypt($id);
        FoodMenu::destroy($id);

        return redirect()->route('admin.food_menu.list')->with([
            'flash_level'   => 'danger',
            'flash_message' => 'Item Deleted',
        ]);
    }

    public function removeImage(Request $request)
    {
        $item_id = $request->input('item_id');
        $index = $request->input('index');
        if ($item_id != '' && $index != '') {
          $item = FoodMenu::findOrFail($item_id);
          if($item->image){
              $images = explode(',', $item->image);
              unset($images[$index]);

              $data['image'] = implode(',', $images);
              $item->update($data);
          }
          return response()->json(['status' => true]);
        }else{
            return response()->json(['status' => false, 'message' => 'Missing parameters']);
        }
    }

    public function calcPrice(Request $request)
    {
        $actual_price = $price = $request->input('price');
        $actual_price = ($actual_price);

        $restaurant_id = $request->input('restaurant_id');
        $type = $request->input('type');
        $html ='';
        if ($price != '' && $restaurant_id != '') {
          $restra = Restaurant::find($restaurant_id);
          if(!$restra){
            return response()->json(['status' => false, 'message' => 'Invalid restaurant.']);
          }
          $item = FoodMerchant::where('user_id', $restra->merchant_id)->first();
          if($item){
            $total = 0;
            $gst = 7;
            if($item->user->hasRole('restaurant-owner-catering')){
              $merchant = Merchant::find(13);
              $wlc_share = $merchant->merchant_share != ""?$merchant->merchant_share:'3.75';
              $naanstap_share_per = $item->naanstap_share != ""?$item->naanstap_share:'1.25';
              $html = '';
              $total = $price;
              $flexm_per = $merchant->myma_transaction_charges != ""?$merchant->myma_transaction_charges:'2.5';
              // if($type == 'cost'){
              //   $gst_val = (($price*($gst/100)));
              //   $price = (($price/(1+($flexm_per/100))));
              //   // $price -= $gst_val;
              //   $html = "<div>Cost Price : ".$price."</div>";
              //   $html .= "<div>Naanstap Fee : 0</div>";
              //   $html .= "<div>WLC Fee : 0</div>";
              //   $html .= "<div>Flexm Fee : ".($actual_price-$price)."</div>";
              //
              //   $total = $price;
              // }else{
              //   $gst_val = (($price*($gst/100)));
              //   $naanstap_share = 0;
              //   $wlc_share = 0;
              //   $flexm_share = (($price*$flexm_per)/100);
              //   // $price += $gst_val;
              //
              //   $html = "<div>Cost Price : ".$actual_price."</div>";
              //   $html .= "<div>Naanstap Fee : 0</div>";
              //   $html .= "<div>WLC Fee : 0</div>";
              //   $html .= "<div>Flexm Fee : ".$flexm_share."</div>";
              //
              //   $total = $price+$flexm_share;
              // }

            }else{
              $merchant = Merchant::find(12);
              $wlc_share_per = $merchant->merchant_share != ""?$merchant->merchant_share:8;
              $naanstap_share_per = $item->naanstap_share != ""?$item->naanstap_share:10;
              $flexm_per = $merchant->myma_transaction_charges != ""?$merchant->myma_transaction_charges:'2.5';

              if($type == 'cost'){

                $flexm_share = (($actual_price*($flexm_per/100)));
                $wlc_share = (($actual_price*($wlc_share_per/100)));
                $gst_val = (($wlc_share*($gst/100)));

                $cost_price = $actual_price-$flexm_share-$wlc_share-$gst_val;
                $naanstap_share = ((($cost_price)/(1+($naanstap_share_per/100))));
                $naanstap_share = (($cost_price-$naanstap_share));
                $price = (($cost_price - $naanstap_share));



                $html = "<div>Cost Price : ".$price."</div>";
                $html .= "<div>Naanstap Fee (".$naanstap_share_per."% of ".$price.") : ".($naanstap_share)."</div>";
                $html .= "<div>WLC Fee (".$wlc_share_per."% of ".($actual_price)."): ".($wlc_share)."</div>";
                $html .= "<div>Flexm Fee (".$flexm_per."% of ".($actual_price)."): ".($flexm_share)."</div>";
                $html .= "<div>GST (".$gst."% of ".($wlc_share)."): ".($gst_val)."</div>";
                // $price -= $gst_val;
                $total = $price;
              }else{
                $naanstap_share = (($price*$naanstap_share_per)/100);

                $selling_price = ((($price+$naanstap_share)/(1-($wlc_share_per/100)-($flexm_per/100)-($gst/100*($wlc_share_per/100)))));
                $wlc_share = (($selling_price*$wlc_share_per/100));
                $flexm_share = (($selling_price*$flexm_per)/100);
                $gst_val = (($wlc_share*($gst/100)));

                $html = "<div>Cost Price : ".($actual_price)."</div>";
                $html .= "<div>Naanstap Fee (".$naanstap_share_per."% of ".$price.") : ".$naanstap_share."</div>";
                $html .= "<div>WLC Fee (".$wlc_share_per."% of ".($selling_price)."): ".$wlc_share."</div>";
                $html .= "<div>Flexm Fee (".$flexm_per."% of ".($selling_price)."): ".$flexm_share."</div>";
                $html .= "<div>GST (".$gst."% of ".($wlc_share)."): ".$gst_val."</div>";

                // $price += $gst_val;
                $total = ($selling_price);
              }
            }
            return response()->json(['status' => true , 'total' => $total, 'html' => $html]);
          }else{
              return response()->json(['status' => false, 'message' => 'Invalid merchant.']);
          }
        }else{
            return response()->json(['status' => false, 'message' => 'Missing parameters']);
        }
    }

    public function foodListing(Request $request)
    {
        $merchant_id = $request->input('merchant_id');

        if ($merchant_id != '') {
          $restra = Restaurant::where('merchant_id', $merchant_id)->first();
          if(!$restra){
            return response()->json(['status' => false, 'message' => 'Invalid restaurant.']);
          }
          $menu = FoodMenu::where('published', '1')->where('restaurant_id', $restra->id)->get();

          $html = view('admin.partial.food_listing', compact('menu'))->render();

          return response()->json(['status' => true , 'html' => $html]);

        }else{
            return response()->json(['status' => false, 'message' => 'Missing parameters']);
        }
    }

    public function getPackageList(Request $request)
    {
      $auth_user = Auth::user();

      $items = FoodPackage::orderBy('id');

      if ($id = $request->input('id')) {
          $items->where('id', $id);
      }

      $limit = 50;
      $items = $items->paginate($limit);
      $paginate_data = $request->except('page');

      return view('admin.food.menu.package.list', compact('items', 'auth_user', 'paginate_data', 'limit'));
    }


    public function getCategoryAdd()
    {
        $auth_user = \Auth::user();
        $users = User::where('blocked', '0')->pluck('email','id');

        return view('admin.food.menu.package.add', compact('auth_user', 'users'));
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

        return redirect()->route('admin.food_category.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Package created successfully.',
        ]);

    }

    public function getCategoryView($id, Request $request)
    {
        $id = decrypt($id);
    		$auth_user = \Auth::user();
    		$item = FoodPackage::findOrFail($id);
        $users = User::where('blocked', '0')->pluck('email','id');

        return view('admin.food.menu.package.view', compact('item', 'users'));
    }

    public function getCategoryEdit($id, Request $request)
    {
        $id = decrypt($id);
    	  $auth_user = \Auth::user();
    	  $item = FoodPackage::findOrFail($id);
        // $status = Status::pluck('name', 'id');
        $users = User::where('blocked', '0')->pluck('email','id');

        return view('admin.food.menu.package.edit', compact('item', 'users'));
    }

    public function postCategoryEdit($id, Request $request)
    {
        $id = decrypt($id);
        /** @var User $item */
        $auth_user = \Auth::user();
        $module = FoodPackage::findOrFail($id);

        $data = $request->only('name');

        $up = $module->update($data);

        // if($up){
        //     $user = User::findOrFail($data['manager_id']);
        //     $user->addPermission('view.maintenance-list');
        // }

        return redirect()->route('admin.food_category.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Package updated successfully.',
        ]);

    }

    public function getCategoryDelete($id)
    {
        $id = decrypt($id);
        FoodPackage::destroy($id);

        return redirect()->route('admin.food_category.list')->with([
            'flash_level'   => 'danger',
            'flash_message' => 'Package Deleted',
        ]);
    }
}
