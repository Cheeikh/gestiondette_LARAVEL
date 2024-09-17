<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStockRequest extends FormRequest
{
    public function authorize()
    {
        // Implémentez votre logique d'autorisation ici
        return true;
    }

    public function rules()
    {
        return [
            'qteStock' => 'required|integer|min:0',
        ];
    }

    public function messages()
    {
        return [
            'qteStock.required' => 'La quantité en stock est obligatoire.',
            'qteStock.integer' => 'La quantité en stock doit être un nombre entier.',
            'qteStock.min' => 'La quantité en stock doit être supérieure ou égale à zéro.',
        ];
    }
}
