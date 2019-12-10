<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Auth;

class AddPageRequest extends FormRequest
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
            // dd($inputs);
            $return['title'] = 'required|unique:mysql_2.pages';
        }

        if(@$inputs['content'] == '' && @$inputs['content_bn'] == '' && @$inputs['content_mn'] == '' && @$inputs['content_ta'] == '' && @$inputs['content_th'] == ''){
            $return['content'] = 'required';
        }

        return $return;
        // return [
        //     'title'              => 'required|unique:mysql_2.pages',
        //     'content'           => 'required',
        // ];

    }

	public function messages(){
		return [
            'title.required' => "The title field is required.",
			'content.required' => "The content field is required.",
		];
	}

    public function sanitize()
    {
        $inputs = $this->all();
        $sanitized = [];
        foreach($inputs as $key => $input){
          if($key == 'content'){
              $sanitized[$key] = $input;
              continue;
          }

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
