<?php

namespace App\Http\Requests\Restaurant;

use Illuminate\Foundation\Http\FormRequest;

class StoreRestaurantRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'category_id' => ['required', 'exists:restaurant_categories,id'],
            'city_id' => ['required', 'exists:cities,id'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'website_url' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'google_rating' => ['nullable', 'numeric', 'between:0,5'],
            'google_place_id' => ['nullable', 'string', 'max:255'],
            'google_reviews' => ['nullable', 'integer', 'min:0'],
            'google_map_url' => [
                'nullable',
                'url',
                'regex:/https?:\/\/(www\.)?(google\.[a-z]+\/maps|maps\.app\.goo\.gl)\/.*/',
                function ($attribute, $value, $fail) {
                    // Additional validation to ensure it's a restaurant link
                    if (!$this->isValidRestaurantLink($value)) {
                        $fail('The link must be a valid Google Maps restaurant link.');
                    }
                },
            ],
            'is_active' => ['required', 'boolean'],
        ];
    }
    
    public function messages()
    {
        return [
            'google_map_url.regex' => 'Please provide a valid Google Maps link (e.g., https://maps.app.goo.gl/xxx or https://google.com/maps/place/...)',
            'city_id.required' => 'The city field is required.',
            'city_id.exists' => 'The selected city is invalid.',
            'category_id.required' => 'The category field is required.',
            'category_id.exists' => 'The selected category is invalid.',
        ];
    }
    
    /**
     * Custom validation for Google Maps link
     */
    private function isValidRestaurantLink($url): bool
    {
        // Basic check for common patterns
        $patterns = [
            '/\/place\//',
            '/maps\.app\.goo\.gl/',
            '/\?q=[^&]*restaurant/i',
            '/\?q=[^&]*cafe/i',
            '/\?q=[^&]*hotel/i',
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url)) {
                return true;
            }
        }
        
        return false;
    }
}