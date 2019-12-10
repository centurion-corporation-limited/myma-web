<?php

namespace App\Http\Requests\JTC;

use Illuminate\Foundation\Http\FormRequest;
use Auth;

class AddCenterRequest extends FormRequest
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
        $this->sanitize();

        $inputs = $this->all();

        $return = [];

        if(@$inputs['title'] == '' && @$inputs['title_bn'] == '' && @$inputs['title_mn'] == '' && @$inputs['title_ta'] == '' && @$inputs['title_th'] == ''){
            $return['title'] = 'required';
        }
        $return['path'] = 'required';
        return $return;
    }

	public function messages(){
		return [
        'title.required' => "The title field is required.",
        'title_bn.required' => "The title field is required.",
        'title_mn.required' => "The title field is required.",
        'title_ta.required' => "The title field is required.",
        'title_th.required' => "The title field is required.",
        'path.required' => "The image field is required.",

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
