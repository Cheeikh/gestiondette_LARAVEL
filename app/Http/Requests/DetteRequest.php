<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Article;

class DetteRequest extends FormRequest
{
    public function authorize()
    {
        // Vous pouvez implémenter votre logique d'autorisation ici
        return true;
    }

    public function rules()
    {
        return [
            'clientId' => 'required|exists:clients,id',
            'date_echeance' => 'required|date|after_or_equal:today',
            'articles' => 'required|array|min:1',
            'articles.*.articleId' => 'required|exists:articles,id',
            'articles.*.qteVente' => 'required|integer|min:1',
            'articles.*.prixVente' => 'required|numeric|min:0',
            'paiement' => 'sometimes|array',
            'paiement.*.montant' => 'nullable|numeric|min:0',
        ];
    }

    public function messages()
    {
        return [
            'clientId.required' => 'Le client est obligatoire.',
            'clientId.exists' => 'Le client spécifié n\'existe pas.',
            'date_echeance.required' => 'La date d\'échéance est requise.',
            'date_echeance.date' => 'La date d\'échéance doit être une date valide.',
            'date_echeance.after_or_equal' => 'La date d\'échéance doit être aujourd\'hui ou dans le futur.',
            'articles.required' => 'Au moins un article est requis.',
            'articles.*.articleId.required' => 'L\'identifiant de l\'article est requis.',
            'articles.*.articleId.exists' => 'L\'article spécifié n\'existe pas.',
            'articles.*.qteVente.required' => 'La quantité de vente est requise.',
            'articles.*.qteVente.integer' => 'La quantité de vente doit être un nombre entier.',
            'articles.*.qteVente.min' => 'La quantité de vente doit être au moins 1.',
            'articles.*.prixVente.required' => 'Le prix de vente est requis.',
            'articles.*.prixVente.numeric' => 'Le prix de vente doit être un nombre.',
            'articles.*.prixVente.min' => 'Le prix de vente doit être supérieur ou égal à zéro.',
            'paiement.*.montant.numeric' => 'Le montant du paiement doit être un nombre.',
            'paiement.*.montant.min' => 'Le montant du paiement doit être supérieur ou égal à zéro.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $articles = $this->input('articles', []);
            foreach ($articles as $index => $articleData) {
                $articleId = $articleData['articleId'];
                $qteVente = $articleData['qteVente'];

                $article = Article::find($articleId);
                if ($article) {
                    $stockDisponible = $article->qteStock - $article->quantite_seuil;

                    if ($qteVente > $stockDisponible) {
                        $validator->errors()->add(
                            "articles.$index.qteVente",
                            "La quantité de vente pour l'article {$article->libelle} ({$qteVente}) dépasse le stock disponible ({$stockDisponible})."
                        );
                    }
                } else {
                    $validator->errors()->add(
                        "articles.$index.articleId",
                        "L'article avec l'ID {$articleId} n'existe pas."
                    );
                }
            }
        });
    }
}
