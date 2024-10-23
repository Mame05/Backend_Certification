<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
use App\Models\Structure;
use App\Models\Poche_sanguin;
use App\Models\Banque_sang;
use App\Models\Rendez_vous;
use Illuminate\Http\Request;
use App\Models\Notification1;
use App\Models\UtilisateurSimple;
use App\Http\Requests\StoreRendez_vousRequest;
use App\Http\Requests\UpdateRendez_vousRequest;

class RendezVousController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(StoreRendez_vousRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Rendez_vous $rendez_vous)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Rendez_vous $rendez_vous)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRendez_vousRequest $request, Rendez_vous $rendez_vous)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rendez_vous $rendez_vous)
    {
        //
    }
     /**
     * Inscrire un utilisateur à une annonce.
     */
    public function inscrire(Request $request, $annonceId)
    {
         // Obtenez l'utilisateur authentifié
    $user = auth()->user();

    // Vérifiez si l'utilisateur a le rôle approprié (role_id = 3)
    if ($user->role_id !== 3) {
        return response()->json([
            'status' => false,
            'message' => 'Seules les utilisateurs simples pourront faire une inscription.'
        ], 403);
    }
        // Récupérer l'annonce
        $annonce = Annonce::find($annonceId);
        if (!$annonce) {
            return response()->json(['message' => 'Annonce non trouvée'], 404);
        }
        // Trouver l'utilisateur simple associé à cet ID utilisateur
        $utilisateur_simple = UtilisateurSimple::where('user_id', $user->id)->first();
        if (!$utilisateur_simple) {
            return response()->json(['error' => 'Utilisateur simple non trouvé.'], 404);
        }

        // Vérifier si l'utilisateur est déjà inscrit à cette annonce
    $rendezVousExist = Rendez_vous::where('annonce_id', $annonce->id)
    ->where('utilisateur_simple_id', $utilisateur_simple->id)
    ->first();

    // Si l'utilisateur est déjà inscrit et que le statut n'est pas "annuler", empêcher l'inscription
    if ($rendezVousExist && $rendezVousExist->statut !== 'annuler') {
    return response()->json(['message' => 'Vous êtes déjà inscrit à cette annonce.'], 403);
    }

    // Si l'utilisateur avait annulé son inscription, supprimer l'inscription annulée
    if ($rendezVousExist && $rendezVousExist->statut === 'annuler') {
    $rendezVousExist->delete(); // On peut aussi simplement modifier le statut à "programmer" si nécessaire.
    }

        // Créer un nouveau rendez-vous avec les informations de l'annonce
        $rendezVous = new Rendez_vous();
        $rendezVous->date_heure = $annonce->date_debut . ' ' . $annonce->heure_debut; // Utilisez la date et l'heure de l'annonce
        $rendezVous->annonce_id = $annonce->id;
        $rendezVous->utilisateur_simple_id = $utilisateur_simple->id; // ID de l'utilisateur authentifié
        $rendezVous->statut = 'programmer'; // Statut de l'inscription
        $rendezVous->save();

        // Créer une notification pour la structure qui a publié l'annonce
        $notification = new Notification1();
        $notification->contenu = "L' utilisateur {$utilisateur_simple->prenom} {$utilisateur_simple->nom} a inscrit à l'annonce : {$annonce->titre}";
        $notification->annonce_id = $annonce->id;
         // Trouver l'utilisateur correspondant à la structure
    $structure = Structure::find($annonce->structure_id);
    
    if ($structure) {
        $notification->user_id = $structure->user_id; // L'ID de l'utilisateur de la structure
        $notification->statut = 'non-lu';
        $notification->save();
    } else {
        return response()->json(['error' => 'Structure non trouvée.'], 500);
    }
        return response()->json(['message' => 'Inscription réussie à l\'annonce.'], 201);
    }

    public function annulerInscription($rendezVousId)
{
    // Trouver le rendez-vous par son ID
    $rendezVous = Rendez_vous::find($rendezVousId);
    
    // Vérifier si le rendez-vous existe
    if (!$rendezVous) {
        return response()->json(['message' => 'Rendez-vous non trouvé.'], 404);
    }

    // Vérifier si l'utilisateur connecté est l'utilisateur simple qui a pris ce rendez-vous
    $user = auth()->user();
    $utilisateur_simple = UtilisateurSimple::where('user_id', $user->id)->first();

    if ($rendezVous->utilisateur_simple_id !== $utilisateur_simple->id) {
        return response()->json(['message' => 'Vous n\'êtes pas autorisé à annuler ce rendez-vous.'], 403);
    }

    // Changer le statut à 'annuler'
    $rendezVous->statut = 'annuler';
    $rendezVous->save();

    // Récupérer l'annonce associée
    $annonce = $rendezVous->annonce;
    
    // Récupérer la structure qui a publié l'annonce
    $structure = Structure::where('id', $annonce->structure_id)->first();
    
    if ($structure) {
        // Créer une notification pour la structure
        $notification = new Notification1();
        $notification->contenu = "L'utilisateur {$utilisateur_simple->prenom} {$utilisateur_simple->nom} a annulé son inscription à l'annonce : {$annonce->titre}";
        $notification->annonce_id = $annonce->id;
        $notification->user_id = $structure->user_id; // L'ID de l'utilisateur correspondant à la structure
        $notification->statut = 'non-lu';
        $notification->save();
    }

    // Retourner une réponse de succès
    return response()->json(['message' => 'Inscription annulée avec succès, et la structure a été notifiée.'], 200);
}

