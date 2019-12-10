<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Auth;

class NotificationRequest extends FormRequest
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
        // $this->sanitize();
        $type = $this->input('sendto');

        $data = [
            'sendto'              => 'required',
            // 'message'                    => 'required',
        ];

        if($type == 'specific'){
            $data['user_id'] = 'required';
        }

        if($type == 'dormitory'){
            $data['dormitory_id'] = 'required';

        }
        return $data;

    }

	public function messages(){
		return [
            'user_id.required' => "Select atleast one user from the list.",
            'dormitory_id.required' => "Select atleast one dormitory from the list.",

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
