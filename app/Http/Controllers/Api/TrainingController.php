<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use JWTAuth, DB;
use App\Models\Module;
use App\Models\Training;
use App\Models\TrainingComplete;
use App\Models\TrainingReview;
use Carbon\Carbon;

class TrainingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
      $user = JWTAuth::toUser($request->input('token'));
      try{
          $training = DB::table('e_train as t')
                        ->join('users as u', 't.created_by', '=', 'u.id')
                        ->select('t.id','u.name as created_by', 't.title as title','t.description as description', 't.type', 't.path as link',
                        't.created_at')
                        ->where('t.id',$id)
                        ->first();
                        //Training::select('id', 'created_by', 'title', 'description', 'path as link', 'type', 'created_at')->find($id);
          if($training){
            $training->link = url($training->link);
            $training->date = Carbon::parse($training->created_at)->format('M d, Y');
            $training->time = Carbon::parse($training->created_at)->format('g:i A');
            unset($training->created_at);

            $read = TrainingComplete::where('e_train_id', $training->id)->where('user_id', $user->id)->first();
            if($read)
              $training->read = "true";
            else
              $training->read = "false";

            return response()->json(['status' => 'success', 'data' => $training, 'message' => 'SUCCESS'], 200);
          }else{
            return response()->json(['status' => 'error', 'data' => 'E-Training does not exist.', 'message' => 'NO_DATA'], 404);
          }
      }catch(Exception $e){
          return response()->json(['status' => 'error', 'data'=> $e->getMessage(), 'message' => 'EXCEPTION_OCCURED'], $e->getStatusCode());
      }
    }

    /**
     * Mark read the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function markRead(Request $request)
    {
      $user = JWTAuth::toUser($request->input('token'));
      try{
		   $id = $request->input('training_id');
           $training = Training::find($id);

           if($training){
             $read = TrainingComplete::updateOrCreate([
               'e_train_id'  => $training->id,
               'user_id'     => $user->id,
               'sign'    => $request->sign
             ]);

             if($read)
               return response()->json(['status' => 'success', 'data' => 'null', 'message' => 'MARKED_READ'], 200);
             else
              return response()->json(['status' => 'error', 'data' => 'Their was a problem while creating new data. Try Later.', 'message' => 'INSERTION_ERROR'], 404);

           }else{
            return response()->json(['status' => 'error', 'data' => 'E-Training does not exist.', 'message' => 'NO_DATA'], 404);
           }
      }catch(Exception $e){
          return response()->json(['status' => 'error', 'data'=> $e->getMessage(), 'message' => 'EXCEPTION_OCCURED'], $e->getStatusCode());
      }
    }

    /**
     * Mark read the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getFeedBack(Request $request)
    {
      $user = JWTAuth::toUser($request->input('token'));
      try{
		       $id = $request->input('module_id');
           $trainings = DB::table('e_train_review as r')->join('users as u', 'r.user_id', '=', 'u.id')
                        ->select('r.comment as message', 'r.created_at', 'u.name')
                        ->where('r.module_id', $id)
                        ->where('r.for_user', $user->id)->get();

           //TrainingReview::where('e_train_id',$id)->select('user_id', 'comment as message', 'created_at')->get();

           if($trainings->count()){
             foreach($trainings as $key => $training){

              $trainings[$key]->date = Carbon::parse($training->created_at)->format('M d, Y');
              $trainings[$key]->time = Carbon::parse($training->created_at)->format('g:i A');
              unset($trainings[$key]->created_at);

            }
            //
             return response()->json(['status' => 'success', 'data' => $trainings, 'message' => 'SUCCESS'], 200);
           }else{
            return response()->json(['status' => 'error', 'data' => 'Their are no review for this E-Training.', 'message' => 'NO_DATA'], 404);
           }
      }catch(Exception $e){
          return response()->json(['status' => 'error', 'data'=> $e->getMessage(), 'message' => 'EXCEPTION_OCCURED'], $e->getStatusCode());
      }
    }

    /**
     * To check if the specified resource is read by the user or not.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function isRead($id, Request $request)
    // {
    //   $user = JWTAuth::toUser($request->input('token'));
    //   try{
    //       $training = Training::findOrFail($id);
    //       $read = TrainingComplete::where('e_train_id', $training->id)->where('user_id', $user->id)->first();
    //
    //       if($read){
    //         return response()->json(['status' => 'success', 'data' => 'Training complete.', 'message' => 'TRAINING_COMPLETE'], 200);
    //       }else{
    //         return response()->json(['status' => 'success', 'data' => 'Training not complete.', 'message' => 'TRAINING_INCOMPLETE'], 200);
    //       }
    //   }catch(Exception $e){
    //       return response()->json(['status' => 'error', 'data'=> $e->getMessage(), 'message' => 'EXCEPTION_OCCURED'], $e->getStatusCode());
    //   }
    // }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
