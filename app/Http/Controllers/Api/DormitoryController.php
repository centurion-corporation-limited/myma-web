<?php

namespace App\Http\Controllers\Api;

use App\Events\MaintenanceStatus;
use App\Events\MaintenanceCreatedEvent;

use Illuminate\Http\Request;
use Validator;
use JWTAuth, DB;
use App\Models\Maintenance;
use App\Models\Dormitory;
use App\Models\File;
use App\Models\Notification;

use Carbon\Carbon, Activity;

class DormitoryController extends Controller
{
    protected function validator(array $data, $user)
    {
      return Validator::make($data, [
            'location'    => 'required',
            'comments'      => 'required',
            'dormitory_id'      => 'required',
        ]);
    }

    public function add(Request $request)
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

          $dormitory = Maintenance::create([
              'user_id'         => $user->id,
              'location'        => $request->location,
              'comments'        => $request->comments,
              'dormitory_id'    => $request->dormitory_id,
              // 'photo_1'         => $request->photo_1,
              // 'photo_2'         => $request->photo_2,
          ]);


          $data = array();
          if($request->photo_1 != ""){
              $photo = $request->photo_1;

              $folder = 'files/dormitory/';
              $photo_path = savePhoto($photo, $folder);
              $data['photo_1'] = $photo_path;
          }

          if($request->photo_2 != ""){
              $photo = $request->photo_2;

              $folder = 'files/dormitory/';
              $photo_path = savePhoto($photo, $folder);
              $data['photo_2'] = $photo_path;
          }

          if($request->photo_3 != ""){
              $photo = $request->photo_3;

              $folder = 'files/dormitory/';
              $photo_path = savePhoto($photo, $folder);
              $data['photo_3'] = $photo_path;
          }

          if($request->photo_4 != ""){
              $photo = $request->photo_4;

              $folder = 'files/dormitory/';
              $photo_path = savePhoto($photo, $folder);
              $data['photo_4'] = $photo_path;
          }

          if($request->photo_5 != ""){
              $photo = $request->photo_5;

              $folder = 'files/dormitory/';
              $photo_path = savePhoto($photo, $folder);
              $data['photo_5'] = $photo_path;
          }

          if(count($data) > 0){
              $dorm = Maintenance::find($dormitory->id);
              $dorm->update($data);
          }

