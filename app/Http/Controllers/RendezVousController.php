<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
use App\Models\Structure;
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

}
