<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Validator;
use JWTAuth, DB;
use App\Models\Feedback;
use App\Models\Contact;
use App\Models\Attachment;
use App\Events\SendFeedbackEvent;
use Carbon\Carbon, Activity;

class FeedbackController extends Controller
{
    protected function validator(array $data, $user)
    {
      return Validator::make($data, [
            'type'      => 'required',
            'name'      => 'required',
            'email'    => 'required',
            'phone'      => 'required',
            'content'      => 'required',
            // 'email_reply'   => 'required',
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
            if($request->type != 'contact'){
                $feedback = Feedback::create([
                    'user_id'         => $user->id,
                    'type'            => $request->type,//feedback or mom
                    'name'            => $request->name,
                    'email'           => $request->email,
                    'phone'           => $request->phone,
                    'content'         => $request->content,
                    'rating'          => @$request->rating,
                    'email_reply'     => $request->email_reply == 0 ? '0':'1', //0, 1
                    'category_id'     => @$request->category_id,

                ]);

                if($request->type == 'mom'){
                  \Log::debug("fired event");
                  event(new SendFeedbackEvent($feedback->id));
                }

            }else{
                $feedback = Contact::create([
                    // 'user_id'         => $user->id,
                    // 'type'            => $request->type,//feedback or mom
                    'name'            => $request->name,
                    'email'           => $request->email,
                    'phone'           => $request->phone,
                    'description'     => $request->content,
                    // 'rating'          => @$request->rating,
                    // 'email_reply'     => $request->email_reply == 0 ? '0':'1', //0, 1
                ]);

            }


          if($feedback){
                if($request->type != 'contact'){
                    Activity::log('Feedback added #'.$feedback->id, @$user->id);
                }else{
                    Activity::log('Created Contact Request #'.$feedback->id, @$user->id);
                }

            return response()->json(['status' => 'success', 'data' => [], 'message' => 'FEEDBACK_SAVED.'], 200);
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
        $validator = $this->people_validator($request->all(), $user);

        if ($validator->fails()) {
          $errors = $validator->errors();
          $message = [];
          foreach($errors->messages() as $key => $error){
              $message[$key] = $error[0];
          }
          return response()->json(['status' => 'error', 'data' => $message, 'message' => 'VALIDATION_ERROR'], 401);
        }

        try{
          $list = Incident::where([
                'user_id'     => $user->id,
              ])->get();

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
        $validator = $this->detail_validator($request->all(), $user);

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
            return response()->json(['status' => 'success', 'data' => $incident, 'message' => 'SUCCESS.'], 200);
          }else{
            return response()->json(['status' => 'error', 'data' => 'Incident does not exist.', 'message' => 'ERROR'], 404);
          }

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }
}
