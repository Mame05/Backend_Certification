<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UtilisateurSimple;
//use Illuminate\Support\Facades\Mail;
use App\Http\Requests\StoreUtilisateurSimpleRequest;
use App\Http\Requests\UpdateUtilisateurSimpleRequest;
use Illuminate\Support\Facades\Auth;

class UtilisateurSimpleController extends Controller
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
    public function store(StoreUtilisateurSimpleRequest $request)
    {
         //
    }

    /**
     * Display the specified resource.
     */
    public function show(UtilisateurSimple $utilisateurSimple)
    {
       //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UtilisateurSimple $utilisateurSimple)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUtilisateurSimpleRequest $request, UtilisateurSimple $utilisateurSimple)
    {
       //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UtilisateurSimple $utilisateurSimple)
    {
        //
    }

    public function showGamification()
    {
       // Obtenir l'utilisateur connecté
       $user = Auth::user();

       // Vérifiez que l'utilisateur est bien un utilisateur simple (role_id = 3)
       if ($user->role_id !== 3) {
        return response()->json(['error' => 'Accès non autorisé.'], 403);
    }

        // Récupérer l'utilisateur simple lié à cet utilisateur
        $utilisateur = UtilisateurSimple::where('user_id', $user->id)
            ->withCount(['rendezVous as nombre_de_dons' => function ($query) {
                $query->where('etat', true); // Compter uniquement les dons effectifs
            }])
            ->first();

        if (!$utilisateur) {
            return response()->json(['error' => 'Utilisateur simple non trouvé.'], 404);
        }

        return response()->json([
            'nom' => $utilisateur->nom,
            'prenom' => $utilisateur->prenom,
            'nombre_de_dons' => $utilisateur->nombre_de_dons,
            'niveau_gamification' => $utilisateur->gamification_level,
            'badge_code' => strtolower($utilisateur->gamification_level), // par ex., 'platine', 'or'
        ]);
    }
}