public function updateEtat(Request $request, Rendez_vous $rendezVous)
    {
        // Obtenir l'utilisateur authentifié
        $user = auth()->user();

        // Vérifiez si l'utilisateur a le rôle de structure (role_id = 2)
        if ($user->role_id !== 2) {
            return response()->json(['error' => 'Vous n\'avez pas l\'autorisation de modifier cet état.'], 403);
        }
        $banqueSang = Banque_sang::findOrFail($request->banque_sang_id);

        // Vérifiez que la banque de sang appartient à la structure de l'utilisateur
        if ($banqueSang->structure->user_id !== $user->id) {
            return response()->json(['error' => 'Vous ne pouvez ajouter des poches que dans votre banque de sang.'], 403);
        }
        // Récupérer l'annonce liée au rendez-vous
        $annonce = Annonce::findOrFail($rendezVous->annonce_id);

        // Vérifiez que l'annonce appartient à la structure (utilisateur)
        if ($annonce->structure->user_id !== $user->id) {
            return response()->json(['error' => 'Vous ne pouvez modifier l\'état que sur vos propres annonces.'], 403);
        }

        // Valider les données (par exemple, pour la colonne 'etat')
        $validator = validator($request->all(), [
            'etat' => 'required|boolean', // Valider que l'état est bien un booléen
        ]);

        // Si la validation échoue, renvoyer les erreurs
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Mettre à jour l'état du rendez-vous
        $rendezVous->etat = $request->etat;
        $rendezVous->save();
        $numero_poche = 'POCHE-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
        // Créer une nouvelle poche de sanguin
        $poche_sanguin = new Poche_sanguin();
        $poche_sanguin->numero_poche = $numero_poche;
        $poche_sanguin->groupe_sanguin = $request->groupe_sanguin;
        $poche_sanguin->date_prelevement = $request->date_prelevement;
        $poche_sanguin->banque_sang_id = $request->banque_sang_id;
        $poche_sanguin->rendez_vouse_id = $rendezVous->id;
        $poche_sanguin->donneur_externe_id = $request->donneur_externe_id;
        $poche_sanguin->save();

         // Mettre à jour le stock et la date de mise à jour
        $banqueSang->stock_actuelle += 1; // Incrémenter le stock actuel
        $banqueSang->date_mise_a_jour = now(); // Mettre à jour la date de mise à jour avec la date actuelle
        $banqueSang->save();

        return response()->json([
            'rendezVous' => $rendezVous,
            'poche_sanguin' => $poche_sanguin,
            'data' => $poche_sanguin,
            
            'message' => 'L\'état du rendez-vous a été mis à jour avec succès!']);
    }

    public function getInscriptions()
{
    // Obtenez l'utilisateur authentifié
    $user = auth()->user();

    // Trouver l'utilisateur simple associé à cet ID utilisateur
    $utilisateur_simple = UtilisateurSimple::where('user_id', $user->id)->first();

    if (!$utilisateur_simple) {
        return response()->json(['error' => 'Utilisateur simple non trouvé.'], 404);
    }
     // Date actuelle
     $dateActuelle = now();

     // Récupérer les inscriptions en cours (annonces dont la date de fin est > date actuelle)
     $inscriptionsEnCours = Rendez_vous::where('utilisateur_simple_id', $utilisateur_simple->id)
         ->whereHas('annonce', function ($query) use ($dateActuelle) {
             $query->where('date_fin', '>', $dateActuelle);
         })
         ->with('annonce') // Charger aussi les détails de l'annonce
         ->get();
 
     // Récupérer les inscriptions historiques (annonces dont la date de fin est <= date actuelle)
     $historiqueInscriptions = Rendez_vous::where('utilisateur_simple_id', $utilisateur_simple->id)
         ->whereHas('annonce', function ($query) use ($dateActuelle) {
             $query->where('date_fin', '<=', $dateActuelle);
         })
         ->with('annonce')
         ->get();
 
     return response()->json([
         'inscriptionsEnCours' => $inscriptionsEnCours,
         'historiqueInscriptions' => $historiqueInscriptions
     ]);
}
public function supprimerHistorique($rendezVousId)
{
    // Obtenez l'utilisateur authentifié
    $user = auth()->user();
    
    // Trouver le rendez-vous par son ID
    $rendez_vous = Rendez_vous::with('annonce')->find($rendezVousId);

    // Vérifier que l'utilisateur est bien celui qui a pris le rendez-vous
    if ($rendez_vous && $rendez_vous->utilisateur_simple_id == $user->utilisateur_simple->id) {

        // Vérifier que la date de fin de l'annonce est passée
        if ($rendez_vous->annonce->date_fin < now()) {
            // Supprimer le rendez-vous
            $rendez_vous->delete();
            return response()->json(['message' => 'Historique supprimé avec succès.']);
        } else {
            return response()->json(['error' => 'Seules les inscriptions à des annonces passées peuvent être supprimées.'], 403);
        }
    }

    return response()->json(['error' => 'Suppression non autorisée.'], 403);
}

