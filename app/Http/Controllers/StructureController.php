<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Structure;
use App\Mail\StructureCreated;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\StoreStructureRequest;
use App\Http\Requests\UpdateStructureRequest;

class StructureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         //afficher la liste des structures
         $structures = Structure::all();
         return response()->json($structures);
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
    public function store(StoreStructureRequest $request)
    {
        // Vérifier que l'utilisateur courant a le rôle avec ID 1
       if (auth()->user()->role_id !== 1) {
            return response()->json(['error' => 'Vous n\'avez pas l\'autorisation d\'ajouter une structure.'], 403);
        }
    
        // Validation des données de la structure
        $validator = validator(
            $request->all(),
            [
                'nom_structure' => ['required', 'string', 'max:255', 'unique:structures'],
                'sigle' => ['required', 'string'],
                'telephone' => ['required', 'string', 'unique:utilisateur_simples', 'regex:/^\d{2}\s?\d{3}\s?\d{2}\s?\d{2}$/'],
                'adresse' => ['required', 'string'],
                'region' => ['required', 'string'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:8'],
            ]
        );
        
        // Si les données ne sont pas valides, renvoyer les erreurs
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
    
        // Créer un nouvel utilisateur
        $user = User::create([
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role_id' => 2,
        ]);
    
        // Créer une nouvelle structure
        $structure = new Structure();
        $structure->user_id = $user->id;
        $structure->nom_structure = $request->nom_structure;
        $structure->sigle = $request->sigle;
        $structure->telephone = $request->telephone;
        $structure->adresse = $request->adresse;
        $structure->region = $request->region;
        $structure->save();

         // Envoyer un email à la structure
        Mail::to($request->email)->send(new StructureCreated($structure, $request->password));

        return response()->json([
            'status' => true,
            'message' => 'Structure créée avec succès',
            'data' => $structure
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Structure $structure)
    {
        return response()->json([
            'nom_structure' => $structure->nom_structure,
            'email' => $structure->user->email,  // Assurez-vous que la relation est correcte
            'sigle' => $structure->sigle,
            'telephone' => $structure->telephone,
            'adresse' => $structure->adresse,
            'region' => $structure->region,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Structure $structure)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStructureRequest $request, Structure $structure)
    {
        $currentUser = auth()->user();
        
        // Vérifier que l'utilisateur courant est soit un admin, soit la structure concernée
        if ($currentUser->role_id !== 1 && $currentUser->id !== $structure->user_id) {
            return response()->json(['error' => 'Vous n\'avez pas l\'autorisation de mettre à jour cette structure.'], 403);
        }
    
        // Validation des données
        $validator = validator(
            $request->all(),
            [
                'nom_structure' => ['required', 'string', 'max:255', 'unique:structures,nom_structure,' . $structure->id],
                'sigle' => ['required', 'string'],
                'telephone' => ['required', 'string', 'unique:utilisateur_simples', 'regex:/^\d{2}\s?\d{3}\s?\d{2}\s?\d{2}$/'],
                'adresse' => ['required', 'string'],
                'region' => ['required', 'string'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $structure->user_id],
                'password' => ['sometimes', 'nullable', 'string', 'min:8'],
            ]
        );
    
        // Si les données ne sont pas valides, renvoyer les erreurs
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
    
        // Mettre à jour les informations de la structure
        $structure->update($request->only('nom_structure', 'sigle', 'telephone', 'adresse', 'region'));
    
        // Mettre à jour les informations de l'utilisateur uniquement si l'utilisateur courant est la structure concernée ou si c'est un admin
        $user = $structure->user;
        if ($currentUser->id === $user->id || $currentUser->role_id === 1) {
            $user->update([
                'email' => $request->email,
                'password' => $request->filled('password') ? bcrypt($request->password) : $user->password,
            ]);
        }
    
        return response()->json([
            'status' => true,
            'message' => 'Les données de la structure ont été mises à jour avec succès'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Structure $structure)
    {
        // Vérifiez les permissions de l'utilisateur
        if (auth()->user()->role_id !== 1) {
            return response()->json(['error' => 'Vous n\'avez pas l\'autorisation de supprimer cette structure.'], 403);
        }
    
        try {
            // Supprimer l'utilisateur associé si nécessaire
            if ($structure->user) {
                $structure->user->delete();
            }
    
            // Supprimer la structure
            $structure->delete();
    
            return response()->json([
                'status' => true,
                'message' => 'Structure supprimée avec succès'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Une erreur est survenue lors de la suppression.'], 500);
        }
    }
    
    

    /**
     * Récupérer les informations de la structure connectée.
     */
    public function getStructureConnectee()
    {
        // Récupérer l'utilisateur connecté
        $user = auth()->user();

        // Vérifier que l'utilisateur est bien associé à une structure
        $structure = Structure::where('user_id', $user->id)->first();

        if (!$structure) {
            return response()->json(['error' => 'Aucune structure associée à cet utilisateur.'], 404);
        }

        // Retourner les informations de la structure
        return response()->json([
            'nom_structure' => $structure->nom_structure,
            'email' => $user->email,
            'sigle' => $structure->sigle,
            'telephone' => $structure->telephone,
            'adresse' => $structure->adresse,
            'region' => $structure->region,
        ]);
    }
}
