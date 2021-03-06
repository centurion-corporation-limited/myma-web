<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Auth;
use Illuminate\Validation\Rule;

class EditServiceRequest extends FormRequest
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
        $id = decrypt($this->id);
        $inputs = $this->all();
        // $this->sanitize();

        $data = [];
        if(@$inputs['title'] == '' && @$inputs['title_bn'] == '' && @$inputs['title_mn'] == '' && @$inputs['title_ta'] == '' && @$inputs['title_th'] == '' ){
            $data['title'] = 'required';
        }

        if(@$inputs['content'] == '' && @$inputs['content_bn'] == '' && @$inputs['content_mn'] == '' && @$inputs['content_ta'] == '' && @$inputs['content_th'] == '' ){
            $data['content'] = 'required';
        }

        if(@$inputs['author'] == '' && @$inputs['author_bn'] == '' && @$inputs['author_mn'] == '' && @$inputs['author_ta'] == '' && @$inputs['author_th'] == '' ){
            $data['author'] = 'required';
        }

        $data['publish'] = 'required';

        return $data;

    }

	public function messages(){
		return [
			// 'path.required' => "You need to upload an advertisement banner.",
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
