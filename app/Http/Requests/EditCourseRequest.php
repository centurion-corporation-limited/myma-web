<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Auth;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EditCourseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request)
    {
        $this->sanitize();
        $id = decrypt($this->id);
        $inputs = $this->all();
        $return = [];

        if(@$inputs['title'] == '' && @$inputs['title_bn'] == '' && @$inputs['title_mn'] == '' && @$inputs['title_ta'] == '' && @$inputs['title_th'] == ''){
            $return['title'] = 'required';
        }

        if(@$inputs['about'] == '' && @$inputs['about_bn'] == '' && @$inputs['about_mn'] == '' && @$inputs['about_ta'] == '' && @$inputs['about_th'] == ''){
            $return['about'] = 'required';
        }

        if(@$inputs['description'] == '' && @$inputs['description_bn'] == '' && @$inputs['description_mn'] == '' && @$inputs['description_ta'] == '' && @$inputs['description_th'] == ''){
            $return['description'] = 'required';
        }

        if(@$inputs['type'] == 'paid'){
            $return['fee'] = 'required';
        }

        $return['start_date'] = 'required';
        $return['end_date'] = 'required';
        $return['duration'] = 'required';

        if(@$inputs['help_text'] == '' && @$inputs['help_text_bn'] == '' && @$inputs['help_text_mn'] == '' && @$inputs['help_text_ta'] == '' && @$inputs['help_text_th'] == ''){
            $return['help_text'] = 'required';
        }

        if(@$inputs['have_image'] != '1'){
            $return['path'] = 'required';
        }

        return $return;
        // return [
        //         'title'      => [
        //                 'required',
        //                 Rule::unique('mysql_2.course')->ignore($id),
        //             ],
        //         'about'   => 'required',
        //         'description'   => 'required',
        //         // 'fee'   => 'required',
        //         'duration' => 'required',
        //         'type' => 'required',
        //         'start_date' => 'required',
        //         'end_date' => 'required',
        //         // 'duration_breakage' => 'required',
        //         'help_text'   => 'required',
        // ];

    }

	public function messages(){
        return [
            'title.required' => "The title field is required.",
            'title_bn.required' => "The title field is required.",
            'title_mn.required' => "The title field is required.",
            'title_ta.required' => "The title field is required.",
			'title_th.required' => "The title field is required.",
            'about.required' => "The about field is required.",
            'about_bn.required' => "The about field is required.",
            'about_mn.required' => "The about field is required.",
            'about_ta.required' => "The about field is required.",
			'about_th.required' => "The about field is required.",
            'description.required' => "The description field is required.",
            'description_bn.required' => "The description field is required.",
            'description_mn.required' => "The description field is required.",
            'description_ta.required' => "The description field is required.",
			'description_th.required' => "The description field is required.",
            'help_text.required' => "The help text field is required.",
            'help_text_bn.required' => "The help text field is required.",
            'help_text_mn.required' => "The help text field is required.",
            'help_text_ta.required' => "The help text field is required.",
            'help_text_th.required' => "The help text field is required.",
			'path.required' => "Banner image field is required.",
		];
	}

    public function sanitize()
    {
        $inputs = $this->all();
        $sanitized = [];
        foreach($inputs as $key => $input){
          if(is_array($input)){
            foreach($input as $ke => $skill){
                $sanitized[$key][$ke] = filter_var($skill,FILTER_SANITIZE_STRING);
            }
          }else{
            $sanitized[$key] = filter_var($input,FILTER_SANITIZE_STRING);
          }
        }

        $this->replace($sanitized);
    }
}
