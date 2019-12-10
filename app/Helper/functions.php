<?php
use Illuminate\Support\Facades\Input;

use Carbon\Carbon;

use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
// use FCM;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Google\Cloud\Translate\TranslateClient;
use App\Models\Activity as LogActivity;
use LaravelFCM\Response\Exceptions\ServerResponseException;

if (!defined('PUBLIC_FOLDER')) {
    define('PUBLIC_FOLDER', '');
}

define('USER_KEY', 'AAAAK0ylZ68:APA91bHnBqoS0AKei3-uSVhdjIHaIs7kIz5iKKSB1juqnPlteAemEGaRVPkU_wKpQAU3gY0e31v-ZRAhi2-tj4TniHGOvni2hRnTrfBLjh_3sN4ssdIrV6m0b1-TZM0Plq3S_KGTRw1W');
define('DRIVER_KEY', 'AAAAdX0NKok:APA91bH1qt85LH_ic46yK9GGrMncrAQHBdfJw_A8ABESBeQZCJK6aTDcnwZ-oBd1WNsfj7OfN43VpMK2HVhKv7xcHQ_bdS3LvpdtblDVSevJMulqV4pbBK86QdctgwZeXzsYLGlqp10p');
define('MERCHANT_KEY', 'AAAA2z8hno0:APA91bEbAh59UKhmhxDZuGbOFKeE6nz_CtND3WjWZFKKgrBXFDIpgzF-8EhsYnc09V1s0TPRpzJkIPKZYNqAtPrciB-qgC0HRUqEGj3x4hmU27XT3h0ZcCgB7Y3672UQKopTXHXv3V2d');
/**
 * Get url public of website
 * Param $url: link
 * Return: http://domain.com/public/link
 *
 * @param $url
 * @return \Illuminate\Contracts\Routing\UrlGenerator|string
 */

 function addActivity($text, $user_id, $data, $response, $url = ''){
   $ip_address = request()->ip();//Request::getClientIp();

   LogActivity::create([
     'text' => $text,
     'user_id' => ($user_id == '' ? null : $user_id),
     'ip_address' => $ip_address,
     'request' => json_encode($data),
     'response' => json_encode($response),
     'url'   => $url

   ]);
 }
 
 function http_get_contents($url, Array $opts = [])
 {
    $ch = curl_init();
    if(!isset($opts[CURLOPT_TIMEOUT])) {
      curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    }
    curl_setopt($ch, CURLOPT_URL, $url);
    if(is_array($opts) && $opts) {
      foreach($opts as $key => $val) {
        curl_setopt($ch, $key, $val);
      }
    }
    if(!isset($opts[CURLOPT_USERAGENT])) {
      curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['SERVER_NAME']);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    if(FALSE === ($retval = curl_exec($ch))) {
      error_log(curl_error($ch));
    }
    return $retval;
 }
 function sendSingleLocal($user, $message, $type, $id, $link, $type){

   $path_to_firebase_cm = 'https://fcm.googleapis.com/fcm/send';
   $token = $user->fcm_token;
   // $token = 'c2zjI8oq8Zw:APA91bHWajsKFLvH0pX_X521wC9nVi4BPbgMp7N2PmzIf1Or1_pk2W95nwLSV3_y6YRdGABWnC7hRLOVQa29m-ooQ0i6aeNgROA4Gt8bycq0lLxXiAChgXMOKdRmnug9csMfMlYrOGlv4JiQOInPOw71X1Ni4Ex6cw';
     $fields = array(
         'to' => $token,
         'priority' => 10,
         'notification' => array('title' => 'Naanstap', 'body' =>  $message ,'sound'=>'Default','icon'=>'fcm_push_icon' ),
         'data' => array('title' => 'Naanstap', 'body' => $message)
     );

     if($type == 'user'){
       $headers = array(
           'Authorization:key=' . env('USER_KEY', USER_KEY),
           'Content-Type:application/json'
       );
     }
     else if($type == 'merchant'){
       $headers = array(
           'Authorization:key=' . env('MERCHANT_KEY', MERCHANT_KEY),
           'Content-Type:application/json'
       );
     }
     else if($type == 'driver'){
       $headers = array(
           'Authorization:key=' . env('DRIVER_KEY', DRIVER_KEY),
           'Content-Type:application/json'
       );
     }


     // Open connection
     $ch = curl_init();
     // Set the url, number of POST vars, POST data
     curl_setopt($ch, CURLOPT_URL, $path_to_firebase_cm);
     curl_setopt($ch, CURLOPT_POST, true);
     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
     curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

     // Execute post
     $result = curl_exec($ch);
     // Close connection
     curl_close($ch);
     return $result;
}

