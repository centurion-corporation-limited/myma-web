<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Auth;

class ProfileRequest extends FormRequest
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
        $user = Auth::user();

        if($user->hasRole('influencer')){
          return [
            'full_name'         => 'required|max:255',
            'instagram_name'    => 'required',
            'contact'           => 'required|numeric',
            'address'           => 'required',
            'postal_code'       => 'required',
            'bank_name'         => '',
            //'account_no'        => 'numeric',

          ];
        }elseif($user->hasRole('brand_manager')){
          return [
            'company_name'      => 'required|max:255',
            'full_name'         => 'required|max:255',
            'contact'           => 'required|numeric|min:10',
            'website_url'       => 'required',
            'company_desc'      => 'required',

          ];

        }
         return [];
    }
}
