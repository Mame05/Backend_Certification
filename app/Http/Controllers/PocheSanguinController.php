<?php

namespace App\Http\Controllers;

use App\Models\Banque_sang;
use App\Models\Poche_sanguin;
use App\Http\Requests\StorePoche_sanguinRequest;
use App\Http\Requests\UpdatePoche_sanguinRequest;

class PocheSanguinController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Récupérer toutes les poches sanguin
        $poche_sanguin = Poche_sanguin::all();
        return response()->json($poche_sanguin);
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
       // Valider les données de la poche sanguin 
        $validator = validator(
            $request->all(),
            [
                'numero_poche' => ['required', 'string', 'max:50', 'unique:poche_sanguins,numero_poche'],
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
            // Créer une nouvelle poche de sanguin
            $poche_sanguin = new Poche_sanguin();
            $poche_sanguin->numero_poche = $request->numero_poche;
            $poche_sanguin->groupe_sanguin = $request->groupe_sanguin;
            $poche_sanguin->date_prelevement = $request->date_prelevement;
            $poche_sanguin->banque_sang_id = $request->banque_sang_id;
            $poche_sanguin->rendez_vouse_id = $request->rendez_vouse_id;
            $poche_sanguin->donneur_externe_id = $request->donneur_externe_id;
            $poche_sanguin->save();
            return response()->json([
                'status' => true,
                'message' => 'Poche sanguin créée avec succès!.', 'data' => $poche_sanguin
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
                'profession' => $donneur_externe->profession
            ];
        }
        // Si le rendez_vouse_id n'est pas null, ajouter les informations de l'utilisateur_simple qui a pris le rendez-vous
        if ($poche_sanguin->rendez_vouse_id !== null) {
            $rendez_vouse = $poche_sanguin;
            
            
            if ($rendez_vouse) {
                $utilisateur_simple = $rendez_vouse;
                // dd($utilisateur_simple);
                $rendez_vouse = $poche_sanguin->rendezVouse; // Utilise la relation définie dans le modèle

                if ($utilisateur_simple) {
                    $response['utilisateur_simple'] = [
                        'utilisateur_simple' => $utilisateur_simple,
                        $utilisateur_simple->rendez_vouse,
                        // Ajoutez les informations annoce et l'utilisateur simple
                        // Utilisez la relation définie dans le modèle

                        $response['utilisateur_simple'] = [
                            'nom' => $utilisateur_simple->nom,
                            'prenom' => $utilisateur_simple->prenom,
                            'telephone' => $utilisateur_simple->telephone,
                            'adresse' => $utilisateur_simple->adresse,
                            'date_naiss' => $utilisateur_simple->date_naiss,
                            'profession' => $utilisateur_simple->profession
                        
                        ]
                    
                        
                    ];
                 
                }
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
            'numero_poche' => ['required', 'string', 'max:255'],
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
        // Mettre à jour la poche
    $poche_sanguin->update($request->only('numero_poche', 'groupe_sanguin', 'date_prelevement', 'banque_sang_id', 'rendez_vouse_id', 'donneur_externe_id'));

        return response()->json([
            'status' => true,
            'message' => 'Poche sanguin modifiée.',
            'data' => $poche_sanguin
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
}
