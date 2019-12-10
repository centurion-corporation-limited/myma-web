<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use JWTAuth, DB;
use App\Models\Module;
use App\Models\Training;
use Carbon\Carbon;

class ModuleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $user = JWTAuth::toUser($request->input('token'));
        try{
            $modules = Module::select('id', 'title', 'description')->get();
            if($modules->count()){
              return response()->json(['status' => 'success', 'data' => $modules, 'message' => 'SUCCESS'], 200);
            }else{
              return response()->json(['status' => 'error', 'data' => 'Modules does not exists.', 'message' => 'NO_DATA'], 404);
            }
        }catch(Exception $e){
            return response()->json(['status' => 'error', 'data'=> $e->getMessage(), 'message' => 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
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
        // $user = JWTAuth::toUser($request->input('token'));
        try{
            $trainings = DB::table('e_train as t')
                          ->join('users as u', 't.created_by', '=', 'u.id')
                          ->join('module as m', 't.module_id', '=', 'm.id')
                          ->select('t.id', 'u.name as created_by', 'm.title as module_title','t.title as title','t.description as description', 't.type', 't.path as link',
                          't.created_at')
                          ->where('t.module_id',$id)
                          ->orderBy('t.created_at', 'asc')
                          ->get();

            // Training::where('module_id', $id)->->orderBy('created_at', 'asc')->get();
            if($trainings->count()){
              foreach($trainings as $key => $training){
                $trainings[$key]->link = url($training->link);
                $trainings[$key]->date = Carbon::parse($training->created_at)->format('M d, Y');
                $trainings[$key]->time = Carbon::parse($training->created_at)->format('g:i A');
                unset($trainings[$key]->created_at);
              }
              return response()->json(['status' => 'success', 'data' => $trainings, 'message' => 'SUCCESS'], 200);
            }else{
              return response()->json(['status' => 'error', 'data' => 'Module does not exist.', 'message' => 'NO_DATA'], 404);
            }
        }catch(Exception $e){
            return response()->json(['status' => 'error', 'data'=> $e->getMessage(), 'message' => 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

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
