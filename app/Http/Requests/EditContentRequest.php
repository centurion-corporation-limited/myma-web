<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Auth;
use Illuminate\Validation\Rule;

class EditContentRequest extends FormRequest
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
    public function rules()
    {
        // dd($this->id);
        // $id = decrypt($this->id);
        $this->sanitize();

        $inputs = $this->all();

        $return = [];

        if(@$inputs['title'] == '' && @$inputs['title_bn'] == '' && @$inputs['title_mn'] == '' && @$inputs['title_ta'] == '' && @$inputs['title_th'] == ''){
            $return['title'] = 'required';
        }
        
        if(@$inputs['course_id'] == '' && @$inputs['course_id_bn'] == '' && @$inputs['course_id_mn'] == '' && @$inputs['course_id_ta'] == '' && @$inputs['course_id_th'] == ''){
            $return['course_id'] = 'required';
        }

        return $return;

    }

	public function messages(){
		return [
            'title.required' => "The title field is required.",
            'title_bn.required' => "The title field is required.",
            'title_mn.required' => "The title field is required.",
            'title_ta.required' => "The title field is required.",
			'title_th.required' => "The title field is required.",
            'course_id.required' => "The course field is required.",
            'course_id_bn.required' => "The course field is required.",
            'course_id_mn.required' => "The course field is required.",
            'course_id_ta.required' => "The course field is required.",
			'course_id_th.required' => "The course field is required.",
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
