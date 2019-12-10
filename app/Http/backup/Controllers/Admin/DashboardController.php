<?php

namespace App\Http\Controllers\Admin;

use App\User, App\Models\Activity, App\Models\Badwords;
use App\Models\Forum, DB;
use App\Models\Maintenance,App\Models\FoodMenu;
use App\Models\Role;
use App\Models\Coupon;
use App\Models\Restaurant;
use App\Models\Order;
use App\Models\Feedback;
use App\Models\LoginHistory;
use App\Models\Transactions;
use App\Models\Payout;
use App\Models\Share;
use App\Models\Wallet;
use App\Models\Merchant;

use App\Models\Advertisement;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\EditShareRequest;
use App\Http\Controllers\Controller;
use Carbon\Carbon, Auth;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Activity as ActivityLog;

class DashboardController extends Controller
{


    public function check(Request $request){
      try{
          $token = $request->input('g-recaptcha-response');
          $client = new Client(['verify' => false]);

          $data = [];
          $result = $client->post('https://www.google.com/recaptcha/api/siteverify', [
            'form_params' => [
              'secret' => '6LfPOnUUAAAAAA8lqt3dS3R_wl-085O7NrMUCAd2',
              'response' => $token,
            ]
          ]);
          $code = $result->getStatusCode(); // 200
          $reason = $result->getReasonPhrase(); // OK
          if($code == "200" && $reason == "OK"){
              $body = $result->getBody();
              $content = json_decode($body->getContents());
              if($content->success){
                session(['captcha' => 'true']);
                return redirect()->route('login');
              }else{
                ActivityLog::log('Something went wrong with the google captcha #'.json_encode($content));
                return redirect()->back();
              }
          }else{
              $body = $result->getBody();
              $content = json_decode(@$body->getContents());
              ActivityLog::log('Something went wrong with the google captcha #'.json_encode($content));
              return redirect()->back();
          }
      }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            ActivityLog::log('Something went wrong with the google captcha #'.json_encode($jsonBody));
            return redirect()->back();
      }catch(GuzzleException $e){
            ActivityLog::log('Something went wrong with the google captcha #'.json_encode($e->getMessage()));
            return redirect()->back();
      }catch(Exception $e){
            ActivityLog::log('Something went wrong with the google captcha #'.json_encode($e->getMessage()));
            return redirect()->back();
      }
    }

    public function getIndex()
    {
        if(\Auth::guest()){
            return redirect()->route('login');
        }else{
          $auth_user = $user = Auth::user();
          // if($_SERVER['REMOTE_ADDR'] == '110.225.220.67'){
            $id = request()->input('id');
            if($id != ''){
              $id = decrypt($id);
              if($user->id != $id){
                Auth::logout();
                return redirect('/login');
              }
            }
          // }
          if($user->hasRole('spuul')){
            $merchant = Merchant::where('user_id', $user->id)->first();
            if($merchant){
                return $this->payoutPage($merchant->id);
            }
          }elseif($user->hasRole('training')){
            $merchant = Merchant::where('user_id', $user->id)->first();
            if($merchant){
                return $this->payoutPage($merchant->id);
            }

          }elseif($user->hasRole('food-admin|restaurant-owner-single|restaurant-owner-catering')){
            $data['total_items'] = FoodMenu::count();
            $now = Carbon::now();

            $data['total_new_orders'] = Order::whereDate('delivery_date', '>=', $now)->count();
            if($auth_user->hasRole('restaurant-owner-single|restaurant-owner-catering')){
              $restaurant = Restaurant::where('merchant_id', $auth_user->id)->first();
              $restaurant_id = "";
              if($restaurant){
                $restaurant_id = $restaurant->id;
              }
              $data['total_items'] = FoodMenu::where('restaurant_id', $restaurant_id)->get()->count();
              $data['total_new_orders'] = Order::whereHas('items', function($q) use ($restaurant_id){
                $q->whereHas('item', function($qq) use ($restaurant_id){
                  $qq->where('restaurant_id', $restaurant_id);
                });
              })->whereDate('delivery_date', '>=', $now)->count();
            }
            // $data['total_caterers'] = User::whereHas('roles', function($q){
            //     $q->where('slug', 'restaurant-owner-catering');
            // })->count();
            //
            // $data['total_outlet'] = User::whereHas('roles', function($q){
            //     $q->where('slug', 'restaurant-owner-single');
            // })->count();

            $data['total_restaurant'] = Restaurant::count();


            $data['total_coupons'] = Coupon::whereDate('expiry','>=', $now)->count();

            $data['total_new_items'] = FoodMenu::where('published', '0')->count();

            return view('admin.dashboard.index', compact('data', 'user'));

          }else{

            $aaj = Carbon::now()->toDateString();

            $login = LoginHistory::whereDate('logged_time', $aaj)->where('type', 'login')->groupby('user_id')->distinct('user_id')->get()->count();

            $logout = LoginHistory::whereDate('logged_time', $aaj)->where('type', 'logout')->groupby('user_id')->distinct('user_id')->get()->count();
            $data['logged_in'] = $login;
            $data['logged_out'] = $logout;

            $data['total_users'] = User::whereHas('roles', function($q){
              $q->where('slug', 'app-user');
            })->count();

            $data['total_ads'] = Advertisement::where('type', '!=','food')->where('status', 'running')->count();

            $currentDate = new Carbon();
            $start = $currentDate->startOfWeek()->subWeek();
            $end = new Carbon();
            $current_week = date("W");

            $sql = "SELECT WEEK(`created_at`,1) w, YEARWEEK(`created_at`,1) k,COUNT(`id`) total FROM `users` where created_at between '".$start."' AND '".$end."' GROUP BY YEARWEEK(`created_at`,1)";
            $result = DB::select($sql);
            $text = "0% Higher than last week";
            $cnt = count($result);
            if($cnt){
              if($cnt == 1){
                $p1 = 0;
                $p2 = $result[0]->total;
              }else{
                $p1 = $result[0]->total;
                $p2 = $result[1]->total;
              }

              $per = number_format((($p1-$p2)/$p2)*100);
              if($cnt == 1 && $result[0]->w == $current_week){
                $per = abs($per);
              }
              if($per < 0)
              $text = abs($per)."% Lower than last week";
              else
              $text = $per."% Higher than last week";
            }
            $data['total_users_text'] = $text;

            $data['not_verified'] = User::where('type', 'free')->whereHas('roles', function($q){
              $q->where('slug', 'app-user');
            })->count();

            $data['bad_words'] = Forum::where('bad_word', '1')->count();

            $data['forum_reported'] = Forum::where('report', '1')->count();

            $sql = "SELECT WEEK(`created_at`,1) w, YEARWEEK(`reported_at`,1) k,COUNT(`id`) total FROM ".config('app.db_portal').".`forum` where report = '1' AND reported_at between '".$start."' AND '".$end."' GROUP BY YEARWEEK(`reported_at`,1)";
            $result = DB::select($sql);
            $text = "0% Higher than last week";
            $cnt = count($result);
            if($cnt){
              if($cnt == 1){
                $p1 = 0;
                $p2 = $result[0]->total;
              }else{
                $p1 = $result[0]->total;
                $p2 = $result[1]->total;
              }

              $per = number_format((($p1-$p2)/$p2)*100, 0);
              if($cnt == 1 && $result[0]->w == $current_week){
                $per = abs($per);
              }
              if($per < 0)
              $text = abs($per)."% Lower than last week";
              else
              $text = abs($per)."% Higher than last week";
            }
            $data['forum_reported_text'] = $text;

            $data['maintenance'] = Maintenance::where('status_id', '1')->count();

            $sql = "SELECT WEEK(`created_at`,1) w, YEARWEEK(`created_at`,1) k,COUNT(`id`) total FROM ".config('app.db_portal').".`maintenance` where created_at between '".$start."' AND '".$end."' GROUP BY YEARWEEK(`created_at`,1)";
            $result = DB::select($sql);
            $text = "0% Higher than last week";
            $cnt = count($result);
            if($cnt){
              if($cnt == 1){
                $p1 = 0;
                $p2 = $result[0]->total;
              }else{
                $p1 = $result[0]->total;
                $p2 = $result[1]->total;
              }

              $per = number_format((($p1-$p2)/$p2)*100, 0);
              if($cnt == 1 && $result[0]->w == $current_week){
                $per = abs($per);
              }
              if($per < 0)
              $text = abs($per)."% Lower than last week";
              else
              $text = abs($per)."% Higher than last week";
            }
            $data['maintenance_text'] = $text;

            $now = Carbon::now()->toDateString();
            $type = 'feedback';
            $items = Feedback::where('type', $type)->whereDate('created_at', $now)->get()->count();

            $data['feedback_count'] = $items;

            $sql = "SELECT WEEK(`created_at`,1) w, YEARWEEK(`created_at`,1) k,COUNT(`id`) total FROM ".config('app.db_portal').".`feedback` where type = 'feedback' AND created_at between '".$start."' AND '".$end."' GROUP BY YEARWEEK(`created_at`,1)";
            $result = DB::select($sql);
            $text = "0% Higher than last week";
            if($_SERVER['REMOTE_ADDR'] == '122.173.134.196'){

              // dd($result);
            }
            $cnt = count($result);
            if($cnt){
              if($cnt == 1){
                $p1 = 0;
                $p2 = $result[0]->total;
              }else{
                $p1 = $result[0]->total;
                $p2 = $result[1]->total;
              }

              $per = number_format((($p1-$p2)/$p2)*100, 0);
              if($cnt == 1 && $result[0]->w == $current_week){
                $per = abs($per);
              }
              if($per < 0)
              $text = abs($per)."% Lower than last week";
              else
              $text = abs($per)."% Higher than last week";
            }
            $data['feedback_text'] = $text;

            $logs = Activity::orderBy('created_at', 'desc')->limit(10)->get();
            foreach($logs as $log){
              if($log->user_id)
              $log->user = User::find($log->user_id);
              else
              $log->user = null;
            }

            $data['logs'] = $logs;

            $user = \Auth::user();

            $analyticsData = \LaravelAnalytics::getVisitorsAndPageViews(7);
            $endDate = Carbon::today();
            $startDate = Carbon::today()->subDays(7);
            $answer = \LaravelAnalytics::performQuery($startDate, $endDate, 'ga:users', ['dimensions' => 'ga:mobileDeviceModel']);

            if (is_null($answer->rows)) {
              $modelWise = [];
            }else{
              foreach ($answer->rows as $pageRow) {
                $modelWise[] = ['model' => $pageRow[0], 'visitors' => $pageRow[1]];
              }
            }


            return view('admin.dashboard.index', compact('data', 'user', 'analyticsData', 'modelWise'));
          }
        }
    }

    public function payoutPage($merchant_id)
    {
        $auth_user = Auth::user();
        // if ($merchant_id = $request->input('merchant_id')) {
            $now = Carbon::now();
            $merchant = Merchant::find($merchant_id);
            // whereDate('payout_date', '>=', $now->toDateString())->
            $payout = Payout::where('merchant_id', $merchant_id)->orderBy('id', 'desc')->first();
            if($payout){
              $end_date = $payout->payout_date;
              $exist = 1;
            }else{
              $exist = 0;
              $start_date = $merchant->start_date != ''?$merchant->start_date:$merchant->created_at;
              $start_date = Carbon::parse($start_date);

              $pay_insert = Payout::create([
                'merchant_id' => $merchant_id,
                'amount' => 0,
                'start_date' => $start_date->toDateString(),
                'status' => 'pending'
              ]);
              $payout = Payout::find($pay_insert->id);
            }

            if($merchant->frequency > 0){
              if($exist){
                  $start_date = Carbon::parse($end_date)->subDays($merchant->frequency);
              }
              $end_date = Carbon::parse($start_date)->addDays($merchant->frequency);
            }else{
              if($exist){
                  $start_date = Carbon::parse($end_date)->subDays(7);
              }
              $end_date = Carbon::parse($start_date)->addDays(7);
            }
            // elseif($merchant->frequency == '1_month'){
            //   if($exist){
            //       $start_date = Carbon::parse($end_date)->subMonth();
            //   }
            //   $end_date = Carbon::parse($start_date)->addMonth();
            // }
            // elseif($merchant->frequency == '2_month'){
            //   if($exist){
            //       $start_date = Carbon::parse($end_date)->subMonth();
            //   }
            //   $end_date = Carbon::parse($start_date)->addMonth();
            // }
            // elseif($merchant->frequency == '3_month'){
            //   if($exist){
            //       $start_date = Carbon::parse($end_date)->subMonth();
            //   }
            //   $end_date = Carbon::parse($start_date)->addMonth();
            // }

            if($exist == 0){
              $payout->update([
                'payout_date' => $end_date
              ]);
            }

            $start_date = $start_date;//->toDateString();
            //subtract a day as time period ended a day before
            $end_date = $end_date->subDay();
            $end_date = $end_date->toDateString();

            $total = Payout::where('merchant_id', $merchant->id)->where('status', 'paid')->sum('amount');
            $sum = Transactions::where('mid', $merchant->mid)->whereDate('created_at', '>=', $start_date)->whereDate('created_at', '<=', $end_date)->sum('other_share');
            if($payout){
              $payout->update([
                'amount'  => $sum
              ]);
            }
            $data['total'] = $total;
            $data['amount'] = $sum;
            $data['next_payout_date'] = $payout->payout_date;
            $data['last_amount'] = 0;
            $today = false;
            if($data['next_payout_date'] == Carbon::now()->toDateString()){
              $today = true;
            }

            if($data['next_payout_date'] < Carbon::now()->toDateString()){
              $data['next_payout_date'] = 0;
            }
            $data['last_payout_pending'] = false;
            $now = Carbon::now()->toDateString();
            $pending = Payout::where('merchant_id', $merchant_id)->where('status', 'pending')->count();
            $data['last_payout_list'] = [];
            $total = 0;

            if($pending == 1 && $data['next_payout_date'] < Carbon::now()->toDateString()){
              $data['amount'] = 0;
              $data['last_payout_pending'] = true;
              $ll = Payout::where('merchant_id', $merchant_id)->where('status', 'pending')->where('payout_date', '<', $now)->orderBy('id', 'desc');
              $data['last_payout_list'] = $ll->get();
              $data['last_amount'] = $ll->sum('amount');
              $total = Payout::where('merchant_id', $merchant_id)->where('status', 'pending')->sum('amount');
            }
            if($pending > 1){
              $data['last_payout_pending'] = true;
              $ll = Payout::where('merchant_id', $merchant_id)->where('status', 'pending')->where('payout_date', '<', $now)->orderBy('id', 'desc');
              $data['last_payout_list'] = $ll->get();
              $data['last_amount'] = $ll->sum('amount');
              $total = Payout::where('merchant_id', $merchant_id)->where('status', 'pending')->sum('amount');
            }
            $data['last_amount'] = number_format($data['last_amount'],2);
            $merchant_id = encrypt($merchant_id);
            return view('admin.payout.view', compact('items', 'auth_user', 'data', 'today', 'total', 'merchant_id'));
        // }
    }

    public function getLoggedIn(Request $request)
    {
        if(\Auth::guest()){
            return view('auth.login');
        }else{
            $aaj = Carbon::now()->toDateString();

            $items = LoginHistory::whereDate('logged_time', $aaj)->where('type', 'login')->groupby('user_id')->distinct('user_id');
            $limit = 10;
            $items = $items->paginate($limit);
            $paginate_data = $request->except('page');

            $user = \Auth::user();
            $title = "Todays Logged in Users";
            $td_title = "Logged In Time";
            return view('admin.dashboard.logged', compact('items', 'user', 'paginate_data','title', 'td_title'));
        }
    }

    public function getLoggedOut(Request $request)
    {
        if(\Auth::guest()){
            return view('auth.login');
        }else{
            $aaj = Carbon::now()->toDateString();

            $items = LoginHistory::whereDate('logged_time', $aaj)->where('type', 'logout')->groupby('user_id')->distinct('user_id');
            $limit = 10;
            $items = $items->paginate($limit);
            $paginate_data = $request->except('page');

            $user = \Auth::user();
            $title = "Todays Logged out Users";
            $td_title = "Logged Out Time";
            return view('admin.dashboard.logged', compact('items', 'user', 'paginate_data', 'title', 'td_title'));
        }
    }

    public function getTransactions(Request $request)
    {

        if(\Auth::guest()){
            return view('auth.login');
        }else{
            $data['myma_share'] = Transactions::sum('myma_share');
            $data['merchant_share'] = Transactions::sum('other_share');
            $data['flexm_share'] = Transactions::sum('flexm_part');
            $items = Transactions::orderBy('created_at', 'desc');
            if($id = $request->input('transaction_id')){
                $items->where('transaction_ref_no', 'like', "%{$id}%");
            }

            $limit = 10;
            $items = $items->paginate($limit);
            $paginate_data = $request->except('page');

            $user = \Auth::user();

            return view('admin.dashboard.transactions', compact('items', 'user', 'paginate_data', 'data'));
        }
    }

    public function viewTransaction($id, Request $request)
    {
        if(\Auth::guest()){
            return view('auth.login');
        }else{
            $id = decrypt($id);
            $item = Transactions::findOrFail($id);
            $wallet = Wallet::where('transaction_ref_no', $item->transaction_ref_no)->first();
            $user = \Auth::user();
            return view('admin.dashboard.view', compact('item', 'user', 'wallet'));
        }
    }

    public function editTransaction($id, Request $request)
    {
        if(\Auth::guest()){
            return view('auth.login');
        }else{
            $id = decrypt($id);
            $item = Transactions::findOrFail($id);
            if($item->status != 'paid'){
                $user = \Auth::user();
                return view('admin.dashboard.edit', compact('item', 'user'));
            }else{
                return redirect()->route('admin.transactions.logs')->with([
                    'flash_level'   => 'error',
                    'flash_message' => "This transaction already paid can't edit its details.",
                ]);
            }
        }
    }

    public function postTransaction($id, EditShareRequest $request)
    {
        if(\Auth::guest()){
            return view('auth.login');
        }else{
            $id = decrypt($id);
            $item = Transactions::findOrFail($id);
            if($item->status != 'paid'){
                $data = $request->only('myma_share','other_share','myma_part', 'flexm_part', 'status');
                $actual_total = $item->myma_part + $item->flexm_part;
                $updated_total = $data['myma_part']+$data['flexm_part'];
                if($actual_total < $updated_total){
                    return back()->withErrors('Transaction charges total cant be greater than original total. That is : '.$actual_total);
                }elseif($actual_total > $updated_total){
                    return back()->withErrors('Transaction charges total cant be less than original total. That is : '.$actual_total);
                }

                $total = $item->transaction_amount;
                $charges = $item->myma_part + $item->flexm_part;
                $total = $total - $charges;
                if($item->myma_share != '' && $item->flexm_share != ''){
                    $updated_share = $data['myma_share']+$data['other_share'];
                    $actual_share = $item->myma_share + $item->other_share;
                    if($actual_share < $updated_share){
                        return back()->withErrors('Share total cant be greater than original total. That is : '.$total);
                    }elseif($actual_share > $updated_share){
                        return back()->withErrors('Share total cant be less than original total. That is : '.$total);
                    }
                }else{

                    $updated_share = $data['myma_share']+$data['other_share'];
                    if($updated_share > $total || $updated_share < $total){
                        return back()->withErrors('Share total cant be greater than or less than original total. That is : '.$total);
                    }
                }

                $item->update($data);

                return redirect()->route('admin.transactions.logs')->with([
                    'flash_level'   => 'success',
                    'flash_message' => 'Details updated successfully.',
                ]);

            }else{
                return redirect()->route('admin.transactions.logs')->with([
                    'flash_level'   => 'error',
                    'flash_message' => "This transaction already paid can't update its details.",
                ]);
            }
        }
    }

    public function getAjaxLogs(){

        $offset = \Input::get('start');
        $limit = \Input::get('length');
        $searched = \Input::get('myKey');

        if($limit == '' || $limit == 0){
            $limit = 10;
        }
        // $offset = $page*$limit;
        $auth_user = \Auth::user();
        $logs = Activity::orderBy('created_at', 'desc');
        if($searched != ''){
            $logs->where('text', 'like', '%'.$searched.'%');
        }
        if(!$auth_user->hasRole('admin')){
            $logs->where('user_id', $auth_user);
        }
        $count_items = $logs->count();
        $items = $logs->limit($limit)->offset($offset)->get();
        $data['draw'] = \Input::get('draw', 1);
        $data['recordsTotal'] = $count_items;
        $data['recordsFiltered'] = $count_items;

        $arr = [];
        foreach($items as $key => $item){
            $dd = [];
            $dd[] = ++$offset;
            $dd[] = $item->text;
            $dd[] = isset($item->user)?$item->user->name:'';

            $item->role = '';
            if($item->user){
                $role_key = current(array_keys($item->user->getRoles()));
                if($role_key){
                    $role = Role::find($role_key);
                    $item->role = $role->name;
                    if($role_key == 3){
                        $item->role .=' ('.$item->user->type.')';
                    }
                }
            }
            $dd[] = $item->role;
            $dd[] = $item->ip_address;
            $dd[] = $item->created_at->format('d/m/Y h:i A');
            // $dd[] = '<a href="'.route('admin.logs.delete', ['id' => $item->id, '_token' => csrf_token()]).'" data-message="Are you sure about deleting this?" class="post-delete"><i class="fa fa-2x fa-trash-o"></i></a>';
            $arr[] = $dd;
        }
        $data['data'] = $arr;
        return response()->json($data);
    }

    public function getLogs(Request $request)
    {
      $auth_user = Auth::user();

      $logs = Activity::orderBy('created_at', 'desc');

      $searched = $request->input('keyword');
      if(@$searched != ''){
          $logs->where('text', 'like', '%'.$searched.'%');
      }

      $from = $request->input('from');
      $to = $request->input('to');
      if($from != '' && $to != ''){
          $logs->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to);
      }

      if(!$auth_user->hasRole('admin')){
          $logs->where('user_id', $auth_user);
      }

      $limit = 10;
      $items = $logs->paginate($limit);

      foreach($items as $key => $item){

          $item->role = '';
          if($item->user){
              $role_key = current(array_keys($item->user->getRoles()));
              if($role_key){
                  $role = Role::find($role_key);
                  $item->role = $role->name;
                  if($role_key == 3){
                      $item->role .=' ('.$item->user->type.')';
                  }
              }
          }
      }

      $paginate_data = $request->except('page');

      $user = User::find(1);
      $message = "User purchased a course by paying $100.";
      $type = "";
      $id = "";
      $link = "";
      sendSingleLocal($user, $message, $type, $id, $link, 'user');

      return view('admin.dashboard.logs', compact('items', 'auth_user', 'paginate_data'));
    }

    public function getLogsOld(Request $request){

        $limit = 10;
        $offset = 0;
        if($request->ajax()){
            $offset = \Input::get('start');
            $limit = \Input::get('length');
            $searched = \Input::get('myKey');

            if($limit == '' || $limit == 0){
                $limit = 10;
            }
        }


        $auth_user = \Auth::user();

        $logs = Activity::orderBy('created_at', 'desc');

        if(@$searched != ''){
            $logs->where('text', 'like', '%'.$searched.'%');
        }

        if(!$auth_user->hasRole('admin')){
            $logs->where('user_id', $auth_user);
        }

        $count_items = $logs->count();
        $items = $logs->limit($limit)->offset($offset)->get();
        $data['draw'] = \Input::get('draw', 1);
        $data['recordsTotal'] = $count_items;
        $data['recordsFiltered'] = $count_items;

        $arr = [];
        foreach($items as $key => $item){
            $dd = [];
            $dd[] = ++$offset;
            $dd[] = $item->text;
            $dd[] = isset($item->user)?$item->user->name:'';

            $item->role = '';
            if($item->user){
                $role_key = current(array_keys($item->user->getRoles()));
                if($role_key){
                    $role = Role::find($role_key);
                    $item->role = $role->name;
                    if($role_key == 3){
                        $item->role .=' ('.$item->user->type.')';
                    }
                }
            }
            $dd[] = $item->role;
            $dd[] = $item->ip_address;
            $dd[] = $item->created_at->format('d/m/Y h:i A');
            $dd[] = '<a href="'.route('admin.logs.delete', ['id' => $item->id, '_token' => csrf_token()]).'" data-message="Are you sure about deleting this?" class="post-delete"><i class="fa fa-2x fa-trash-o"></i></a>';
            $arr[] = $dd;
        }
        $data['data'] = $arr;
        if($request->ajax()){
            return response()->json($data);
        }

        // $items = $logs->limit(10)->get();
        // foreach($items as $item){
        //     $item->role = '';
        //     if($item->user){
        //         $key = current(array_keys($item->user->getRoles()));
        //         if($key){
        //             $role = Role::find($key);
        //             $item->role = $role->name;
        //             if($key == 3){
        //                 $item->role .=' ('.$item->user->type.')';
        //             }
        //         }
        //     }
        // }
        return view('admin.dashboard.logs', compact('items'));
    }

    public function getDelete($id)
    {
        Activity::destroy($id);

        return redirect()->route('admin.activity.logs')->with([
            'flash_level'   => 'danger',
            'flash_message' => 'Activity Deleted',
        ]);
    }

    public function getTest(){
        $words = 'My bad word bitch fuck';
        $obj = Badwords::select('language', 'word')->get()->toArray();

        $clean_words = \ConsoleTVs\Profanity\Builder::blocker($words)->dictionary($obj);
        echo "<pre>";print_r($clean_words->clean());
        // print_r($clean_words->text($words));
        print_r($clean_words->badWords());
        dd($clean_words->filter());
    }

    public function getUpload()
    {
        // if(\Auth::guest()){
        //     return view('auth.login');
        // }else{
            return view('admin.dashboard.upload');
        // }
    }

    public function postUpload(Request $request)
    {
        $data = $request->only('type');

        if($request->hasFile('file')) {
          $file = $request->file('file');

          $folder = "files/".$data['type'];
          $mimeType = $file->getClientMimeType();

          $filename = $file->getClientOriginalName();
          $filename = preg_replace("/[^a-zA-Z0-9.]/", "_", $filename);
          $extension     = pathinfo($filename, PATHINFO_EXTENSION);
          // $blacklist = array(".php", ".phtml", ".php3", ".php4", ".js", ".shtml", ".pl" ,".py");
          // foreach ($blacklist as $files)
          // {
          $allowed = '.csv';
              if(!preg_match("/$allowed\$/i", $filename))
              {
                return back()->withErrors([
                  'Only csv files are allowed to upload.',
                ]);
              }
          // }

          // if(in_array($mimeType, $video_mime_types)){
              //throw error
          // }
          if($data['type'] == 'remittance'){
            $filename    = "remittance_report.csv";
          }else{
            $filename    = "wallet_report.csv";
          }
          $path = uploadFlexmDoc($file, $folder, $filename);

        }

        return redirect()->route('admin.upload.flexm')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Uploaded successfully.',
        ]);
    }
}
