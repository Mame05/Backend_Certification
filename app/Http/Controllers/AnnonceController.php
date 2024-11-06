<?php

namespace App\Http\Controllers;

use Log;
use App\Models\User;
use App\Models\Annonce;
use App\Models\Structure;
use App\Models\Notification1;
use App\Models\UtilisateurSimple;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreAnnonceRequest;
use App\Http\Requests\UpdateAnnonceRequest;
use App\Notifications\AnnonceModifiedNotification;
use App\Notifications\AnnoncePublishedNotification;

class AnnonceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Récupérer l'utilisateur authentifié
        $user = auth()->user();
       // Vérifier si l'utilisateur a le rôle de structure (role_id = 2)
    if ($user->role_id == 2) {
        // Récupérer la structure de cet utilisateur
        $structure = Structure::where('user_id', $user->id)->first();

        // Si la structure existe, récupérer ses annonces
        if ($structure) {
            $annonces = Annonce::where('structure_id', $structure->id)->get()->map(function ($annonce) {
            // Compter le nombre d'inscrits
            $nombreInscrits = $annonce->rendezVous()->count();
             // Ajouter le nombre d'inscrits à l'annonce
             $annonce->nombre_inscrits = $nombreInscrits;
             return $annonce;
            });
            // Retourner les annonces au format JSON
            return response()->json($annonces);
        } else {
            return response()->json(['message' => 'Structure non trouvée'], 404);
        }
    }
     // Vérifier si l'utilisateur a le rôle d'admin ou d'utilisateur simple
     elseif ($user->role_id == 1 || $user->role_id == 3 ) { 
        // Récupérer toutes les annonces
        $annonces = Annonce::all();
        return response()->json($annonces);
    }  
    else {
        // Si l'utilisateur n'a pas les droits, renvoyer une réponse d'accès refusé
        return response()->json(['message' => 'Accès non autorisé'], 403);
    }
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAnnonceRequest $request)
    {
        // Obtenez l'utilisateur authentifié
    $user = auth()->user();
    // Vérifiez si l'utilisateur a le rôle approprié (role_id = 2)
    if ($user->role_id !== 2) {
        return response()->json(['error' => 'Vous n\'avez pas l\'autorisation de publier une annonce.'], 403);
    }

    
    // Valider les données de l'annonce
    $validator = validator(
    $request->all(),
    [
        'titre' => ['required', 'string', 'max:255'],
        'type_annonce' => ['required', 'in:collecte,besoin_urgence'],
        'nom_lieu' => ['required', 'string', 'max:255'],
        'adresse_lieu' => ['required', 'string', 'max:255'],
        'date_debut' => ['required', 'date'],
        'date_fin' => ['nullable', 'date'],
        'heure_debut' => ['required', 'date_format:H:i'],
        'heure_fin' => ['nullable', 'date_format:H:i'],
        'groupe_sanguin_requis' => ['required_if:type_annonce,besoin_urgence', 'string', 'max:30'],
        'nombre_poches_vise' => ['nullable', 'integer'],
        'description' => ['required', 'string'],
        'contact_responsable' => ['required', 'string',  'regex:/^\d{2}\s?\d{3}\s?\d{2}\s?\d{2}$/'],
    ]
);
// Si les données ne sont pas valides, renvoyer les erreurs
if ($validator->fails()) {
    return response()->json(['error' => $validator->errors()], 422);
}

     // Récupérez la structure associée à l'utilisateur
     $structure = Structure::where('user_id', $user->id)->first(); // Récupérer la structure par l'user_id

     // Vérifiez si la structure existe
     if (!$structure) {
         return response()->json(['error' => 'Aucune structure associée à cet utilisateur.'], 404);
     }

    // Créer l'annonce
    $annonce = new Annonce();
    $annonce->structure_id = $structure->id; // Utiliser l'id de la structure
    $annonce->titre = $request->titre;
    $annonce->type_annonce = $request->type_annonce;
    $annonce->nom_lieu = $request->nom_lieu;
    $annonce->adresse_lieu = $request->adresse_lieu;
    $annonce->date_debut = $request->date_debut;
    $annonce->date_fin = $request->date_fin;
    $annonce->heure_debut = $request->heure_debut;
    $annonce->heure_fin = $request->heure_fin;
    $annonce->groupe_sanguin_requis = $request->groupe_sanguin_requis;
    $annonce->nombre_poches_vise = $request->nombre_poches_vise;
    $annonce->description = $request->description;
    $annonce->contact_responsable = $request->contact_responsable;
    $annonce->save();

    // Envoyer des notifications en fonction du type d'annonce
if ($annonce->type_annonce == 'collecte') {
    // Notification à tous les utilisateurs simples
    $utilisateursSimples = UtilisateurSimple::whereHas('user', function($query) {
        $query->where('role_id', 3);  // Assurez-vous que l'utilisateur a le role_id = 3
    })->get(); // Récupérer les utilisateurs simples sous forme de collection Eloquent
} elseif ($annonce->type_annonce == 'besoin_urgence') {
    // Notification seulement aux utilisateurs dont le groupe sanguin correspond à celui requis
    $utilisateursSimples = UtilisateurSimple::whereHas('user', function($query) {
        $query->where('role_id', 3);  // Utilisateurs ayant le role_id = 3
    })
    ->where('groupe_sanguin', $annonce->groupe_sanguin_requis)  // Comparer avec le groupe sanguin requis
    ->get();  // Récupérer les utilisateurs simples sous forme de collection Eloquent
}

// Envoyer les notifications aux utilisateurs sélectionnés
foreach ($utilisateursSimples as $utilisateur_simple) {

    // Enregistrement de la notification dans notification1s
    $notification = new Notification1();
    $notification->contenu = "Nouvelle annonce : {$annonce->titre}";
    $notification->annonce_id = $annonce->id;
    $notification->user_id = $utilisateur_simple->user_id; 
    $notification->statut = 'non-lu'; 
    $notification->save();
    
}
    return response()->json([
        'status' => true,
        'message' => 'Annonce publiée et notifications envoyées.',
        'data' => $annonce
    ]);
}
    /**
     * Display the specified resource.
     */
   /* public function show(Annonce $annonce)
    {
         // Obtenir l'utilisateur authentifié
    $user = auth()->user();

    // Vérifier si l'utilisateur a le rôle de structure
    if ($user->role_id !== 2) {
        return response()->json(['message' => 'Accès non autorisé'], 403);
    }

    // Vérifier si l'utilisateur est associé à la structure de l'annonce
    $structure = Structure::where('user_id', $user->id)->first();
    if (!$structure || $structure->id !== $annonce->structure_id) {
        return response()->json(['message' => 'Accès non autorisé'], 403);
    }
        // Compte le nombre d'inscrits pour cette annonce
    $nombreInscrits = $annonce->rendezVous()->count(); // Utilise la relation définie

     // Récupérer les informations des utilisateurs inscrits et l'ID des rendez-vous
     $inscrits = $annonce->rendezVous()->with('utilisateurSimple')->get()->map(function($rendezVous) {
        return [
            'id' => $rendezVous->id, // ID du rendez-vous
            //'id' => $rendezVous->utilisateurSimple->id,
            'utilisateur_id' => $rendezVous->utilisateurSimple->id, // ID de l'utilisateur
            'nom' => $rendezVous->utilisateurSimple->nom,
            'prenom' => $rendezVous->utilisateurSimple->prenom,
            'etat' => $rendezVous->etat, // État du rendez-vous
        ];
    });
        return response()->json([
            'titre' => $annonce->titre,
            'type_annonce' => $annonce->type_annonce,
            'nom_lieu' => $annonce->nom_lieu,
            'adresse_lieu' => $annonce->adresse_lieu,
            'date_debut' => $annonce->date_debut,
            'date_fin' => $annonce->date_fin,
            'heure_debut' => $annonce->heure_debut,
            'heure_fin' => $annonce->heure_fin,
            'groupe_sanguin_requis' => $annonce->groupe_sanguin_requis,
            'nombre_poches_vise' => $annonce->nombre_poches_vise,
            'description' => $annonce->description,
            'contact_responsable' => $annonce->contact_responsable,
            'structure' => $annonce->structure,
            'nombre_inscrits' => $nombreInscrits, // Ajoute le nombre d'inscrits ici
            'inscrits' => $inscrits, // Informations sur les inscrits
        ]);
    }*/
    public function show(Annonce $annonce)
{
    // Obtenir l'utilisateur authentifié
    $user = auth()->user();

    // Récupérer les informations générales de l'annonce
    $annonceDetails = [
        'titre' => $annonce->titre,
        'type_annonce' => $annonce->type_annonce,
        'nom_lieu' => $annonce->nom_lieu,
        'adresse_lieu' => $annonce->adresse_lieu,
        'date_debut' => $annonce->date_debut,
        'date_fin' => $annonce->date_fin,
        'heure_debut' => $annonce->heure_debut,
        'heure_fin' => $annonce->heure_fin,
        'groupe_sanguin_requis' => $annonce->groupe_sanguin_requis,
        'nombre_poches_vise' => $annonce->nombre_poches_vise,
        'description' => $annonce->description,
        'contact_responsable' => $annonce->contact_responsable,
        'structure' => $annonce->structure,
    ];

    // Si l'utilisateur est authentifié
    if ($user) {
        // Si l'utilisateur est un administrateur (role_id = 1), il peut voir tous les détails
        if ($user->role_id === 1) {
            return response()->json($annonceDetails);
        }

        // Si l'utilisateur est une structure (role_id = 2), vérifier s'il est lié à cette annonce
        if ($user->role_id === 2) {
            $structure = Structure::where('user_id', $user->id)->first();
            if (!$structure || $structure->id !== $annonce->structure_id) {
                return response()->json(['message' => 'Accès non autorisé'], 403);
            }

            // Compter le nombre d'inscrits
            $nombreInscrits = $annonce->rendezVous()->count();

            // Récupérer les informations des utilisateurs inscrits
            $inscrits = $annonce->rendezVous()->with('utilisateurSimple')->get()->map(function($rendezVous) {
                return [
                    'id' => $rendezVous->id, // ID du rendez-vous
                    'utilisateur_id' => $rendezVous->utilisateurSimple->id, // ID de l'utilisateur
                    'nom' => $rendezVous->utilisateurSimple->nom,
                    'prenom' => $rendezVous->utilisateurSimple->prenom,
                    'etat' => $rendezVous->etat, // État du rendez-vous
                ];
            });

            // Ajouter le nombre d'inscrits et la liste des inscrits aux détails de l'annonce
            $annonceDetails['nombre_inscrits'] = $nombreInscrits;
            $annonceDetails['inscrits'] = $inscrits;

            return response()->json($annonceDetails);
        }

        // Si l'utilisateur est un utilisateur simple (role_id = 3), il peut voir l'annonce sans certaines informations sensibles
        if ($user->role_id === 3) {
             // Compter le nombre d'inscrits
             $nombreInscrits = $annonce->rendezVous()->count();
             $annonceDetails['nombre_inscrits'] = $nombreInscrits;
            return response()->json($annonceDetails);

        }
    }

    // Pour les utilisateurs non authentifiés ou pour les rôles non gérés, retourner une réponse par défaut
    return response()->json(['message' => 'Accès non autorisé ou utilisateur non authentifié'], 403);
}


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Annonce $annonce)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAnnonceRequest $request, Annonce $annonce)
    {
         // Obtenez l'utilisateur authentifié
        $user = auth()->user();

    // Vérifiez si l'utilisateur a le rôle approprié (role_id = 2)
    if ($user->role_id !== 2) {
        return response()->json([
            'status' => false,
            'message' => 'Vous n\'avez pas l\'autorisation de modifier cette annonce.'
        ], 403);
    }
    // Récupérez la structure associée à l'utilisateur
    $structure = Structure::where('user_id', $user->id)->first(); // Récupérer la structure par l'user_id

    // Vérifiez si la structure existe
    if (!$structure) {
        return response()->json(['error' => 'Aucune structure associée à cet utilisateur.'], 404);
    }

    // Vérifiez si l'annonce appartient à l'utilisateur authentifié
    if ($annonce->structure_id !== $structure->id) {
        return response()->json([
            'status' => false,
            'message' => 'Vous ne pouvez modifier que vos propres annonces.'
        ], 403);
    }
         // Valider les données de l'annonce
    $validator = validator(
        $request->all(),
        [
            'titre' => ['required', 'string', 'max:255'],
            'type_annonce' => ['required', 'in:collecte,besoin_urgence'],
            'nom_lieu' => ['required', 'string', 'max:255'],
            'adresse_lieu' => ['required', 'string', 'max:255'],
            'date_debut' => ['required', 'date'],
            'date_fin' => ['required', 'date'],
            'heure_debut' => ['required', 'date_format:H:i'],
            'heure_fin' => ['required', 'date_format:H:i'],
            'groupe_sanguin_requis' => ['required_if:type_annonce,besoin_urgence', 'string', 'max:30'],
            'nombre_poches_vise' => ['required', 'integer'],
            'description' => ['required', 'string'],
            'contact_responsable' => ['required', 'string',  'regex:/^\d{2}\s?\d{3}\s?\d{2}\s?\d{2}$/'],
        ]
    );
    // Si les données ne sont pas valides, renvoyer les erreurs
    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 422);
    }
    // Si toutes les conditions sont remplies mettez à jour l'annonce

    // Mettre à jour l'annonce
    $annonce->update($request->only('titre', 'type_annonce', 'nom_lieu', 'adresse_lieu', 'date_debut', 'date_fin', 'heure_debut', 'heure_fin', 'groupe_sanguin_requis', 'nombre_poches_vise', 'description', 'contact_responsable'));

    // Envoyer des notifications en fonction du type d'annonce
