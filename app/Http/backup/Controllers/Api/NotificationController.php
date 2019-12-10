<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Validator;
use JWTAuth, DB;
use App\Models\Notification;
use App\Models\NotifyUser;
use Carbon\Carbon;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function listout(Request $request)
    {
      $user = JWTAuth::toUser($request->input('token'));

      try{
          $limit = (isset($request->limit) && $request->limit > 0)?$request->limit:10;
          $page_no = (isset($request->page_no) && $request->page_no > 1)?$request->page_no:1;
          $offset = ($page_no-1)*$limit;

          $created_at = $user->created_at->toDateString();

          $count = Notification::where(function($q) use($user){
            $q->where('user_id',$user->id)->orWhere(function($qq){
              $qq->whereNull('user_id')->where('type', 'general');
            });
          })->whereDate('created_at', '>=',$created_at)->get()->count();

          $notifications = Notification::select('title', 'message', 'created_at', 'id', 'link')
                         ->where(function($q) use($user){
                            $q->where('user_id',$user->id)->orWhere(function($qq){
                              $qq->whereNull('user_id')->where('type', 'general');
                            });
                          })
                        ->whereDate('created_at', '>=',$created_at)
                        ->orderBy('created_at', 'desc')
                        ->offset($offset)->limit($limit)
                        ->get();
          if($notifications->count()){
            // foreach($notifications as $key => $notification){
            //   $notifications[$key]->date = Carbon::parse($notification->created_at)->format('M d, Y');
            //   $notifications[$key]->time = Carbon::parse($notification->created_at)->format('g:i A');
            //   unset($notifications[$key]->created_at);
            // }
            return response()->json(['status' => 'success', 'total' => $count, 'data' => $notifications, 'message' => 'SUCCESS'], 200);
          }else{
            return response()->json(['status' => 'error', 'data' => 'No notification found.', 'message' => 'NO_DATA'], 404);
          }
      }catch(Exception $e){
          return response()->json(['status' => 'error', 'data'=> $e->getMessage(), 'message' => 'EXCEPTION_OCCURED'], $e->getStatusCode());
      }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
      $user = JWTAuth::toUser($request->input('token'));
      try{
          $id = $request->input('id');
          $id = explode(',',$id);
          $notification = Notification::whereIn('id', $id)->delete();//->where('user_id', $user->id)
          // if($notification){
            // $notification->delete();
            return response()->json(['status' => 'success', 'data' => 'Notification Deleted.', 'message' => 'SUCCESS'], 200);
          // }else{
            // return response()->json(['status' => 'error', 'data' => 'Notifications does not exists.', 'message' => 'NO_DATA'], 404);
          // }
      }catch(Exception $e){
          return response()->json(['status' => 'error', 'data'=> $e->getMessage(), 'message' => 'EXCEPTION_OCCURED'], $e->getStatusCode());
      }
    }
}
