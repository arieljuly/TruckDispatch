<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AreaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'area_name' => 'required|string|max:255',
            'required_liters' => 'required|numeric|min:0',
        ];
    }

    public function messages()
    {
        return [
            'area_name.required' => 'Please enter the area name or address.',
            'required_liters.required' => 'Please enter the required liters for this area.',
        ];
    }
}