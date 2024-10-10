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
Route::group([
    "middleware" => ["auth"]
], function(){
Route::get("logout", [ApiController::class, "logout"]);
Route::get("refresh", [ApiController::class, "refreshToken"]);
Route::get("profile", [ApiController::class, "profile"]);

// CRUD Structures
Route::apiResource('structures', StructureController::class);

// CRUD Annonces
Route::apiResource('annonces', AnnonceController::class);

// Notifications
Route::get('notifications1', [Notification1Controller::class, 'index']);
Route::get('notifications1/unread', [Notification1Controller::class, 'unread']);
Route::post('notifications1/{id}/read', [Notification1Controller::class, 'markAsRead']);

// Rendez-vous
Route::post('annonces/{annonceId}/inscrire', [RendezVousController::class, 'inscrire']);
Route::patch('/rendez-vous/{id}/annuler', [RendezVousController::class, 'annulerInscription']);
// Valider un don
Route::put('/rendez-vous/{rendezVous}/etat', [RendezVousController::class, 'updateEtat']);

// Banque de sang
Route::apiResource('banque-sangs', BanqueSangController::class);

// Poche sanguine
Route::apiResource('poche-sanguins', PocheSanguinController::class);

// Donneur externe
Route::apiResource('donneur-externes', DonneurExterneController::class);



});




