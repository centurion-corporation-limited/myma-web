<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Validator;
use JWTAuth, DB, Auth, Activity;
use App\Models\Search;
use Carbon\Carbon;

class ActivityController extends Controller
{
    protected function validator(array $data)
    {
        $rules = [
            'word' => 'required',
        ];

        return Validator::make($data, $rules);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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
             $word = strtolower(trim($request->input('word')));
             $term = Search::where('word', $word)->first();
             if($term){
                 $term->count = ++$term->count;
                 $saved = $term->save();
             }else{
                 $data['word'] = $word;
                 $data['count'] = 1;
                 $saved = Search::create($data);
             }

             if($saved){
               return response()->json(['status' => 'success', 'data' => [], 'message' => 'SUCCESS'], 200);
             }else{
               return response()->json(['status' => 'error', 'data' => 'null', 'message' => 'NO_DATA'], 404);
             }
         }catch(Exception $e){
             return response()->json(['status' => 'error', 'data'=> $e->getMessage(), 'message' => 'EXCEPTION_OCCURED'], $e->getStatusCode());
         }
     }


    public function store(Request $request)
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
          if($request->snapshot != '')
              $image = $this->savePhoto($request->snapshot);
          else {
            $image = null;
          }
          $before =[
            'longitude' => $request->longitude,
            'latitude' => $request->latitude

            ] ;

            $occurence = Occurence::create([
              'user_id'       => $user->id,
              'reply_by'      => $user->id,
              'type'          => $request->type,
              'message'       => $request->message,
              'book_date'     => $request->book_date,
              'book_time'     => $request->book_time,
              'snapshot'      => $image,
              'longitude'     => $request->longitude,
              'latitude'      => $request->latitude,
              'location_id'   => $request->location_id,
            ]);
            $after = [
              'longitude' => $occurence->longitude,
              'latitude' => $occurence->latitude

            ];
            if($occurence){
              return response()->json(['status' => 'success', 'data' => 'Occurence added', 'message' => 'OCCURENCE_CREATED.', 'before_save' => $before, 'after_save' => $after], 200);
            }else{
              return response()->json(['status' => 'error', 'data' => 'Their was a problem while creating new data. Try Later.', 'message' => 'INSERTION_ERROR'], 404);
            }
          }catch(Exception $e){
            return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
          }
    }
}
