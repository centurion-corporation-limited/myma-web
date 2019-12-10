<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Auth;
use Illuminate\Validation\Rule;

class EditPageRequest extends FormRequest
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
        $this->sanitize();
        $inputs = $this->all();
        $return = [];

        if(@$inputs['title'] == '' && @$inputs['title_bn'] == '' && @$inputs['title_mn'] == '' && @$inputs['title_ta'] == '' && @$inputs['title_th'] == ''){
            $return['title'] = ['required', Rule::unique('mysql_2.pages')->ignore($id)];
        }

        if(@$inputs['content'] == '' && @$inputs['content_bn'] == '' && @$inputs['content_mn'] == '' && @$inputs['content_ta'] == '' && @$inputs['content_th'] == ''){
            $return['content'] = 'required';
        }

        return $return;

        // return [
        //         'title'              => [
        //             'required',
        //              Rule::unique('mysql_2.pages')->ignore($id),
        //         ],
        //         'content'                    => 'required',
        // ];

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
