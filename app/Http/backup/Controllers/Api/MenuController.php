<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Validator;
use JWTAuth, DB;
use App\Models\Menu;
use App\Models\MenuCategory;
use App\Models\Mpopular;
use App\Models\Dormitory;
use App\User;
use Carbon\Carbon, Activity;

class MenuController extends Controller
{
    protected function validator(array $data, $user)
    {
      return Validator::make($data, [
            'menu_id' => 'required',
        ]);
    }

    public function addCount(Request $request)
    {
      $user = JWTAuth::toUser($request->input('token'));
      $validator = $this->validator($request->all(), $user);

      if ($validator->fails()) {
          $errors = $validator->errors();
          $message = [];
          foreach($errors->messages() as $key => $error){
              $message[$key] = $error[0];
          }
          return response()->json(['status' => 'error', 'data' => $message, 'message' => 'VALIDATION_ERROR'], 401);
      }

      try{
          $data = [
              'user_id'        => $user->id,
              'menu_id'        => $request->menu_id
          ];

          $menu = Mpopular::where($data)->whereDate('created_at', Carbon::now()->toDateString())->first();

          if($menu){
              $menu->count = ++$menu->count;
              $result = $menu->save();
          }else{
              $result = Mpopular::create($data);
          }
          if($result){
              return response()->json(['status' => 'success', 'data' => [], 'message' => 'CREATED.'], 200);
          }else{
              return response()->json(['status' => 'error', 'data' => 'Their was a problem while creating new data. Try Later.', 'message' => 'INSERTION_ERROR'], 404);
          }

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }

    public function getList(Request $request)
    {
        try{
            $token = \Input::get('token');
            $language = \Input::get('language');

            $allow_dorm_reporting = true;
            if($token != ''){
                $user = JWTAuth::toUser($token);

                if($user->type == 'registered_verified'){
                    // $fin = verifyFin($user->profile->fin_no);
                    // if($fin && $fin->verified){
                        // $dorm = Dormitory::where('full_name', $fin->dormitory)->first();
                        // if(!$dorm){
                        //     $allow_dorm_reporting = false;
                        // }
                    // }else{
                    //     $allow_dorm_reporting = false;
                    // }
                }else{
                    $allow_dorm_reporting = false;
                }
            }
            // if($token == ''){
            //     $arr = explode('?', $language);
            //     $language = $arr[0];
            //     $token = $arr[1];
            // }

            if($language == 'bengali'){
                $label = 'bn';
            }elseif($language == 'chinese' || $language == 'mandarin'){
                $language = 'mandarin';
                $label = 'mn';
            }elseif($language == 'tamil'){
                $label = 'ta';
            }elseif($language == 'thai'){
                $label = 'th';
            }else{
                $label = '';
            }

            if($allow_dorm_reporting){
                $not_allowed = [];
            }else{
                $not_allowed = ['11','12','25'];
            }
            $data = Menu::select('id','name','name_bn','name_ta','name_mn','name_th', 'access','slug', 'icon')->where('active', '1')->whereNotIn('id', $not_allowed);
            if(!empty($user) && $user->type == 'free'){
                $data->where('access', $user->type);
            }
            $data = $data->orderBy('order', 'asc')->get();
            foreach($data as $item){
                if($label != ''){
                    if($item['name_'.$label] != ''){
                        $item['name'] = $item['name_'.$label];
                    }
                }

                unset($item['name_mn']);
                unset($item['name_bn']);
                unset($item['name_ta']);
                unset($item['name_th']);
                if($item->slug == 'aspri')
                    $item->url = getOption('aspri_link');//'http://ipi.org.sg/';//'http://aitc.fortiddns.com:8069/event';
                elseif($item->slug == 'custom1')
                    $item->url = getOption('4d_link');//'http://www.singaporepools.com.sg/en/product/Pages/4d_results.aspx';
                elseif($item->slug == 'custom2')
                    $item->url = getOption('toto_link');//'http://www.singaporepools.com.sg/en/product/Pages/toto_results.aspx';
                elseif($item->slug == 'food')
                    $item->url = url('').'/customer/dashboard?token='.$token.'&time='.time();
                elseif($item->slug == 'games')
                    $item->url = getOption('games_link');//'http://gamecenter.yogrt.co/g/v1/#/?appid=152324230061613002';
                elseif($item->slug == 'free-wifi')
                    $item->url = getOption('wifi_link');
                else
                    $item->url = '';

                // if(strpos($item->slug, 'custom') !== false)
                //     $item->icon = static_file('images/icon-mom.png');
                // else
                if($item->slug == 'notifications' && $item->icon == '')
                    $item->icon = static_file('images/a2.png');
                elseif($item->slug == 'mom' && $item->icon == '')
                    $item->icon = static_file('images/img-m.jpg');
                elseif($item->slug == 'free-wifi' && $item->icon == '')
                    $item->icon = static_file('images/icon-mom.png');
                elseif($item->slug == 'food' && $item->icon == '')
                    $item->icon = static_file('images/naan.png');
                else{
                    if($item->icon != ''){
                        $item->icon = static_file($item->icon);
                    }else{
                        $item->icon = '';
                    }
                }


                // if($item->slug == 'custom1')
                //     $item->icon = static_file('images/4d.png');
                // if($item->slug == 'custom2')
                //     $item->icon = static_file('images/toto.png');

                if(in_array($item->id, [4,7,17])){
                    $item->soon = true;
                }else{
                    $item->soon = false;
                }
            }
            return response()->json(['status' => 'success', 'data' => $data, 'message' => 'SUCCESS'], 200);

        }catch(Exception $e){
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
    }
}
