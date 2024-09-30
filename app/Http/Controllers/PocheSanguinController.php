<?php

namespace App\Http\Controllers;

use App\Models\Section;
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
        return response()->json(['error' => 'Vous n\'avez pas l\'autorisation d\'ajouter une section.'], 403);
       }
       // Vérifiez que la section existe
    $section = Section::findOrFail($request->section_id);

    // Vérifiez que la section appartient bien à une banque de sang
    $banqueSang = $section->banqueSang;

    if (!$banqueSang) {
        return response()->json(['error' => 'La section sélectionnée n\'est pas liée à une banque de sang.'], 403);
    }

    // Vérifiez que la banque de sang appartient à la structure de l'utilisateur
    if ($banqueSang->structure->user_id !== $user->id) {
        return response()->json(['error' => 'Vous ne pouvez ajouter des poches que dans les sections de votre banque de sang.'], 403);
    }
      
        // Valider les données de la poche sanguin 
        $validator = validator(
            $request->all(),
            [
                'numero_poche' => ['required', 'string', 'max:50', 'unique:poche_sanguins,numero_poche'],
                'groupe_sanguin' => ['required', 'in:A+,A-,B+,B-,O+,O-,AB+,AB-'],
                'nom_donneur' => ['required', 'string', 'max:255'],
                'prenom_donneur' => ['required', 'string', 'max:255'],
                'telephone_donneur' => ['required', 'string', 'unique:poche_sanguins,telephone_donneur', 'regex:/^\d{2}\s?\d{3}\s?\d{2}\s?\d{2}$/'], // Unicité du téléphone parmi les poches de sang.
                'adresse_donneur' => ['required', 'string', 'max:255'],
                'sexe_donneur' => ['required', 'string', 'in:M,F'], // Vous pouvez adapter la validation 'in' selon les valeurs possibles dans votre application.
                'date_naiss_donneur' => ['required', 'date', 'before:today'], // La date de naissance doit être une date valide avant aujourd'hui.
                'numero_identite_national_donneur' => ['required', 'string', 'unique:poche_sanguins,numero_identite_national_donneur'], // Unicité de l'identité nationale.
                'profession_donneur' => ['required', 'string', 'max:255'],
                'date_prelevement' => ['required', 'date'], // Date valide pour le prélèvement.
                'section_id' => ['required', 'exists:sections,id'], // Vérifie que la section existe dans la table sections.

            ]
        );
        // Si les données ne sont pas valides, renvoyer les erreurs
        if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 422);
        }

        // Vérifier que le groupe sanguin correspond au nom de la section
        if (strcasecmp(trim($request->groupe_sanguin), trim($section->nom_section)) !== 0) {
            return response()->json([
                'error' => 'Le groupe sanguin ne correspond pas au nom de la section.'
            ], 403);
        }
        

            // Créer une nouvelle poche de sanguin
            $poche_sanguin = new Poche_sanguin();
            $poche_sanguin->numero_poche = $request->numero_poche;
            $poche_sanguin->groupe_sanguin = $request->groupe_sanguin;
            $poche_sanguin->nom_donneur = $request->nom_donneur;
            $poche_sanguin->prenom_donneur = $request->prenom_donneur;
            $poche_sanguin->telephone_donneur = $request->telephone_donneur;
            $poche_sanguin->adresse_donneur = $request->adresse_donneur;
            $poche_sanguin->sexe_donneur = $request->sexe_donneur;
            $poche_sanguin->date_naiss_donneur = $request->date_naiss_donneur;
            $poche_sanguin->numero_identite_national_donneur = $request->numero_identite_national_donneur;
            $poche_sanguin->profession_donneur = $request->profession_donneur;
            $poche_sanguin->date_prelevement = $request->date_prelevement;
            $poche_sanguin->section_id = $request->section_id;
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
        return response()->json([
        'numero_poche' => $poche_sanguin->numero_poche,
        'groupe_sanguin' => $poche_sanguin->groupe_sanguin,
        'nom_donneur' => $poche_sanguin->nom_donneur,
        'prenom_donneur' => $poche_sanguin->prenom_donneur,
        'telephone_donneur' => $poche_sanguin->telephone_donneur,
        'adresse_donneur' => $poche_sanguin->adresse_donneur,
        'sexe_donneur' => $poche_sanguin->sexe_donneur,
        'date_naiss_donneur' => $poche_sanguin->date_naiss_donneur,
        'numero_identite_national_donneur' => $poche_sanguin->numero_identite_national_donneur,
        'profession_donneur' => $poche_sanguin->profession_donneur,
        'date_prelevement' => $poche_sanguin->date_prelevement,
        // Informations supplémentaires
        'nom_section' => $poche_sanguin->section->nom_section,  // Nom de la section
        'matricule_banque' => $poche_sanguin->section->banqueSang->matricule,  // Matricule de la banque de sang
        'nom_structure' => $poche_sanguin->section->banqueSang->structure->nom_structure,  // Nom de la structure
        ]);
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
            'message' => 'Vous n\'avez pas l\'autorisation de modifier cette poche sanguine.'
        ], 403);
    }
       // Vérifiez que la section existe
       $section = Section::findOrFail($request->section_id);

       // Vérifiez que la section appartient bien à une banque de sang
       $banqueSang = $section->banqueSang;
   
       if (!$banqueSang) {
           return response()->json(['error' => 'La section sélectionnée n\'est pas liée à une banque de sang.'], 403);
       }
   
       // Vérifiez que la banque de sang appartient à la structure de l'utilisateur
       if ($banqueSang->structure->user_id !== $user->id) {
           return response()->json(['error' => 'Vous ne pouvez modifier des poches que dans les sections de votre banque de sang.'], 403);
       }
    // Valider les données de la poche sanguin 
    $validator = validator(
        $request->all(),
        [
            'numero_poche' => ['required', 'string', 'max:255'],
            'groupe_sanguin' => ['required', 'in:A+,A-,B+,B-,O+,O-,AB+,AB-'],
            'nom_donneur' => ['required', 'string', 'max:255'],
            'prenom_donneur' => ['required', 'string', 'max:255'],
            'telephone_donneur' => ['required', 'string', 'regex:/^\d{2}\s?\d{3}\s?\d{2}\s?\d{2}$/'], // Unicité du téléphone parmi les poches de sang.
            'adresse_donneur' => ['required', 'string', 'max:255'],
            'sexe_donneur' => ['required', 'string', 'in:M,F'], // Vous pouvez adapter la validation 'in' selon les valeurs possibles dans votre application.
            'date_naiss_donneur' => ['required', 'date', 'before:today'], // La date de naissance doit être une date valide avant aujourd'hui.
            'numero_identite_national_donneur' => ['required', 'string'], // Unicité de l'identité nationale.
            'profession_donneur' => ['required', 'string', 'max:255'],
            'date_prelevement' => ['required', 'date'], // Date valide pour le prélèvement.
            'section_id' => ['required', 'exists:sections,id'], // Vérifie que la section existe dans la table sections.

        ]
    );
        // Si les données ne sont pas valides, renvoyer les erreurs
        if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 422);
        }
     // Vérifier que le groupe sanguin correspond au nom de la section
     if (strcasecmp(trim($request->groupe_sanguin), trim($section->nom_section)) !== 0) {
        return response()->json([
            'error' => 'Le groupe sanguin ne correspond pas au nom de la section.'
        ], 403);
    }
        // Mettre à jour la poche
    $poche_sanguin->update($request->only('numero_poche', 'groupe_sanguin', 'nom_donneur', 'prenom_donneur', 'telephone_donneur',
        'adresse_donneur', 'sexe_donneur', 'date_naiss_donneur',
        'numero_identite_national_donneur',
        'profession_donneur', 'date_prelevement', 'section_id'));

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
            'message' => 'Vous n\'avez pas l\'autorisation de supprimer cette poche sanguine.'
        ], 403);
    }
     // Vérifiez que la section appartient à la banque de sang de l'utilisateur
     if ($poche_sanguin->section->banqueSang->structure->user_id !== $user->id) {
        return response()->json(['error' => 'Vous ne pouvez supprimer des poches que dans les sections de votre banque de sang.'], 403);
    }
     // Si toutes les conditions sont remplies, supprimez la poche 

     $poche_sanguin->delete();

     return response()->json([
         'status' => true,
         'message' => 'Poche sanguin supprimée avec succès'
     ]);
    }
}
