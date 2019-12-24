<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Auth;

class AddEmployeeRequest extends FormRequest
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
        $role = $this->input('role');

        $this->sanitize();

        $data = [
            'email'                   => 'nullable|email|max:255',
            'password'                => 'required|min:8|max:20|regex:/^.*(?=.{3,})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[\d\X]).*$/',
            'name'                    => 'required|max:255',
            'role'                    => 'required',
        ];

        if($role == 'app-user'){
            $data['dormitory_id'] = 'required|not_in:0';
            $data['fin_no'] = 'required';
            $data['phone'] = 'required';
            $data['street_address'] = 'required';
            $data['zip_code'] = 'required';
            $data['wp_expiry'] = 'required | date';
            $data['gender'] = 'required';
            $data['dob'] = 'required|date|olderThan:18';
            // $data['work_permit_front'] = 'required_with:fin_no';
            // $data['work_permit_back'] = 'required_with:fin_no';
            //$data['wp_expiry'] = 'date|nullable';
        }else{
            $data['email'] = 'required|email|max:255';
        }

        if($role == 'driver'){
            $data['gender'] = 'required';
            $data['fin_no'] = 'required';
            $data['vehicle_no'] = 'required';
            $data['phone'] = 'required';
            $data['street_address'] = 'required';
        }

        // if($role == "employee"){
        //   $data['nric'] = 'required';
        // }
        return $data;

    }

	public function messages(){
		return [
            // 'emp_id.required' => "The Employee Id field is required.",
            'zip_code.required' => "The postal code field is required.",
            'dob.required' => "The date of birth field is required.",
            'password.required' => "The password must contain a Uppercase letter, a lowercase letter, a digit, a special character and minimum length should be 8.",
            'dob.older_than' => "User should be 18 years old.",
            
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
