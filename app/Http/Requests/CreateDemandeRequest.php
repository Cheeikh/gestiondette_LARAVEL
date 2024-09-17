<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateDemandeRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette demande.
     */
    public function authorize()
    {
        return $this->user() && $this->user()->role_id === 3;
    }

    /**
     * Obtient les règles de validation qui s'appliquent à la requête.
     */
    public function rules()
    {
        return [
            'description' => 'sometimes|string',
            'articles' => 'required|array|min:1',
            'articles.*.id' => 'required|exists:articles,id',
            'articles.*.quantity' => 'required|integer|min:1',
            'articles.*.price' => 'required|numeric|min:0',
        ];
    }

    /**
     * Messages d'erreur personnalisés pour les validations.
     */
    public function messages()
    {
        return [
            'articles.*.id.exists' => 'L\'article spécifié n\'existe pas.',
            'articles.*.quantity.required' => 'La quantité est requise pour chaque article.',
            'articles.*.quantity.integer' => 'La quantité doit être un nombre entier.',
            'articles.*.quantity.min' => 'La quantité doit être au moins de 1.',
            'articles.*.price.required' => 'Le prix est requis pour chaque article.',
            'articles.*.price.numeric' => 'Le prix doit être un nombre.',
            'articles.*.price.min' => 'Le prix doit être supérieur ou égal à zéro.',
            'total_amount.max' => 'Le montant total dépasse votre limite autorisée.',
            'total_amount.prohibited' => 'Vous ne pouvez pas créer une demande car vous avez actuellement des dettes en cours.',
        ];
    }

    /**
     * Ajouter des validations supplémentaires après la validation initiale.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $client = $this->user()->client;
            $articles = $this->input('articles', []);
            $totalAmount = 0;

            foreach ($articles as $index => $articleData) {
                $price = $articleData['price'];
                $quantity = $articleData['quantity'];

                // Calculer le montant total
                $totalAmount += $price * $quantity;

                // Vérifier que le prix est positif
                if ($price < 0) {
                    $validator->errors()->add("articles.$index.price", 'Le prix doit être supérieur ou égal à zéro.');
                }

                // Vérifier que la quantité est positive
                if ($quantity < 1) {
                    $validator->errors()->add("articles.$index.quantity", 'La quantité doit être au moins de 1.');
                }
            }

            // Stocker le montant total calculé
            $this->total_amount = $totalAmount;

            // Validations supplémentaires basées sur la catégorie du client
            if ($client && $client->category) {
                if ($client->category->libelle === 'Silver' && $client->max_montant) {
                    $remainingAmount = $client->max_montant - $client->dettes->sum('montant');
                    if ($totalAmount > $remainingAmount) {
                        $validator->errors()->add('total_amount', 'Le montant total dépasse votre limite autorisée.');
                    }
                } elseif ($client->category->libelle === 'Bronze') {
                    $hasDebts = $client->dettes->sum('montant') > 0;
                    if ($hasDebts) {
                        $validator->errors()->add('total_amount', 'Vous ne pouvez pas créer une demande car vous avez actuellement des dettes en cours.');
                    }
                }
            }
        });
    }
}
