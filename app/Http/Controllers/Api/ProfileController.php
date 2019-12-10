<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Validator;
use JWTAuth, DB, Hash;
use App\User;
use App\Models\UserProfile;
use App\Models\UserAuto;
use App\Models\Advertisement;
use App\Models\Mpopular;
use App\Models\Dormitory;
use App\Models\Menu;
use App\Models\Servicess;
use App\Models\ServicesLang;

use App\Models\Forum;
use App\Models\Course;
use App\Models\CourseLanguage;
use App\Models\Impression;
use App\Models\MomTopic;
use App\Models\MomTopicLang;
use App\Models\Country;
use App\Models\Residence;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Crypt;

use Illuminate\Contracts\Encryption\DecryptException;

use Carbon\Carbon, Activity;

use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;

class ProfileController extends Controller
{

    public function sendSingle(Request $request){
        $user = JWTAuth::toUser($request->input('token'));
        $type = (isset($request->type) && $request->type != '')? $request->type:'general';
        $id = (isset($request->id) && $request->id != '')? $request->id:'';
        $link = (isset($request->link) && $request->link != '')? $request->link:'';

        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60*20);
        $notificationBuilder = new PayloadNotificationBuilder('my title');
        $notificationBuilder->setBody('Hello world')->setSound('default')->setClickAction('FCM_PLUGIN_ACTIVITY');
        $dataBuilder = new PayloadDataBuilder();

        $dataBuilder->addData([
            'type' => $type
        ]);

        if($id != ''){
            $dataBuilder->addData([
                'id' => $id
            ]);
        }

