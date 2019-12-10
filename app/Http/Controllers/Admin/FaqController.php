<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Models\Faq;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class FaqController extends Controller
{
  public function getList()
  {
      $auth_user = \Auth::user();

      $items = Faq::orderBy('id', 'ASC')->get();

      return view('admin.faq.list', compact('auth_user', 'items'));
  }

  public function getAdd()
  {
      $auth_user = \Auth::user();

      // $user = User::findOrFail($id);
      // $profile = $user->profile;
      // $bank = $user->bank_details;
      // // $title = ucfirst("Edit {$this->title}");
      // Breadcrumb::add($title, route('admin.user.edit', $item->id));
      // $questions = SpecialQuestion::orderBy('sort_order', 'ASC')->get(['id', 'question']);

      return view('admin.faq.add', compact('auth_user'));
  }

  public function postAdd(Request $request)
  {
        $data = $request->input();

        Faq::create($data);

        return redirect()->route('admin.faq.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Succesfully Added.',
        ]);
  }

  public function getEdit($id, Request $request)
  {
      $auth_user = \Auth::user();

      $item = Faq::findOrFail($id);
      // $title = ucfirst("Edit {$this->title}");
      // Breadcrumb::add($title, route('admin.user.edit', $item->id));
      // $questions = SpecialQuestion::orderBy('sort_order', 'ASC')->get(['id', 'question']);

      return view('admin.faq.edit', compact('auth_user', 'item'));
  }

  public function postEdit($id, Request $request)
    {
        $data = $request->input();
        $item = Faq::findOrFail($id);

        $item->update($data);

        return redirect()->route('admin.faq.list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Update success',
        ]);
    }

    public function getDelete($id)
    {
        Faq::destroy($id);

        return redirect()->route('admin.faq.list')->with([
            'flash_level'   => 'danger',
            'flash_message' => 'Deleted',
        ]);
    }

    public function postDelete($id)
    {
        Faq::destroy($id);

        return redirect()->route('admin.faq.list')->with([
            'flash_level'   => 'danger',
            'flash_message' => 'Deleted',
        ]);
    }
}
