<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Auth;

class EditAdRequest extends FormRequest
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
        $type = $this->input('type');
        $adv_type = $this->input('adv_type');

        $data = [
            'sponsor_id'              => 'required',
            'type'                    => 'required',
            'report_whom'             => 'required',
            // 'path'                    => 'required',
            // 'adv_type'                => 'required',
            // 'plan_id'                 => 'required',
            // 'link'                    => 'required',
        ];

        if($type == 'home'){
            $data['slider_order'] = 'required';
        }

        if($adv_type == 2){
            // $data['start'] = 'required|date';
            // $data['end'] =   'required|date';
        }
        return $data;

    }

	public function messages(){
        return [
            'sponsor_id.required' => "Sponsor field is required.",
            'report_whom.required' => "Managed by field is required.",
			'path.required' => "You need to upload an advertisement banner.",
            'adv_type.required' => 'Advertisement Type is required.',
            'start.required' => 'Start date field is required.',
            'end.required' => 'End date field is required.',
		];
	}
}
