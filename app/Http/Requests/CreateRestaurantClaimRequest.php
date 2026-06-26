<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateRestaurantClaimRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required',
            'email' => 'required|email|unique:restaurant_claims,email,NULL,id,restaurant_id,'.$this->restaurant->id.',deleted_at,NULL',
            'phone' => 'required|regex:/^\+?[1-9]\d{1,14}$/',
            'notes' => 'nullable',
            'document' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'latitude' => 'required_with:longitude|numeric|between:-90,90',
            'longitude' => 'required_with:latitude|numeric|between:-180,180',
        ];
    }

    public function messages(): array
    {
        return [
            'document.mimes' => 'The document must be a file of type: jpg, jpeg, png, pdf.',
            'document.max' => 'The document may not be greater than 2MB.',
            'email.unique' => 'The email has already been claimed for this restaurant.',
            'phone.regex' => 'The phone number is not valid.',
            'latitude.required_with' => 'Latitude is required when longitude is provided.',
            'longitude.required_with' => 'Longitude is required when latitude is provided.',
            'latitude.between' => 'Latitude must be between -90 and 90.',
            'longitude.between' => 'Longitude must be between -180 and 180.',
         ]; 
    }
}
