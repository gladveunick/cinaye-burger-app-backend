<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BurgerController;
use App\Http\Controllers\CommandeController;
use App\Models\Commande;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout']);


// Routes accessibles uniquement par les gestionnaires authentifiés


// Routes pour les burgers

Route::post('burgers', [BurgerController::class, 'store']);
Route::put('burgers/{id}', [BurgerController::class, 'update']);

// Route::apiResource('burgers', BurgerController::class);

Route::post('burgers/{id}/archive', [BurgerController::class, 'archive']);
Route::post('burgers/{id}/restore', [BurgerController::class, 'restore']);
Route::get('/test-burger-list', [BurgerController::class, 'burgerList']);

// Routes pour les commandes
Route::get('/commandes', [CommandeController::class, 'index']);
Route::get('/commandes/{id}', [CommandeController::class, 'show']);
Route::put('/commandes/{id}', [CommandeController::class, 'update']);
Route::post('/commandes/{id}/annuler', [CommandeController::class, 'annuler']);
Route::post('/commandes/{id}/terminer', [CommandeController::class, 'terminer']);
Route::post('/commandes/{id}/payer', [CommandeController::class, 'payer']);

// Routes pour les statistiques journalières
Route::get('/statistiques-journalieres', [CommandeController::class, 'statistiquesJournalieres']);

// Routes pour les commandes en cours
Route::get('/en-cours', [CommandeController::class, 'commandesEnCours']);

// Route commande annuler
Route::get('/annuler', [CommandeController::class, 'commandesAnnuler']);

// Route commande valider
Route::get('/valider', [CommandeController::class, 'commandesValider']);

// Route pour filtrer les commandes
Route::get('/filtrer-commandes', [CommandeController::class, 'filtrerCommandes']);

// Route pour le CA
Route::get('/monthly-revenue', [CommandeController::class, 'getMonthlyRevenue']);



//  Route publique 
Route::get('burgers', [BurgerController::class, 'index']);
Route::get('burgers/{id}', [BurgerController::class, 'show']);
Route::post('/commandes', [CommandeController::class, 'store']);