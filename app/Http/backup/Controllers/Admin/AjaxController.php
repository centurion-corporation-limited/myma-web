<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Models\UserProfile;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Models\FcmToken;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\FoodCategory;
use App\Models\FoodMenu;
use App\Models\Coupon;
use Cart;

use Auth;
use Activity;
use Carbon\Carbon;

use Google\Cloud\Translate\TranslateClient;

class AjaxController extends Controller
{

    public function addToken(Request $request)
    {
        if(\Auth::check()){
          $data['fcm_token'] = $request->input('fcm_token');
          $exist = FcmToken::where($data)->first();
          if($exist){
            return json_encode(['status' => 'true', 'message' => 'Already exist']);

          }else{
            $data['user_id'] = \Auth::id();
            $result = FcmToken::create($data);
          }

          if($result){
              return json_encode(['status' => 'true', 'message' => 'Successfully added token.']);
          }else{
              return json_encode(['status' => 'false', 'message' => 'Try later error while adding token.']);
          }
        }
        return json_encode(['status' => 'true', 'message' => '']);

    }

    public function getRoleList()
    {
        $items = Role::pluck('name' ,'slug');

        return view('admin.user.role_list', compact('items'));
    }

    public function addTag(Request $request)
    {
    	$name = $request->tag;
        $data['name'] = $name;
        $data['slug'] = str_slug($data['name']);
        $module = FoodCategory::create($data);

        return json_encode($module);
    }

    public function getUser(Request $request)
    {
    	$id = $request->user_id;
    	$user = User::with('profile')->findOrFail($id);
        if($user){
            if($user->profile && $user->profile->dormitory)
                $user['dormitory'] = $user->profile->dormitory;

            $user_info['name'] = $user->name;
            $user_info['email'] = $user->email;
            $user_info['profile_pic'] = @$user->profile->profile_pic;
            $user_info['dormitory'] = @$user['dormitory'];

            $data['user'] = $user_info;
            $data['status'] = true;
        }else{
            $data['user'] = [];
            $data['status'] = false;
        }
        return json_encode($data);
    }

    public function convert(Request $request){
        putenv('GOOGLE_APPLICATION_CREDENTIALS='.storage_path('MYMA-4e81280813ae.json'));

		try{

			$projectId = 'myma-1525172879039';
			$translate = new TranslateClient([
				'projectId' => $projectId
			]);

			# The text to translate
			$text = $request->input('text');

			# The target language
			$target = $request->input('language', 'en');

			# Translates some text into Russian
            if(is_array($text)){
                $translation = [];
                foreach($text as $txt){
                    $translation[] = $translate->translate($text, [
                        'target' => $target
                    ]);
                }
            }else{
                $translation = $translate->translate($text, [
                    'target' => $target
                ]);
            }
			return json_encode($translation);

		}
		catch(Exception $e){
			return json_encode($e->getMessage());
		}
    }

    public function applyCoupon(Request $request)
    {
        $code = $request->code;
        Cart::restore(Auth::id());

        $cart = Cart::content();
        $type = '';
        foreach($cart as $cart_item){
            $type = $cart_item->options['type'];
            break;
        }

        $coupon = Coupon::whereDate('expiry','>=', Carbon::now())->where('code', strtoupper($code))->get();
        if($coupon->count()){
            $cou = $coupon->first();
            // $flag = false;
            // if($merchant_id != 0 && $merchant_id == $cou->merchant_id){
            //   if($cou->item_ids != ''){
            //       $arr = explode(',', $cou->item_ids);
            //       foreach($arr as $ar)[
            //         if(in_array($ar, $ids)){
            //           $flag = true;
            //         }
            //       ]
            //   }else{
            //       $flag = true;
            //   }
            // }
            // if($merchant_id == 0){
            //   $flag = true;
            // }
            $applied = false;
            foreach($cart as $cart_item){
                $flag = false;
                $food_item = FoodMenu::find($cart_item->id);
                $merchant_id = 0;
                if(@$food_item->restaurant->merchant_id){
                  $merchant_id = $food_item->restaurant->merchant_id;
                }
                if($cou->merchant_id == $merchant_id){
                  if($cou->item_ids != ''){
                    $ids = explode(',', $cou->item_ids);
                    if(in_array($cart_item->id, $ids)){
                      $flag = true;
                      $applied = true;
                    }
                  }else{
                    $applied = true;
                    $flag = true;
                  }

                  if($flag){
                    Cart::update($cart_item->rowId, ['options' => ['type' => $type,
                        'coupon_id' => $cou->id, 'discount_type' => $cou->type, 'value' => $cou->value]]);
                  }
                }
            }
            if($applied){
              $data['error'] = false;
              $data['msg'] = 'Coupon discount applied successfully';
            }else{
              $data['error'] = true;
              $data['msg'] = 'Invalid Coupon Code';
            }

        }else{
            $data['error'] = true;
            $data['msg'] = 'Invalid Coupon Code';
        }
        return json_encode($data);
    }

    public function removeCoupon(Request $request)
    {
        Cart::restore(Auth::id());

        $cart = Cart::content();
        $type = '';
        foreach($cart as $cart_item){
            $type = $cart_item->options['type'];
            break;
        }

        foreach($cart as $cart_item){
            Cart::update($cart_item->rowId, ['options' => ['type' => $type]]);
        }
        $data['error'] = false;
        $data['msg'] = 'Coupon removed successfully';
        // }else{
        //     $data['error'] = true;
        //     $data['msg'] = 'Invalid Coupon Code';
        // }
        return json_encode($data);
    }

}
