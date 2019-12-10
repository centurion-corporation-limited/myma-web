<?php

namespace App\Http\Controllers\Admin;

use App\Models\Option;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth, Activity;

class SettingsController extends Controller
{
    public function getIndex()
    {
        $title = 'Settings';
        return view('admin.settings', compact('title'));
    }

    public function postIndex(Request $request)
    {
        // foreach ($request->input('options_autoload') as $key => $value) {
        //     if (is_array($value)) {
        //         $value = serialize($value);
        //     }
        //
        //     Option::setOption($key, $value, true);
        // }

        $auth_user = Auth::user();
        $flag = false;
        foreach ($request->input('options') as $key => $value) {
            $flag = true;
            if (is_array($value)) {
                $value = serialize($value);
            }
            Option::setOption($key, $value);
        }
        if($flag){
            Activity::log('Updated general settings by '.$auth_user->name);
        }

        if ($image = $request->file('mrt_map')) {
        		if (!is_dir(public_path('files/uploads'))) {
        			mkdir(public_path('files/uploads'), 755, true);
        		}
                $time = time().'.'.$image->getClientOriginalExtension();
                // $path = $image->storeAs($time, \Auth::id());
        		$image->move(public_path('files/uploads'), $time);//$image->getClientOriginalName());
        		Option::setOption('mrt_map', "files/uploads/{$time}");

                Activity::log('Updated mrt map image by '.$auth_user->name);
        }

        // if ($file = $request->file('flexm_howto_content')) {
        // 		if (!is_dir(public_path('files/uploads'))) {
        // 			mkdir(public_path('files/uploads'), 755, true);
        // 		}
        //     $time = time().'.'.$file->getClientOriginalExtension();
        // 		$file->move(public_path('files/uploads'), $time);
        // 		Option::setOption('flexm_howto_content', static_file("files/uploads/{$time}"));
        // }


        return redirect()->back()->with([
            'flash_level'   => 'success',
            'flash_message' => 'Update success',
        ]);
    }
}
