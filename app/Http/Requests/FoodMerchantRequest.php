<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Auth;

class FoodMerchantRequest extends FormRequest
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
        $catering = $this->input('catering');

        if($catering == "1"){
          return [
              'naanstap_share'          => 'required',
              'sub_limit'               => 'required',
              'per_sub_price'           => 'required',
              'start_date'              => 'required',
              'frequency'               => 'required',
          ];
        }else{
            return [
                'naanstap_share'          => 'required',
                'start_date'              => 'required',
                'frequency'               => 'required',
            ];
        }


    }

	public function messages(){
		return [
            'sub_limit.required' => "The subscriptino limit field is required.",
			      'per_sub_price.required' => "The per subscriptino price field is required.",
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
