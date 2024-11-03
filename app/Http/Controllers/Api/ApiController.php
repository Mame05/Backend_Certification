<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\UtilisateurSimple;
use App\Models\Structure;
use Illuminate\Validation\Rule; // Importation correcte de Rule
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
/*public function login(Request $request)
{
    // Validation des données
    /$validator = validator(
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
        "expires_in" => env("JWT_TTL") * 60  . 'seconds'
    ]);
}*/

public function login(Request $request)
    {
        // Validation des données
        $validator = validator($request->all(), [
            'email' => ['required', 'email', 'string'],
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        // Vérifier si l'utilisateur existe
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'message' => 'Utilisateur non trouvé',
            ], 404); // User not found
        }

        // Vérifier si le mot de passe est correct
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Mot de passe incorrect',
            ], 401); // Incorrect password
        }

        // Authentification réussie, générer le token
        $token = auth()->guard('api')->login($user);

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'role_id' => $user->role_id, // Inclure le role_id dans la réponse
            'user' => $user,
            'expires_in' => auth()->guard('api')->factory()->getTTL() * 60, // Expiration en secondes
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

// récupérer le profil utilisateur simple connecté
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

// Récuperer tous les utilisateur simples
public function getAllUtilisateurSimples()
{
    // Récupérer tous les utilisateurs simples avec leurs utilisateurs associés
    $utilisateurs_simples = UtilisateurSimple::with('user')->get();

    // Vérifier si des utilisateurs simples sont trouvés
    if ($utilisateurs_simples->isEmpty()) {
        return response()->json([
            "status" => false,
            "message" => "Aucun utilisateur simple trouvé"
        ], 404);
    }

    // Préparer les données des utilisateurs simples
    $data = $utilisateurs_simples->map(function ($utilisateur_simple) {
        return [
            "email" => $utilisateur_simple->user->email,
            "nom" => $utilisateur_simple->nom,
            "prenom" => $utilisateur_simple->prenom,
            "telephone" => $utilisateur_simple->telephone,
            "adresse" => $utilisateur_simple->adresse,
            "sexe" => $utilisateur_simple->sexe,
            "date_naiss" => $utilisateur_simple->date_naiss,
            "photo" => $utilisateur_simple->photo,
            "profession" => $utilisateur_simple->profession,
            "groupe_sanguin" => $utilisateur_simple->groupe_sanguin,
        ];
    });

    // Retourner la réponse JSON avec les données de tous les utilisateurs simples
    return response()->json([
        "status" => true,
        "message" => "Liste des utilisateurs simples récupérée avec succès",
        "data" => $data
    ]);
}

// Recupérer le structure connecté
public function profileStructure()
{
    // Récupérer la structure connecté
    $user = auth()->user();

    // Récupérer les informations de la structure associé à cet utilisateur
    $structure = Structure::where('user_id', $user->id)->first();

    // Vérifier si la structure existe
    if (!$structure) {
        return response()->json([
            "status" => false,
            "message" => "structure non trouvé"
        ], 404);
    }
    // Retourner les informations de l'utilisateur et de l'utilisateur_simple
    return response()->json([
        "status" => true,
        "message" => "Données de profil récupérées avec succès",
        "data" => [
            "email" => $user->email,
            "password" => $user->password, 
            "nom" => $structure->nom_structure,
            "sigle" => $structure->sigle,
            "telephone" => $structure->telephone,
            "adresse" => $structure->adresse,
            "region" => $structure->region,    
        ]
    ]);
}


// refresher le token d'authentification
public function refreshToken(Request $request){
    $token = auth()->refresh();
    return response()->json([
        "access_token" => $token,
        "token_type" => "bearer",
        "expires_in" => env("JWT_TTL") * 60  .'seconds'
    ]);
}


// Mettre à jour le profil
public function updateProfile(Request $request)
{
    // Récupérer l'utilisateur connecté
    $user = auth()->user();

    // Récupérer les informations de l'utilisateur simple associé à cet utilisateur
    $utilisateur_simple = UtilisateurSimple::where('user_id', $user->id)->first();

    // Vérifier si l'utilisateur existe
    if (!$utilisateur_simple) {
        return response()->json([
            "status" => false,
            "message" => "Utilisateur non trouvé"
        ], 404);
    }

    // Validation des données de la requête
    $validator = validator(
        $request->all(),
        [
            'email' => [
                'required', 
                'string', 
                'email', 
                'max:255', 
                // L'email doit être unique uniquement si c'est un nouvel email
                Rule::unique('users')->ignore($user->id)
            ],
            'password' => ['nullable', 'string', 'min:8'], // Le mot de passe peut être vide
            'nom' => ['required', 'string'],
            'prenom' => ['required', 'string'],
            'telephone' => [
                'required', 
                'string', 
                'regex:/^\d{2}\s?\d{3}\s?\d{2}\s?\d{2}$/',
                // L'unicité n'est vérifiée que si le numéro de téléphone est modifié
                Rule::unique('utilisateur_simples')->ignore($utilisateur_simple->id)
            ],
            'adresse' => ['nullable', 'string'],
            'sexe' => ['nullable', 'string'],
            'date_naiss' => ['nullable', 'date', 'before:2006-01-01'],
            'photo' => ['nullable', 'file', 'image', 'max:2048'],
            'profession' => ['nullable', 'string'],
            'groupe_sanguin' => ['nullable', 'string'],
        ]
    );
    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 422);
    }

    // Mettre à jour les informations de l'utilisateur simple
    $utilisateur_simple->update([
        'nom' => $request->nom,
        'prenom' => $request->prenom,
        'telephone' => $request->telephone,
        'adresse' => $request->adresse,
        'sexe' => $request->sexe,
        'date_naiss' => $request->date_naiss,
        'photo' => $request->hasFile('photo') ? $request->file('photo')->store('photos') : $utilisateur_simple->photo, // Gérer l'upload de la photo
        'profession' => $request->profession,
        'groupe_sanguin' => $request->groupe_sanguin,
    ]);

    return response()->json([
        "status" => true,
        "message" => "Profil mis à jour avec succès",
        "data" => $utilisateur_simple
    ]);

}
}
