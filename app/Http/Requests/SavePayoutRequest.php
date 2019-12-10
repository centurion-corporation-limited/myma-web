<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Auth;

class SavePayoutRequest extends FormRequest
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

        $data = [
            'transaction_id'    => 'required|unique:mysql_2.payout',
            'value_date'        => 'required',
            'remarks'           => 'required'
        ];
        return $data;

    }

	// public function messages(){
	// 	return [
  //           'course_id.required' => "The course field is required.",
  //           'path.required' => "The image field is required.",
  //           'tags.required' => "The category field is required.",
  //           'restaurant_id.required' => "The restaurant field is required.",
	// 	];
	// }

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
