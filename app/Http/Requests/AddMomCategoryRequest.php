<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Auth;

class AddMomCategoryRequest extends FormRequest
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
        $language = $this->input('language');
        $inputs = $this->all();
        $this->sanitize();

        $data = [];
        if(@$inputs['title'] == '' && @$inputs['title_bn'] == '' && @$inputs['title_mn'] == '' && @$inputs['title_ta'] == '' && @$inputs['title_th'] == '' ){
            $data = ['title' => 'required|unique:mysql_2.mom_category_locale'];
        }
        $data['path'] = 'required';

        return $data;

    }

	public function messages(){
		return [
            'path.required' => "Image is required.",
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
