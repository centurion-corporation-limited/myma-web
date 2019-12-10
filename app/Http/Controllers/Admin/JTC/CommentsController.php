<?php

namespace App\Http\Controllers\Admin\JTC;

use App\User;
use App\Models\JTC\Comment;
use App\Models\JTC\Event;
use App\Models\JTC\Detail;
use App\Models\JTC\DetailLang;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\AddDetailRequest;
use App\Http\Requests\JTC\EditDetailRequest;
use App\Http\Controllers\Controller;
use Auth, Activity;

class CommentsController extends Controller
{
    public function getTopicList(Request $request)
    {
      $auth_user = Auth::user();

      $items = Comment::query();

      if ($name = $request->input('name')) {
          $name = strtolower($name);
          $items->whereHas('lang_content', function($q) use($name){
              $q->whereRaw('lower(`title`) like ?', array("%{$name}%"));
          });
      }

      $event = '';
      if ($event_id = $request->input('topic_id')) {
          $event_id = decrypt($event_id);
          $event = Detail::find($event_id);
          $items->where('topic_id', $event_id);
      }

      $limit = 10;
      $items = $items->paginate($limit);
      $paginate_data = $request->except('page');

      return view('admin.jtc.comments.list', compact('items', 'auth_user', 'paginate_data', 'event'));
    }

    public function getTopicEdit($id, Request $request)
    {
        $id = decrypt($id);
    	  $auth_user = \Auth::user();
    	  $item = Comment::findOrFail($id);

        return view('admin.jtc.comments.edit', compact('item', 'users', 'events'));
    }

    public function postTopicEdit($id, Request $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $id = decrypt($id);
        $module = Comment::findOrFail($id);

        $data = $request->only('comment');

        $module->update($data);

        Activity::log('Updated comment #'.@$id. ' by '.$auth_user->name);

        return redirect()->route('admin.jtc.comments.list', ['topic_id' => encrypt($module->topic_id)])->with([
            'flash_level'   => 'success',
            'flash_message' => 'Detail entry updated successfully.',
        ]);

    }

    public function getTopicDelete($id)
    {
        $comment = Comment::find($id);
        Comment::destroy($id);

        $auth_user = Auth::user();
        Activity::log('Deleted a comment entry #'.@$id. ' by '.$auth_user->name);

        return redirect()->route('admin.jtc.comments.list', ['topic_id' => encrypt($comment->topic_id)])->with([
            'flash_level'   => 'danger',
            'flash_message' => 'Comment deleted',
        ]);
    }
}
