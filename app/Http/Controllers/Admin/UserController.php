<?php

namespace App\Http\Controllers\Admin;

use App\Events\AccountCreated;
use App\User;
use App\Models\UserProfile;
use App\Models\Role;
use App\Models\Dormitory;
use Illuminate\Http\Request;
use App\Helper\RandomStringGenerator;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

use App\Http\Requests;
use App\Http\Requests\AddEmployeeRequest;
use App\Http\Requests\EditEmployeeRequest;
use App\Http\Controllers\Controller;
use Auth,  Carbon\Carbon;
use Activity, Excel;
use PHPExcel_Worksheet_Drawing;

class UserController extends Controller
{
    public function downloadPermit(Request $request)
    {
        $auth_user = \Auth::user();
        $id = $request->input('id');
        $type = $request->input('type');
        try{
          $id = decrypt($id);
        }catch(DecryptException $e){
            abort('404');
        }
        $profile = UserProfile::where('user_id',$id)->first();
        if($type == 'front'){
          $extension = explode('.', $profile->wp_front);
          if(isset($extension[1])){
            $extensions = 'jpg';
          }else{
            $extensions = $extension[1];
          }
          Activity::log('Downloaded work permit photo - front');
          return response()->download(public_path($profile->wp_front), $profile->phone.'.'.$extensions);
        }elseif($type == 'back'){
          $extension = explode('.', $profile->wp_back);
          if(isset($extension[1])){
            $extensions = 'jpg';
          }else{
            $extensions = $extension[1];
          }
          Activity::log('Downloaded work permit photo - back');
          return response()->download(public_path($profile->wp_back), $profile->phone.'_1.'.$extensions);
        }

        abort('404');
    }
    
    public function exportExcel(Request $request)
    {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300);
        // $id = decrypt($id);
        // $data['item'] = Incident::findOrFail($id);
        $items = User::orderBy('created_at');
    
        $verified = $request->input('verified');
        if ($verified == 'false' && $verified != "") {
            $items = $items->where('type', 'free');
        }
        if ($role_id = $request->input('role')) {
            $items = $items->whereHas('roles', function ($q) use ($role_id) {
                $q->where('slug', $role_id);
            });
        }

        if ($name = $request->input('name')) {
            $items->where('name', 'like', "%{$name}%");
        }
        
        if ($phone = $request->input('phone')) {
            $items->whereHas('profile', function ($q) use ($phone) {
                $q->where('phone', 'like', "%{$phone}%");
            });
        }
        
        if ($good = $request->input('good_for_wallet')) {
            $items->where('good_for_wallet', $good);
        }
        if ($updated_at = $request->input('updated_at')) {
            $updated_at = Carbon::createFromFormat('d/m/Y', $updated_at);
            $items->whereDate('updated_at', $updated_at);
        }
        
        if ($fin_no = $request->input('fin_no')) {
            $fin_no = strtoupper($fin_no);
            $items->whereHas('profile', function ($q) use ($fin_no) {
                $q->where('fin_no', 'like', "%{$fin_no}%");
            });
        }
        $dorm = $request->input('dormitory_id');
        if ($dorm != '' && $dorm != 0) {
            $items->whereHas('profile', function ($q) use ($dorm) {
                $q->where('dormitory_id', $dorm);
            });
        }
        if ($searchValue = $request->input('email')) {
            $searched = User::all()->filter(function($record) use($searchValue) {
                        $email = $record->email;
                        try{
                            $email = Crypt::decrypt($email);
                        }catch(DecryptException $e){

                        }
                        if (strpos($email, $searchValue) !== false) {
                              return $record;
                        }
            });
            if($searched->count()){
                $existing = $searched->pluck('id');
            }
            else{
                $existing = [];
            }
            $items->whereIn('id', $existing);
        }

        $paymentsArray = [];
        $items = $items->get();

        if(count($items)){
            if($role_id == 'app-user'){
                $paymentsArray[] = array('Name','Email', 'Fin No', 'Type','Phone','Gender','DOB', 'Dormitory','Block','Sub Block','Floor No','Unit No',
                'Room No', 'ZIP Code', 'Street Address', 'WP Expiry', 'Signup Date');
            }else{
                $paymentsArray[] = array('Name','Email');
            }
        }

