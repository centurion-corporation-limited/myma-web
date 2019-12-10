<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Auth;

class ShareRequest extends FormRequest
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
        $input = $this->all();
        $data = [
            // 'merchant_name'              => 'required',
            // 'merchant_category_code'     => 'required',
            // 'dormitory_id'               => 'required',
            'myma_transaction_share'     => 'required',
            'merchant_share'             => 'required',
            'frequency'                  => 'required',
            'merchant_address_1'         => 'required',
            'bank_name'                  => 'required|max:35',
            'bank_address'               => 'required|max:70',
            'bank_country'               => 'required|max:2',
            'account_number'             => 'required',
            'product_type'               => 'required',
            'revenue_model'              => 'required',
            //'v_cost_type'                => 'required',
            'gst'                        => 'required',
        ];

        if(@$input['gst'] == 1){
          $data['gst_number'] = 'required';
        }

        return $data;

    }

	public function messages(){
		return [
            'name.required' => "The dormitory name field is required.",
			      'merchant_share.required' => "The profit share field is required.",
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
