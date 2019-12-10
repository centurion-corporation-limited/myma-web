<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Models\Maintenance;
use App\Models\Dormitory;
use App\Models\Status;
use App\Models\Batch;
use App\Models\BusRoute;
use App\Models\BusStop;
use Illuminate\Http\Request;
use App\Http\Requests\AddDormRequest;
use App\Http\Requests\EditDormRequest;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth, Activity;

class DormitoryController extends Controller
{
    public function getList(Request $request)
    {
      $auth_user = Auth::user();

      $items = Dormitory::query();

      if ($id = $request->input('id')) {
          $items->where('id', $id);
      }

      $limit = 10;
      $items = $items->sortable(['id' => 'desc'])->paginate($limit);
      $paginate_data = $request->except('page');

      return view('admin.dormitory.list', compact('items', 'auth_user', 'paginate_data'));
    }


    public function getAdd()
    {
        $auth_user = \Auth::user();
        $users = User::where('blocked', '0')->select('email','id')->get();

        return view('admin.dormitory.add', compact('auth_user', 'users'));
    }

    public function postAdd(AddDormRequest $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $data = $request->only('name', 'manager_id');
        $data['status_id'] = 4;
        $module = Dormitory::create($data);
        if($module){
            Activity::log('Created new dormitory '.$module->name.' #'.$module->id. ' by '.$auth_user->name);

            $user = User::findOrFail($data['manager_id']);
            $user->addPermission('view.maintenance-list');
        }

        return redirect()->route('admin.dormitory.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Dormitory added successfully.',
        ]);

    }

    public function getView($id, Request $request)
    {
        $id = decrypt($id);
		$auth_user = \Auth::user();
		$item = Dormitory::findOrFail($id);
        $users = User::where('blocked', '0')->pluck('email','id');

        return view('admin.dormitory.view', compact('item', 'users'));
    }

    public function getEdit($id, Request $request)
    {
        $id = decrypt($id);
    	$auth_user = \Auth::user();
    	$item = Dormitory::findOrFail($id);
        // $status = Status::pluck('name', 'id');
        $users = User::where('blocked', '0')->select('email','id')->get();
        return view('admin.dormitory.edit', compact('item', 'users'));
    }

    public function postEdit($id, EditDormRequest $request)
    {
        $id = decrypt($id);
        /** @var User $item */
        $auth_user = \Auth::user();
        $module = Dormitory::findOrFail($id);

        $data = $request->only('name', 'manager_id', 'status_id');

        $up = $module->update($data);

        if($up){
            Activity::log('Updated dormitory information '.$module->name.' #'.$module->id. ' by '.$auth_user->name);

            $user = User::findOrFail($data['manager_id']);
            $user->addPermission('view.maintenance-list');
        }

        return redirect()->route('admin.dormitory.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Dormitory details updated successfully.',
        ]);

    }

    public function addBatch(Request $request)
    {
        $dorms = Dormitory::pluck('id')->all();

        $ifToday = Batch::whereDate('created_at', date('Y-m-d'))->first();

        if(!$ifToday){
            foreach($dorms as $item){
                $data['dormitory_id'] = $item;
                $val = Batch::orderBy('created_at','desc')->first();
                if($val == ""){
                    $data['batch_b'] = $id = 'BB'. sprintf("%02d", $item). sprintf("%05d", $val) ;
                    $data['batch_l'] = $id = 'BL'. sprintf("%02d", $item). sprintf("%05d", $val) ;
                    $data['batch_d'] = $id = 'BD'. sprintf("%02d", $item). sprintf("%05d", $val) ;
                }else{
                    $data['batch_b'] = $id = 'BB'. sprintf("%02d", $item). sprintf("%05d", $val->id) ;
                    $data['batch_l'] = $id = 'BL'. sprintf("%02d", $item). sprintf("%05d", $val->id) ;
                    $data['batch_d'] = $id = 'BD'. sprintf("%02d", $item). sprintf("%05d", $val->id) ;
                }
                Batch::create($data);
            }
        }
        // return redirect()->route('admin.dormitory.list')->with([
        //     'flash_level'   => 'success',
        //     'flash_message' => 'Dormitory details updated successfully.',
        // ]);

    }

    public function addRoutes()
    {

        $client = new Client(['headers' => ['AccountKey' => 'frpVoDiyQ3KelpOKYY9UmA==']]); //GuzzleHttp\Client
        $skip = 0;
        do{
            $result = $client->get('http://datamall2.mytransport.sg/ltaodataservice/BusRoutes?$skip='.$skip);
            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();

                $content = json_decode($body->getContents());
                if(count($content->value)){
                    foreach($content->value as $row){

                        $data['ServiceNo'] = $row->ServiceNo;
                        $data['Direction'] = $row->Direction;
                        $data['Operator'] = $row->Operator;
                        $data['StopSequence'] = $row->StopSequence;
                        $data['BusStopCode'] = $row->BusStopCode;
                        $data['Distance'] = $row->Distance;
                        $data['WD_FirstBus'] = $row->WD_FirstBus;
                        $data['WD_LastBus'] = $row->WD_LastBus;
                        $data['SAT_FirstBus'] = $row->SAT_FirstBus;
                        $data['SAT_LastBus'] = $row->SAT_LastBus;
                        $data['SUN_FirstBus'] = $row->SUN_FirstBus;
                        $data['SUN_LastBus'] = $row->SUN_LastBus;

                        BusRoute::create($data);
                    }
                }
                else{
                    echo "worked";
                    exit;
                }
            }else{
                echo "exception occured";
                exit;
            }
            $skip += 500;
        }while(1);


    }

    public function addBusStops()
    {
        $client = new Client(['headers' => ['AccountKey' => 'frpVoDiyQ3KelpOKYY9UmA==']]); //GuzzleHttp\Client
        $skip = 0;
        do{
            $result = $client->get('http://datamall2.mytransport.sg/ltaodataservice/BusStops?$skip='.$skip);
            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();

                $content = json_decode($body->getContents());
                if(count($content->value)){
                    foreach($content->value as $row){

                        $data['name'] = $row->Description;
                        $data['road_name'] = $row->RoadName;
                        $data['name_slug'] = strtolower($row->Description);
                        $data['road_name_slug'] = strtolower($row->RoadName);
                        $data['code'] = $row->BusStopCode;
                        $data['latitude'] = $row->Latitude;
                        $data['longitude'] = $row->Longitude;

                        BusStop::create($data);
                    }
                }
                else{
                    echo "worked";
                    exit;
                }
            }else{
                echo "exception occured";
                exit;
            }
            $skip += 500;
        }while(1);
    }

    public function getDelete($id)
    {
        Dormitory::destroy($id);

        $auth_user = Auth::user();
        Activity::log('Deleted dormitory #'.$module->id. ' by '.$auth_user->name);

        return redirect()->route('admin.dormitory.list')->with([
            'flash_level'   => 'danger',
            'flash_message' => 'Dormitory Deleted',
        ]);
    }

    public function postDelete($id)
    {
        Dormitory::delete($id);
        return redirect()->route('admin.dormitory.list')->with([
          'flash_level'   => 'danger',
          'flash_message' => 'Dormitory Deleted',
        ]);

    }

    public function getCarousels()
    {

        $client = new Client(['headers' => ['Accept' => 'application/vnd.spuul.v3']]); //GuzzleHttp\Client
        $skip = 0;

            $result = $client->get('http://api.spuul.com/carousels');
            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
                $body = $result->getBody();

                // $content = json_decode($body->getContents());
                dd($body);
            }else{
                echo "exception occured";
                exit;
            }




    }
}
