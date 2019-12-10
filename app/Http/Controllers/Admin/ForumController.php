<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Models\Forum;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Models\Badwords;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth, Activity;

class ForumController extends Controller
{
    public function getList(Request $request)
    {
      $auth_user = Auth::user();

      $items = Forum::query();

      if ($name = $request->input('name')) {
          $name = strtolower($name);
          $items->whereRaw('lower(`title`) like ?', array("%{$name}%"));
          // ->orWhereRaw('lower(`content`) like ?', array("%{$name}%"));
          // $items->whereHas('lang_content', function($q) use($name){
          //     $q->whereRaw('lower(`title`) like ?', array("%{$name}%"));
          // });
      }

      if ($id = $request->input('id')) {
          $items->where('id', $id);
      }
      $bad_words = $request->input('bad_words');
      if ($bad_words == 'true') {
          $items->where('bad_word', '1');
      }

      $reported = $request->input('reported');
      if ($reported == 'true') {
          $items->where('report', '1');
      }

      if ($topic_id = $request->input('topic_id')) {
          $topic_id = decrypt($topic_id);
          $items->where('topic_id', $topic_id);
      }

      $items = $items->with('latestComment')->withCount('likes', 'comments');

      $limit = 10;
      $items = $items->sortable(['id' => 'desc'])->paginate($limit);
      $paginate_data = $request->except('page');
//       foreach($items as $key => $item){
//           echo "<pre>";print_r($item->latestComment[0]->created_at);
//       }
// die();
      return view('admin.forum.list', compact('items', 'auth_user', 'bad_words', 'reported', 'paginate_data'));
    }


    public function getAdd()
    {
        $auth_user = \Auth::user();

        return view('admin.forum.add', compact('auth_user'));
    }

    public function postAdd(Request $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $data = $request->only('title', 'content');

        $module = Forum::create($data);

        Activity::log('Added new forum '.$data['title'].' #'.$module->id. ' by '.$auth_user->name);

        return redirect()->route('admin.forum.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Forum added successfully.',
        ]);

    }

    public function getEdit($id, Request $request)
    {
    		$auth_user = \Auth::user();
            $id = decrypt($id);
    		$item = Forum::findOrFail($id);
            $bad_words = $request->input('bad_words');
            if ($bad_words == 'true') {
                $parameters['bad_words'] = 'true';
            }

        return view('admin.forum.edit', compact('item', 'bad_words'));
    }

    public function unReport($id)
    {
    	$auth_user = \Auth::user();
        $id = decrypt($id);
    	$item = Forum::findOrFail($id);
        $item->update(['report' => '0']);

        Activity::log('Forum status changed frm flagged to not flagged #'.$id. ' by '.$auth_user->name);

        return back()->with([
            'flash_level'   => 'success',
            'flash_message' => 'Report status changed successfully.',
        ]);
    }

    public function postEdit($id, Request $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $id = decrypt($id);
        $item = Forum::findOrFail($id);
        $data = $request->only('title', 'content');

        $obj = Badwords::select('language', 'word')->get()->toArray();
        $clean_words = \ConsoleTVs\Profanity\Builder::blocker(strtolower($request->content))->dictionary($obj)->clean();
        if($clean_words)
          $clean_words = \ConsoleTVs\Profanity\Builder::blocker(strtolower($request->title))->dictionary($obj)->clean();

        $data['bad_word'] = $clean_words?'0':'1';
        $module = $item->update($data);

        Activity::log('Updated forum #'.$id. ' by '.$auth_user->name);
        $parameters = [];
        $bad_words = $request->input('bad_words');
        if ($bad_words == 'true') {
            $parameters['bad_words'] = 'true';
        }

        return redirect()->route('admin.forum.list', $parameters)->with([
            'flash_level'   => 'success',
            'flash_message' => 'Forum updated successfully.',
        ]);

    }


    public function getView($id, Request $request)
    {
        $id = decrypt($id);
    	$auth_user = \Auth::user();

    	$item = Forum::findOrFail($id);

        return view('admin.forum.view', compact('item'));
    }

    public function postReply($id, Request $request)
    {
        /** @var User $item */
        $auth_user = \Auth::user();
        $id = decrypt($id);
        $data = $request->only('comment');
        $data['forum_id'] = $id;
        $data['user_id'] = $auth_user->id;

        Comment::create($data);

        Activity::log('Commented on forum #'.$id. ' by '.$auth_user->name);

        return redirect()->back()->with([
            'flash_level'   => 'success',
            'flash_message' => 'Replied successfully.',
        ]);

    }

    public function getDelete($id)
    {
        Forum::destroy($id);
        $auth_user = Auth::user();
        Activity::log('Deleted forum #'.$id. ' by '.$auth_user->name);

        return redirect()->route('admin.forum.list')->with([
            'flash_level'   => 'danger',
            'flash_message' => 'E-Module Deleted',
        ]);
    }

    public function postDelete($id)
    {
        Forum::delete($id);
        return redirect()->route('admin.forum.list')->with([
          'flash_level'   => 'danger',
          'flash_message' => 'E-Module Deleted',
        ]);

    }

    public function getCommentsDelete($id)
    {
        Comment::destroy($id);
        $auth_user = Auth::user();
        Activity::log('Deleted comment from forum by '.$auth_user->name);

        return redirect()->back()->with([
            'flash_level'   => 'success',
            'flash_message' => 'Comment Deleted',
        ]);
    }

}
