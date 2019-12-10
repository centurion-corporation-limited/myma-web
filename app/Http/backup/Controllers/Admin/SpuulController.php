<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Models\BusStop;
use App\Models\Transactions;
use App\Models\SpuulPlan;
use Illuminate\Http\Request;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

use App\Http\Requests\AddSpuulRequest;
use App\Http\Requests\EditSpuulRequest;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth, Activity;
use Carbon\Carbon;

class SpuulController extends Controller
{

    public function getTransactions(){
        $auth_user = Auth::user();

        $items = Transactions::orderBy('created_at', 'desc');

        $limit = 10;
        $items = $items->paginate($limit);
        $paginate_data = $request->except('page');

        return view('admin.spuul.list', compact('items', 'auth_user', 'paginate_data'));

    }

    public function getList(Request $request){
        $auth_user = Auth::user();

        $items = SpuulPLan::query();

        $limit = 10;
        $items = $items->sortable()->paginate($limit);
        $paginate_data = $request->except('page');

        return view('admin.spuul.plan.list', compact('items', 'auth_user', 'paginate_data'));

    }

    public function getAdd(){

        $auth_user = Auth::user();
        $type = ['1' => 'Monthly', '2' => 'yearly'];;

        return view('admin.spuul.plan.add', compact('auth_user', 'type'));
    }

    public function postAdd(AddSpuulRequest $request)
    {
        /** @var User $item */
        $auth_user = Auth::user();
        $data = $request->only('type', 'price', 'list_order');
        $exist = SpuulPlan::where('type', $data['type'])->where('status', '1')->get();
        if($exist->count()){
            if($data['type'] == '1')
                $type = 'Monthly';
            else
                $type = 'Yearly';

            return back()->withErrors("Can't add a new ".$type." plan as long as the previous one is active.");
        }else{
            $module = SpuulPlan::create($data);
        }

        return redirect()->route('admin.spuul.plan.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Plan added successfully.',
        ]);

    }
    public function getEdit($id, Request $request)
    {
    	$auth_user = \Auth::user();
        $id = decrypt($id);
        $item = SpuulPlan::findOrFail($id);

        return view('admin.spuul.plan.edit', compact('item'));
    }

    public function postEdit($id, EditSpuulRequest $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $id = decrypt($id);
        $module = SpuulPlan::findOrFail($id);

        // if($module->ads && $module->ads->count() == 0){
            $data = $request->only('price', 'status', 'list_order');
            // if($module)
            $module->update($data);
            return redirect()->route('admin.spuul.plan.list')->with([
                'flash_level'   => 'success',
                'flash_message' => 'Plan details updated successfully.',
            ]);
        // }else{
        //     return redirect()->route('admin.spuul.plan.list')->with([
        //         'flash_level'   => 'error',
        //         'flash_message' => "Plan can't be updated as this has been in use.",
        //     ]);
        // }


    }

    public function getDelete($id)
    {
        SpuulPlan::destroy($id);

        return redirect()->route('admin.spuul.plan.list')->with([
            'flash_level'   => 'danger',
            'flash_message' => 'Plan Deleted',
        ]);
    }

    public function viewTransactions(){
        $auth_user = Auth::user();

        $items = Transactions::orderBy('created_at', 'desc');

        $limit = 10;
        $items = $items->paginate($limit);
        $paginate_data = $request->except('page');

        return view('admin.spuul.list', compact('items', 'auth_user', 'paginate_data'));

    }

    public function getToken()
    {
        try{
            $client = new Client();
            $result = $client->post('https://api.spuul.com/oauth/token',[
                'form_params' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => '1e9240872cfda7989634346aa89cd3bf05d6a0462756f58d79e445788cca8d66',
                    'client_secret' => '6bdfaae72a9a41b551958c3ff0e1057a9ef25632f2f562049cba8b384dce41e3'
                ]
            ]);
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

        }catch(Exception $e){

        }
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
