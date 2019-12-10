<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Auth;

class AddItemRequest extends FormRequest
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
            'name'              => 'required|unique:mysql_2.food_menu',
            'description'        => 'required',
            'tags'     => 'required',
            'course_id'        => 'required',
            'price'        => 'required',
            'type'        => 'required',
            'restaurant_id'        => 'required',
            'image'              => 'required',
            // 'published'        => 'required',
        ];

        if(@$input['type'] == 'package'){
            $data['breakfast'] = 'required';
            $data['lunch'] = 'required';
            $data['dinner'] = 'required';
        }
        return $data;

    }

	public function messages(){
		return [
            'course_id.required' => "The course field is required.",
            'image.required' => "The image field is required.",
            'tags.required' => "The category field is required.",
            'restaurant_id.required' => "The restaurant field is required.",
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
