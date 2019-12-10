<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Validator;
use JWTAuth, DB;
use App\Models\Incident;
use App\Models\File;
use File as PublicStorage;

use Carbon\Carbon, Activity;

class IncidentController extends Controller
{
    protected function validator(array $data, $user)
    {
      return Validator::make($data, [
            'date'         => 'required',
            'time'         =>'required',
            'location'     => 'required',
            'dormitory_id' => 'required',
            'details'      => 'required',
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
          $incident = Incident::create([
              'user_id'         => $user->id,
              'date'            => date('Y-m-d', strtotime($request->date)),
              'time'            => $request->time,
              'dormitory_id'    => $request->dormitory_id,
              'location'        => $request->location,
              'details'         => $request->details,
              'photo_id'        => $request->photo_id,
              'video_id'        => $request->video_id,
              'audio_id'        => $request->audio_id,
          ]);

          if($incident){
              // $data = array();
              // if($request->photo_id != ""){
              //     $photo = $request->photo_id;
              //
              //     $folder = 'files/incident/';
              //     $photo_path = savePhoto($photo, $folder);
              //     $data['photo_id'] = $photo_path;
              // }
              // if(count($data) > 0){
                  // $inci = Incident::find($incident->id);
                  // $inci->update($data);
              // }
              Activity::log('Incident reported #'.$incident->id, $user->id);
            return response()->json(['status' => 'success', 'data' => [], 'message' => 'INCIDENT_SAVED.'], 200);
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
            $limit = (isset($request->limit) && $request->limit > 0)?$request->limit:10;
            $page_no = (isset($request->page_no) && $request->page_no > 1)?$request->page_no:1;
            $offset = ($page_no-1)*$limit;

          $list = Incident::select('id','user_id','date','time','dormitory_id','location','created_at')->where([
                'user_id'     => $user->id,
              ])->offset($offset)->limit($limit)->get();

          if($list){
            return response()->json(['status' => 'success', 'data' => $list, 'message' => 'DATA_LIST.'], 200);
          }else{
            return response()->json(['status' => 'error', 'data' => 'Their was a problem while creating new data. Try Later.', 'message' => 'INSERTION_ERROR'], 404);
          }

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    protected function view_validator(array $data, $user)
    {
      return Validator::make($data, [
            'incident_id'       => 'required',
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
          $incident_id = $request->incident_id;
          $incident = Incident::where('id', $incident_id)->first();

          if($incident){
              $incident['photo'] = File::whereIn('id', explode(',',$incident->photo_id))->get();
              $incident['audio'] = File::whereIn('id', explode(',',$incident->audio_id))->get();
              $incident['video'] = File::whereIn('id', explode(',',$incident->video_id))->get();

            return response()->json(['status' => 'success', 'data' => $incident, 'message' => 'SUCCESS.'], 200);
          }else{
            return response()->json(['status' => 'error', 'data' => 'Incident does not exist.', 'message' => 'ERROR'], 404);
          }

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    protected function media_upload_validator(array $data, $user)
    {
      return Validator::make($data, [
            'media_file'      => 'required',
        ]);
    }

    public function media_upload(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $validator = $this->media_upload_validator($request->all(), $user);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $message = [];
            foreach($errors->messages() as $key => $error){
              $message[$key] = $error[0];
            }
            return response()->json(['status' => 'error', 'data' => $message, 'message' => 'VALIDATION_ERROR'], 401);
        }

        try{
          if(isset($request->media_file)){
            // $full_file_name =  $request->file('media_file')->store('files/incident/'.$incident_id);
            $file = \Input::file('media_file');
              // return response()->json(['status' => 'error', 'data' => $file, 'message' => 'DATA_SAVED.'], 200);

            // $file = $request->file('media_file');
            $folder_upload = 'files/uploads/';
            $filename      = $file->getClientOriginalName();

            if (!file_exists(public_path($folder_upload))) {
              mkdir(public_path($folder_upload), 0755, true);
            }
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
            while(file_exists($folder_upload . '/' . $actual_name . "." . $extension))
            {
                $actual_name = (string) $original_name . $i;
                $filename    = $actual_name . "." . $extension;
                $i++;
            }
            $full_file_name  = $folder_upload . $filename;
            $file->move($folder_upload, $filename);
            if ($file->isValid()) {
            }
            // else{
            //
            //   return response()->json(['status' => 'error', 'data' => 'File not valid', 'message' => 'DATA_SAVED.'], 200);
            // }

            $media = File::create([
              'path'        => $full_file_name,
              'type'        => $request->media_type
            ]);

            if($media){
                $data['attachment_id'] = $media->id;
                $data['media_url'] = env('APP_URL').$full_file_name;

                return response()->json(['status' => 'success', 'data' => $data, 'message' => 'DATA_SAVED.'], 200);
            }else{
                return response()->json(['status' => 'error', 'data' => 'Their was a problem while saving data. Try Later.', 'message' => 'INSERTION_ERROR'], 404);
            }
          }else{
              return response()->json(['status' => 'error', 'data' => 'File not there', 'message' => 'FILE_ERROR.'], 200);
          }


        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    protected function media_delete_validator(array $data, $user)
    {
      return Validator::make($data, [
            'attachment_id'      => 'required',
        ]);
    }

    public function media_delete(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $validator = $this->media_delete_validator($request->all(), $user);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $message = [];
            foreach($errors->messages() as $key => $error){
              $message[$key] = $error[0];
            }
            return response()->json(['status' => 'error', 'data' => $message, 'message' => 'VALIDATION_ERROR'], 401);
        }

        try{
          $file_id = $request->input('attachment_id');
          $file = File::find($file_id);
          if($file){

            $result = PublicStorage::delete($file->getPath());
            $file->delete();
            
            if($result){
                return response()->json(['status' => 'success', 'data' => '', 'message' => 'File deleted.'], 200);
            }else{
                return response()->json(['status' => 'error', 'data' => $result, 'message' => 'Could not delete file.'], 404);
            }
          }else{
              return response()->json(['status' => 'error', 'data' => '', 'message' => 'Invalid attachment Id.'], 200);
          }


        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }
}
