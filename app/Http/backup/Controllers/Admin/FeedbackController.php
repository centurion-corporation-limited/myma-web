<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Events\FeedbackReplyEvent;
use App\Events\FeedbackNotification;

use Carbon\Carbon;
use App\Models\Feedback;
use App\Models\FeedbackReply;
use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Crypt;

use Illuminate\Contracts\Encryption\DecryptException;

use App\Http\Requests;
use App\Http\Requests\FeedbackRequest;
use App\Http\Controllers\Controller;
use Auth, Event, Activity;

class FeedbackController extends Controller
{
    public function getList(Request $request)
    {
      $auth_user = Auth::user();

      $items = Feedback::query();

      if ($name = $request->input('name')) {
          $name = strtolower($name);
          $items->whereRaw('lower(`name`) like ?', array("%{$name}%"));
      }

      if ($email = $request->input('email')) {
          $email = strtolower($email);
          $items->whereRaw('lower(`email`) like ?', array("%{$email}%"));
      }

      if ($phone = $request->input('phone')) {
          $items->where('phone', 'like',"%{$phone}%");
      }

      $type = \Input::get('type');
      if($type != '' && $type == 'mom'){
          $type = 'mom';
      }else{
          $type = 'feedback';
      }
      $items->where('type', $type);

      if ($reported = \Input::get('reported')) {
          if($reported == 'today'){
              $now = Carbon::now()->toDateString();
              $items->whereDate('created_at', $now);
          }
      }

      $limit = 10;
      $items = $items->sortable(['id' => 'desc'])->paginate($limit);
      $paginate_data = $request->except('page');

      return view('admin.feedback.list', compact('items', 'auth_user', 'type', 'paginate_data'));
    }


    public function getAdd()
    {
        $auth_user = \Auth::user();

        return view('admin.feedback.add', compact('auth_user'));
    }

    public function postAdd(Request $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $data = $request->only('title');

        $module = Feedback::create($data);

        return redirect()->route('admin.feedback.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Feedback added successfully.',
        ]);

    }
    public function getReply($id, Request $request)
    {
        $id = decrypt($id);
    	$auth_user = \Auth::user();
    	$item = Feedback::findOrFail($id);
        if($_SERVER['REMOTE_ADDR'] == '110.225.199.96'){

        }
        return view('admin.feedback.reply', compact('item'));
    }

    public function postReply($id, FeedbackRequest $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $id = decrypt($id);
        $feedback = Feedback::findOrFail($id);
        //
        $data = $request->only('feedback');
        $data['user_id'] = $auth_user->id;
        $data['feedback_id'] = $id;

        $module = FeedbackReply::create($data);

        $searchValue= $feedback['email'];
        $users = User::all()->filter(function($record) use($searchValue) {
            $email = $record->email;
            try{
                $email = Crypt::decrypt($email);
            }catch(DecryptException $e){
            }
            if($email == $searchValue) {
                return $record;
            }
        });
        if(count($users)){
            $user = $users->first();
            // $user = User::where('email',$feedback['email'])->first();
        }
        Activity::log('Replied to feedback #'.$id. ' by '.$auth_user->name);

        Notification::create(['type' => 'feedback', 'title' => 'Feedback Reply', 'message' => $data['feedback'], 'user_id' => @$user->id, 'created_by' => $auth_user->id]);

        if($module && $feedback->email_reply == 1){
            event(new FeedbackReplyEvent($module->id));
            // Event::fire('feedback.reply', $module->id);
        }

        event(new FeedbackNotification($id, $data['feedback']));

        // Event::fire('feedback.notification', [$id, $data['feedback']]);

        return redirect()->route('admin.feedback.list', ['type' => $feedback->type])->with([
            'flash_level'   => 'success',
            'flash_message' => 'Reply sent successfully.',
        ]);

    }

    public function getDelete($id)
    {
        Feedback::destroy($id);

        $auth_user = Auth::user();
        Activity::log('Deleted feedback #'.$module->id. ' by '.$auth_user->name);

        return redirect()->route('admin.feedback.list')->with([
            'flash_level'   => 'danger',
            'flash_message' => 'Feedback Deleted',
        ]);
    }

    public function postDelete($id)
    {
        Feedback::delete($id);
        return redirect()->route('admin.feedback.list')->with([
          'flash_level'   => 'danger',
          'flash_message' => 'Feedback Deleted',
        ]);

    }
}