function sendSMS($phone, $message){
  $api_username = 'APISTYUG21USK';
  $api_password = 'APISTYUG21USKSTYUG';
  $message = rawurlencode(stripslashes($message));

  $path_to_firebase_cm = 'http://gateway80.onewaysms.sg/api2.aspx?apiusername='.$api_username.'&apipassword='.$api_password.'&mobileno='.$phone.'&senderid=MyMA&languagetype=1&message='.$message;

    // Open connection
    $ch = curl_init();
    // Set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $path_to_firebase_cm);
    curl_setopt($ch, CURLOPT_POST, true);
    //curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Length: 0'));
    //curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

    // Execute post
    $result = curl_exec($ch);
    // Close connection
    curl_close($ch);
    \Log::debug(json_encode($result));
    return $result;
}
function getYoutubeIdFromUrl($url) {
     $parts = parse_url($url);
     if(isset($parts['query'])){
         parse_str($parts['query'], $qs);
         if(isset($qs['v'])){
             return $qs['v'];
         }else if(isset($qs['vi'])){
             return $qs['vi'];
         }
     }
     if(isset($parts['path'])){
         $path = explode('/', trim($parts['path'], '/'));
         return $path[count($path)-1];
     }
     return NULL;
}

function translateLang($text){
     putenv('GOOGLE_APPLICATION_CREDENTIALS='.storage_path('MYMA-4e81280813ae.json'));

     try{

         $projectId = 'myma-1525172879039';
         $translate = new TranslateClient([
             'projectId' => $projectId
         ]);

         # The target language
         $target = 'en';

         # Translates some text into Russian
         $translation = $translate->translate($text, [
             'target' => $target
         ]);
         return $translation;

     }
     catch(Exception $e){
         return $e->getMessage();
     }
}

function verifyFin($fin_no)
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

function sendBrowser($fcm_token, $message, $link = ''){

     $optionBuilder = new OptionsBuilder();
     $optionBuilder->setTimeToLive(60*20);
     $notificationBuilder = new PayloadNotificationBuilder('Myma');

     $notificationBuilder->setBody($message)->setSound('default')->setClickAction('FCM_PLUGIN_ACTIVITY');
     $dataBuilder = new PayloadDataBuilder();

     if($link != ''){
         $dataBuilder->addData([
             'link' => $link
         ]);
     }

     $option = $optionBuilder->build();
     $notification = $notificationBuilder->build();
     $data = $dataBuilder->build();
     $token = $fcm_token;
     $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);
     \Log::debug(json_encode($downstreamResponse));
     $downstreamResponse->numberSuccess();
     $downstreamResponse->numberFailure();
     $downstreamResponse->numberModification();
     //return Array - you must remove all this tokens in your database
     $downstreamResponse->tokensToDelete();
     //return Array (key : oldToken, value : new token - you must change the token in your database )
     $downstreamResponse->tokensToModify();
     //return Array - you should try to resend the message to the tokens in the array
     $downstreamResponse->tokensToRetry();

     return true;
 }

