<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnnonceController;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\StructureController;
use App\Http\Controllers\Notification1Controller;

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
Route::post('structures/{id}/restore', [StructureController::class, "restore"]);

// CRUD Annonces
Route::apiResource('annonces', AnnonceController::class);

// Notifications
Route::get('notifications1', [Notification1Controller::class, 'index']);
Route::get('notifications1/unread', [Notification1Controller::class, 'unread']);
Route::post('notifications1/{id}/read', [Notification1Controller::class, 'markAsRead']);

});



