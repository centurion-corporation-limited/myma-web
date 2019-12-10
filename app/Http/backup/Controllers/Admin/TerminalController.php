<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Models\Merchant;
use App\Models\Terminal;
use App\Models\MerchantCode;
use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;

use App\Http\Requests;
use App\Http\Requests\AddMomCategoryRequest;
use App\Http\Requests\EditMomCategoryRequest;
use App\Http\Controllers\Controller;
use Auth, Activity, JWTAuth;

class TerminalController extends Controller
{

    public function getList(Request $request)
    {
      $auth_user = Auth::user();
      $items = Terminal::orderBy('id');
      $items->whereHas('merchant', function($q) {
          $q->where('type', 'instore')->where('active', 1);
      });

      if ($merchant_id = $request->input('merchant_id')) {
          $merchant_id = decrypt($merchant_id);
          $items->where('merchant_id', $merchant_id);

      }

      if ($name = $request->input('name')) {
          $name = strtolower($name);
          $items->whereHas('merchant', function($q) use($name){
              $q->whereRaw('lower(`merchant_name`) like ?', array("%{$name}%"));
          });
      }

      $limit = 10;
      $items = $items->paginate($limit);
      $paginate_data = $request->except('page');

      $names = Merchant::where('type', 'instore')->where('active', 1)->pluck('merchant_name', 'id');

      return view('admin.terminal.list', compact('items', 'auth_user', 'paginate_data', 'names'));
    }


    public function getAdd(Request $request)
    {
        $auth_user = \Auth::user();
        $merchant = Merchant::where('type', 'instore')->pluck('merchant_name', 'id');
        $merchant_id = $request->input('merchant_id');
        $merchant_id = decrypt($merchant_id);

        return view('admin.terminal.add', compact('auth_user', 'languages', 'merchant','merchant_id'));
    }

    public function postAdd(Request $request)
    {
        /** @var User $item */
        $url = config('app.url').'api/v1/flexm/';
        $auth_user = \Auth::user();
        $data = $request->only('merchant_id','location');
        $data['payment_mode'] = 2;
        $tid = '';
        try{
          $exist = Merchant::find($data['merchant_id']);
          if($exist){
            $dat['token'] = JWTAuth::fromUser($auth_user);
            $dat['payment_mode'] = 2;
            $dat['merchant_code'] = $exist->merchant_code;
            $client = new Client();
            $result = $client->post($url.'create/terminal',[
              'form_params' => $dat
            ]);

            $code = $result->getStatusCode(); // 200
            $reason = $result->getReasonPhrase(); // OK
            if($code == "200" && $reason == "OK"){
              $body = $result->getBody();
              $content = json_decode($body->getContents());

              if($content->status == 'success'){
                $msg = $content->message;
                $cont = $content->data;
                $terone['merchant_id'] = $exist['id'];
                $terone['payment_mode'] = 2;
                $tid = $terone['tid'] = $cont->tid;
                $tero = Terminal::where($terone)->first();
                $terone['location'] = $data['location'];
                $terone['status'] = $cont->status;
                if($tero){
                  $tero->update($terone);
                }else{
                  Terminal::create($terone);
                }
              }
              else{
                return back()->withInput()->withErrors(['Could not create terminal. Try again later']);
              }
            }else{
              abort(404);
            }
            $data['mid'] = $exist->mid;
            \QrCode::format('png')->merge(public_path('/images/flexm.png'), .2, true)->errorCorrection('H')->margin(1)->size(400)->generate($data['mid'].'_'.$tid, '../public/files/merchant/'.$data['mid'].'_'.$tid.'.png');
            $qr_code = 'files/merchant/'.$data['mid'].'_'.$tid.'.png';

            $terminal = Terminal::where('tid', $tid)->first();
            if($terminal)
              $terminal->update(['qr_code' => $qr_code]);

            return redirect()->route('admin.terminal.list')->with([
              'flash_level'   => 'success',
              'flash_message' => 'Terminal created successfully.',
            ]);
          }
        }catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            $jsonBody = json_decode((string) $response->getBody());

            return back()->withInput()->withErrors([@$jsonBody->errors]);
            // return response()->json(['status' => 'error', 'data' => $jsonBody->errors, 'message'=> $jsonBody->message], 200);
        }catch(GuzzleException $e){
            return back()->withInput()->withErrors([$e->getMessage()]);
        }catch(Exception $e){
            return back()->withInput()->withErrors([$e->getMessage()]);
        }
    }
    public function getEdit($id, Request $request)
    {
        $auth_user = \Auth::user();
        $id = decrypt($id);
    	  $item = Merchant::findOrFail($id);
        $merchant = Merchant::pluck('merchant_name', 'id');

        return view('admin.terminal.edit', compact('item', 'languages', 'merchant'));
    }

    public function postEdit($id, Request $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $id = decrypt($id);
        $module = Merchant::findOrFail($id);
        $data = $request->only('merchant_name', 'merchant_category_code');

        $module->update($data);
        // Activity::log('Mom category updated #'.@$id. ' by '.$auth_user->name);

        return redirect()->route('admin.terminal.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Merchant details updated successfully.',
        ]);

    }

    public function getDelete($id)
    {
        Merchant::destroy($id);

        $auth_user = Auth::user();
        Activity::log('Deleted merchant by '.$auth_user->name);

        return redirect()->route('admin.terminal.list')->with([
            'flash_level'   => 'danger',
            'flash_message' => 'Merchant Deleted',
        ]);
    }

    public function postDelete($id)
    {
        Merchant::delete($id);
        Activity::log('Deleted merchant by '.$auth_user->name);
        return redirect()->route('admin.terminal.list')->with([
          'flash_level'   => 'danger',
          'flash_message' => 'Merchant Deleted',
        ]);

    }

    public function getQRView($id, Request $request)
    {
        $id = decrypt($id);
    	  $item = Terminal::findOrFail($id);
        if($item){
            return view('admin.merchant.qrcode', compact('item'));
        }
        abort('404');
    }
}