        foreach($items as $item){
            $fin_no = $item->profile?$item->profile->fin_no:'';
            try{
              $fin_no = decrypt($fin_no);
            }catch(DecryptException $e){

            }
            $arr = [];
            $arr[] = $item->name;
            $arr[] = $item->email;
            $arr[] = $fin_no;
            if($role_id == 'app-user'){
                if($item->type == 'free'){
                  $arr[] = 'Not-Verified';
                }else{
                  $arr[] = 'Verified';
                }
                $arr[] = $item->profile?$item->profile->phone:'';
                $arr[] = $item->profile?$item->profile->gender:'';
                $arr[] = ($item->profile && $item->profile->dob != '0000-00-00')?$item->profile->dob:'';
                $arr[] = $item->profile?($item->profile->dormitory?$item->profile->dormitory->name:''):'';
                $arr[] = $item->profile?$item->profile->block:'';
                $arr[] = $item->profile?$item->profile->sub_block:'';
                $arr[] = $item->profile?$item->profile->floor_no:'';
                $arr[] = $item->profile?$item->profile->unit_no:'';
                $arr[] = $item->profile?$item->profile->room_no:'';
                $arr[] = $item->profile?$item->profile->zip_code:'';
                $arr[] = $item->profile?($item->profile->dormitory?$item->profile->dormitory->address:$item->profile->street_address):'';
                $arr[] = ($item->profile && $item->profile->wp_expiry != '0000-00-00')?$item->profile->wp_expiry:'';
                $arr[] = $item->created_at->format('d/m/Y');
                // $arr[] = ($item->profile && $item->profile->wp_front != '')?url($item->profile->wp_front):'';
            }

            $paymentsArray[] = $arr;
        }

        // dd($paymentsArray);

