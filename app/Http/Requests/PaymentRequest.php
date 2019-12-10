<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Auth;

class PaymentRequest extends FormRequest
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
        return [
            'deliver_type'             => 'required',
            'delivery_date'            => 'required',
            'phone_no'                 => 'required',
        ];

    }

	public function messages(){
		return [
			// 'campaign_type_sponsor.required_without' => "You need to select at least one of the campaign type to proceed.",
			// 'campaign_type_renum.required_without' => "You need to select at least one of the campaign type to proceed."
		];
	}
}
