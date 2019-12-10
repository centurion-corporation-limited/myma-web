<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Auth;

class AddTopicRequest extends FormRequest
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
        if(@$inputs['title'] == '' && @$inputs['title_bn'] == '' && @$inputs['title_mn'] == '' && @$inputs['title_ta'] == '' && @$inputs['title_th'] == ''){
            return [
                'title'              => 'required',
                // 'title'              => 'required_without_all:title_mn,title_bn,title_ta,title_th',
            ];
        }

        if(@$inputs['description'] == '' && @$inputs['description_bn'] == '' && @$inputs['description_mn'] == '' && @$inputs['description_ta'] == '' && @$inputs['description_th'] == ''){
            return [
                'description'              => 'required',
                // 'title'              => 'required_without_all:title_mn,title_bn,title_ta,title_th',
            ];
        }

        if(@$inputs['language'] == 'bengali' && @$inputs['title_bn'] == ''){
            return [
                'title_bn'              => 'required',
            ];
        }

        if(@$inputs['language'] == 'mandarin' && @$inputs['title_mn'] == ''){
            return [
                'title_mn'              => 'required',
            ];
        }

        if(@$inputs['language'] == 'tamil' && @$inputs['title_ta'] == ''){
            return [
                'title_ta'              => 'required',
            ];
        }

        if(@$inputs['language'] == 'thai' && @$inputs['title_th'] == ''){
            return [
                'title_th'              => 'required',
            ];
        }

        if(@$inputs['language'] == 'bengali' && @$inputs['description_bn'] == ''){
            return [
                'description_bn'              => 'required',
            ];
        }

        if(@$inputs['language'] == 'mandarin' && @$inputs['description_mn'] == ''){
            return [
                'description_mn'              => 'required',
            ];
        }

        if(@$inputs['language'] == 'tamil' && @$inputs['description_ta'] == ''){
            return [
                'description_ta'              => 'required',
            ];
        }

        if(@$inputs['language'] == 'thai' && @$inputs['description_th'] == ''){
            return [
                'description_th'              => 'required',
            ];
        }

        return [];

    }

	public function messages(){
		return [
            'title.required' => "The title field is required.",
            'title_bn.required' => "The title field is required.",
            'title_mn.required' => "The title field is required.",
            'title_ta.required' => "The title field is required.",
			'title_th.required' => "The title field is required.",
            'description.required' => "The description field is required.",
            'description_bn.required' => "The description field is required.",
            'description_mn.required' => "The description field is required.",
            'description_ta.required' => "The description field is required.",
			'description_th.required' => "The description field is required.",
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