function sendSingle($user, $message, $type = 'notifications', $id = '', $link = '', $path = '', $message_type = 'text'){

     $optionBuilder = new OptionsBuilder();
     $optionBuilder->setTimeToLive(60*20);
     $notificationBuilder = new PayloadNotificationBuilder('Myma');
     
     $notificationBuilder->setBody(strip_tags($message))->setSound('default')->setClickAction('FCM_PLUGIN_ACTIVITY')->setIcon('fcm_push_icon');
     
     $dataBuilder = new PayloadDataBuilder();

     $dataBuilder->addData([
         'type' => $type,
         'image' => 'https://static.pexels.com/photos/4825/red-love-romantic-flowers.jpg',
         'format_type' => $message_type,
         'path' => $path,
         'link' => $path
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
     $token = $user->fcm_token;//'c2csAcHNzmE:APA91bFcgvNoH-w0ML8JclXqUQbuwIAajYMLto2dk2jQVva7psVhQzn8rYABg8vQKEUi9PhbLlmz4iGl-Udl6OX9PBZo03M9LpddUq9W-PWxxIh7-HFvVVpIECCT0huHaJD4FtAvFDrG';;
     try{
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
     }catch(ServerResponseException $e){
         \Log::debug('Fcm Exception');
         \Log::debug($e->getMessage());
     }

     return true;

 }
// function sendSingle($user, $message, $type = 'notifications', $id = '', $link = ''){

//      $optionBuilder = new OptionsBuilder();
//      $optionBuilder->setTimeToLive(60*20);
//      $notificationBuilder = new PayloadNotificationBuilder('Myma');
//     // \Log::debug($message);
//      $notificationBuilder->setBody($message)->setSound('default')->setClickAction('FCM_PLUGIN_ACTIVITY');
//      $dataBuilder = new PayloadDataBuilder();

//      $dataBuilder->addData([
//          'type' => $type
//      ]);

//      if($id != ''){
//          $dataBuilder->addData([
//              'id' => $id
//          ]);
//      }

//      if($link != ''){
//          $dataBuilder->addData([
//              'link' => $link
//          ]);
//      }

//      $option = $optionBuilder->build();
//      $notification = $notificationBuilder->build();
//      $data = $dataBuilder->build();
//      $token = $user->fcm_token;//'c2csAcHNzmE:APA91bFcgvNoH-w0ML8JclXqUQbuwIAajYMLto2dk2jQVva7psVhQzn8rYABg8vQKEUi9PhbLlmz4iGl-Udl6OX9PBZo03M9LpddUq9W-PWxxIh7-HFvVVpIECCT0huHaJD4FtAvFDrG';;
//      $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);

//      $downstreamResponse->numberSuccess();
//      $downstreamResponse->numberFailure();
//      $downstreamResponse->numberModification();
//      //return Array - you must remove all this tokens in your database
//      $downstreamResponse->tokensToDelete();
//      //return Array (key : oldToken, value : new token - you must change the token in your database )
//      $downstreamResponse->tokensToModify();
//      //return Array - you should try to resend the message to the tokens in the array
//      $downstreamResponse->tokensToRetry();

//      return true;

//  }

 function sendMultiple($title, $description)
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

function addhttp($url) {
    if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
        $url = "http://" . $url;
    }
    return $url;
}

function public_url($url)
{
    $url = preg_replace('/^\//', '', $url);
    $public = env('PUBLIC_FOLDER', PUBLIC_FOLDER);

    return url($public . '/' . $url);
}

function getDistance( $latitude1, $longitude1, $latitude2, $longitude2 ) {
    $earth_radius = 6371;

    $dLat = deg2rad( $latitude2 - $latitude1 );
    $dLon = deg2rad( $longitude2 - $longitude1 );

    $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * sin($dLon/2) * sin($dLon/2);
    $c = 2 * asin(sqrt($a));
    $d = $earth_radius * $c;

    return $d*1000;
}
function truncate_number( $number, $precision = 2) {
  // Zero causes issues, and no need to truncate
  if ( 0 == (int)$number ) {
    return $number;
  }
  // Are we negative?
  $negative = $number / abs($number);
  // Cast the number to a positive to solve rounding
  $number = abs($number);
  // Calculate precision number for dividing / multiplying
  $precision = pow(10, $precision);
  // Run the math, re-applying the negative value to ensure returns correctly negative / positive
  return floor( $number * $precision ) / $precision * $negative;
}
/**
 * Get url file static
 * Use for file static other server
 *
 * @param $url
 * @return \Illuminate\Contracts\Routing\UrlGenerator|string
 */
function static_file($file)
{
    return public_url($file);
}

/**
 * Limit word
 *
 * @param $value
 * @param int $words
 * @param string $end
 * @return string
 */
function limit_word($value, $words = 100, $end = '...')
{
    return \Illuminate\Support\Str::words($value, $words, $end);
}

/**
 * Selected in select option
 *
 * @param $current
 * @param null $value
 * @return string
 */
function selected($current, $value)
{
    if (!is_null($value) && $current == $value) {
        return 'selected';
    }

    return '';
}

/**
 * Get option from database
 *
 * @param $name
 * @param null $default
 * @return string
 */
function getOption($name, $default = null)
{
    global $autoload_options;

    // if (! $autoload_options) {
    //     $autoload_options = \App\Models\Option::where('autoload', 1)->get()->keyBy('name');
    // }

    if (isset($autoload_options[$name])) {
        if($autoload_options[$name]->value == "")
          return $default;
        return $autoload_options[$name]->value;
    } else {
        $option = \App\Models\Option::getOption($name, $default);
        return $option;
    }
}

/**
 * @param $routeName
 * @param string $activeClass
 * @return string
 */
function isMenuActive($routeName, $activeClass = 'active')
{
    if (Route::currentRouteName() === $routeName) {
        return $activeClass;
    }

    return '';
}

/**
 * @param $routeGroups
 * @param string $activeClass
 * @return string
 */
function isMenuGroupActive($routeGroups, $activeClass = 'start active open')
{
    $currentRoute = Route::currentRouteName();
    if (is_array($routeGroups)) {
        foreach ($routeGroups as $routeGroup) {
            if (preg_match("/^$routeGroup\./", $currentRoute)) {
                return $activeClass;
            }
        }
    } elseif (is_string($routeGroups) && preg_match("/^$routeGroups/", $currentRoute)) {
        return $activeClass;
    }

    return '';
}

/**
 * @param $route
 * @param $label
 * @return string
 */
function renderMenu($route, $label)
{
    if (Auth::user()->can($route)) {
        $class_active = isMenuActive($route);
        $url = route($route);

        $html = "";
        $html .= "<li class='{$class_active}'>";
        $html .= "<a href='{$url}'>{$label}</a>";
        $html .= "</li>";

        return $html;
    }
}

/**
 * @param $routes
 * @return bool
 */
function show_group_menu($routes)
{
    foreach ($routes as $key => $route) {
        if (Auth::user()->can($route)) {
            return true;
        }
    }

    return false;
}

/**
 * @param $bytes
 * @param int $decimals
 * @return string
 */
function human_filesize($bytes, $decimals = 2)
{
    $size = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
    $factor = floor((strlen($bytes) - 1) / 3);

    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
}

/**
 * @param $image_id
 * @param bool $static_file
 * @return bool|\Illuminate\Contracts\Routing\UrlGenerator|string
 */
function image_url($image_id, $static_file = true)
{
    /** @var \App\Models\File $file */
    $file = \App\Models\File::find($image_id);
    if ($file) {
        if ($static_file) {
            return static_file($file->url);
        } else {
            return $file->url;
        }
    }

    /**
     * @Todo: No image
     */
    return false;
}

/**
 * @param $avatar
 * @return bool|\Illuminate\Contracts\Routing\UrlGenerator|string
 */
function get_avatar_social_or_default($avatar)
{
    if (!$avatar) {
        return false;
    }

    if (preg_match('/^http/', $avatar)) {
        return $avatar;
    }

    return static_file($avatar);
}

/**
 * @param $file_name
 * @return bool
 */
function isImageFile($file_name)
{
    $temp        = explode('.', $file_name);
    $ext         = end($temp);
    $image_types = array('gif', 'jpg', 'jpeg', 'png', 'jpe');

    if (in_array($ext, $image_types)) {
        return true;
    }

    return false;
}

function parseBladeCode($string,array $args=array()){

	$generated = Blade::compileString($string);

        ob_start(); extract($args,EXTR_SKIP);

        try
        {
            eval('?>'.$generated);
        }

        catch (\Exception $e)
        {
            ob_get_clean(); throw $e;
        }

        $content = ob_get_clean();

        return $content;
}

function savePhoto($photo,$folder_upload, $type = 'png')
{
    $fileName = '';
    try {
        if(strlen($photo) > 128) {
            // list($ext, $data)   = explode(';', $photo);
            // list(, $data)       = explode(',', $data);


            $data = base64_decode($photo);

            $fileName = mt_rand().time().'.'.$type;
            $user = Auth::user();

            // $user_name = str_replace(' ', '_', $user->name);
            // $date_time = date('Y');
            // $folder_upload = "files/snapshots/";
            if (!file_exists(public_path($folder_upload))) {
              mkdir(public_path($folder_upload), 0755, true);
            }
            file_put_contents($folder_upload.$fileName, $data);
        }
    }
    catch (\Exception $e) {
        return $e->getMessage();
    }
    return $folder_upload.$fileName;
}

function savePhotoDumy($photo,$folder_upload, $type = 'png')
{
  $fileName = '';
  try {
      if(strlen($photo) > 128) {
          // list($ext, $data)   = explode(';', $photo);
          // list(, $data)       = explode(',', $data);
          $photo = substr($photo, strpos($photo, ',') + 1);

          $data = base64_decode($photo);
          $fileName = mt_rand().time().'.'.$type;
          $user = Auth::user();

          // $user_name = str_replace(' ', '_', $user->name);
          // $date_time = date('Y');
          // $folder_upload = "files/snapshots/";
          if (!file_exists(public_path($folder_upload))) {
            mkdir(public_path($folder_upload), 0755, true);
          }
          file_put_contents($folder_upload.$fileName, $data);
      }
  }
  catch (\Exception $e) {
      return $e->getMessage();
  }
  return $folder_upload.$fileName;
}

function uploadPhoto($file,$folder)
{

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

    return $full_file_name;
}

function uploadFlexmDoc($file,$folder, $filename)
{

    if (!is_dir($folder)) {
        mkdir($folder, 755, true);
    }
    // $filename = $file->getClientOriginalName();
    // $filename = preg_replace("/[^a-zA-Z0-9.]/", "_", $filename);
    // $actual_name   = pathinfo($filename, PATHINFO_FILENAME);
    // $original_name = $actual_name;
    // $extension     = pathinfo($filename, PATHINFO_EXTENSION);
    // $filename    = "remittance_report." . $extension;
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


    // $i = 1;
    // while(file_exists($folder . '/' . $actual_name . "." . $extension))
    // {
    //     $actual_name = (string) $original_name . $i;
    //     $filename    = $actual_name . "." . $extension;
    //     $i++;
    // }

    $full_file_name  = $folder . '/' . $filename;

    $file->move($folder, $filename);

    return $full_file_name;
}

function csvToArray($filename = '', $delimiter = ',', $type = '', $head = [])
{
    if (!file_exists($filename) || !is_readable($filename))
        return false;

    $header = null;
    $data = array();
    if (($handle = fopen($filename, 'r')) !== false)
    {
        while (($row = fgetcsv($handle, 1000, $delimiter)) !== false)
        {
            if (!$header){
              if($type == 'remittance' || $type == 'wallet'){
                $header = $head;
              }else{
                $header = $row;
              }
            }
            else
                $data[] = array_combine($header, $row);
        }
        fclose($handle);
    }

    return $data;
}
