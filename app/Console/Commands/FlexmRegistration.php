<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\User;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Carbon\Carbon, JWTAuth;

class FlexmRegistration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flexm:register';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To register user for flexm.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
    //   $users = User::whereHas('roles', function($q){
    //     $q->where('slug', 'app-user');
    //   })->whereHas('profile', function($q){
    //     $q->whereNotNull('fin_no')->whereNotNull('phone')->where('dormitory_id', 3);
    //   })->with('profile')->where('flexm_account', '0')->where('flexm_error', '0')
    //   ->orderBy('id', 'desc')->limit(500)->get();
      
      $users = User::whereHas('roles', function($q){
        $q->where('slug', 'app-user');
      })->whereHas('profile', function($q){
        $q->whereNotNull('fin_no')->whereNotNull('phone');
        // ->whereIn('phone', [81378156]);
        //->where('dormitory_id', 3);
      })
      ->whereIn('id', [9075,9077,9081,9082,9085,9096,9097,9098,9100,9102,9113,9115,9123,9128,9134,9146,9158,9165,9169,9173,9175,9181,9183,9188,9193,9197,9199,9202,9206,9211,9217,9219,9222,9223,9229,9241,9243,9244,9247,9248,9250,9252,9253,9254,9261,9263,9266,9267,9270,9273,9274,9275,9278,9280,9282,9283,9286,9287,9289,9291,9296,9297,9298,9299,9300,9305,9307,9311,9312,9313,9315,9321,9322,9327,9332,9336,9344,9346,9349,9355,9359,9363,9366,9371,9378,9379,9382,9385,9387,9388,9391,9393,9394,9395,9398,9399,9404,9416,9419,9423,
      9424,9431,9436,9441,9446,9447,9454,9457,9459,9461,9464,9466,9468,9470,9471,9473,9474,9475,9485,9487,9488,9492,9496,9499,9501,9507,9509,9512,9515,9517,9520,9522,9530,9533,9536,9541,9545,9546,9547,9549,9553,9555,9561,9563,9568,9570,9575,9579,9581,9582,9588,9596,9600,9601,9607,9613,9616,9629,9633,9638,9639,9643,9644,9646,9648,9651,9653,9655,9728,9730,9733,9735,9736,9737,9739,9743,9746,9749,9756,9758,9759,9760,9761,9762,9765,9766,9769,9770,9772,9774,9775,9777,9783,9788,9791,9794,9795,9796,9798,9799,
      9803,9804,9805,9806,9807,9809,9810,9811,9812,9813,9816,9817,9818,9819,9822,9823,9826,9830,9831,9832,9834,9835,9837,9838,9841,9842,9843,9844,9845,9846,9847,9848,9849,9851,9853,9855,9856,9858,9861,9863,9864,9865,9866,9867,9868,9869,9870,9872,9873,9874,9875,9876,9877,9879,9881,9882,9883,9885,9886,9887,9888,9889,9894,9895,9897,9898,9899,9900,9902,9903,9904,9906,9907,9908,9909,9910,9911,9913,9914,9915,9916,9917,9920,9921,9922,9923,9924,9925,9926,9928,9929,9930,9931,9932,9933,9934,9935,9936,9939,9940,
      9941,9942,9943,9944,9945,9946,9947,9948,9949,9950,9951,9952,9954,9955,9956,10201,10202,10203,10214,10218,10230,10237,10250,10262,10280,10282,10286,10287,10288,10290,10291,10293,10295,10296,10299,10301,10304,10307,10308,10311,10313,10321,10322,10326,10335,10336,10338,10339,10344,10345,10349,10350,10353,10356,10360,10364,10366,10370,10375,10376,10386,10388,10391,10394,10399,10404,10405,10406,10411,10412,10413,10415,10417,10420,10421,10424,10425,10426,10431,10433,10438,10440,10446,10454,10465,10466,10469,10470,10475,10478,10481,10486,10490,10491,10495,10500,10504,10505,10509,10513,
      10516,10518,10521,10524,10527,10528,10529,10533,10536,10539,10540,10542,10544,10545,10546,10550,10552,10557,10564,10565,10567,10569,10570,10572,10575,10580,10584,10586,10590,10595,10602,10604,10613,10614,10623,10624,10627,10628,10629,10633,10634,10636,10638,10646,10651,10662,10664,10668,10669,10671,10677,10680,10681,10691,10692,10693,10696,10699,10700,10704,10705,10708,10709,10712,10713,10715,10721,10723,10731,10732,10734,10739,10740,10743,10744,10746,10747,10758,10761,10767,10768,10770,10772,10774,10775,10778,10789,10790,10794,10797,10798,10802,10804,10806,10810,10811,10812,10815,10817,10819,
      10820,10822,10823,10824,10825,10827,10828,10834,10836,10838,10841,10843,10845,10848,10850,10855,10861,10862,10867,10876,10879,10887,10890,10892,10896,10897,10904,10910,10913,10916,10921,10926,10928,10930,10933,10935,10937,10941,10942,10945,10948,10949,10951,10952,10954,10955,10956,10958,10960,10966,10968,10969,10972,10974,10975,10976,10977,10979,10980,10993,10995,10998,11000,11002,11004,11011,11015,11017,11019,11026,11028,11029,11034,11037,11038,11041,11045,11056,11057,11061,11064,11070,11080,11081,11085,11086,11092,11095,11100,11101,11106,11107,11118,11120,11125,11126,11128,11133,11137,11139,
      11141,11145,11151,11152,11153,11155,11156,11164,11165,11167,11171,11173,11177,11178,11182,11188,11220,11222,11226,11227,11231,11233,11235,11243,11252,11295,11321,11328,11329,11339,11364,11367,11372,11380,11384,11387,11393,11396,11402,11413,11423,11443,11450,11460,11517,11532,11533,11534,11535,11536,11537,11538,11539,11540,11541,11542,11543,11544,11545,11546,11547,11548,11549,11551,11553,11554,11555,11557,11558,11559,11560,11561,11562,11563,11565,11566,11567,11569,11572,11574,11575,11576,11577,11578,11579,11580,11581,11582,11583,11584,11585,11587,11589,11590,11591,11593,11594,11596,11597,11598,
      11599,11600,11601,11602,11603,11604,11605,11606,11607,11608,11609,11610,11611,11612,11613,11615,11616,11617,11618,11619,11620,11621,11622,11625,11631,11634,11637,11639,11646,11649,11650,11651,11652,11654,11655,11659,11661,11662,11665,11668,11671,11672,11675,11676,11681,11683,11685,11688,11692,11700,11702,11705,11709,11710,11712,11714,11717,11720,11721,11723,11725,11727,11728,11730,11736,11739,11745,11750,11752,11757,11764,11765,11766,11769,11772,11776,11779,11783,11788,11796,11797,11801,11806,11807,11810,11812,11816,11820,11828,11834,11848,11852,11853,11905,11907,11908,11911,11915,11936,11942,
      11943,11947,11948,11952,11955,11956,11957,11960,11963,11964,11965,11968,11971,11973,11974,11977,11982,11986,11994,11998,12013,12017,12022,12026,12029,12030,12035,12042,12045,12051,12054,12057,12058,12063,12064,12067,12072,12079,12086,12088,12089,12092,12095,12098,12100,12106,12114,12116,12117,12120,12121,12126,12134,12138,12141,12146,12151,12158,12159,12165,12166,12199,12212,12236,12237,12238,12242,12243,12244,12248,12252,12253,12258,12270,12271,12274,12275,12277,12278,12283,12284,12291,12295,12303,12304,12308,12309,12310,12314,12315,12318,12328,12330,12332,12333,12336,12337,12340,12341,12342,
      12347,12348,12351,12355,12357,12358,12360,12362,12363,12366,12367,12370,12373,12378,12380,12381,12383,12386,12389,12391,12400,12402,12405,12411,12412,12413,12418,12422,12423,12425,12427,12429,12430,12433,12434,12435,12439,12440,12441,12442,12446,12447,12449,12450,12451,12452,12454,12458,12461,12462,12463,12470,12472,12474,12475,12476,12478,12483,12485,12487,12489,12490,12746,12751,12752,12795,12796,12800,12822,12888,13023,14444])
      ->with('profile')->where('flexm_account', '0')->where('flexm_error', '0')//->where('good_for_wallet', 'Y')
      ->orderBy('id', 'desc')->limit(1500)->get();
      
        // \Log::debug(json_encode($users));
        // dd('yes');
        // return true;
      $date = Carbon::now()->format('Ymd');

      foreach($users as $user){
        $current_time = Carbon::now()->toDateTimeString();
        $user->update(['flexm_cron' => '1', 'flexm_cron_date' => $current_time]);

        $user_id = $user->id;

        $profile = @$user->profile;

        if($profile){
          if($profile->phone == ''){
            $user->update(['flexm_error' => '1', 'flexm_cron' => '1', 'flexm_error_text' => 'User does not have a phone number']);
            continue;
          }
          if($profile->street_address == "" && $profile->dormitory_id == ""){
            $user->update(['flexm_error' => '1', 'flexm_cron' => '1', 'flexm_error_text' => 'User does not have address.']);
            continue;
          }
          elseif($profile->fin_no == ""){
            $user->update(['flexm_error' => '1', 'flexm_cron' => '1', 'flexm_error_text' => 'User does not have fin no.']);
            continue;
          }
          //elseif($profile->zip_code == ""){
            //continue;
          //}
          elseif($profile->wp_front == ""){
            //$user->update(['flexm_error' => '1', 'flexm_cron' => '1', 'flexm_error_text' => 'User does not have a work permit photo(front)']);
            //continue;
          }elseif($profile->wp_back == ""){
            //$user->update(['flexm_error' => '1', 'flexm_cron' => '1', 'flexm_error_text' => 'User does not have a work permit photo(back)']);
            //continue;
          }
          $mobile_no = $user->profile->phone;
          if(strlen($mobile_no) > 8 || strlen($mobile_no) < 8){
            $user->update(['flexm_error' => '1', 'flexm_cron' => '1', 'flexm_error_text' => 'Mobile Number is not of required length.']);
            continue;
          }
        }else{
            $user->update(['flexm_error' => '1', 'flexm_cron' => '1', 'flexm_error_text' => 'User does not have a profile.']);
            continue;
        }

        $url = url('/api/v1/flexm/register');
        try{
            $name = explode(' ', $user->name);
            if(count($name) < 2){
              //$user->update(['flexm_error' => '1', 'flexm_cron' => '1', 'flexm_error_text' => 'User does not have a full name']);
              //continue;
            }
            $data['token'] = JWTAuth::fromUser($user);
            $data['password'] = 'Password@123';
            $data['password_confirmation'] = 'Password@123';
            $data['device_signature'] = 'testx';
            $data['nationality'] = 'Indian';
            $data['cron'] = true;
            $client = new Client(['headers' => ['Content-type' => 'application/json']]);

            $result = $client->post($url, [
                'form_params' => $data,
            ]);

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK

            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();

                $content = json_decode($body->getContents());
                if(@$content->status == 'success'){
                    $msg = $content->message;
                    $cont = $content->data;
                    $user->update(['flexm_error' => '0', 'flexm_error_text' => '', 'good_for_wallet' => 'D']);
                    
                    if($profile->wp_front != ''){
                      $path = $profile->wp_front;
                      $extension = explode('.', $path);
                      if(!isset($extension[1])){
                        $extensions = 'jpg';
                      }else{
                        $extensions = $extension[1];
                      }
                      $date = Carbon::now()->toDateString();
                      $folder = 'files/flexm_ftp/'.$date;
                      if (!file_exists(public_path($folder))) {
                        mkdir(public_path($folder), 0755, true);
                      }
                      $to = public_path().'/files/flexm_ftp/'.$date.'/'.$profile->phone.'_wp1.'.$extensions;
                      $path = public_path().'/'.$path;
                      if(file_exists($path)){
                          \File::copy($path, $to);
                      }
                    }
                    if($profile->wp_back != ''){
                      $path = $profile->wp_back;
                      $extension = explode('.', $path);
                      if(!isset($extension[1])){
                        $extensions = 'jpg';
                      }else{
                        $extensions = $extension[1];
                      }
                      $date = Carbon::now()->toDateString();
                      $folder = 'files/flexm_ftp/'.$date;
                      if (!file_exists(public_path($folder))) {
                        mkdir(public_path($folder), 0755, true);
                      }
                      $to = public_path().'/files/flexm_ftp/'.$date.'/'.$profile->phone.'_wp2.'.$extensions;
                      $path = public_path().'/'.$path;
                      if(file_exists($path)){
                          \File::copy($path, $to);
                      }
                    }
                    //continue;
                }else{
                    $d = $content;
                    if(isset($content->data)){
                        $d = $content->data;
                    }
                    if(isset($content->full)){
                        $d = $content->full;
                    }
                    $user->update(['flexm_error' => '1', 'flexm_cron' => '1', 'flexm_error_text' => @json_encode($d)]);
                    continue;
                }
            }

        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());
            $user->update(['flexm_error' => '1', 'flexm_cron' => '1', 'flexm_error_text' => @$jsonBody]);
            // addActivity('Flexm error in bulk registration - '.@$mobile_no, @$user_id, @$data, @$jsonBody, $url);
            // return response()->json(['status' => 'error', 'data' => @$jsonBody->errors, 'message'=> $msg], 200);
        }catch(GuzzleException $e){
            $user->update(['flexm_error' => '1', 'flexm_cron' => '1', 'flexm_error_text' => @$e->getMessage()]);
            // addActivity('Flexm error in bulk registration - '.@$mobile_no, @$user_id, @$data, @$e->getMessage(), $url);
             // return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }catch(Exception $e){
            $user->update(['flexm_error' => '1', 'flexm_cron' => '1', 'flexm_error_text' => @$e->getMessage()]);
            // addActivity('Flexm error in bulk registration - '.@$mobile_no, @$user_id, @$data, @$e->getMessage(), $url);
            // return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], 200);
        }
      }
    }
}
