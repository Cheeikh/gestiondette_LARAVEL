<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleRequest extends FormRequest
{
    public function authorize()
    {
        return true;  // Ensure this is adjusted according to your application's authorization logic
    }

    public function rules()
    {
        return [
            'libelle' => 'required|string|unique:articles,libelle',
            'prix' => 'required|numeric|min:0',  // Ensure the price is non-negative
            'qteStock' => 'required|integer|min:0'  // Ensure the quantity in stock is non-negative
        ];
    }

    public function messages()
    {
        return [
            'libelle.required' => 'Le libellé est obligatoire.',
            'libelle.unique' => 'Ce libellé est déjà utilisé. Veuillez en choisir un autre.',
            'prix.required' => 'Le prix est obligatoire.',
            'prix.numeric' => 'Le prix doit être un nombre.',
            'prix.min' => 'Le prix doit être supérieur ou égal à zéro.',
            'qteStock.required' => 'La quantité en stock est obligatoire.',
            'qteStock.integer' => 'La quantité en stock doit être un nombre entier.',
            'qteStock.min' => 'La quantité en stock doit être supérieure ou égale à zéro.'
        ];
    }
}
