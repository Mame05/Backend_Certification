<?php

namespace App\Http\Controllers;

use App\Models\Structure;
use App\Models\Banque_sang;
use App\Http\Requests\StoreBanque_sangRequest;
use App\Http\Requests\UpdateBanque_sangRequest;

class BanqueSangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Récupérer toutes les banques de sang
        $banques_sang = Banque_sang::all();
        
        // Retourner les banques de sang sous forme de JSON
        return response()->json($banques_sang);
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
    public function store(StoreBanque_sangRequest $request)
    {
         // Obtenez l'utilisateur authentifié
        $user = auth()->user();
        // Vérifiez si l'utilisateur a le rôle approprié (role_id = 2)
        if ($user->role_id !== 2) {
        return response()->json(['error' => 'Vous n\'avez pas l\'autorisation d\'ajouter une banque de sang.'], 403);
    }

    
    // Valider les données de la banque de sang
    $validator = validator(
    $request->all(),
    [
        'matricule' => ['required', 'string','max:255', 'unique:banque_sangs'],
        'stock_actuelle' => ['required', 'integer', 'min:0'],
        'date_mise_a_jour' => ['required', 'date'],
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

    // Créer une nouvelle banque de sang
    $banque_sang = new Banque_sang();
    $banque_sang->matricule = $request->matricule;
    $banque_sang->stock_actuelle = $request->stock_actuelle;
    $banque_sang->date_mise_a_jour = $request->date_mise_a_jour;
    $banque_sang->structure_id = $structure->id; // Utiliser l'id de la structure
    $banque_sang->save();

    return response()->json([
        'status' => true,
        'message' => 'Banque de sang créée avec succès!.',
        'data' => $banque_sang
    ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Banque_sang $banque_sang)
    {
        return response()->json([
            'matricule' => $banque_sang->matricule,
            'stock_actuelle' => $banque_sang->stock_actuelle,
            'date_mise_a_jour' => $banque_sang->date_mise_a_jour,
            'structure' => $banque_sang->structure
        ]
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Banque_sang $banque_sang)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBanque_sangRequest $request, Banque_sang $banque_sang)
    {
        // Obtenez l'utilisateur authentifié
        $user = auth()->user();

    // Vérifiez si l'utilisateur a le rôle approprié (role_id = 2)
    if ($user->role_id !== 2) {
        return response()->json([
            'status' => false,
            'message' => 'Vous n\'avez pas l\'autorisation de modifier cette banque de sang.'
        ], 403);
    }
    // Récupérez la structure associée à l'utilisateur
    $structure = Structure::where('user_id', $user->id)->first(); // Récupérer la structure par l'user_id

    // Vérifiez si la structure existe
    if (!$structure) {
        return response()->json(['error' => 'Aucune structure associée à cet utilisateur.'], 404);
    }

    // Vérifiez si la banque de sang appartient à l'utilisateur authentifié
    if ($banque_sang->structure_id !== $structure->id) {
        return response()->json([
            'status' => false,
            'message' => 'Vous ne pouvez modifier que vos propres banque de sang.'
        ], 403);
    }
    $validator = validator(
        $request->all(),
        [
            'matricule' => ['required', 'string','max:255'],
            'stock_actuelle' => ['required', 'integer', 'min:0'],
            'date_mise_a_jour' => ['required', 'date'],
        ]
    );
    
        // Si les données ne sont pas valides, renvoyer les erreurs
        if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 422);
        }
        // Mettre à jour la banque
        $banque_sang->update($request->only('matricule', 'stock_actuelle', 'date_mise_a_jour'));

        return response()->json([
            'status' => true,
            'message' => 'Banque de sang modifiée.',
            'data' => $banque_sang
        ]); 
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Banque_sang $banque_sang)
    {
        // Obtenez l'utilisateur authentifié
    $user = auth()->user();

    // Vérifiez si l'utilisateur a le rôle approprié (role_id = 2)
    if ($user->role_id !== 2) {
        return response()->json([
            'status' => false,
            'message' => 'Vous n\'avez pas l\'autorisation de supprimer cette banque de sang.'
        ], 403);
    }
    // Récupérez la structure associée à l'utilisateur
    $structure = Structure::where('user_id', $user->id)->first(); // Récupérer la structure par l'user_id

    // Vérifiez si la structure existe
    if (!$structure) {
        return response()->json(['error' => 'Aucune structure associée à cet utilisateur.'], 404);
    }

    // Vérifiez si l'annonce appartient à l'utilisateur authentifié
    if ($banque_sang->structure_id !== $structure->id) {
        return response()->json([
            'status' => false,
            'message' => 'Vous ne pouvez supprimer que vos propres banque de sang.'
        ], 403);
    }
     // Si toutes les conditions sont remplies, supprimez l'annonce
       

     $banque_sang->delete();

     return response()->json([
         'status' => true,
         'message' => 'Banque de sang supprimée avec succès'
     ]);
    }
}
