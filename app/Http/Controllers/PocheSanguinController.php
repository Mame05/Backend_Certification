<?php

namespace App\Http\Controllers;

use App\Models\Banque_sang;
use App\Models\Poche_sanguin;
use App\Models\DonneurExterne;
use App\Models\Structure;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB; // Importer DB pour l'utilisation des requêtes
use Illuminate\Http\Request;
use App\Http\Requests\StorePoche_sanguinRequest;
use App\Http\Requests\UpdatePoche_sanguinRequest;

class PocheSanguinController extends Controller
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

        // Si la structure existe, récupérer ses poches sanguines
        if ($structure) {
            // Récupérer les banques de sang associées à la structure
            $banques_sang = Banque_sang::where('structure_id', $structure->id)->pluck('id');

            // Récupérer les poches sanguines associées aux banques de sang
            $poches_sanguines = Poche_sanguin::whereIn('banque_sang_id', $banques_sang)->get();

            // Retourner les poches sanguines au format JSON
            return response()->json($poches_sanguines);
        } else {
            return response()->json(['message' => 'Structure non trouvée'], 404);
        }
    }
    // Vérifier si l'utilisateur a le rôle d'admin ou d'utilisateur simple
    elseif ($user->role_id == 1 || $user->role_id == 3) {
        // Récupérer toutes les poches sanguines
        $poches_sanguines = Poche_sanguin::all();
        
        // Retourner les poches sanguines sous forme de JSON
        return response()->json($poches_sanguines);
    } else {
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
    public function store(StorePoche_sanguinRequest $request)
    {
        // Obtenez l'utilisateur authentifié
    $user = auth()->user();

    // Vérifiez si l'utilisateur a le rôle approprié (role_id = 2)
    if ($user->role_id !== 2) {
        return response()->json(['error' => 'Vous n\'avez pas l\'autorisation d\'ajouter une poche de sang.'], 403);
    }

    // Vérifiez que la banque de sang existe
    $banqueSang = Banque_sang::findOrFail($request->banque_sang_id);

    // Vérifiez que la banque de sang appartient à la structure de l'utilisateur
    if ($banqueSang->structure->user_id !== $user->id) {
        return response()->json(['error' => 'Vous ne pouvez ajouter des poches que dans votre banque de sang.'], 403);
    }

    // Valider les données de la poche sanguine 
    $validator = validator(
        $request->all(),
        [
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'telephone' => ['required', 'string', 'regex:/^\d{2}\s?\d{3}\s?\d{2}\s?\d{2}$/'],
            'adresse' => ['required', 'string'],
            'sexe' => ['required', 'in:M,F'],
            'date_naiss' => ['required', 'date'],
            'profession' => ['required', 'string', 'max:255'],
            'groupe_sanguin' => ['required', 'in:A+,A-,B+,B-,O+,O-,AB+,AB-'],
            'date_prelevement' => ['required', 'date'], // Date valide pour le prélèvement.
            'banque_sang_id' => ['required', 'exists:banque_sangs,id'], // Assurez-vous que la banque de sang existe
            'rendez_vouse_id' => ['nullable', 'exists:rendez_vouses,id'],
            'donneur_externe_id' => ['nullable', 'exists:donneur_externes,id'],
        ]
    );

    // Si les données ne sont pas valides, renvoyer les erreurs
    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 422);
    }

    // Crée ou récupère le donneur
    $donneur_externe = DonneurExterne::firstOrCreate(
        ['telephone' => $request->telephone],
        [
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'adresse' => $request->adresse,
            'sexe' => $request->sexe,
            'date_naiss' => $request->date_naiss,
            'profession' => $request->profession,
            'groupe_sanguin' => $request->groupe_sanguin,
        ]
    );

    // Gérer le nombre de dons dans la table pivot `donneur_structure`
    $structure = $banqueSang->structure;
    $nombre_dons = 1; // Valeur par défaut pour un nouveau donneur dans une nouvelle structure

    if ($donneur_externe->structures()->where('structure_id', $structure->id)->exists()) {
        // Incrémentez le nombre de dons si l'association existe
        $donneur_externe->structures()->updateExistingPivot($structure->id, [
            'nombre_dons' => \DB::raw('nombre_dons + 1')
        ]);
        $nombre_dons = $donneur_externe->structures()->where('structure_id', $structure->id)->first()->pivot->nombre_dons;
    } else {
        // Créez une nouvelle association dans la table pivot avec un nombre de dons initialisé à 1
        $donneur_externe->structures()->attach($structure->id, ['nombre_dons' => 1]);
    }

    // Créer la poche de sang
    $numero_poche = 'POCHE-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
    $poche_sanguin = new Poche_sanguin();
    $poche_sanguin->numero_poche = $numero_poche;
    $poche_sanguin->groupe_sanguin = $donneur_externe->groupe_sanguin;
    $poche_sanguin->date_prelevement = $request->date_prelevement;
    $poche_sanguin->banque_sang_id = $request->banque_sang_id;
    $poche_sanguin->rendez_vouse_id = $request->rendez_vouse_id;
    $poche_sanguin->donneur_externe_id = $donneur_externe->id;
    $poche_sanguin->save();

    // Mise à jour du stock de la banque de sang
    $banqueSang->increment('stock_actuelle');
    $banqueSang->update(['date_mise_a_jour' => now()]);

    return response()->json([
        'status' => true,
        'DONNER' => [
            'donneur_externe' => $donneur_externe,
            'nombre_dons' => $nombre_dons,
        ],
        'message' => 'Poche sanguine créée avec succès!',
        'data' => $poche_sanguin
    ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Poche_sanguin $poche_sanguin)
    {
        // Commencez avec les informations de la poche sanguine
        $response = [
            'numero_poche' => $poche_sanguin->numero_poche,
            'groupe_sanguin' => $poche_sanguin->groupe_sanguin,
            'date_prelevement' => $poche_sanguin->date_prelevement, 
            'banque_sang_id' => $poche_sanguin->banque_sang_id,  
        ];

        // Si le donneur_externe_id n'est pas null, ajouter les informations du donneur externe
        if ($poche_sanguin->donneur_externe_id !== null) {
            $donneur_externe = $poche_sanguin->donneur_externe; // Utilise la relation définie dans le modèle
            $response['donneur_externe'] = [
                'nom' => $donneur_externe->nom,
                'prenom' => $donneur_externe->prenom,
                'telephone' => $donneur_externe->telephone,
                'adresse' => $donneur_externe->adresse,
                'date_naiss' => $donneur_externe->date_naiss,
                'profession' => $donneur_externe->profession,
            ];
        }
    
        // Si le rendez_vouse_id n'est pas null, ajouter les informations de l'utilisateur_simple
        if ($poche_sanguin->rendez_vouse_id !== null) {
            $rendez_vouse = $poche_sanguin->rendezVouse; // Utilise la relation définie dans le modèle
            $utilisateur_simple = $rendez_vouse->utilisateur_simple ?? null;
    
            if ($utilisateur_simple) {
                $response['utilisateur_simple'] = [
                    'nom' => $utilisateur_simple->nom,
                    'prenom' => $utilisateur_simple->prenom,
                    'telephone' => $utilisateur_simple->telephone,
                    'adresse' => $utilisateur_simple->adresse,
                    'date_naiss' => $utilisateur_simple->date_naiss,
                    'profession' => $utilisateur_simple->profession,
                ];
            }
        }
    
        // Retourner la réponse JSON
        return response()->json($response);
    }
        
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Poche_sanguin $poche_sanguin)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    /**  Modification des poches sanguins pour les donneurs externes  */
    public function update(UpdatePoche_sanguinRequest $request, Poche_sanguin $poche_sanguin)
    {
        // Obtenez l'utilisateur authentifié
        $user = auth()->user();

    // Vérifiez si l'utilisateur a le rôle approprié (role_id = 2)
    if ($user->role_id !== 2) {
        return response()->json([
            'status' => false,
            'message' => 'Vous n\'avez pas l\'autorisation de modifier cette poche de sang.'
        ], 403);
    }
      // Vérifiez que la banque de sang existe
      $banqueSang = Banque_sang::findOrFail($request->banque_sang_id);

      // Vérifiez que la banque de sang appartient à la structure de l'utilisateur
      if ($banqueSang->structure->user_id !== $user->id) {
          return response()->json(['error' => 'Vous ne pouvez modifier des poches que dans votre propre banque de sang.'], 403);
      }
      
    // Valider les données de la poche sanguin 
    $validator = validator(
        $request->all(),
        [
            // Validation des champs de la donneur externe
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'telephone' => ['required', 'string', 'regex:/^\d{2}\s?\d{3}\s?\d{2}\s?\d{2}$/'],
            'adresse' => ['required', 'string'],
            'sexe' => ['required', 'in:M,F'],
            'date_naiss' => ['required', 'date'],
            'profession' => ['required', 'string', 'max:255'],

            // Validation des champs de la poche sanguin
            //'numero_poche' => ['required', 'string', 'max:255'],
            'groupe_sanguin' => ['required', 'in:A+,A-,B+,B-,O+,O-,AB+,AB-'],
            'date_prelevement' => ['required', 'date'], // Date valide pour le prélèvement.
            'banque_sang_id' => ['required', 'exists:banque_sangs,id'], // Assurez-vous que la banque de sang existe
            'rendez_vouse_id' => 'nullable|exists:rendez_vouses,id',
            'donneur_externe_id' => 'nullable|exists:donneur_externes,id',

        ]
    );
        // Si les données ne sont pas valides, renvoyer les erreurs
        if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 422);
        }
         // Initialiser la variable $donneur_externe
    $donneur_externe = null;
      // Mettre à jour le donneur externe
    if ($request->donneur_externe_id) {
    $donneur_externe = DonneurExterne::findOrFail($request->donneur_externe_id);
    $donneur_externe->update($request->only('nom', 'prenom', 'telephone', 'adresse', 'sexe', 'date_naiss', 'profession', 'groupe_sanguin'));
}

    // Mettre à jour la poche sanguine
    $poche_sanguin->update($request->only('groupe_sanguin', 'date_prelevement', 'banque_sang_id', 'rendez_vouse_id', 'donneur_externe_id'));

    return response()->json([
        'status' => true,
        'message' => 'Poche sanguine et/ou donneur externe modifiée avec succès.',
        'Donneur' => $donneur_externe,
        'data' => $poche_sanguin
    ]);
    }

    /** Modification des poches sanguins pour les donneurs internes i.e les utilisateurs simples  */
    public function updatePoche(Request $request, $id)
    {
        // Obtenir l'utilisateur authentifié
        $user = auth()->user();
    
        // Vérifiez si l'utilisateur a le rôle approprié (role_id = 2)
        if ($user->role_id !== 2) {
            return response()->json([
                'status' => false,
                'message' => 'Vous n\'avez pas l\'autorisation de modifier cette poche de sang.'
            ], 403);
        }
    
        // Trouver la poche sanguine par son ID
        $pocheSanguin = Poche_sanguin::findOrFail($id); // Assurez-vous de récupérer la poche
    
        // Vérifiez que la banque de sang existe
        $banqueSang = Banque_sang::findOrFail($request->banque_sang_id);
    
        // Vérifiez que la banque de sang appartient à la structure de l'utilisateur
        if ($banqueSang->structure->user_id !== $user->id) {
            return response()->json(['error' => 'Vous ne pouvez modifier des poches que dans votre propre banque de sang.'], 403);
        }
    
        // Valider les nouvelles données
        $validator = validator($request->all(), [
            'groupe_sanguin' => ['required', 'in:A+,A-,B+,B-,O+,O-,AB+,AB-'],
            'date_prelevement' => ['required', 'date'],
            'banque_sang_id' => ['required', 'exists:banque_sangs,id'],
            'donneur_externe_id' => ['nullable', 'exists:donneur_externes,id']
        ]);
    
        // Si la validation échoue, renvoyer les erreurs
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
    
        // Mettre à jour les informations de la poche de sang
        $pocheSanguin->groupe_sanguin = $request->groupe_sanguin;
        $pocheSanguin->date_prelevement = $request->date_prelevement;
        $pocheSanguin->banque_sang_id = $request->banque_sang_id;
        $pocheSanguin->donneur_externe_id = $request->donneur_externe_id;
        $pocheSanguin->save();
    
        return response()->json([
            'message' => 'Poche de sang mise à jour avec succès',
            'pocheSanguin' => $pocheSanguin
        ]);
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Poche_sanguin $poche_sanguin)
    {
         // Obtenez l'utilisateur authentifié
    $user = auth()->user();

    // Vérifiez si l'utilisateur a le rôle approprié (role_id = 2)
    if ($user->role_id !== 2) {
        return response()->json([
            'status' => false,
            'message' => 'Vous n\'avez pas l\'autorisation de supprimer cette poche de sang.'
        ], 403);
    }
     // Vérifiez que la banque de sang existe
     $banqueSang = Banque_sang::findOrFail($poche_sanguin->banque_sang_id);

    // Vérifiez que la banque de sang appartient à la structure de l'utilisateur
    if ($banqueSang->structure->user_id !== $user->id) {
        return response()->json(['error' => 'Vous ne pouvez supprimer des poches que dans votre propre banque de sang.'], 403);
    }
    
     
     // Si toutes les conditions sont remplies, supprimez la poche 

     $poche_sanguin->delete();

     return response()->json([
         'status' => true,
         'message' => 'Poche sanguin supprimée avec succès'
     ]);
    }

    // Méthode pour permettre d'avoir le nombres de poches ajouté par mois 
    public function getPochesSanguinsParMois()
    {
    // Récupérer l'utilisateur authentifié
    $user = auth()->user();
    // Vérifier que l'utilisateur a un rôle de structure
    if ($user->role_id != 2) {
        return response()->json(['message' => 'Accès interdit.'], 403);
    }

    // Vérifier si l'utilisateur a une structure associée
    $structure = Structure::where('user_id', $user->id)->first();

    if (!$structure) {
        return response()->json(['message' => 'Structure non trouvée.'], 404);
    }
    // Vérifier les banques de sang associées à la structure
    $banquesDeSang = DB::table('banque_sangs') // Change le nom de la table si nécessaire
        ->where('structure_id', $structure->id) // Supposons que structure_id est la clé étrangère dans la table banques_de_sang
        ->pluck('id');

    if ($banquesDeSang->isEmpty()) {
        return response()->json(['message' => 'Aucune banque de sang trouvée pour cette structure.'], 404);
    }
    // Obtenir l'année actuelle
    $anneeActuelle = date('Y');

    $resultats = DB::table('poche_sanguins')
        ->select(DB::raw('DATE_FORMAT(created_at, "%M") as mois, COUNT(*) as nombre_poches'))
        ->whereIn('banque_sang_id', $banquesDeSang)
        ->whereYear('created_at', $anneeActuelle) // Filtrer par l'année actuelle
        ->groupBy(DB::raw('DATE_FORMAT(created_at, "%M"), MONTH(created_at), YEAR(created_at)')) // Grouper par mois et année
        ->orderBy(DB::raw('MONTH(created_at)')) // Trier par mois numérique
        ->get();



    if ($resultats->isEmpty()) {
        return response()->json(['message' => 'Aucune poche de sang trouvée pour cette structure.'], 404);
    }
    return response()->json($resultats);
}

    
}