        Excel::create('Users', function($excel) use ($paymentsArray) {
          // Set the spreadsheet title, creator, and description
            $excel->setTitle('User List');
            $excel->setCreator('Myma')->setCompany('Singapore');
            // $excel->setDescription('payments file');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($paymentsArray) {
                $sheet->fromArray($paymentsArray, null, 'A1', false, false);
            });
        })->export('xls');
    }
    
    public function exportFlexmExcel(Request $request)
    {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300);
        // $id = decrypt($id);
        // $data['item'] = Incident::findOrFail($id);
        $items = User::orderBy('created_at');
    
        $role_id = 'app-user';
        $items = User::whereHas('roles', function ($q) use ($role_id) {
              $q->where('slug', $role_id);
        })->where(function($q){
            $q->where('flexm_cron', 1);
        });

        if ($name = $request->input('name')) {
            $items->where('name', 'like', "%{$name}%");
        }
        
        if ($phone = $request->input('phone')) {
            $items->whereHas('profile', function ($q) use ($phone) {
                $q->where('phone', 'like', "%{$phone}%");
            });
        }
        if ($fin_no = $request->input('fin_no')) {
            $fin_no = strtoupper($fin_no);
            $items->whereHas('profile', function ($q) use ($fin_no) {
                $q->where('fin_no', 'like', "%{$fin_no}%");
            });
        }
        $dorm = $request->input('dormitory_id');
        if ($dorm != '' && $dorm != 0) {
            $items->whereHas('profile', function ($q) use ($dorm) {
                $q->where('dormitory_id', $dorm);
            });
        }
        if ($searchValue = $request->input('email')) {
            $searched = User::all()->filter(function($record) use($searchValue) {
                        $email = $record->email;
                        try{
                            $email = Crypt::decrypt($email);
                        }catch(DecryptException $e){

                        }
                        if (strpos($email, $searchValue) !== false) {
                              return $record;
                        }
            });
            if($searched->count()){
                $existing = $searched->pluck('id');
            }
            else{
                $existing = [];
            }
            $items->whereIn('id', $existing);
        }

        $paymentsArray = [];
        $items = $items->orderby('flexm_cron_date','desc')->get();

        if(count($items)){
            if($role_id == 'app-user'){
                $paymentsArray[] = array('ID', 'Name','Email','Phone', 'Status','Reason');
            }else{
                $paymentsArray[] = array('ID', 'Name','Email');
            }
        }
        $i = 1;
        foreach($items as $item){
            $fin_no = $item->profile?$item->profile->fin_no:'';
            try{
              $fin_no = decrypt($fin_no);
            }catch(DecryptException $e){

            }
            $arr = [];
            $arr[] = $i++;
            $arr[] = $item->name;
            $arr[] = $item->email;
            // $arr[] = $fin_no;
            if($role_id == 'app-user'){
                $arr[] = $item->profile?$item->profile->phone:'';
                if($item->flexm_account == 1){
                    $arr[] = 'Registered';
                }else{
                    $arr[] = 'Error';
                }
                $arr[] = $item->flexm_error_text;
            }

            $paymentsArray[] = $arr;
        }

        // dd($paymentsArray);

        Excel::create('Users', function($excel) use ($paymentsArray) {
          // Set the spreadsheet title, creator, and description
            $excel->setTitle('User List');
            $excel->setCreator('Myma')->setCompany('Singapore');
            // $excel->setDescription('payments file');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($paymentsArray) {
                $sheet->fromArray($paymentsArray, null, 'A1', false, false);
            });
        })->export('xls');
    }

    public function getList(Request $request)
    {
      ini_set('memory_limit', '512M');

      $auth_user = Auth::user();

      $role_id = $request->input('role');
      if($auth_user->hasRole('app-user-manager') && $role_id != 'app-user'){
        return redirect()->route('admin.user.role-list');
      }
      $not_in = [5,6,7,8];
      $skip = false;
      if($auth_user->hasRole('food-admin')){
          $roles = Role::whereIn('id', $not_in)->pluck('slug')->toArray();
          if(in_array($role_id, $roles)){

          }else{
            $skip = true;
          }
      }else{
          $roles = Role::whereNotIn('id', $not_in)->pluck('slug')->toArray();
          if(in_array($role_id, $roles)){

          }else{
            $skip = true;
          }
      }

      if ($role_id = $request->input('role')) {
          $items = User::whereHas('roles', function ($q) use ($role_id) {
              $q->where('slug', $role_id);
          });
      }else{
          return redirect()->route('admin.user.role-list');
      }
      $verified = $request->input('verified');
      if ($verified == 'false') {
          $items = $items->where('type', 'free');
      }

      // if($_SERVER['REMOTE_ADDR'] == '122.173.131.248'){
      if ($good = $request->input('good_for_wallet')) {
          $items->where('good_for_wallet', $good);
      }
      if ($updated_at = $request->input('updated_at')) {
          $updated_at = Carbon::createFromFormat('d/m/Y', $updated_at);
          $items->whereDate('updated_at', $updated_at->toDateString());
      }
        if ($phone = $request->input('phone')) {
            $items->whereHas('profile', function ($q) use ($phone) {
                $q->where('phone', 'like', "%{$phone}%");
            });
        }
        if ($fin_no = $request->input('fin_no')) {
            $fin_no = strtoupper($fin_no);
            $items->whereHas('profile', function ($q) use ($fin_no) {
                $q->where('fin_no', 'like', "%{$fin_no}%");
            });
        }
      // }
      // if($auth_user->hasRole('admin')){
      //   $items = User::orderBy('created_at', 'desc');
      // }else if($auth_user->hasRole('sub_admin')){
      //   $items = User::whereHas('roles', function($q){
      //     $q->where('slug', '!=', 'admin');
      //   })->orderBy('created_at', 'desc');
      // }

      if ($name = $request->input('name')) {
          $items->where('name', 'like', "%{$name}%");
      }

      if ($searchValue = $request->input('email')) {
          $searchValue = strtolower($searchValue);
          $searched = User::all()->filter(function($record) use($searchValue) {
                      $email = $record->email;
                      try{
                          $email = Crypt::decrypt($email);
                      }catch(DecryptException $e){

                      }
                      if (strpos($email, $searchValue) !== false) {
                            return $record;
                      }
          });
          if($searched->count()){
              $existing = $searched->pluck('id');
          }
          else{
              $existing = [];
          }
          $items->whereIn('id', $existing);
      }

      if ($username = $request->input('username')) {
          $items->where(function ($q) use ($username) {
              $q->where('name', 'like', "%{$username}%")
                  ->orWhere('email', 'like', "%{$username}%");
          });
      }

      $dorm = $request->input('dormitory_id');
      if ($dorm != '' && $dorm != 0) {
          $items->whereHas('profile', function ($q) use ($dorm) {
              $q->where('dormitory_id', $dorm);
          });
      }

      // if($role_id == 'app-user'){
          // $items->groupBy('fin_no');
      // }

      $limit = 50;
      $users = $items->sortable(['id' => 'desc'])->paginate($limit);
      $paginate_data = $request->except('page');

      $dorm = Dormitory::pluck('name' ,'id')->toArray();
      $dorm[0] = 'Select Dormitory';
      ksort($dorm);

      $goods[''] = 'Select Good for wallet';
      $goods['Y'] = 'Yes';
      $goods['N'] = 'No';
      $goods['C'] = 'Corrected';
      $goods['D'] = 'Done';
      return view('admin.user.list', compact('users', 'auth_user', 'paginate_data', 'role_id', 'dorm', 'goods'));
    }
    
    public function getFlexmList(Request $request)
    {
      $auth_user = Auth::user();

      $role_id = 'app-user';
      $items = User::whereHas('roles', function ($q) use ($role_id) {
              $q->where('slug', $role_id);
          })->where(function($q){
            $q->where('flexm_cron', 1);
          });

      if ($phone = $request->input('phone')) {
            $items->whereHas('profile', function ($q) use ($phone) {
                $q->where('phone', 'like', "%{$phone}%");
            });
      }
      if ($fin_no = $request->input('fin_no')) {
            $fin_no = strtoupper($fin_no);
            $items->whereHas('profile', function ($q) use ($fin_no) {
                $q->where('fin_no', 'like', "%{$fin_no}%");
            });
      }
      if ($name = $request->input('name')) {
          $items->where('name', 'like', "%{$name}%");
      }

      if ($searchValue = $request->input('email')) {
          $searchValue = strtolower($searchValue);
          $searched = User::all()->filter(function($record) use($searchValue) {
                      $email = $record->email;
                      try{
                          $email = Crypt::decrypt($email);
                      }catch(DecryptException $e){

                      }
                      if (strpos($email, $searchValue) !== false) {
                            return $record;
                      }
          });
          if($searched->count()){
              $existing = $searched->pluck('id');
          }
          else{
              $existing = [];
          }
          $items->whereIn('id', $existing);
      }

      if ($username = $request->input('username')) {
          $items->where(function ($q) use ($username) {
              $q->where('name', 'like', "%{$username}%")
                  ->orWhere('email', 'like', "%{$username}%");
          });
      }

      $dorm = $request->input('dormitory_id');
      if ($dorm != '' && $dorm != 0) {
          $items->whereHas('profile', function ($q) use ($dorm) {
              $q->where('dormitory_id', $dorm);
          });
      }
      $limit = 50;
      $users = $items->sortable(['flexm_cron_date' => 'desc'])->paginate($limit);
      $paginate_data = $request->except('page');

      $dorm = Dormitory::pluck('name' ,'id')->toArray();
      $dorm[0] = 'Select Dormitory';
      ksort($dorm);

      return view('admin.user.flexm_registered', compact('users', 'auth_user', 'paginate_data', 'role_id', 'dorm'));
    }
    
    public function getRoleList()
    {
        $auth_user = \Auth::user();
        $not_in = [5,6,7,8];
        if($auth_user->hasRole('food-admin')){
          $items = Role::whereIn('id', $not_in)->pluck('name' ,'slug');
        }else{
          if($auth_user->hasRole('app-user-manager')){
            $items = Role::whereIn('slug', ['app-user'])->pluck('name' ,'slug');
          }else{
            $items = Role::whereNotIn('id', $not_in)->pluck('name' ,'slug');
          }

        }

        return view('admin.user.role_list', compact('items'));
    }

    public function getAdd()
    {
        $auth_user = \Auth::user();
        $not_in = [5,6,7,8];
        if($auth_user->hasRole('food-admin')){
            $roles = Role::whereIn('id', $not_in)->pluck('name' ,'slug');
        }else{
            $roles = Role::whereNotIn('id', $not_in)->pluck('name' ,'slug');
        }
        $dorm = Dormitory::pluck('name' ,'id')->toArray();
        $dorm[0] = 'Select Dormitory';
        ksort($dorm);
        return view('admin.user.add', compact('auth_user', 'roles', 'dorm'));
    }

    public function postAdd(AddEmployeeRequest $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $data = $request->only('email', 'language', 'name', 'password', 'dormitory_id');
        $role = $request->input('role');
        $data['email'] = strtolower($data['email']);
        $searchValue = $data['email'];
        if($searchValue != ""){
            $items = User::all()->filter(function($record) use($searchValue) {
                        $email = $record->email;
                        try{
                            $email = Crypt::decrypt($email);
                        }catch(DecryptException $e){
    
                        }
                        if(($email) == $searchValue) {
                            return $record;
                        }
            });
            if(count($items)){
                return redirect()->back()->withInput($request->input())->withErrors([
                    'email' => 'The email has already been taken.',
                ]);
            }
        }

        $pass = $data['password'];
        $data['password'] = bcrypt($data['password']);
        $data['register_by'] = 'portal';

        $data_profile = $request->only('fin_no', 'vehicle_no', 'phone', 'gender', 'dob', 'street_address', 'block', 'sub_block', 'room_no', 'floor_no', 'unit_no', 'zip_code', 'dormitory_id');

        $data['type'] = 'free';
        if($role == 'driver'){
          $data['type'] = $request->type;
        }
        $dorm = '';
        $fin = '';
        if($role == 'app-user' && isset($data_profile['fin_no']) && $data_profile['fin_no'] != ''){
                $data['type'] = 'registered';
                $fin = verifyFin($data_profile['fin_no']);
                if($fin && $fin->verified){
                    $dorm = Dormitory::where('full_name', $fin->dormitory)->first();
                    $data['type'] = 'registered_verified';
                    \QrCode::format('png')->size(400)->generate($data_profile['fin_no'], '../public/files/qrcodes/'.$data_profile['fin_no'].'.png');
                    $data['qr_code'] = 'files/qrcodes/'.$data_profile['fin_no'].'.png';
                }
        }

        if($data_profile['floor_no'] == ''){
            $data_profile['floor_no'] = NULL;
        }
        if($data_profile['unit_no'] == ''){
            $data_profile['unit_no'] = NULL;
        }
        if($data_profile['room_no'] == ''){
            $data_profile['room_no'] = NULL;
        }
        // if($request->input('role') == 'employee'){
        //   $data['nric'] = $request->input('nric');
        //   $data['grade'] = $request->input('grade');
        //   $data['job_scope'] = $request->input('job_scope');
        // }
        $user = User::create($data);
        $data_profile['user_id'] = $user->id;
        if ($request->hasFile('profile_pic')) {
          $file = $request->file('profile_pic');
          $folder = "files/profile";

          if (!is_dir($folder)) {
              mkdir($folder, 755, true);
          }
          $filename = $file->getClientOriginalName();
          // $blacklist = array(".php", ".phtml", ".php3", ".php4", ".js", ".shtml", ".pl" ,".py");
          // foreach ($blacklist as $files)
          // {
          //     if(preg_match("/$files\$/i", $filename))
          //     {
          //       return response()->json([
          //         'flash_level'   => 'danger',
          //         'flash_message' => 'Uploading executable files Not Allowed.',
          //       ], 412);
          //     }
          // }
            // if($file->getSize() > 10485760){
            //     return response()->json([
            //       'flash_level'   => 'danger',
            //       'flash_message' => 'File size must be equal to or less than 10mb.',
            //     ], 413);
            // }

          $filename = preg_replace("/[^a-zA-Z0-9.]/", "_", $filename);
          $actual_name   = pathinfo($filename, PATHINFO_FILENAME);
          $original_name = $actual_name;
          $extension     = pathinfo($filename, PATHINFO_EXTENSION);

          $i = 1;
          while(file_exists($folder . '/' . $actual_name . "." . $extension))
          {
              $actual_name = (string) $original_name . $i;
              $filename    = $actual_name . "." . $extension;
              $i++;
          }
          $full_file_name  = $folder . '/' . $filename;
          $file->move($folder, $filename);

          // $full_image_name = $folder . str_random() . '.jpg';
          // \Image::make($file->getRealPath())->fit(200, 200, function ($constraint) {
          //     $constraint->upsize();
          // })->save(public_path($full_image_name));

          $data_profile['profile_pic'] = $full_file_name;
        }

        if($role = $request->input('role')){
          $user->assignRole($role);
        }

        if($user->hasRole('app-user')){
          $generator = new RandomStringGenerator;
          $tokenLength = 32;

          $token = $generator->generate($tokenLength);
          $flag = true;
          while($flag){
            $exist = User::where('uid', $token)->first();
            if($exist){
              $token = $generator->generate($tokenLength);
            }else{
              $flag = false;
            }
          }
          $user->uid = $token;
          $user->save();
        }

        UserProfile::create($data_profile);
        Activity::log('New user created - #'.$user->id.' by '. $auth_user->name );

        event(new AccountCreated($user->id, $pass));
        // \Event::fire('user.inform_account', [$user->id, $pass]);

        return redirect()->route('admin.user.role-list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'User added successfully.',
        ]);

    }
    public function getEdit($id, Request $request)
    {

    	$auth_user = \Auth::user();
        $not_in = [5,6,7,8];
        if($auth_user->hasRole('food-admin')){
            $roles = Role::whereIn('id', $not_in)->pluck('name' ,'id');
        }else{
            $roles = Role::whereNotIn('id', $not_in)->pluck('name' ,'id');
        }
        try{
            $id = decrypt($id);
        }catch(DecryptException $e){
              abort('404');
        }
        	  $user = User::findOrFail($id);

            $dorm = Dormitory::pluck('name' ,'id')->toArray();
            $dorm[0] = 'Select Dormitory';
            ksort($dorm);

            if($user->profile){
                if($user->profile->wp_expiry == '0000-00-00'){
                    $user->profile->wp_expiry = '';
                }
                if($user->profile->dob == '0000-00-00'){
                    $user->profile->dob = '';
                }
            }
            if(@$user->profile->fin_no != ''){
              try{
                $user->profile->fin_no = decrypt($user->profile->fin_no);
              }catch(DecryptException $e){

              }
            }

        // $profile = $user->profile;

        return view('admin.user.edit', compact('user', 'roles', 'dorm', 'auth_user'));
    }

    public function getView($id, Request $request)
    {
    	$auth_user = \Auth::user();
        // $not_in = [5,6,7,8];
        // if($auth_user->hasRole('food-admin')){
        //     $roles = Role::whereIn('id', $not_in)->pluck('name' ,'slug');
        // }else{
        //     $roles = Role::whereNotIn('id', $not_in)->pluck('name' ,'slug');
        // }
        
        $roles = Role::pluck('name' ,'id');
        try{
            $id = decrypt($id);
        }catch(DecryptException $e){
              abort('404');
        }
        	  $user = User::findOrFail($id);

            if($user && $user->profile && $user->profile->fin_no != ''){
              try{
                $user->profile->fin_no = decrypt($user->profile->fin_no);
              }catch(DecryptException $e){

              }
            }
            //\Log::debug(json_encode($user->getRoles()));
        return view('admin.user.view', compact('user', 'roles', 'auth_user'));
    }

    public function postEdit($id, EditEmployeeRequest $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $id = decrypt($id);
        $user = User::findOrFail($id);

        if($request->input('password')){
          $data = $request->only('email', 'language', 'name', 'password', 'blocked', 'dormitory_id', 'good_for_wallet');
          $data['password'] = bcrypt($data['password']);
        }else{
          $data = $request->only('email', 'language', 'name', 'blocked', 'dormitory_id', 'good_for_wallet');
        }
        if($user->good_for_wallet != $data['good_for_wallet']){
          $data['good_by'] = $auth_user->id;
          $data['good_date'] = Carbon::now();
        }
        $data['email'] = strtolower($data['email']);
        $searchValue = $data['email'];
        if($searchValue != ""){
            $items = User::all()->filter(function($record) use($searchValue, $id) {
                        $email = $record->email;
                        try{
                            $email = Crypt::decrypt($email);
                        }catch(DecryptException $e){
    
                        }
                        if($email == $searchValue && $id != $record->id) {
                            return $record;
                        }
            });
            if(count($items)){
                return redirect()->back()->withInput($request->input())->withErrors([
                    'email' => 'The email has already been taken.',
                ]);
            }
        }
        $data_profile = $request->only('fin_no', 'vehicle_no', 'phone', 'gender', 'dob', 'street_address', 'block', 'sub_block', 'room_no', 'floor_no', 'unit_no', 'zip_code', 'wp_expiry', 'dormitory_id');
        
        $data_profile['fin_no'] = strtoupper($data_profile['fin_no']);
        $searchValue = $data_profile['fin_no'];

        if($searchValue != ""){
            $items = UserProfile::all()->filter(function($record) use($searchValue, $id) {
                    $email = $record->fin_no;
                    try{
                        $email = Crypt::decrypt($email);
                    }catch(DecryptException $e){

                    }
                    if($email == $searchValue && $id != $record->user_id) {
                        return $record;
                    }
            });
            if(count($items)){
                return redirect()->back()->withInput($request->input())->withErrors([
                    'fin_no' => 'The Fin No has already been taken.',
                ]);
            }
        }
        $phone = $data_profile['phone'];
        if($phone != ""){
            $items = UserProfile::where('phone', $phone)->where('user_id', '!=', $id)->first();
            if($items){
                return redirect()->back()->withInput($request->input())->withErrors([
                    'phone' => 'The phone no has already been taken.',
                ]);
            }
        }
        
        $role = $request->input('role');
        $data['type'] = 'free';
        if($role == 6){
          $data['type'] = $request->type;
        }
        $dorm = '';
        $fin = '';
        if($role == '3' && isset($data_profile['fin_no']) && $data_profile['fin_no'] != ''){

                $data['type'] = 'registered';
                $fin = verifyFin($data_profile['fin_no']);
                if($fin && $fin->verified){
                    $dorm = Dormitory::where('full_name', $fin->dormitory)->first();
                    $data['type'] = 'registered_verified';
                    \QrCode::format('png')->size(400)->generate($data_profile['fin_no'], '../public/files/qrcodes/'.$id.'.png');
                    $data['qr_code'] = 'files/qrcodes/'.$id.'.png';
                }
        }

        if ($request->hasFile('profile_pic')) {
          $file = $request->file('profile_pic');
          $folder = "files/profile";

          if (!is_dir($folder)) {
              mkdir($folder, 755, true);
          }
          $filename = $file->getClientOriginalName();
          // $blacklist = array(".php", ".phtml", ".php3", ".php4", ".js", ".shtml", ".pl" ,".py");
          // foreach ($blacklist as $files)
          // {
          //     if(preg_match("/$files\$/i", $filename))
          //     {
          //       return response()->json([
          //         'flash_level'   => 'danger',
          //         'flash_message' => 'Uploading executable files Not Allowed.',
          //       ], 412);
          //     }
          // }
            // if($file->getSize() > 10485760){
            //     return response()->json([
            //       'flash_level'   => 'danger',
            //       'flash_message' => 'File size must be equal to or less than 10mb.',
            //     ], 413);
            // }

          $filename = preg_replace("/[^a-zA-Z0-9.]/", "_", $filename);
          $actual_name   = pathinfo($filename, PATHINFO_FILENAME);
          $original_name = $actual_name;
          $extension     = pathinfo($filename, PATHINFO_EXTENSION);

          $i = 1;
          while(file_exists($folder . '/' . $actual_name . "." . $extension))
          {
              $actual_name = (string) $original_name . $i;
              $filename    = $actual_name . "." . $extension;
              $i++;
          }
          $full_file_name  = $folder . '/' . $filename;
          $file->move($folder, $filename);

          // $full_image_name = $folder . str_random() . '.jpg';
          // \Image::make($file->getRealPath())->fit(200, 200, function ($constraint) {
          //     $constraint->upsize();
          // })->save(public_path($full_image_name));

          $data_profile['profile_pic'] = $full_file_name;
        }

        if ($request->hasFile('wp_front')) {
          $file = $request->file('wp_front');
          $folder = "files/permit";

          if (!is_dir($folder)) {
              mkdir($folder, 755, true);
          }
          $filename = $file->getClientOriginalName();
          $filename = preg_replace("/[^a-zA-Z0-9.]/", "_", $filename);
          $actual_name   = pathinfo($filename, PATHINFO_FILENAME);
          $original_name = $actual_name;
          $extension     = pathinfo($filename, PATHINFO_EXTENSION);

          $i = 1;
          while(file_exists($folder . '/' . $actual_name . "." . $extension))
          {
              $actual_name = (string) $original_name . $i;
              $filename    = $actual_name . "." . $extension;
              $i++;
          }
          $full_file_name  = $folder . '/' . $filename;
          $file->move($folder, $filename);

          $data_profile['wp_front'] = $full_file_name;
        }

        if ($request->hasFile('wp_back')) {
          $file = $request->file('wp_back');
          $folder = "files/permit";

          if (!is_dir($folder)) {
              mkdir($folder, 755, true);
          }
          $filename = $file->getClientOriginalName();
          $filename = preg_replace("/[^a-zA-Z0-9.]/", "_", $filename);
          $actual_name   = pathinfo($filename, PATHINFO_FILENAME);
          $original_name = $actual_name;
          $extension     = pathinfo($filename, PATHINFO_EXTENSION);

          $i = 1;
          while(file_exists($folder . '/' . $actual_name . "." . $extension))
          {
              $actual_name = (string) $original_name . $i;
              $filename    = $actual_name . "." . $extension;
              $i++;
          }
          $full_file_name  = $folder . '/' . $filename;
          $file->move($folder, $filename);

          $data_profile['wp_back'] = $full_file_name;
        }

        $roles = $user->getRoles();

        if(!in_array($request->input('role'), array_keys($roles))){
          $user->revokeRole($roles);
          $user->assignRole($request->input('role'));
        }
        $role = array_values($roles);
        $data['password_retry'] = 0;
        $user->update($data);
        if($data_profile['floor_no'] == ''){
            $data_profile['floor_no'] = NULL;
        }
        if($data_profile['unit_no'] == ''){
            $data_profile['unit_no'] = NULL;
        }
        if($data_profile['room_no'] == ''){
            $data_profile['room_no'] = NULL;
        }
        if($user->profile){
            $profile = UserProfile::where('user_id', $user->id)->first();
            $var = $profile->update($data_profile);
            $profile->vehicle_no = $data_profile['vehicle_no'];
            $profile->save();
            // $user->profile->update($data_profile);
        } 
        else{
            $data_profile['user_id'] = $user->id;
            UserProfile::create($data_profile);
        }
        Activity::log('User details updated - #'.$user->id.' by '.$auth_user->name);

        return redirect()->route('admin.user.list', ['role' => $role[0]])->with([
            'flash_level'   => 'success',
            'flash_message' => 'User details updated successfully.',
        ]);

    }


    public function getDelete($id)
    {
        $profile = UserProfile::where('user_id', $id)->first();
        if($profile){
            \File::delete($profile->wp_front);
            \File::delete($profile->wp_back);
            \File::delete($profile->profile_pic);
            $profile->delete();
        }
        $user = User::where('id', $id)->first();
        if($user){
            \File::delete($user->qr_code);
        }
        User::destroy($id);
        Activity::log('User account deleted - '.$id);
        return redirect()->back()->with([
            'flash_level'   => 'danger',
            'flash_message' => 'User Deleted',
        ]);
    }

    public function postDelete($id)
    {
        User::delete($id);
        Activity::log('User account deleted - '.$id);

        return redirect()->route('admin.user.list')->with([
          'flash_level'   => 'danger',
          'flash_message' => 'User Deleted',
        ]);

    }

}
