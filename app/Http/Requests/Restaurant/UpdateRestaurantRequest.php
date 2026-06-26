<?php

namespace App\Http\Requests\Restaurant;

class UpdateRestaurantRequest extends StoreRestaurantRequest
{
    public function authorize()
    {
        return true;
    }
    
    public function rules()
    {
        return parent::rules();
    }
}