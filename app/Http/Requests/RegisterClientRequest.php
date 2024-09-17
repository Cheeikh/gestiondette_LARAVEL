<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterClientRequest extends FormRequest
{
    public function authorize()
    {
        // Vous pouvez implémenter votre logique d'autorisation ici
        return true;
    }

    public function rules()
    {
        return [
            'surname' => 'required|string|max:255|unique:clients,surname',
            'telephone' => [
                'required',
                'string',
                'max:15',
                'unique:clients,telephone',
                // 'regex:/^[0-9]{9}$/', // Décommentez et ajustez si nécessaire
            ],
            'adresse' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:clients,email',
            'category_id' => 'required|exists:categories,id',
            'max_montant' => [
                'required_if:category_id,2',
                'prohibited_unless:category_id,2',
                'numeric',
            ],
            'user' => 'sometimes|array',
            'user.nom' => 'required_with:user|string|max:255',
            'user.prenom' => 'required_with:user|string|max:255',
            'user.login' => 'required_with:user|string|unique:users,login|max:255',
            'user.password' => [
                'required_with:user',
                'string',
                'min:8', // Augmenté à 8 caractères pour plus de sécurité
                'regex:/[A-Z]/', // Doit contenir au moins une majuscule
                'regex:/[a-z]/', // Doit contenir au moins une minuscule
                'regex:/[0-9]/', // Doit contenir au moins un chiffre
                'regex:/[@$!%*?&]/', // Doit contenir au moins un caractère spécial
            ],
            'user.role_id' => 'required_with:user|integer|exists:roles,id',
            'user.photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'surname.required' => 'Le champ nom est obligatoire.',
            'surname.unique' => 'Ce nom est déjà utilisé. Veuillez en choisir un autre.',
            'telephone.required' => 'Le numéro de téléphone est obligatoire.',
            'telephone.unique' => 'Ce numéro de téléphone est déjà utilisé.',
            'telephone.regex' => 'Le numéro de téléphone doit être au format 0600000000.',
            'adresse.max' => 'L\'adresse ne peut pas dépasser 255 caractères.',
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'category_id.required' => 'Le champ catégorie est obligatoire.',
            'category_id.exists' => 'La catégorie sélectionnée est invalide.',
            'max_montant.required_if' => 'Le champ max_montant est obligatoire pour la catégorie Silver.',
            'max_montant.prohibited_unless' => 'Le champ max_montant n\'est pas autorisé pour cette catégorie.',
            'max_montant.numeric' => 'Le champ max_montant doit être un nombre.',
            'user.nom.required_with' => 'Le champ nom est obligatoire lorsque les informations utilisateur sont fournies.',
            'user.prenom.required_with' => 'Le champ prénom est obligatoire lorsque les informations utilisateur sont fournies.',
            'user.login.required_with' => 'Le champ login est obligatoire lorsque les informations utilisateur sont fournies.',
            'user.login.unique' => 'Ce login est déjà utilisé.',
            'user.password.required_with' => 'Le mot de passe est obligatoire lorsque les informations utilisateur sont fournies.',
            'user.password.min' => 'Le mot de passe doit comporter au moins 8 caractères.',
            'user.password.regex' => 'Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial.',
            'user.role_id.required_with' => 'Le rôle est obligatoire lorsque les informations utilisateur sont fournies.',
            'user.role_id.exists' => 'Le rôle sélectionné est invalide.',
            'user.photo.image' => 'Le fichier doit être une image.',
            'user.photo.max' => 'L\'image ne doit pas dépasser 2 Mo.',
        ];
    }
}
