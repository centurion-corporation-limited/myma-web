<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Auth;
use Illuminate\Validation\Rule;

class EditShareRequest extends FormRequest
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
        $this->sanitize();

        return [
            'flexm_part'            => 'required',
            'myma_part'             => 'required',
            'myma_share'            => 'required',
            'other_share'           => 'required',
            'status'                => 'required',

        ];

    }

	public function messages(){
		return [
            'flexm_part.required' => "Flexm share in transaction charges is a required field.",
            'myma_part.required' => "Myma share in transaction charges is a required field.",
            'myma_share.required' => "Myma share in item share is a required field.",
            'other_share.required' => "Merchant share in item share is a required field.",
			      'status.required' => "Status is a required field.",
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
