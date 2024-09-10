<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DetteRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'clientId' => 'required|exists:clients,id',
            'articles' => 'required|array|min:1',
            'articles.*.articleId' => 'required|exists:articles,id',
            'articles.*.qteVente' => 'required|integer|min:1',
            'articles.*.prixVente' => 'required|numeric|min:0',
            'paiement' => 'sometimes|array',
            'paiement.montant' => 'nullable|numeric'
        ];
    }


    public function messages()
    {
        return [
            'clientId.required' => 'Le client est obligatoire.',
            'clientId.exists' => 'Le client spécifié n\'existe pas.',
            'articles.required' => 'Au moins un article est requis.',
            'articles.*.articleId.exists' => 'L\'article spécifié n\'existe pas.',
            'articles.*.qteVente.required' => 'La quantité de vente est requise.'
        ];
    }
}
