<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Auth;

class AddCouponRequest extends FormRequest
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
        $type = $this->input('type');

        $data = [
            'code'              => 'required|unique:mysql_2.food_coupons,code',
            // 'merchant_id'       => 'required',
            'type'              => 'required',
            'value'             => 'required',
            'expiry'            => 'required'
        ];

        if($type == 'percent'){
            $data['value'] .= '|min:0|max:100';
        }

        return $data;
    }

	public function messages(){
		return [
			// 'path.required' => "You need to upload an advertisement banner.",
            // 'adv_type.required' => 'Advertisement Type is required.'
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