// Methode qui permet de recuperer les donneurs pour 
public function getUsersWithCompletedInscriptions()
{
    // Obtenir l'utilisateur authentifié
    $user = auth()->user();

    // Vérifier que l'utilisateur est bien une structure
    if ($user->role_id !== 2) {
        return response()->json(['message' => 'Seules les structures peuvent accéder à cette ressource.'], 403);
    }

    // Récupérer la structure correspondante
    $structure = Structure::where('user_id', $user->id)->first();

    // Vérifier si la structure existe
    if (!$structure) {
        return response()->json(['message' => 'Structure non trouvée.'], 404);
    }

    // Récupérer toutes les annonces de la structure
    $annonces = Annonce::where('structure_id', $structure->id)->pluck('id');

    // Vérifier si des annonces sont trouvées
    if ($annonces->isEmpty()) {
        return response()->json(['message' => 'Aucune annonce trouvée pour cette structure.'], 404);
    }

    // Récupérer les utilisateurs simples qui ont des inscriptions avec l'état booléen true
    $utilisateurs = UtilisateurSimple::whereHas('rendezVous', function ($query) use ($annonces) {
        $query->whereIn('annonce_id', $annonces)
              ->where('etat', true); // Vérifier si l'état est true
    })->withCount(['rendezVous as nombre_de_dons' => function ($query) {
        $query->where('etat', true);
    }])->get()->map(function ($utilisateur) {
        $dernierDon = $utilisateur->rendezVous()
        ->where('etat', true)
        ->latest('created_at') // Récupérer le dernier rendez-vous avec état `true`
        ->first();

        // Formater la date pour l'affichage
        $dernierDonDate = $dernierDon ? $dernierDon->created_at->format('Y-m-d') : 'Aucun don';
        return [
            'nom_complet' => $utilisateur->prenom . ' ' . $utilisateur->nom,
            'telephone' => $utilisateur->telephone,
            'groupe_sanguin' => $utilisateur->groupe_sanguin, // Assurez-vous que cette colonne existe dans votre modèle
            'nombre_de_dons' => $utilisateur->nombre_de_dons,
            'dernier_don' => $dernierDonDate,
        ];
    });

    // Vérifier si des utilisateurs sont trouvés
    if ($utilisateurs->isEmpty()) {
        return response()->json(['message' => 'Aucun utilisateur trouvé avec des inscriptions complétées.'], 404);
    }

    // Retourner les utilisateurs
    return response()->json($utilisateurs);
}





 


}