          if($dormitory){
              event(new MaintenanceCreatedEvent($dormitory->id, $request->dormitory_id));
              // \Event::fire('maintenance.created', [$dormitory->id, $request->dormitory_id]);
              Activity::log('Dormitory maintenance request created #'.$dormitory->id, $user->id);

            return response()->json(['status' => 'success', 'data' => $dormitory, 'message' => 'We have received your feedback. Our reprensentative will get to you within 2 days.'], 200);
          }else{
            return response()->json(['status' => 'error', 'data' => 'Their was a problem while creating new data. Try Later.', 'message' => 'INSERTION_ERROR'], 404);
          }

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function listout(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));

        try{
          $data = Maintenance::select('id', 'status_id', 'user_id', 'created_at', 'location')->with('status')
          ->where('user_id', $user->id)->orderBy('created_at','desc')->get();
          // foreach($data as $d){
          //     $d->created_at = date('d M, Y', strtotime($d->created_at));
          // }
          return response()->json(['status' => 'success', 'data' => $data, 'message' => ''], 200);

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    protected function view_validator(array $data, $user)
    {
      return Validator::make($data, [
            'case_id'       => 'required',
        ]);
    }

    public function view(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $validator = $this->view_validator($request->all(), $user);

        if ($validator->fails()) {
              $errors = $validator->errors();
              $message = [];
              foreach($errors->messages() as $key => $error){
                  $message[$key] = $error[0];
              }
              return response()->json(['status' => 'error', 'data' => $message, 'message' => 'VALIDATION_ERROR'], 401);
        }

        try{
          $case_id = $request->case_id;
          $dormitory = Maintenance::where('id', $case_id)->with('dormitory', 'status')->first();

          if($dormitory){
            $dormitory['photo_1'] = $dormitory['photo_1'] != ""?url($dormitory['photo_1']):'';
            $dormitory['photo_2'] = $dormitory['photo_2'] != ""?url($dormitory['photo_2']):'';
            $dormitory['photo_3'] = $dormitory['photo_3'] != ""?url($dormitory['photo_3']):'';
            $dormitory['photo_4'] = $dormitory['photo_4'] != ""?url($dormitory['photo_4']):'';
            $dormitory['photo_5'] = $dormitory['photo_5'] != ""?url($dormitory['photo_5']):'';
            $dormitory['files'] = $dormitory->files;
            unset($dormitory['status_id']);
            unset($dormitory['dormitory_id']);
            unset($dormitory['send_to']);
            unset($dormitory['fin']);
            unset($dormitory['language']);

            return response()->json(['status' => 'success', 'data' => $dormitory, 'message' => 'SUCCESS'], 200);
          }else{
            return response()->json(['status' => 'error', 'data' => 'Does not exists.', 'message' => 'ERROR'], 404);
          }

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }


    protected function validator_complete(array $data, $user)
    {
      return Validator::make($data, [
            'case_id'    => 'required',
        ]);
    }

    public function markComplete(Request $request)
    {
      $user = JWTAuth::toUser($request->input('token'));
      $validator = $this->validator_Complete($request->all(), $user);

      if ($validator->fails()) {
          $errors = $validator->errors();
          $message = [];
          foreach($errors->messages() as $key => $error){
              $message[$key] = $error[0];
          }
          return response()->json(['status' => 'error', 'data' => $message, 'message' => 'VALIDATION_ERROR'], 401);
      }

      try{
          $id = $request->input('case_id');
          $item = Maintenance::find($id);
          $from  = $item->status?$item->status->name:'open';
          if($item->status_id == 3){
              return response()->json(['status' => 'error', 'data' => 'Already completed', 'message' => 'VALIDATION_ERROR'], 200);
          }
          if($item){
              $dormitory = $item->update([
                  'remarks'         => $request->remarks,
                  'status_id'       => 3,
                  'logged_by'       => $user->id,
                  'completed_at'    => \Carbon\Carbon::now()->toDateTimeString()
              ]);
              $to = 'Completed';

              if($request->photo_1 != ""){
                  $photo = $request->photo_1;

                  $folder = 'files/dormitory/';
                  $photo_path = savePhoto($photo, $folder);
                  File::create([
                      'path' => $photo_path,
                      'type' => 'maintenance',
                      'ref_id' => $id
                  ]);
              }
              if($request->photo_2 != ""){
                  $photo = $request->photo_2;

                  $folder = 'files/dormitory/';
                  $photo_path = savePhoto($photo, $folder);
                  File::create([
                      'path' => $photo_path,
                      'type' => 'maintenance',
                      'ref_id' => $id
                  ]);
              }
              if($request->photo_3 != ""){
                  $photo = $request->photo_3;

                  $folder = 'files/dormitory/';
                  $photo_path = savePhoto($photo, $folder);
                  File::create([
                      'path' => $photo_path,
                      'type' => 'maintenance',
                      'ref_id' => $id
                  ]);
              }
              if($request->photo_4 != ""){
                  $photo = $request->photo_4;

                  $folder = 'files/dormitory/';
                  $photo_path = savePhoto($photo, $folder);
                  File::create([
                      'path' => $photo_path,
                      'type' => 'maintenance',
                      'ref_id' => $id
                  ]);
              }
              if($request->photo_5 != ""){
                  $photo = $request->photo_5;

                  $folder = 'files/dormitory/';
                  $photo_path = savePhoto($photo, $folder);
                  File::create([
                      'path' => $photo_path,
                      'type' => 'maintenance',
                      'ref_id' => $id
                  ]);
              }

              Activity::log('Dormitory maintenance #'.$item->id.' marked complete by '.$user->name, $user->id);
              $message = 'Status of the maintenance #'.$item->id.' has been updated from '.$from.' to '.$to;
              Notification::create(['type' => 'maintenance', 'title' => 'Maintenance marked Complete', 'message' => $message, 'user_id' => $user->id, 'created_by' => $user->id]);
              event(new MaintenanceStatus($user->id, $id, $from, $to));
              // \Event::fire('maintenance.status', [$user->id, $id, $from, $to]);

            return response()->json(['status' => 'success', 'data' => [], 'message' => 'UPDATED.'], 200);
          }else{
            return response()->json(['status' => 'error', 'data' => 'Maintenance record not found', 'message' => 'INVALID_ID'], 200);
          }

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function dormitory_list(Request $request)
    {
        // $user = JWTAuth::toUser($request->input('token'));

        try{
          $list = Dormitory::all();

          return response()->json(['status' => 'success', 'data' => $list, 'message' => ''], 200);

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }
}
