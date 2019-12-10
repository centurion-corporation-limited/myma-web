<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Auth;

class EditRestraRequest extends FormRequest
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
        return [
            'user_name'           => 'required',
            'name'              => 'required',
            // 'email'           => 'required',
            // 'password'           => 'required',
            'gst_no'           => 'required',
            'nea_number'           => 'required',
            'bank_name'           => 'required',
            'bank_number'       => 'required',
            'phone_no'           => 'required',
            'address'           => 'required',
            'latitude'           => 'required',
            'longitude'           => 'required',
            'open_at'           => 'required',
            'closes_at'           => 'required',
        ];

    }

	public function messages(){
		return [
            'user_name.required' => "The name field is required.",
            'name.required' => "The restaurant name field is required.",
            'email.required' => "The email field is required.",
            'password.required' => "The password field is required.",
            'nea_number.required' => "The nea license field is required.",
            'bank_number.required' => "The bank account number field is required.",

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
