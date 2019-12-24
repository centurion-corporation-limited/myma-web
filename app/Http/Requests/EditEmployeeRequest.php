<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Auth;
use Illuminate\Validation\Rule;
use App\Models\UserProfile;

class EditEmployeeRequest extends FormRequest
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
        $role = $this->input('role');
        $profile = UserProfile::where('user_id', $id)->first();
        $this->sanitize();

        $data = [
            'email'                  => [
                    'email',
                    'max:255',
                    //Rule::unique('users')->ignore($id),
                    'nullable'
                ],
            'name'                    => 'required|max:255',
            'role'                    => 'required',
            'blocked'                 => 'required',
        ];

        if($this->input('password') != ''){
            $data['password'] = 'min:8|max:20|regex:/^.*(?=.{3,})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[\d\X]).*$/';
        }

        if($role == 3){
            $data['dormitory_id'] = 'required|not_in:0';
            $data['fin_no'] = 'required';
            $data['phone'] = 'required';
            $data['street_address'] = 'required';
            $data['zip_code'] = 'required';
            $data['wp_expiry'] = 'required | date';
            $data['gender'] = 'required';
            $data['dob'] = 'required|date|olderThan:18';

            if($profile && $profile->wp_front != ''){

            }else{
                $data['wp_front'] = 'required_with:fin_no';
            }
            if($profile && $profile->wp_back != ''){

            }else{
                $data['wp_back'] = 'required_with:fin_no';
            }
        }else{
            $data['email'] = [
                    'email',
                    'max:255',
                    //Rule::unique('users')->ignore($id),
                    'required'
                ];
        }

        if($role == 6){
            $data['gender'] = 'required';
            $data['fin_no'] = 'required';
            $data['vehicle_no'] = 'required';
            $data['phone'] = 'required';
            $data['street_address'] = 'required';
        }

        return $data;
    }

	public function messages(){
		return [
            'zip_code.required' => "The postal code field is required.",
            'dob.required' => "The date of birth field is required.",
			'emp_id.required' => "The Employee Id field is required.",
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
