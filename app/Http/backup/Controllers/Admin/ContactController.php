<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Models\Contact;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth, Event, Activity;

class ContactController extends Controller
{
    public function getList(Request $request)
    {
      $auth_user = Auth::user();

      $items = Contact::query();

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
      // if ($id = $request->input('id')) {
      //     $items->where('id', $id);
      // }

      $limit = 10;
      $items = $items->sortable(['id' => 'desc'])->paginate($limit);
      $paginate_data = $request->except('page');

      return view('admin.contact.list', compact('items', 'auth_user', 'paginate_data'));
    }

    public function getView($id, Request $request)
    {
        $id = decrypt($id);
    	$auth_user = \Auth::user();
    	$item = Contact::findOrFail($id);

        return view('admin.contact.view', compact('item'));
    }

    public function getReply($id, Request $request)
    {
        $id = decrypt($id);
    	$auth_user = \Auth::user();
    	$item = Feedback::findOrFail($id);

        return view('admin.feedback.reply', compact('item'));
    }

    public function postReply($id, Request $request)
    {
        /** @var User $item */
        $auth_user = Auth::user();
        $id = decrypt($id);
        $feedback = Feedback::findOrFail($id);
        //
        $data = $request->only('feedback');
        $data['user_id'] = $auth_user->id;
        $data['feedback_id'] = $id;

        $module = FeedbackReply::create($data);

        Activity::log('Replied to feedback #'.$id. ' by '.$auth_user->name);

        if($module && $feedback->email_reply == 1){
            Event::fire('feedback.reply', $module->id);
        }else{
            Event::fire('feedback.notification', [$id, $data['feedback']]);
        }
        return redirect()->route('admin.feedback.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Reply sent successfully.',
        ]);

    }

    public function getDelete($id)
    {
        Feedback::destroy($id);
        $auth_user = Auth::user();
        Activity::log('Deleted feedback #'.$id. ' by '.$auth_user->name);

        return redirect()->route('admin.contact.list')->with([
            'flash_level'   => 'danger',
            'flash_message' => 'Contact request deleted',
        ]);
    }

    public function postDelete($id)
    {
        Feedback::delete($id);
        $auth_user = Auth::user();
        Activity::log('Deleted feedback #'.$id. ' by '.$auth_user->name);
        return redirect()->route('admin.contact.list')->with([
          'flash_level'   => 'danger',
          'flash_message' => 'Contact request deleted',
        ]);

    }
}