if ($annonce->type_annonce == 'collecte') {
    // Notification à tous les utilisateurs simples
    $utilisateursSimples = UtilisateurSimple::whereHas('user', function($query) {
        $query->where('role_id', 3);  // Assurez-vous que l'utilisateur a le role_id = 3
    })->get(); // Récupérer les utilisateurs simples sous forme de collection Eloquent
} elseif ($annonce->type_annonce == 'besoin_urgence') {
    // Notification seulement aux utilisateurs dont le groupe sanguin correspond à celui requis
    $utilisateursSimples = UtilisateurSimple::whereHas('user', function($query) {
        $query->where('role_id', 3);  // Utilisateurs ayant le role_id = 3
    })
    ->where('groupe_sanguin', $annonce->groupe_sanguin_requis)  // Comparer avec le groupe sanguin requis
    ->get();  // Récupérer les utilisateurs simples sous forme de collection Eloquent
}

// Envoyer les notifications aux utilisateurs sélectionnés
foreach ($utilisateursSimples as $utilisateur_simple) {
    $notification = new Notification1();
    $notification->contenu = "L'annonce a été modifiée : {$annonce->titre}";
    $notification->annonce_id = $annonce->id;
    $notification->user_id = $utilisateur_simple->user_id; 
    $notification->statut = 'non-lu'; 
    $notification->save();
    
}

    return response()->json([
    'status' => true,
    'message' => 'Annonce modifiée et notifications envoyées.',
    'data' => $annonce
]); 
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Annonce $annonce)
    {
        // Obtenez l'utilisateur authentifié
    $user = auth()->user();

    // Vérifiez si l'utilisateur a le rôle approprié (role_id = 2)
    if ($user->role_id !== 2) {
        return response()->json([
            'status' => false,
            'message' => 'Vous n\'avez pas l\'autorisation de supprimer cette annonce.'
        ], 403);
    }
    // Récupérez la structure associée à l'utilisateur
    $structure = Structure::where('user_id', $user->id)->first(); // Récupérer la structure par l'user_id

    // Vérifiez si la structure existe
    if (!$structure) {
        return response()->json(['error' => 'Aucune structure associée à cet utilisateur.'], 404);
    }

    // Vérifiez si l'annonce appartient à l'utilisateur authentifié
    if ($annonce->structure_id !== $structure->id) {
        return response()->json([
            'status' => false,
            'message' => 'Vous ne pouvez supprimer que vos propres annonces.'
        ], 403);
    }

    // Si toutes les conditions sont remplies, supprimez l'annonce
       

        $annonce->delete();

        return response()->json([
            'status' => true,
            'message' => 'Annonce supprimée avec succès'
        ]);
    }
}
