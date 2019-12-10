<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Auth;
use Illuminate\Validation\Rule;

class EditDormRequest extends FormRequest
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
        $id = decrypt($this->id);

        return [
            'name'              => [
                    'required',
                    Rule::unique('mysql_2.dormitory')->ignore($id)
                ],
            'manager_id'        => 'required',
        ];

    }

	public function messages(){
		return [
            'name.required' => "The dormitory name field is required.",
			'manager_id.required' => "The manager field is required.",
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
