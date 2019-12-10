<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Validator;
use JWTAuth, DB;
use App\Models\Emergency;
use App\Models\Category;

use Carbon\Carbon;

class EmergencyController extends Controller
{
    public function listout(Request $request)
    {
        // $user = JWTAuth::toUser($request->input('token'));

        try{

          $language = \Input::get('language', 'english');
          $list = Emergency::whereHas('category', function($q){
              $q->where('type_id', 1);
          })->with('category')->get();


          if($list){
              $data = [];

              foreach($list as $li){
                  $cat = '';
                  if($language == 'bengali'){
                      if($li->name_bn != ''){
                          $li->name = $li->name_bn;
                      }
                      $cat = $li->category->name_bn;
                  }elseif($language == 'chinese'){
                      if($li->name_mn != ''){
                          $li->name = $li->name_mn;
                      }
                      $cat = $li->category->name_mn;
                  }elseif($language == 'tamil'){
                      if($li->name_ta != ''){
                          $li->name = $li->name_ta;
                      }
                      $cat = $li->category->name_ta;
                  }elseif($language == 'thai'){
                      if($li->name_th != ''){
                          $li->name = $li->name_th;
                      }
                      $cat = $li->category->name_th;
                  }

                  if($cat == '')
                    $cat = $li->category->name;

                  unset($li->category);
                  $data[$cat][] = $li;
              }
            return response()->json(['status' => 'success', 'data' => $data, 'message' => 'DATA_LIST.'], 200);
          }else{
            return response()->json(['status' => 'error', 'data' => 'Their was a problem while creating new data. Try Later.', 'message' => 'INSERTION_ERROR'], 404);
          }

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function taxiList(Request $request)
    {
        // $user = JWTAuth::toUser($request->input('token'));

        try{
          $language = \Input::get('language', 'english');
          $list = Emergency::whereHas('category', function($q){
              $q->where('type_id', 2);
          })->with('category')->get();

          $taxi_feedback = getOption('taxi_feedback');
          $taxi_lost_found = getOption('taxi_lost_found');
          if($list){
              $data = $numbers = [];
              foreach($list as $li){
                  $cat = '';
                  if($language == 'bengali'){
                      if(getOption('taxi_feedback_bn') != ''){
                          $taxi_feedback = getOption('taxi_feedback_bn');
                      }
                      if(getOption('taxi_lost_found_bn') != ''){
                          $taxi_lost_found = getOption('taxi_lost_found_bn');
                      }
                      if($li->name_bn != ''){
                          $li->name = $li->name_bn;
                      }
                      $cat = $li->category->name_bn;
                  }elseif($language == 'chinese'){
                      $data['taxi_lost_found_title'] = '失物招领';
                      $data['taxi_feedback_title'] = '德士反馈';
                      if(getOption('taxi_feedback_mn') != ''){
                          $taxi_feedback = getOption('taxi_feedback_mn');
                      }
                      if(getOption('taxi_lost_found_mn') != ''){
                          $taxi_lost_found = getOption('taxi_lost_found_mn');
                      }
                      if($li->name_mn != ''){
                          $li->name = $li->name_mn;
                      }
                      $cat = $li->category->name_mn;
                  }elseif($language == 'tamil'){
                      if(getOption('taxi_feedback_ta') != ''){
                          $taxi_feedback = getOption('taxi_feedback_ta');
                      }
                      if(getOption('taxi_lost_found_ta') != ''){
                          $taxi_lost_found = getOption('taxi_lost_found_ta');
                      }
                      if($li->name_ta != ''){
                          $li->name = $li->name_ta;
                      }
                      $cat = $li->category->name_ta;
                  }elseif($language == 'thai'){
                      if(getOption('taxi_feedback_th') != ''){
                          $taxi_feedback = getOption('taxi_feedback_th');
                      }
                      if(getOption('taxi_lost_found_th') != ''){
                          $taxi_lost_found = getOption('taxi_lost_found_th');
                      }
                      if($li->name_th != ''){
                          $li->name = $li->name_th;
                      }
                      $cat = $li->category->name_th;
                  }


                  if($cat == '')
                    $cat = $li->category->name;

                  unset($li->category);
                  $numbers[$cat][] = $li;
              }
              $data['numbers'] = $numbers;
              $mrt_map = getOption('mrt_map');
              if($mrt_map != ''){
                  $mrt_map = url($mrt_map);
              }
              $data['taxi_lost_found_title'] = 'Taxi Lost & Found';
              $data['taxi_feedback_title'] = 'Taxi Feedback';

              $data['taxi_lost_found'] = $taxi_lost_found;
              $data['taxi_feedback'] = $taxi_feedback;
              $data['taxi_email'] = 'feedback@taxisingapore.com';
              $data['mrt_map'] = $mrt_map;

              // $type = pathinfo($mrt_map, PATHINFO_EXTENSION);
              // $data_mrt = file_get_contents($mrt_map);
              // $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data_mrt);
              // $data['mrt_map_base64'] = $base64;

            return response()->json(['status' => 'success', 'data' => $data, 'message' => 'DATA_LIST.'], 200);
          }else{
            return response()->json(['status' => 'error', 'data' => 'Their was a problem while creating new data. Try Later.', 'message' => 'INSERTION_ERROR'], 404);
          }

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }
}
