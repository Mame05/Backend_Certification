<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnnonceController;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\StructureController;
use App\Http\Controllers\BanqueSangController;
use App\Http\Controllers\RendezVousController;
use App\Http\Controllers\PocheSanguinController;
use App\Http\Controllers\Notification1Controller;
use App\Http\Controllers\DonneurExterneController;



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post("register", [ApiController::class, "register"]);
Route::post("login", [ApiController::class, "login"]);

Route::middleware('auth:api')->group(function () {

Route::get("logout", [ApiController::class, "logout"]);
Route::get("refresh", [ApiController::class, "refreshToken"]);
Route::get("profile", [ApiController::class, "profile"]);
Route::put("updateProfile", [ApiController::class, "updateProfile"]);
Route::get("profileStructure", [ApiController::class, "profileStructure"]);
Route::get("utilisateurs-simples", [ApiController::class, 'getAllUtilisateurSimples']);
// CRUD Structures
Route::apiResource('structures', StructureController::class);

// CRUD Annonces
Route::apiResource('annonces', AnnonceController::class);

// Notifications
Route::get('notifications1', [Notification1Controller::class, 'index']);
Route::get('notifications1/unread', [Notification1Controller::class, 'unread']);
Route::put('notifications1/{id}/read', [Notification1Controller::class, 'markAsRead']);
Route::delete('notifications1/{id}', [Notification1Controller::class, 'destroy']);


// Rendez-vous
Route::post('annonces/{annonceId}/inscrire', [RendezVousController::class, 'inscrire']);
Route::patch('/rendez-vous/{id}/annuler', [RendezVousController::class, 'annulerInscription']);
Route::get('/user/inscriptions', [RendezVousController::class, 'getInscriptions']);// permet d'obtenir les inscriptions d'un utilisateur
Route::delete('/rendez-vous/{id}/supprimer', [RendezVousController::class, 'supprimerHistorique']);
Route::get('/structure/utilisateurs-inscriptions/{structureId?}', [RendezVousController::class, 'getUsersWithCompletedInscriptions']);



// Valider un don
Route::put('/rendez-vous/{rendezVous}/etat', [RendezVousController::class, 'updateEtatAddPoche']);





// Banque de sang
Route::apiResource('banque-sangs', BanqueSangController::class);

// Poche sanguine
Route::apiResource('poche-sanguins', PocheSanguinController::class);
Route::put('/poche-sanguin/{id}', [PocheSanguinController::class, 'updatePoche']);
Route::get('/poches-par-mois', [PocheSanguinController::class, 'getPochesSanguinsParMois']);



// Donneur externe
Route::apiResource('donneur-externes', DonneurExterneController::class);
Route::get('/structure/donneurs/{structureId?}', [ DonneurExterneController::class, 'getDonneursParStructure']);



});



