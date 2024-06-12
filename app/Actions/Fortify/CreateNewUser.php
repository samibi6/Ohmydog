<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'tel' =>
            ['required', 'regex:/^((\+|00)32\s?|0)([1-9][0-9]?(\s?[0-9]{2}){4})$|^((\+|00)33\s?|0)([1-9][0-9]?(\s?[0-9]{2}){4})$/'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ], [
            'tel.regex' => 'Le format du numéro de téléphone est invalide.',
            'password.min' => 'Le mot de passe doit contenir au minimu 8 caractères.',
            'password.mixed' => 'Le mot de passe doit contenir au moins une majuscule et une miniscule.',
            'password.letters' => 'Le mot de passe doit contenir au moins une lettre.',
            'password.symbols' => 'le mot de passe doit contenir au moins un symbole.',
            'password.numbers' => 'le mot de passe doit contenir au moins un chiffre.',
            'password.confirm' => 'la confirmation du mot de passe ne correspond pas au mot de passe saisi',
            'name.required' => 'Le champ nom est obligatoire.',
            'name.string' => 'Le champ nom doit être une chaîne de caractères.',
            'name.max' => 'Le champ nom ne peut pas dépasser 255 caractères.',

            'prenom.required' => 'Le champ prenom est obligatoire.',
            'prenom.string' => 'Le champ prenom doit être une chaîne de caractères.',
            'prenom.max' => 'Le champ prenom ne peut pas dépasser 255 caractères.',

            'email.required' => 'Le champ email est obligatoire.',
            'email.string' => 'Le champ email doit être une chaîne de caractères.',
            'email.email' => 'Le champ email doit être une adresse email valide.',
            'email.max' => 'Le champ email ne peut pas dépasser 255 caractères.',
            'email.unique' => 'Cette adresse email est déjà utilisée.',

            'tel.required' => 'Le champ téléphone est obligatoire.',

            'password.required' => 'Le champ mot de passe est obligatoire.',
            'password.string' => 'Le champ mot de passe doit être une chaîne de caractères.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',

        ])->validate();

        return User::create([
            'name' => $input['name'] . ' ' . $input['prenom'],
            'email' => $input['email'],
            'tel' => $input['tel'],
            'role_id' => 2,
            'password' => Hash::make($input['password']),
        ]);
    }
}