        if($link != ''){
            $dataBuilder->addData([
                'link' => $link
            ]);
        }

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();
        $user = User::find(8);
        $token = $user->fcm_token;//'c2csAcHNzmE:APA91bFcgvNoH-w0ML8JclXqUQbuwIAajYMLto2dk2jQVva7psVhQzn8rYABg8vQKEUi9PhbLlmz4iGl-Udl6OX9PBZo03M9LpddUq9W-PWxxIh7-HFvVVpIECCT0huHaJD4FtAvFDrG';;
        $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);

        $downstreamResponse->numberSuccess();
        $downstreamResponse->numberFailure();
        $downstreamResponse->numberModification();
        //return Array - you must remove all this tokens in your database
        $downstreamResponse->tokensToDelete();
        //return Array (key : oldToken, value : new token - you must change the token in your database )
        $downstreamResponse->tokensToModify();
        //return Array - you should try to resend the message to the tokens in the array
        $downstreamResponse->tokensToRetry();
        return response()->json(['status' => 'success', 'data' => 'notification sent', 'message' => ''], 200);

    }

    private function sendMultiple($title, $description)
    {

        try{
            $optionBuilder = new OptionsBuilder();
            $optionBuilder->setTimeToLive(60*20);
            $notificationBuilder = new PayloadNotificationBuilder($title);
            $notificationBuilder->setBody($description)->setSound('default');

            $dataBuilder = new PayloadDataBuilder();
            $dataBuilder->addData(['a_data' => 'my_data']);
            $option = $optionBuilder->build();
            $notification = $notificationBuilder->build();

            $data = $dataBuilder->build();
            // You must change it to get your tokens

            $tokens = User::whereHas('roles', function($q){
                $q->where('slug', 'employee');
            })->where('blocked', '0')->whereNotNull('fcm_token')->pluck('fcm_token')->toArray();

            $downstreamResponse = FCM::sendTo($tokens, $option, $notification);
            // echo "<pre>";print_r($downstreamResponse);die();
            $downstreamResponse->numberSuccess();
            $downstreamResponse->numberFailure();
            $downstreamResponse->numberModification();
            //return Array - you must remove all this tokens in your database
            $downstreamResponse->tokensToDelete();
            //return Array (key : oldToken, value : new token - you must change the token in your database )
            $downstreamResponse->tokensToModify();
            //return Array - you should try to resend the message to the tokens in the array
            $downstreamResponse->tokensToRetry();
            // return Array (key:token, value:errror) - in production you should remove from your database the tokens present in this array
            $downstreamResponse->tokensWithError();
        }
        catch(Exception $e){
        dd($e->getMessage());
      }

    }

    public function home(Request $request)
    {
        $token = $request->input('token');
        $user = '';
        if($token != ''){
          $user = JWTAuth::toUser($token);
        }

        $allow_dorm_reporting = true;
        if($user && $user->profile && $user->profile->fin_no != ''){
            $fin = verifyFin($user->profile->fin_no);
            if($fin && $fin->verified){
                $dorm = Dormitory::where('full_name', $fin->dormitory)->first();
                if(!$dorm){
                    $allow_dorm_reporting = false;
                }
            }else{
                $allow_dorm_reporting = false;
            }
        }
        try{
            $language = (isset($request->language) && $request->language != '')? $request->language:'';

            if($language == 'bengali'){
                $label = 'bn';
            }elseif($language == 'chinese'){
                $language = "mandarin";
                $label = 'mn';
            }elseif($language == 'tamil'){
                $label = 'ta';
            }elseif($language == 'thai'){
                $label = 'th';
            }else{
                $label = '';
            }
            $now = Carbon::now();
            // $adds = Advertisement::join('plans', 'plans.id', '=', 'advertisement.plan_id')
            // ->join('impressions', 'impressions.ad_id', '=', 'advertisement.id')
            // ->where('impressions.impressions', '>=', 'pl_imp')
            // ->where('advertisement.type', 'home')->where('advertisement.adv_type', '1')->limit(6)
            // ->select('advertisement.*', 'impressions.ad_id','impressions.impressions', 'plans.impressions AS pl_imp')
            // ->orderBy('created_at', 'desc')
            // ->get();

            // $data['adds'] = $adds;


          // $list = Page::select('id', 'title')->get();

          // $ads =  Advertisement::select('advertisement.*','impressions.impressions as ad_imp')
          // ->leftJoin('impressions', function($join) {
          //      $join->on('advertisement.id', '=', 'impressions.ad_id');
          // })->leftJoin('plans', function($join) {
          //      $join->on('advertisement.plan_id', '=', 'plans.id');
          // })->where(function($q) use ($now){
          //     $q->where(function($qq) use($now){
          //        $qq->where('adv_type', '2')->whereDate('start', '<=', $now)->where('end', '>=', $now);
          //     })->orWhere(function($qw){
          //        $qw->where('adv_type', '1')->where('plans.impressions','>=', 'impressions.impressions');
          //     });
          // })
          // ->where('advertisement.type', 'home')->limit(6)->orderBy('slider_order', 'asc')->get();//->toArray();

          $ads =  Advertisement::select('advertisement.*','impressions.impressions as ad_imp')
          ->leftJoin('impressions', function($join) {
               $join->on('advertisement.id', '=', 'impressions.ad_id');
          })->where('advertisement.status', 'running')
          ->where('advertisement.type', 'home')->limit(6)->orderBy('slider_order', 'asc')->get();//->toArray();

          // if(count($ads) < 1){
          //     $ads = Advertisement::where('type', 'home')->limit(1)->orderBy('created_at', 'desc')->get()->toArray();
          // }
          $as = [];
          foreach($ads as $key => $ad){
              $a['id'] = $ad['id'];
              // $a['title'] = $ad['title'];
              $a['path'] = url($ad['path']);
              if($ad['adv_type'] == 1){
                 $a['ad_type'] = 'impression';
              }else{
                 $a['ad_type'] = 'date';
              }
              $a['link'] = @$ad['link'];
              $a['impressions'] = @$ad->plan->impressions;
              $a['start'] = $ad['start'];
              $a['end'] = $ad['end'];
              $a['slider_order'] = $ad['slider_order'];
              $a['ad_imp'] = $ad['ad_imp'];
              $as[] = $a;
          }
          $data['slider'] = $as;

          $where_label = 'title';
          if($label)
            $where_label .='_'.$label;

          $mom = MomTopic::whereHas('lang_content', function($q) use($language){
                $q->where('language', $language);
          })->orderBy('created_at', 'desc')->limit(3)->get()->toArray();
          foreach($mom as $key => $mo){
              $ser = MomTopicLang::where('topic_id', $mo['id'])->where('language', $language)->first();
              if($ser){
                  if($mo['type'] == 'file' || $mo['type'] == 'image' || $mo['type'] == 'video'){
                      $mom[$key]['content'] = url($ser->content);
                  }else{
                      $mom[$key]['content'] = $ser->content;
                      if($mo['type'] == 'youtube'){
                          $mom[$key]['content'] = $ser->video_id;
                      }
                  }
                  $mom[$key]['title'] = $ser->title;

              }else{
                  $mom[$key]['content'] = '';
                  $mom[$key]['title'] = '';
              }
              $mom[$key]['image'] = url((isset($mom[$key]['image']) && $mom[$key]['image'] != "" )? $mom[$key]['image'] : 'images/13481508821533457544.png');
          }
          $data['topic'] = $mom;

          // $mom = Services::whereHas('lang_content', function($q) use($language){
          //     $q->where('language', $language);
          // })->where('type', 'mom')->orderBy('created_at', 'desc')->limit(3)->get()->toArray();
          //
          // foreach($mom as $key => $mo){
          //     $ser = ServicesLang::where('services_id', $mo['id'])->where('language', $language)->first();
          //     if($ser){
          //         $mom[$key]['title'] = $ser->title;
          //         $mom[$key]['content'] = $ser->content;
          //         $mom[$key]['author'] = $ser->author;
          //     }else{
          //         $mom[$key]['title'] = '';
          //         $mom[$key]['content'] = '';
          //         $mom[$key]['author'] = '';
          //     }
          //
          //     unset($mom[$key]['title_ta']);
          //     unset($mom[$key]['title_mn']);
          //     unset($mom[$key]['title_bn']);
          //     // unset($mom[$key]['title_th']);
          //     unset($mom[$key]['content_ta']);
          //     unset($mom[$key]['content_mn']);
          //     unset($mom[$key]['content_bn']);
          //     // unset($mom[$key]['content_th']);
          //     unset($mom[$key]['author_ta']);
          //     unset($mom[$key]['author_mn']);
          //     unset($mom[$key]['author_bn']);
          //     // unset($mom[$key]['author_th']);
          //
          //     $mom[$key]['image'] = url((isset($mom[$key]['image']) && $mom[$key]['image'] != "" )? $mom[$key]['image'] : 'images/placeholder.png');
          // }
          // $data['services'] = $mom;

          if($user && $user->type != 'free'){
              $forums = Forum::orderBy('created_at', 'desc')->with('topic', 'user')->withCount('comments')->limit(4)->get()->toArray();
              foreach($forums as $key => $forum){
                  $forums[$key]['created_by'] = @$forum['user']['name'];
                  $forums[$key]['image'] = url((isset($forum['topic']['image']) && $forum['topic']['image'] != "" )? $forum['topic']['image'] : 'files/topic/default.jpg');
                  unset($forums[$key]['user']);
                  unset($forums[$key]['topic']);
              }
              $data['forums'] = $forums;
          }else{
              $data['forums'] = [];
          }

          if($user && $user->type != 'free'){
              $learning = Course::whereHas('lang_content', function($q) use($language){
                  $q->where('language', $language);
              })->where('course_type', 'course')->where('end_date', '>', Carbon::now())->orderBy('created_at', 'desc')->limit(3)->get()->toArray();
              foreach($learning as $key => $learn){

                  $cl = CourseLanguage::where('course_id', $learn['id'])->where('language', $language)->first();
                  if($cl){
                      $learning[$key]['title'] = str_limit($cl['title'], 18);
                  }

                  if($learn['image'] == ''){
                      $img = url('files/course/course-01.jpg');
                  }else{
                      $img = url($learn['image']);
                  }
                  $learning[$key]['image'] = $img;
              }
              $data['elearning'] = $learning;
          }else{
              $data['elearning'] = [];
          }
          $not_in = ['26'];

          if($user == '' || ($user && $user->type == 'free')){
              $menu = Menu::whereNotIn('id', $not_in)->where('access', 'free')->limit(4)->pluck('id')->toArray();
          }else{

              if($user->type == 'registered_verified'){
                  $not_allowed = ['26'];
              }else{
                  $not_allowed = ['11','12','25', '26'];
              }
              $menu = Mpopular::whereNotIn('menu_id', $not_allowed)->where('user_id', $user->id)->orderBy('count', 'desc')->groupBy('menu_id')->limit(4)->with('menu')->get()->toArray();

              switch(count($menu)){
                  case 1:
                  $arr = [];
                  $id = [];
                  foreach($menu as $me){
                      $id[] = $me['menu_id'];
                      $ar['id'] = $me['menu_id'];
                      $ar['name'] = $me['menu']['name'];
                      $arr[] = $ar;
                  }
                  // $a_menu = Menu::whereNotIn('id', $id)->select('id', 'name')->limit(3)->get()->toArray();
                  $a_menu = Menu::whereNotIn('id', $id)->limit(3)->pluck('id')->toArray();
                  $menu = array_merge($id, $a_menu);
                  break;
                  case 2:
                  $arr = [];
                  $id = [];
                  foreach($menu as $me){
                      $id[] = $me['menu_id'];
                      $ar['id'] = $me['menu_id'];
                      $ar['name'] = $me['menu']['name'];
                      $arr[] = $ar;
                  }
                  // $a_menu = Menu::whereNotIn('id', $id)->select('id', 'name')->limit(2)->get()->toArray();
                  $a_menu = Menu::whereNotIn('id', $id)->limit(2)->pluck('id')->toArray();
                  $menu = array_merge($id, $a_menu);

                  break;
                  case 3:
                  $arr = [];
                  $id = [];
                  foreach($menu as $me){
                      $id[] = $me['menu_id'];
                      $ar['id'] = $me['menu_id'];
                      $ar['name'] = $me['menu']['name'];
                      $arr[] = $ar;
                  }
                  // $a_menu = Menu::whereNotIn('id', $id)->select('id', 'name')->limit(1)->get()->toArray();
                  $a_menu = Menu::whereNotIn('id', $id)->limit(1)->pluck('id')->toArray();
                  $menu = array_merge($arr, $a_menu);
                  break;
                  case 4:
                  $arr = [];
                  $id = [];
                  foreach($menu as $me){
                      $id[] = $me['menu_id'];
                      $ar['id'] = $me['menu_id'];
                      $ar['name'] = $me['menu']['name'];
                      $arr[] = $ar;
                  }
                  // $menu = $arr;
                  $menu = $id;
                  break;
                  default:
                  // $menu = Menu::select('id', 'name')->limit(4)->get()->toArray();
                  $menu = Menu::limit(4)->pluck('id')->toArray();
              }

          }
          $menus = Menu::whereIn('id',$menu)->select('id','name','name_bn','name_ta','name_mn', 'name_th', 'slug', 'url')->get()->toArray();
          foreach($menus as $k => $mn){
              if($label != ''){
                  if($mn['name_'.$label] != ''){
                      $menus[$k]['name'] = $mn['name_'.$label];
                  }
              }

              if($menus[$k]['slug'] == 'aspri')
                  $menus[$k]['url'] = getOption('aspri_link');//'http://ipi.org.sg/';//'http://aitc.fortiddns.com:8069/event';
              elseif($menus[$k]['slug'] == 'custom1')
                  $menus[$k]['url'] = getOption('4d_link');//'http://www.singaporepools.com.sg/en/product/Pages/4d_results.aspx';
              elseif($menus[$k]['slug'] == 'custom2')
                  $menus[$k]['url'] = getOption('toto_link');//'http://www.singaporepools.com.sg/en/product/Pages/toto_results.aspx';
              elseif($menus[$k]['slug'] == 'food')
                  $menus[$k]['url'] = url('').'/customer/dashboard?token='.$token.'&time='.time();
              elseif($menus[$k]['slug'] == 'invoices')
                  $menus[$k]['url'] = route('customer.invoices.list', $token);
              elseif($menus[$k]['slug'] == 'games')
                  $menus[$k]['url'] = getOption('games_link');//'http://gamecenter.yogrt.co/g/v1/#/?appid=152324230061613002';
              elseif($menus[$k]['slug'] == 'free-wifi')
                  $menus[$k]['url'] = getOption('wifi_link');
              elseif($mn['url'] == '')
                  $menus[$k]['url'] = '';

              // if($menus[$k]['slug'] == 'aspri')
              //     $menus[$k]['url'] = 'http://ipi.org.sg/';//'http://aitc.fortiddns.com:8069/event';
              // elseif($menus[$k]['slug'] == 'food')
              //     $menus[$k]['url'] = url('').'/customer/dashboard?token='.$token.'&time='.time();
              // elseif($menus[$k]['slug'] == 'games')
              //     $menus[$k]['url'] = 'http://gamecenter.yogrt.co/g/v1/#/?appid=152324230061613002';
              // else
              //     $menus[$k]['url'] = '';

              if(strpos($menus[$k]['slug'], 'custom') !== false)
                  $menus[$k]['icon'] = static_file('images/icon-mom.png');
              elseif($menus[$k]['slug'] == 'notifications')
                  $menus[$k]['icon'] = static_file('images/a2A.png');
              elseif($menus[$k]['slug'] == 'mom')
                  $menus[$k]['icon'] = static_file('images/img-m.jpg');
              elseif($menus[$k]['slug'] == 'food')
                  $menus[$k]['icon'] = static_file('images/naan.png');
              else
                  $menus[$k]['icon'] = '';

              if($menus[$k]['slug'] == 'custom1')
                $menus[$k]['icon'] = static_file('images/4d.png');
              if($menus[$k]['slug'] == 'custom2')
                $menus[$k]['icon'] = static_file('images/toto.png');
              unset($menus[$k]['name_mn']);
              unset($menus[$k]['name_bn']);
              unset($menus[$k]['name_ta']);
              unset($menus[$k]['name_th']);
          }
          $data['menu'] = $menus;

          $now = Carbon::now();
          if($user){
            $end = Carbon::parse($user->profile->wp_expiry);
          }else{
            $end = Carbon::now();
          }
          $length = $now->diffInDays($end, false);

          if($user == "" || ($user && $user->type == 'free')){
              $flag = false;
          }else{
              if($length >= 0 && $length <= 7){
                  $flag = true;
              }elseif($length < 0){
                  if($user->type != 'free'){
                      $auth_user = User::find($user->id);
                      //disabled as wp expiry field is no longer mandatory and so that makes it not a criteria for changing user type
                      // $ddd = $auth_user->update(['type' => 'free']);
                      // Activity::log('User type changed to free on expiry of work permit', $user->id);
                  }
                  $flag = false;
              }else{
                  $flag = false;
              }
          }
          $data['flexm'] = @$user->flexm_account;
          $data['duration'] = 10000;
          $data['expiry_popup'] = [
              'show'    => $flag,
              'message' => 'Your work permit is about to expire, your account type will become ‘Free User’ instead of ‘Registered User’.',
          ];

          $event = Servicess::where('publish', '1')->where('type' , 'event-news');
          $event->where(function($q) use($user){
            $q->whereNull('dormitory_id')->orWhere('dormitory_id', 0);
            // if($user){
            //   $q->where('dormitory_id', $user->dormitory_id)
            // }else{
            //
            // }
          });

          $event = $event->orderBy('created_at', 'desc');
          $event = $event->limit(3)->withCount('likes')->get();
          foreach($event as $res){
              $ser = ServicesLang::where('services_id', $res['id'])->where('language', @$language)->first();
              if($ser){
                  $res['title'] = $ser->title;
                  $res['author'] = $ser->author == ''?'Unknown':$ser->author;
              }else{
                  $ser = ServicesLang::where('services_id', $res['id'])->where('language', 'english')->first();
                  if($ser){
                      $res['title'] = $ser->title;
                      $res['author'] = $ser->author == ''?'Unknown':$ser->author;
                  }else{
                      if($label != ''){
                          if($res['title_'.$label] != ''){
                              $res['title'] = $res['title_'.$label];
                          }
                          if($res['content_'.$label] != ''){
                              $res['content'] = $res['content_'.$label];
                          }
                          if($res['author_'.$label] != ''){
                              $res['author'] = $res['author_'.$label];
                          }
                      }
                  }
              }
              if($res['image'] != ''){
                  $res['image'] = url($res['image']);
              }else{
                  $res['image'] = url('images/placeholder.png');
              }
              $res['user_name'] = isset($res['author']) && $res['author'] != ''?$res['author']:'Admin';
              if($res['author_image'] == ''){
                  $prof = url('files/profile/user.jpg');
              }else{
                  $prof = url($res['author_image']);
              }
              $res['user_image'] = $prof;
              unset($res['author']);
              unset($res['author_image']);
              unset($res['profile']);
          }
          $data['events'] = $event;
          $data['food_url'] = url('').'/customer/dashboard?token='.$token.'&time='.time();
          $data['wifi_url'] = getOption('wifi_link');
          
          if($data){
            return response()->json(['status' => 'success', 'data' => $data, 'message' => 'DATA_LIST.'], 200);
          }else{
            return response()->json(['status' => 'error', 'data' => 'Their was a problem while fetching new data. Try Later.', 'message' => 'INSERTION_ERROR'], 404);
          }
        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function getPopupAd(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));

        try{
            $now = Carbon::now();
            // $ads = Advertisement::where(function($q) use ($now){
            //     $q->where(function($qq) use($now){
            //        $qq->where('adv_type', '2')->whereDate('start', '<=', $now)->where('end', '>=', $now);
            //     })->orWhere(function($qq){
            //        $qq->where('adv_type', '1')->whereHas('plan', function($qw){
            //            $qw->where('plans.impressions','>=', 'ad_imp');
            //        });
            //     });
            // })->leftJoin('impressions', function($join) {
            //      $join->on('advertisement.id', '=', 'impressions.ad_id');
            // })->select('advertisement.*','impressions.impressions as ad_imp')
            // ->where('type', 'landing')->limit(1)->orderBy('created_at', 'desc')->get();

          $ads = Advertisement::where('status', 'running')->where('type', 'landing')->limit(1)->orderBy('created_at', 'desc')->get()->toArray();
          $as = [];
          foreach($ads as $ad){
              $a['id'] = $ad['id'];
              // $a['title'] = $ad['title'];
              $a['path'] = url($ad['path']);
              if($ad['adv_type'] == 1){
                 $a['ad_type'] = 'impression';
              }else{
                 $a['ad_type'] = 'date';
              }
              $a['link'] = @$ad['link'];
              $a['impressions'] = @$ad['impressions'];
              $a['start'] = $ad['start'];
              $a['end'] = $ad['end'];
              $a['slider_order'] = $ad['slider_order'];
              $as[] = $a;
          }

          if(count($as) >= 0){
            return response()->json(['status' => 'success', 'data' => $as, 'message' => 'DATA_LIST.'], 200);
          }else{
            return response()->json(['status' => 'error', 'data' => 'Their was a problem while fetching new data. Try Later.', 'message' => 'INSERTION_ERROR'], 404);
          }
        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }
    public function profile(Request $request)
    {
        $userr = JWTAuth::toUser($request->input('token'));
        $user = $userr->toArray();
        try{
          // $list = Page::select('id', 'title')->get();
          $profile = UserProfile::where('user_id', $userr->id)->first();
          // $user = User::where('id', $user->id)->first();
          $fin_no = '';
          $street_address = "";
          if($profile){
              $street_address = $profile->street_address;
              if($profile->dormitory){
                  $street_address = $profile->dormitory->address;
              }
              $fin_no = $profile->fin_no;
              try{
                  $fin_no = decrypt($fin_no);
              }catch(DecryptException $e){
              }
              $profile = $profile->toArray();
          }
          $user['email'] = $userr->email;
          $user['qr_code'] = (@$userr->qr_code != '')?url(@$userr->qr_code):null;
          $user['phone'] = @$profile['phone'];
          $user['fin_no'] = @$fin_no;
          if($profile['profile_pic'] == ''){
              $user['profile_pic'] = (@$profile['profile_pic'] != '')?url(@$profile['profile_pic']):null;
          }else{
              $user['profile_pic'] = url($profile['profile_pic']);
          }
          $user['gender'] = @$profile['gender'];
          $user['dob'] = @$profile['dob'];
          $user['block'] = @$profile['block'];
          $user['sub_block'] = @$profile['sub_block'];
          $user['floor_no'] = @$profile['floor_no'];
          $user['unit_no'] = @$profile['unit_no'];
          $user['room_no'] = @$profile['room_no'];
          $user['zip_code'] = @$profile['zip_code'];
          $user['street_address'] = @$street_address;
          $user['dormitory_id'] = @$profile['dormitory_id'];
          $user['wp_front'] = (@$profile['wp_front'] != '')?url(@$profile['wp_front']):null;
          $user['wp_back'] = (@$profile['wp_back'] != '')?url(@$profile['wp_back']):null;
          $user['wp_expiry'] = (isset($profile['wp_expiry']) && $profile['wp_expiry'] != '0000-00-00')?$profile['wp_expiry']:'';
          $user['receive_notification'] = @$profile['receive_notification'];

          if($user){
              Activity::log('Accessed profile', $userr->id);
            return response()->json(['status' => 'success', 'data' => $user, 'message' => 'DATA_LIST.'], 200);
          }else{
            return response()->json(['status' => 'error', 'data' => 'Their was a problem while fetching new data. Try Later.', 'message' => 'INSERTION_ERROR'], 404);
          }
        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    protected function save_validator(array $data, $user)
    {
      return Validator::make($data, [
            'ad_id'       => 'required',
        ]);
    }

    public function save(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));

        try{
            $data = $request->all();

            if(@$data['name'] != ""){
                $user->name = @$data['name'];
            }
            
            if(@$data['country_id'] != ""){
                $user->country_id = @$data['country_id'];
            }

            if((@$data['old_password'] != "") && (@$data['password'] != "") && (@$data['password_confirmation'] != "")){
                if($data['password'] != $data['password_confirmation']){
                    return response()->json(['status' => 'error', 'data' => ["Password does not match"], 'message' => 'VALIDATION_ERROR'], 200);
                }
                elseif(Hash::check($data['old_password'], $user->password)){
                    $user->password = bcrypt($data['password']);
                }else{
                    return response()->json(['status' => 'error', 'data' => ["Old password does not match"], 'message' => 'VALIDATION_ERROR'], 200);
                }

            }
            $searchValue = strtolower(@$data['email']);
            if($searchValue != ""){
              $items = User::all()->filter(function($record) use($searchValue, $user) {
                          $email = $record->email;
                          try{
                              $email = Crypt::decrypt($email);
                          }catch(DecryptException $e){

                          }
                          if(($email) == $searchValue && $user->id != $record->id) {
                              return $record;
                          }
              });
              if(count($items)){
                return response()->json(['status' => 'error', 'data' => 'The email has already been taken.', 'message' => 'The email has already been taken.'], 200);
              }else{
                $user->email = $data['email'];
              }
            }
            $profile = UserProfile::where('user_id', '!=',$user->id)->where('phone', @$data['phone'])->first();
            if($profile){
              return response()->json(['status' => 'error', 'data' => "", 'message' => 'Phone number already exist.'], 200);
            }
            $profile = UserProfile::where('user_id', $user->id)->first();
            $type = 'free';
            if(@$data['fin_no'] != ""){
                $user_profile['fin_no'] = strToUpper(@$data['fin_no']);

                $fin = $this->verifyFin($data['fin_no']);
                $type = 'registered';
                if($fin && $fin->verified){
                    $dorm = Dormitory::where('full_name', $fin->dormitory)->first();
                    $data['dormitory_id'] = $dorm->id;
                    $type = 'registered_verified';
                }

                $user->type = $type;
                \QrCode::format('png')->size(400)->generate($data['fin_no'], '../public/files/qrcodes/'.$user->id.'.png');
                $user->qr_code = 'files/qrcodes/'.$user->id.'.png';
            }
            // else{
            //     $user_profile['fin_no'] = @$data['fin_no'];
            //     $user->update(['type', 'free']);
            // }
            if(@$data['phone'] != ""){
                $user_profile['phone'] = @$data['phone'];
            }
            if(@$data['profile_pic'] != ""){
                $photo = $data['profile_pic'];

                $folder = 'files/profile/';
                $photo_path = savePhoto($photo, $folder);
                $user_profile['profile_pic'] = $photo_path;
            }
            if(@$data['gender'] != ""){
                $user_profile['gender'] = @$data['gender'];
            }
            if(@$data['dob'] != ""){
                $dob = explode('/', $data['dob']);
                if(count($dob) > 1){
                    $data['dob'] = Carbon::createFromFormat('d/m/Y', $data['dob'])->toDateString();
                    $user_profile['dob'] = $data['dob'];
                }else{
                    $user_profile['dob'] = Carbon::parse($data['dob'])->toDateString();    
                }
                
            }
            if(@$data['block'] != ""){
                $user_profile['block'] = @$data['block'];
            }
            if(@$data['sub_block'] != ""){
                $user_profile['sub_block'] = @$data['sub_block'];
            }
            if(@$data['floor_no'] != ""){
                $user_profile['floor_no'] = @$data['floor_no'];
            }
            if(@$data['unit_no'] != ""){
                $user_profile['unit_no'] = @$data['unit_no'];
            }
            if(@$data['room_no'] != ""){
                $user_profile['room_no'] = @$data['room_no'];
            }
            if(@$data['zip_code'] != ""){
                $user_profile['zip_code'] = @$data['zip_code'];
            }
            if(@$data['street_address'] != ""){
                $user_profile['street_address'] = @$data['street_address'];
            }
            if(@$data['dormitory_id'] != ""){
                $user_profile['dormitory_id'] = @$data['dormitory_id'];
            }
            if(@$data['wp_front'] != ""){
                $photo = $data['wp_front'];

                $folder = 'files/permit/';
                $photo_path = savePhoto($photo, $folder);
                $user_profile['wp_front'] = $photo_path;
            }
            if(@$data['wp_back'] != ""){
                $photo = $data['wp_back'];

                $folder = 'files/permit/';
                $photo_path = savePhoto($photo, $folder);
                $user_profile['wp_back'] = $photo_path;
            }
            if(@$data['wp_expiry'] != ""){
                $user_profile['wp_expiry'] = Carbon::parse(@$data['wp_expiry'])->toDateString();
            }

            if(@$data['receive_notification'] != ""){
                $user_profile['receive_notification'] = @$data['receive_notification'];
            }

          if($user->save() && $profile->update($user_profile)){
              Activity::log('Updated profile', $user->id);
              return response()->json(['status' => 'success', 'data' => [
                  'type' => $user->type
              ], 'message' => 'DATA_UPDATED.'], 200);
          }else{
            return response()->json(['status' => 'error', 'data' => 'Their was a problem while fetching new data. Try Later.', 'message' => 'INSERTION_ERROR'], 404);
          }
        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    protected function view_validator(array $data, $user)
    {
      return Validator::make($data, [
            'ad_id'       => 'required',
        ]);
    }

    public function addImpression(Request $request)
    {

        $user = JWTAuth::toUser($request->input('token'));
        $validator = $this->view_validator($request->all(), $user);

        if ($validator->fails()) {
              $errors = $validator->errors();
              $message = [];
              foreach($errors->messages() as $key => $error){
                  $message[$key] = $error[0];
              }
              return response()->json(['status' => 'error', 'data' => $message, 'message' => 'VALIDATION_ERROR'], 200);
        }

        try{
          $ad_id = $request->ad_id;

          $service = Advertisement::find($ad_id);
          if(!empty($service)){
              $imp = Impression::where('ad_id', $ad_id)->first();
              if($imp){
                  $imp->impressions = ++$imp->impressions;
                  $imp->save();
              }else{
                  $data['ad_id'] = $ad_id;
                  Impression::create($data);
              }
              Activity::log('Impression added. Ad #'.$ad_id, $user->id);
              return response()->json(['status' => 'success', 'data' => 'Impression added', 'message' => 'SUCCESS'], 200);
          }else{
              return response()->json(['status' => 'success', 'data' => 'Ad does not exist', 'message' => 'ERROR'], 200);
          }

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function getContact(Request $request)
    {
        try{
            $data['phone'] = getOption('contact_us_no');
            $data['email'] = getOption('contact_us_email');
            $data['duration'] = 5000;
            
            return response()->json(['status' => 'success', 'data' => $data, 'message' => 'SUCCESS'], 200);
        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    protected function fin_validator(array $data)
    {
      return Validator::make($data, [
            'fin_no'       => 'required',
        ]);
    }

    public function verify(Request $request)
    {
        $validator = $this->fin_validator($request->all());

        if($request->input('token'))
            $user = JWTAuth::toUser($request->input('token'));
        else
            $user = '';

        if ($validator->fails()) {
              $errors = $validator->errors();
              $message = [];
              foreach($errors->messages() as $key => $error){
                  $message[$key] = $error[0];
              }
              return response()->json(['status' => 'error', 'data' => $message, 'message' => $message], 200);
        }

        try{
          $fin_no = $request->fin_no;
          $prof_fin_no = '';
          $profile = '';
          if($user != ''){
              $profile = UserProfile::where('user_id', $user->id)->first();
              if($profile){
                  $prof_fin_no = $profile->fin_no;
                  // if($fin_no == $prof_fin_no){
                  //     return response()->json(['status' => 'error', 'data' => 'Already have an account with this fin no', 'message' => 'ERROR'], 200);
                  // }

                  // try{
                  // }catch(DecryptException $e){
                  //     $prof_fin_no = $profile->fin_no;
                  // }
              }
          }
          $prof_fin = '';
          if($profile == '' || ($fin_no != $prof_fin_no)){
              // $exist = UserProfile::where('fin_no', $fin_no)->get()->count();
              $exist = UserProfile::all()->filter(function($record) use($fin_no) {
                          $prof_fin = $record->fin_no;
                          try{
                              $prof_fin = Crypt::decrypt($prof_fin);
                          }catch(DecryptException $e){

                          }
                          if(strToUpper($prof_fin) == strToUpper($fin_no)) {
                              return $record;
                          }
              });
              if($exist->count()){
                  return response()->json(['status' => 'error', 'data' => 'Already have an account with this fin no', 'message' => 'Already have an account with this fin no', 'prefil' => []], 200);
              }else{
                $exist = UserAuto::all()->filter(function($record) use($fin_no) {
                            $prof_fin = $record->fin_no;
                            try{
                                $prof_fin = Crypt::decrypt($prof_fin);
                            }catch(DecryptException $e){

                            }
                            if(strToUpper($prof_fin) == strToUpper($fin_no)) {
                                return $record;
                            }
                });
                if($exist->count()){
                    $d = $exist->first();
                    if($d){
                      $fin_no = $d->fin_no;
                      try{
                          $fin_no = decrypt($fin_no);
                      }catch(DecryptException $e){
                      }
                      $f = $d->toArray();
                      $f['fin_no'] = $fin_no;
                      unset($f['nationality']);
                      unset($f['dorm']);
                      if($f['dormitory_id'] != ''){
                        $f['street_address'] = '';
                      }
                      return response()->json(['status' => 'success', 'data' => 'Correct.', 'message' => 'SUCCESS', 'prefil' => $f], 200);
                    }else{
                      return response()->json(['status' => 'success', 'data' => 'Correct.', 'message' => 'SUCCESS', 'prefil' => []], 200);
                    }
                }else{
                  return response()->json(['status' => 'success', 'data' => 'Correct.', 'message' => 'SUCCESS', 'prefil' => []], 200);
                }
              }
          }else{
              return response()->json(['status' => 'success', 'data' => 'Correct.', 'message' => 'SUCCESS'], 200);
          }
          // return response()->json(['status' => 'success', 'data' => 'Valid FIN No', 'message' => 'SUCCESS'], 200);
          // $response = $this->verifyFin($fin_no);
          // if(isset($response->verified) && $response->verified){
          //     return response()->json(['status' => 'success', 'data' => 'Valid FIN No', 'message' => 'SUCCESS'], 200);
          // }else{
          //     return response()->json(['status' => 'error', 'data' => 'This resident with FIN number:'.$fin_no.' is not found.', 'message' => 'ERROR'], 200);
          // }

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> $e->getMessage()],200);
        }
    }

    public function verifyFin($fin_no)
    {
        // $fin_no = 'G5098264K';
        try{
            $client = new Client(); //GuzzleHttp\Client
            $result = $client->get("http://residents.centurioncorp.com.sg/mymaapi/api/resident?json={'fin_no':'".$fin_no."','phone_no':'','gender':''}");
            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();
                $content = json_decode($body->getContents());

                return $content;
            }else{
                return false;
            }
        }catch(Exception $e){
            return false;
        }
    }
    
    public function getNations(Request $request)
    {
        try{
          $list = Country::select('id', 'nationality')->get()->toArray();
          return response()->json(['status' => 'success', 'data' => $list, 'message' => 'SUCCESS'], 200);
        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }
    
    public function getResidence(Request $request)
    {
        try{
            $list = Residence::select('id', 'en_short_name as name')->get()->toArray();
            return response()->json(['status' => 'success', 'data' => $list, 'message' => 'SUCCESS'], 200);
        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }
}
