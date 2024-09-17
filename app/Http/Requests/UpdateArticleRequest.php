<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateArticleRequest extends FormRequest
{
    public function authorize()
    {
        // Implémentez votre logique d'autorisation ici
        return true;
    }

    public function rules()
    {
        $articleId = $this->route('id'); // Récupère l'ID de l'article depuis la route

        return [
            'libelle' => 'required|string|unique:articles,libelle,' . $articleId,
            'prix' => 'required|numeric|min:0',
            'qteStock' => 'required|integer|min:0',
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
            'qteStock.min' => 'La quantité en stock doit être supérieure ou égale à zéro.',
        ];
    }
}
