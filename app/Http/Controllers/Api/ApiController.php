<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\UtilisateurSimple;
use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    public function register(Request $request)
    {
     // Validation des données
     $validator = validator(
        $request->all(),
        [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'nom' => ['required', 'string'],
            'prenom' => ['required', 'string'],
            'telephone' => ['required', 'string', 'unique:utilisateur_simples', 'regex:/^\d{2}\s?\d{3}\s?\d{2}\s?\d{2}$/'],
            'adresse' => ['required', 'string'],
            'sexe' => ['required', 'string'],
            'date_naiss' => ['required', 'date', 'before:2006-01-01'],
            'photo' => ['nullable', 'file', 'image', 'max:2048'],
            'profession' => ['required', 'string'],
            'groupe_sanguin' => ['required', 'string'],
        ]
    );

    // Si les données ne sont pas valides, renvoyer les erreurs
    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 422);
    }

    $photo = $request->file('photo');

    // Stocker les fichiers
    $photoPath = $photo ? $photo->store('photos', 'public') : null;

    // Création de l'utilisateur
    $user = User::create([
        'email' => $request->email,
        'password' => bcrypt($request->password),
        'role_id' => 3, // Assigner le rôle d_utilisateur_simple
    ]);

    // Création de l'utilisateur_simple
    $utilisateur_simple = UtilisateurSimple::create([
        'user_id' => $user->id,
        'nom' => $request->nom,
        'prenom' => $request->prenom,
        'telephone' => $request->telephone,
        'adresse' => $request->adresse,
        'sexe' => $request->sexe,
        'date_naiss' => $request->date_naiss,
        'photo' => $photoPath,
        'profession' => $request->profession,
        'groupe_sanguin' => $request->groupe_sanguin,
    ]);

    return response()->json([
        "status" => true,
        "message" => "Utilisateur enregistré avec succès"
    ]);
}


// connexion
public function login(Request $request)
{
    // Validation des données
    $validator = validator(
        $request->all(),
        [
            'email' => 'required|email|string',
            'password' => 'required|string',
        ]
    );
    // Si les données ne sont pas valides, renvoyer les erreurs
    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 422);
    }
    // Si les données sont valides, authentifier l'utilisateur
    $credentials = $request->only('email', 'password');
    $token = auth()->attempt($credentials);
    // Si les informations de connexion ne sont pas correctes, renvoyer une erreur 401  
    if (!$token) {
        return response()->json(['message' => 'Information de connexion incorrectes'], 401);
    }
    // Renvoyer le token d'authentification
    return response()->json([
        "access_token" => $token,
        "token_type" => "bearer",
        "user" => auth()->user(),
        "expires_in" => env("JwT_TTL") * 30  . 'seconds'
    ]);
}

// déconnexion
public function logout(Request $request){
    auth()->logout();
    return response()->json([
        'status' =>true,
       'message' => 'Utilisateur déconnecté'
    ]);
}

// récupérer le profil utilisateur connecté
public function profile()
{
    // Récupérer l'utilisateur connecté
    $user = auth()->user();

    // Récupérer les informations de l'utilisateur simple associé à cet utilisateur
    $utilisateur_simple = UtilisateurSimple::where('user_id', $user->id)->first();

    // Vérifier si l'utilisateur existe
    if (!$utilisateur_simple) {
        return response()->json([
            "status" => false,
            "message" => "utilisateur non trouvé"
        ], 404);
    }

    // Retourner les informations de l'utilisateur et de l'utilisateur_simple
    return response()->json([
        "status" => true,
        "message" => "Données de profil récupérées avec succès",
        "data" => [
            "email" => $user->email,
            "password" => $user->password, 
            "nom" => $utilisateur_simple->nom,
            "prenom" => $utilisateur_simple->prenom,
            "telephone" => $utilisateur_simple->telephone,
            "adresse" => $utilisateur_simple->adresse,
            "sexe" => $utilisateur_simple->sexe,
            "date_naiss" => $utilisateur_simple->date_naiss,
            "photo" => $utilisateur_simple->photo,
            "profession" => $utilisateur_simple->profession,
            "groupe_sanguin" => $utilisateur_simple->groupe_sanguin,
        ]
    ]);
}


// refresher le token d'authentification
public function refreshToken(Request $request){
    $token = auth()->refresh();
    return response()->json([
        "access_token" => $token,
        "token_type" => "bearer",
        "expires_in" => env("JWT_TTL") * 30  .'seconds'
    ]);
}
}
