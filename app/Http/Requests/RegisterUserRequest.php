<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'login' => 'required|string|unique:users,login|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => [
                'required',
                'string',
                'min:5',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*?&]/',
            ],
            'role_id' => 'required|exists:roles,id|in:1,2',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'nom.required' => 'Le champ nom est obligatoire.',
            'prenom.required' => 'Le champ prénom est obligatoire.',
            'login.required' => 'Le champ login est obligatoire.',
            'login.unique' => 'Ce login est déjà utilisé.',
            'email.required' => 'Le champ email est obligatoire.',
            'email.email' => 'Le format de l\'email est invalide.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit comporter au moins 5 caractères.',
            'password.regex' => 'Le mot de passe doit contenir une majuscule, une minuscule, un chiffre et un caractère spécial.',
            'role_id.required' => 'Le rôle est obligatoire.',
            'role_id.exists' => 'Le rôle sélectionné est invalide.',
            'photo.image' => 'Le fichier doit être une image.',
            'photo.mimes' => 'Le fichier doit être de type jpeg, png, jpg ou gif.',
            'photo.max' => 'La taille de l\'image ne doit pas dépasser 2MB.',
            'role_id.in' => 'Le rôle sélectionné est invalide. Seules les valeurs 1 ou 2 sont acceptées.',
        ];
    }
}
