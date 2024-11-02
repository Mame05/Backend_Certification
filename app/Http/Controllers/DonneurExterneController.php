<?php

namespace App\Http\Controllers;

use App\Models\Banque_sang;
use App\Models\Poche_sanguin;
use App\Models\DonneurExterne;
use App\Models\Structure;
use App\Http\Requests\StoreDonneurExterneRequest;
use App\Http\Requests\UpdateDonneurExterneRequest;


class DonneurExterneController extends Controller
{
    public function index()
    {
        // Récupérer toutes les poches sanguins
        $donneur_externe = DonneurExterne::all();
        return response()->json($donneur_externe);
    }

    public function store(StoreDonneurExterneRequest $request)
    {
        // Vérification des droits d'accès
        if (auth()->user()->role_id !== 2) {
            return response()->json(['error' => 'Vous n\'avez pas l\'autorisation d\'ajouter un donneur externe.'], 403);
        }
        $validator = validator(
            $request->all(),
            [
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'telephone' => ['required', 'string', 'unique:donneur_externes,telephone', 'regex:/^\d{2}\s?\d{3}\s?\d{2}\s?\d{2}$/'],
            'adresse' => ['required', 'string'],
            'sexe' => ['required', 'in:M,F'],
            'date_naiss' => ['required', 'date'],
            'profession' => ['required', 'string', 'max:255'],
            'groupe_sanguin' => ['required', 'in:A+,A-,B+,B-,O+,O-,AB+,AB-'],

            
        ]);
        // Si les données ne sont pas valides, renvoyer les erreurs
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
            }

         // Créer une nouvelle donneur externe
         $donneur_externe = new DonneurExterne();
         $donneur_externe->nom = $request->nom;
         $donneur_externe->prenom = $request->prenom;
         $donneur_externe->telephone = $request->telephone;
         $donneur_externe->adresse = $request->adresse;
         $donneur_externe->sexe = $request->sexe;
         $donneur_externe->date_naiss = $request->date_naiss;
         $donneur_externe->profession = $request->profession;
         $donneur_externe->groupe_sanguin = $request->groupe_sanguin;
         $donneur_externe->save();

        return response()->json([
            'status' => true,
            'message' => 'Donneur externe créé avec succès!',
            'data' => $donneur_externe
        ]);
    }

    public function show(DonneurExterne $donneur_externe)
    {
        return response()->json([
            'nom' => $donneur_externe->nom,
            'prenom' => $donneur_externe->prenom,
            'telephone' => $donneur_externe->telephone,
            'adresse' => $donneur_externe->adresse,
           'sexe' => $donneur_externe->sexe,
            'date_naiss' => $donneur_externe->date_naiss,
            'profession' => $donneur_externe->profession,
            'groupe_sanguin' => $donneur_externe->groupe_sanguin,
        ]);
    }

    public function update(UpdateDonneurExterneRequest $request, DonneurExterne $donneur_externe)
    {
        // Vérification des droits d'accès
        if (auth()->user()->role_id !== 2) {
            return response()->json(['message' => 'Vous n\'avez pas l\'autorisation de modifier cette donneur externe.'], 403);
        }

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
        ]);
        // Si les données ne sont pas valides, renvoyer les erreurs
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
            }

        $donneur_externe->update($request->only('nom', 'prenom', 'telephone', 'address', 'sexe', 'date_naiss', 'profession', 'groupe_sanguin'));

        return response()->json([
            'status' => true,
            'message' => 'Donneur externe mis à jour avec succès!',
            'data' => $donneur_externe
        ]);
    }

    public function destroy(DonneurExterne $donneur_externe)
    {
        // Vérification des droits d'accès
        if (auth()->user()->role_id !== 2) {
            return response()->json(['message' => 'Vous n\'avez pas l\'autorisation de supprimer cette donneur externe.'], 403);
        }
        $donneur_externe->delete();

        return response()->json([
            'status' => true,
            'message' => 'Donneur externe supprimé avec succès!'
        ]);
    }

    public function getDonneursParStructure()
    {
        $user = auth()->user();
   
        // Vérifiez si l'utilisateur a le rôle approprié (role_id = 2)
        if ($user->role_id !== 2) {
            return response()->json(['error' => 'Vous n\'avez pas l\'autorisation de voir les donneurs externes.'], 403);
        }
    
        // Récupérer la structure de l'utilisateur
        $structure = Structure::where('user_id', $user->id)->first();
        if (!$structure) {
            return response()->json(['error' => 'Aucune structure associée trouvée pour cet utilisateur.'], 404);
        }
    
        // Récupérer les donneurs externes associés à cette structure, en incluant le nombre de dons et la date du dernier don
        $donneurs = $structure->donneursExternes()->get()->map(function ($donneur) {
            return [
                'id' => $donneur->id,
                'nom' => $donneur->nom,
                'prenom' => $donneur->prenom,
                'telephone' => $donneur->telephone,
                'nombre_dons' => $donneur->pivot->nombre_dons,
                'dernier_don_date' => Poche_sanguin::where('donneur_externe_id', $donneur->id)
                    ->orderBy('date_prelevement', 'desc')
                    ->value('date_prelevement'),
            ];
        });
    
        // Retourner le résultat
        return response()->json([
            'status' => true,
            'donneurs' => $donneurs,
        ]);
    }

}
