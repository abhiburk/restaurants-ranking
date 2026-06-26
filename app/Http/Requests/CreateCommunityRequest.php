<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateCommunityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    
    public function rules(): array
    {
        return [
            'city_id' => ['required', 'exists:cities,id'], // check if the city exists and is active
            'motivation' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'city_id.required' => 'City field is required',
        ];
    }
}
