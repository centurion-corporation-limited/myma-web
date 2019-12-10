<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Auth;

class AddMomTopicRequest extends FormRequest
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

        $data = [
            'category_id' => 'required',
            'language'    => 'required',
        ];
        if(@$inputs['title'] == '' && @$inputs['title_bn'] == '' && @$inputs['title_mn'] == '' && @$inputs['title_ta'] == '' && @$inputs['title_th'] == '' ){
            $data['title'] = 'required|unique:mysql_2.mom_topic_locale';
        }

        $data['path'] = 'required';
        $data['type'] = 'required';

        if(@$inputs['content'] == '' && @$inputs['content_bn'] == '' && @$inputs['content_mn'] == '' && @$inputs['content_ta'] == '' && @$inputs['content_th'] == '' ){
            $data['content'] = 'required';
        }

        return $data;
    }

	public function messages(){
		return [
            'category_id.required' => "The Category field is required.",
            'type.required' => "The content type field is required.",
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
