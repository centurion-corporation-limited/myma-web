<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Validator;
use JWTAuth, DB, Activity;
use App\Models\BusRoute;
use App\Models\BusStop;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

use Carbon\Carbon;

class BusController extends Controller
{
    public function list_route(Request $request)
    {
        // $user = JWTAuth::toUser($request->input('token'));

        try{
            $limit = (isset($request->limit) && $request->limit > 0)?$request->limit:10;
            $page_no = (isset($request->page_no) && $request->page_no > 1)?$request->page_no:1;
            $keyword = (isset($request->keyword) && $request->keyword != '')?$request->keyword:'';

            $offset = ($page_no-1)*$limit;

            $list = BusRoute::groupBy('ServiceNo');
            if($keyword != ""){
                $list->where('ServiceNo', 'like','%'.strtoupper($keyword).'%');
            }
            $list = $list->offset($offset)->limit($limit)->get();
          if($list){
            return response()->json(['status' => 'success', 'data' => $list, 'message' => 'DATA_LIST.'], 200);
          }else{
            return response()->json(['status' => 'error', 'data' => 'Their was a problem while creating new data. Try Later.', 'message' => 'INSERTION_ERROR'], 404);
          }

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    protected function validator(array $data)
    {
      return Validator::make($data, [
            'no'      => 'required',
        ]);
    }

    public function list_route_stops(Request $request)
    {
        // $user = JWTAuth::toUser($request->input('token'));
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            $errors = $validator->errors();
            $message = [];
            foreach($errors->messages() as $key => $error){
                $message[$key] = $error[0];
            }
            return response()->json(['status' => 'error', 'data' => $message, 'message' => 'VALIDATION_ERROR'], 401);
        }

        try{
            // $limit = (isset($request->limit) && $request->limit > 0)?$request->limit:10;
            // $page_no = (isset($request->page_no) && $request->page_no > 1)?$request->page_no:1;
            $no = strtoupper($request->no);
            $keyword = (isset($request->keyword) && $request->keyword != '')?$request->keyword:'';

            // $offset = ($page_no-1)*$limit;

            $list = BusRoute::where('ServiceNo', $no)->where('Direction', '1')
            ->join('bus_stops', 'bus_routes.BusStopCode', '=', 'bus_stops.code')
            ->select('bus_routes.*', 'bus_stops.name');
            if($keyword != ""){
                // $list->where('ServiceNo', 'like','%'.strtoupper($keyword).'%');
            }
            $list = $list->get();//offset($offset)->limit($limit)->
            $list2 = BusRoute::where('ServiceNo', $no)->where('Direction', '2')
            ->join('bus_stops', 'bus_routes.BusStopCode', '=', 'bus_stops.code')->select('bus_routes.*', 'bus_stops.name')->get();
          if($list){
            return response()->json(['status' => 'success', 'data' => $list, 'data2' => $list2, 'message' => 'DATA_LIST.'], 200);
          }else{
            return response()->json(['status' => 'error', 'data' => 'Their was a problem while creating new data. Try Later.', 'message' => 'INSERTION_ERROR'], 404);
          }

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function list_stops(Request $request)
    {
        // $user = JWTAuth::toUser($request->input('token'));

        try{
            $limit = (isset($request->limit) && $request->limit > 0)?$request->limit:10;
            $page_no = (isset($request->page_no) && $request->page_no > 1)?$request->page_no:1;
            $keyword = (isset($request->keyword) && $request->keyword != '')?$request->keyword:'';

            $offset = ($page_no-1)*$limit;

            $list = BusStop::orderBy('code');

            if($keyword != ""){
                $list->where('name_slug', 'like','%'.strtolower($keyword).'%')->orWhere('road_name_slug', 'like', '%'.strtolower($keyword).'%')->orWhere('code', 'like', '%'.strtolower($keyword).'%');
            }else if($keyword == "" && $request->latitude != '' && $request->longitude != ''){
                $sql = 'SELECT id, (3956 * 2 * ASIN(SQRT( POWER(SIN(('.$request->latitude.' - abs(dest.latitude)) * pi()/180 / 2),2) +
                COS('.$request->latitude.' * pi()/180 ) * COS( abs(dest.latitude) * pi()/180) * POWER(SIN(('.$request->longitude.' - dest.longitude) * pi()/180 / 2), 2) )))
                as distance FROM '.config("app.db_portal").'.`bus_stops` dest having distance < 10 ORDER BY distance limit 10';
                $items = \DB::select($sql);
                $ids = [];
                foreach($items as $sql_dd){
                    $ids[] = $sql_dd->id;
                }
                // return response()->json(['status' => 'success', 'data' => $ids, 'message' => 'DATA_LIST.'], 200);
                if(count($ids)){
                    $list = $list->whereIn('id', $ids);
                }
            }

            $list = $list->offset($offset)->limit($limit)->get();
          if($list){
            return response()->json(['status' => 'success', 'data' => $list, 'message' => 'DATA_LIST.'], 200);
          }else{
            return response()->json(['status' => 'error', 'data' => 'Their was a problem while creating new data. Try Later.', 'message' => 'INSERTION_ERROR'], 404);
          }

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    protected function arrival_validator(array $data)
    {
      return Validator::make($data, [
            'code'      => 'required',
        ]);
    }

    public function getArrival(Request $request)
    {
        // $user = JWTAuth::toUser($request->input('token'));
        $validator = $this->arrival_validator($request->all());

        if ($validator->fails()) {
            $errors = $validator->errors();
            $message = [];
            foreach($errors->messages() as $key => $error){
                $message[$key] = $error[0];
            }
            return response()->json(['status' => 'error', 'data' => $message, 'message' => 'VALIDATION_ERROR'], 401);
        }

        try{
            $code = $request->code;

            $list = $this->getBusses($code);
          if($list){
            return response()->json(['status' => 'success', 'data' => $list, 'message' => 'DATA_LIST.'], 200);
          }else{
            return response()->json(['status' => 'error', 'data' => 'No data found', 'message' => 'NO_DATA'], 200);
          }

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    public function getBusses($code)
    {
        $client = new Client(['headers' => ['AccountKey' => 'frpVoDiyQ3KelpOKYY9UmA==']]); //GuzzleHttp\Client
        $skip = 0;

        $result = $client->get('http://datamall2.mytransport.sg/ltaodataservice/BusArrivalv2?BusStopCode='.$code);
        $code = $result->getStatusCode(); // 200
        $reason = $result->getReasonPhrase(); // OK
        if($code == "200" && $reason == "OK"){
            $body = $result->getBody();
            $content = json_decode($body->getContents());
            if(count($content->Services)){
                foreach($content->Services as $row){
                    $datetime1 = new \DateTime();
                    $datetime2 = new \DateTime($row->NextBus->EstimatedArrival);
                    $interval = $datetime1->diff($datetime2);
                    $row->NextBus->minutes = $interval->format('%i min');

                    $datetime2 = new \DateTime($row->NextBus2->EstimatedArrival);
                    $interval = $datetime1->diff($datetime2);
                    $row->NextBus2->minutes = $interval->format('%i min');

                    $datetime2 = new \DateTime($row->NextBus3->EstimatedArrival);
                    $interval = $datetime1->diff($datetime2);
                    $row->NextBus3->minutes = $interval->format('%i min');
                }

                return $content->Services;
            }
            else{
                return false;
            }
        }else{
            return false;
        }
    }

}
