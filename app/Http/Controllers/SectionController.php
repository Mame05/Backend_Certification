<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\Structure;
use App\Http\Requests\StoreSectionRequest;
use App\Http\Requests\UpdateSectionRequest;

class SectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Récupérer toutes les sections
        $sections = Section::all();
        return response()->json($sections);
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
    public function store(StoreSectionRequest $request)
    {
        // Obtenez l'utilisateur authentifié
        $user = auth()->user();
        // Vérifiez si l'utilisateur a le rôle approprié (role_id = 2)
        if ($user->role_id !== 2) {
        return response()->json(['error' => 'Vous n\'avez pas l\'autorisation d\'ajouter une section.'], 403);
       }
       
       // Récupérez la structure associée à l'utilisateur
    $structure = Structure::where('user_id', $user->id)->with('banqueSangs')->first();

    // Vérifiez si la structure existe
    if (!$structure) {
        return response()->json(['error' => 'Aucune structure associée à cet utilisateur.'], 404);
    }

    // Vérifiez si la structure a des banques de sang associées
    if (!$structure->banqueSangs || $structure->banqueSangs->isEmpty()) {
        return response()->json(['error' => 'Aucune banque de sang associée à cette structure.'], 404);
    }
    // Vérifiez si la banque de sang appartient à la structure de l'utilisateur
    if (!in_array($request->banque_sang_id, $structure->banqueSangs->pluck('id')->toArray())) {
        return response()->json(['error' => 'Vous ne pouvez ajouter une section que dans vos propres banques de sang.'], 403);
    }
       
       // Valider les données de la section de la banque de sang
       $validator = validator(
       $request->all(),
       [
           'nom_section' => ['required', 'in:A+,A-,B+,B-,O+,O-,AB+,AB-'],
           'banque_sang_id' => ['required', 'exists:banque_sangs,id'],           
       ]
   );  
     
       // Si les données ne sont pas valides, renvoyer les erreurs
       if ($validator->fails()) {
       return response()->json(['error' => $validator->errors()], 422);
       }
       
       
     // Créer une nouvelle section
     $section = new Section();
     $section->nom_section = $request->nom_section;
     $section->banque_sang_id = $request->banque_sang_id;
     $section->save();

     return response()->json([
        'status' => true,
        'message' => 'Section créée avec succès!.',
        'data' => $section
    ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Section $section)
    {
        // Charger les informations de la banque de sang et de la structure associée
        $banqueSang = $section->banqueSang; // Relation vers la banque de sang
        $structure = $banqueSang->structure; // Relation vers la structure via la banque de sang
        return response()->json([
            'nom_section' => $section->nom_section,
            'banque_sang_id' => [
                'id' => $banqueSang->id,
                'matricule' => $banqueSang->matricule, // Nom de la banque de sang
            ],
            'structure' => [
            'id' => $structure->id,
            'nom_structure' => $structure->nom_structure, // Nom de la structure
        ]
        ]
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Section $section)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSectionRequest $request, Section $section)
    {
        // Obtenez l'utilisateur authentifié
        $user = auth()->user();

        // Vérifiez si l'utilisateur a le rôle approprié (role_id = 2)
        if ($user->role_id !== 2) {
        return response()->json([
            'status' => false,
            'message' => 'Vous n\'avez pas l\'autorisation de modifier cette section.'
        ], 403);
    }
        // Récupérez la structure associée à l'utilisateur
        $structure = Structure::where('user_id', $user->id)->first(); // Récupérer la structure par l'user_id

        // Vérifiez si la structure existe
        if (!$structure) {
        return response()->json(['error' => 'Aucune structure associée à cet utilisateur.'], 404);
    }

    // Vérifiez si la section appartient à la banque de sang l'utilisateur authentifié
    if ($section->banqueSang->structure_id !== $structure->id) {
        return response()->json(['error' => 'Vous ne pouvez modifier que les sections de vos propres banques de sang.'], 403);
    }

    // Vérifiez si la structure a des banques de sang associées
    if (!$structure->banqueSangs || $structure->banqueSangs->isEmpty()) {
        return response()->json(['error' => 'Aucune banque de sang associée à cette structure.'], 404);
    }
    // Vérifiez si la banque de sang appartient à la structure de l'utilisateur
    if (!in_array($request->banque_sang_id, $structure->banqueSangs->pluck('id')->toArray())) {
        return response()->json(['error' => 'Vous ne pouvez changer l\'emplacement d\'une section que dans vos propres banques de sang.'], 403);
    }
    // Valider les données de la section de la banque de sang
    $validator = validator(
        $request->all(),
        [
            'nom_section' => ['required', 'in:A+,A-,B+,B-,O+,O-,AB+,AB-'],
            'banque_sang_id' => ['required', 'exists:banque_sangs,id'],           
        ]
    );    
        // Si les données ne sont pas valides, renvoyer les erreurs
        if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 422);
        }
         // Mettre à jour la banque
         $section->update($request->only('nom_section', 'banque_sang_id'));

         return response()->json([
             'status' => true,
             'message' => 'Section modifiée.',
             'data' => $section
         ]); 
         
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Section $section)
    {
        // Obtenez l'utilisateur authentifié
        $user = auth()->user();

        // Vérifiez si l'utilisateur a le rôle approprié (role_id = 2)
        if ($user->role_id !== 2) {
        return response()->json([
            'status' => false,
            'message' => 'Vous n\'avez pas l\'autorisation de supprimer cette section.'
        ], 403);
        }
        // Récupérez la structure associée à l'utilisateur
        $structure = Structure::where('user_id', $user->id)->first(); // Récupérer la structure par l'user_id

        // Vérifiez si la structure existe
        if (!$structure) {
        return response()->json(['error' => 'Aucune structure associée à cet utilisateur.'], 404);
    }

    // Vérifiez si la section appartient à l'utilisateur authentifié
    if ($section->banqueSang->structure_id !== $structure->id) {
        return response()->json(['error' => 'Vous ne pouvez supprimer que les sections de vos propres banques de sang.'], 403);
    }
     // Si toutes les conditions sont remplies, supprimez    l'annonce       
     $section->delete();

     return response()->json([
         'status' => true,
         'message' => 'Section supprimée avec succès'
     ]);
    }
}
