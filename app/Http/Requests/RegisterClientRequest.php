<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterClientRequest extends FormRequest
{
    public function authorize()
    {
        return true;  // Autoriser tous les utilisateurs à faire cette requête (peut être ajusté selon les besoins)
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
                'regex:/^[0-9]{9}$/'
            ],
            'adresse' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:1024', // Valide l'image, doit être inférieure à 1 Mo (1024 Ko)
            'user' => 'nullable|array',
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
            'user.role_id' => 'required_with:user|integer|exists:roles,id', // Assure que le rôle est valide
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
            'photo.image' => 'Le fichier doit être une image.',
            'photo.max' => 'L\'image ne doit pas dépasser 1 Mo.',
            'user.nom.required_with' => 'Le champ nom est obligatoire lorsque les informations utilisateur sont fournies.',
            'user.prenom.required_with' => 'Le champ prénom est obligatoire lorsque les informations utilisateur sont fournies.',
            'user.login.required_with' => 'Le champ login est obligatoire lorsque les informations utilisateur sont fournies.',
            'user.login.unique' => 'Ce login est déjà utilisé.',
            'user.password.required_with' => 'Le mot de passe est obligatoire lorsque les informations utilisateur sont fournies.',
            'user.password.min' => 'Le mot de passe doit comporter au moins 8 caractères.',
            'user.password.regex' => 'Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial.',
            'user.role_id.required_with' => 'Le rôle est obligatoire lorsque les informations utilisateur sont fournies.',
            'user.role_id.exists' => 'Le rôle sélectionné est invalide.',
        ];
    }

    
}