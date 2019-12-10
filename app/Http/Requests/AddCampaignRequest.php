<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Auth;

class AddCampaignRequest extends FormRequest
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
            'title'                   => 'required|max:255',
            'company_name'            => 'required|max:255',
            //'photo'                 => 'required',
            'category_id'             => 'required',
            'campaign_type_sponsor'   => 'required_without:campaign_type_renum',
            'campaign_type_renum'     => 'required_without:campaign_type_sponsor',
            'close_date'              => 'required',
            'close_time'              => 'required',
            'start_date'              => 'required',
            'start_time'              => 'required',
            'end_date'                => 'required',
            'end_time'                => 'required',
            'objective'               => 'required',
            'instagram_tag'           => '',
            'hashtag'                 => '',
            'geotag'                  => '',

        ];

    }
	
	public function messages(){
		return [
			'campaign_type_sponsor.required_without' => "You need to select at least one of the campaign type to proceed.",
			'campaign_type_renum.required_without' => "You need to select at least one of the campaign type to proceed."
		];
	}
}
