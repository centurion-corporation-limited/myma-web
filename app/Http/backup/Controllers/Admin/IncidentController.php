<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\User;
use App\Models\Incident;
use App\Models\Dormitory;
use App\Models\File;
use App\Models\IncidentPhoto;
use App\Models\IncidentPeople;
use App\Http\Controllers\Controller;

use Carbon\Carbon;
use App\Http\Requests;
use Auth, Input, Exception, Activity;

class IncidentController extends Controller
{

  public function getList(Request $request)
  {
    $auth_user = Auth::user();

    $users = User::whereHas('roles', function($q){
        $q->where('slug', '=', 'employee');
    })->orderBy('created_at', 'desc')->get();

    $book_date = '';

    if($user_id = $request->input('user_id')){
      $sel_user = User::find($user_id);
      $items = Incident::where('user_id', $sel_user->id);

    }else{
      $items = Incident::query();
    }

    // if ($book_date = $request->input('book_date')) {
    //     $items->whereDate('date', $book_date);
    // }
    if ($name = $request->input('text')) {
        $name = strtolower($name);
        $items->whereRaw('lower(`location`) like ?', array("%{$name}%"))
        ->orWhereRaw('lower(`details`) like ?', array("%{$name}%"));
    }

    $dormitory_id = $request->input('dormitory_id');
    if ($dormitory_id != '' && $dormitory_id != '0') {
        $items->where('dormitory_id', $dormitory_id);
    }

    $limit = 10;
    $items = $items->sortable(['id' => 'desc'])->paginate($limit);
    $paginate_data = $request->except('page');

    $dormitories = Dormitory::pluck('name', 'id')->toArray();

    array_unshift($dormitories, 'Please select a dorm');

    return view('admin.incident.list', compact('items', 'auth_user', 'sel_user', 'users', 'book_date', 'paginate_data', 'dormitories', 'dormitory_id'));

  }

  public function exportPDF($id, Request $request)
  {
      $id = decrypt($id);
      $data['item'] = Incident::findOrFail($id);

      $photo_ids = explode(',', $data['item']->photo_id);
      $data['photos'] = File::whereIn('id', $photo_ids)->get();

      $video_ids = explode(',', $data['item']->video_id);
      $data['videos'] = File::whereIn('id', $video_ids)->get();
      $audio_ids = explode(',', $data['item']->audio_id);
      $data['audios'] = File::whereIn('id', $audio_ids)->get();

      //return view('pdf.incident', $data);
      $name = str_slug($data['item']->location_name.'_'.$data['item']->date);
      $pdf = \PDF::loadView('pdf.incident', $data);
      return $pdf->download($name.'.pdf');
  }

  public function getView($id, Request $request)
  {
      $id = decrypt($id);
      $auth_user = \Auth::user();

      $item = Incident::findOrFail($id);

      $photo_ids = explode(',', $item->photo_id);
      $item_photos = File::whereIn('id', $photo_ids)->get();

      $video_ids = explode(',', $item->video_id);
      $item_videos = File::whereIn('id', $video_ids)->get();
      $audio_ids = explode(',', $item->audio_id);
      $item_audios = File::whereIn('id', $audio_ids)->get();

      $item_media = [];//IncidentPhoto::where('incident_id',$id)->whereIn('type', ['audio', 'video'])->get();
      // $title = ucfirst("Edit {$this->title}");
      // Breadcrumb::add($title, route('admin.user.edit', $item->id));

      return view('admin.incident.view', compact('auth_user', 'item', 'item_photos', 'item_media', 'item_audios', 'item_videos'));
  }



  public function getDelete($id)
  {
      Incident::destroy($id);

      $auth_user = Auth::user();
      Activity::log('Deleted incident #'.$id. ' by '.$auth_user->name);

      return redirect()->back()->with([
          'flash_level' => 'danger',
          'flash_message' => 'Deleted',
      ]);
  }

}
