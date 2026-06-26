<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateVoteRequest extends FormRequest
{
    
    public function rules(): array
    {
        return [
            'visitor_id' => ['required', 'string', 'max:255'],
            'vote_source' => ['sometimes', 'in:qr,url'],
            'rating' => ['required', 'in:0,1,2,3,4,5'], // 0 = no rating
            'comment' => ['nullable', 'string', 'max:255'],
            'amenities' => ['nullable', 'array'],
            // 'latitude' => 'required_with:longitude|numeric|between:-90,90',
            // 'longitude' => 'required_with:latitude|numeric|between:-180,180',
        ];
    }

    public function messages(): array
    {
        return [
            // 'latitude.required_with' => 'Latitude is required when longitude is provided.',
            // 'longitude.required_with' => 'Longitude is required when latitude is provided.',
            // 'latitude.between' => 'Latitude must be between -90 and 90.',
            // 'longitude.between' => 'Longitude must be between -180 and 180.',
        ];
    }
}
