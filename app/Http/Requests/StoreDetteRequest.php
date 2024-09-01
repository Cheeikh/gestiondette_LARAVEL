<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDetteRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Assurez-vous que l'utilisateur est authentifié via le middleware
    }

    public function rules()
    {
        return [
            'montant' => 'required|numeric|min:0.01',
            'clientId' => [
                'required',
                'integer',
                Rule::exists('clients', 'id'),
            ],
            'articles' => 'required|array|min:1',
            'articles.*.articleId' => [
                'required',
                'integer',
                Rule::exists('articles', 'id'),
            ],
            'articles.*.qteVente' => 'required|integer|min:1',
            'articles.*.prixVente' => 'required|numeric|min:0.01',
            'paiement' => 'nullable|array',
            'paiement.montant' => 'required_with:paiement|numeric|min:0.01',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->has('paiement') && $this->input('paiement.montant') > $this->input('montant')) {
                $validator->errors()->add('paiement.montant', 'Le montant du paiement ne peut pas dépasser le montant de la dette.');
            }

            if ($this->has('articles')) {
                foreach ($this->input('articles') as $index => $article) {
                    $articleModel = \App\Models\Article::find($article['articleId']);
                    if ($articleModel && $article['qteVente'] > $articleModel->qte_stock) {
                        $validator->errors()->add("articles.$index.qteVente", "La quantité vendue dépasse la quantité en stock pour l'article ID {$article['articleId']}.");
                    }
                }
            }
        });
    }

    public function messages()
    {
        return [
            'montant.required' => 'Le montant de la dette est requis.',
            'clientId.required' => 'Le client est requis.',
            'articles.required' => 'Au moins un article doit être ajouté.',
            // Ajoutez d'autres messages personnalisés si nécessaire
        ];
    }
}
