<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BoutiqueController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\ImagesProduitController;
use App\Http\Controllers\HorairesBoutiqueController;
use App\Http\Controllers\SignalementController;
use App\Http\Controllers\JournalAuditController;
use App\Http\Controllers\DemandeController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Test
Route::get('/test', function () {
    return 'API fonctionne';
});

// Authentication
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Consultation (Visiteur)
Route::apiResource('boutiques', BoutiqueController::class)->only([
    'index',
    'show'
]);

Route::apiResource('categories', CategoryController::class)->only([
    'index',
    'show'
]);

Route::apiResource('produits', ProduitController::class)->only([
    'index',
    'show'
]);

// Le visiteur peut envoyer une demande d'achat
Route::post('/demandes', [DemandeController::class, 'store']);

// Le visiteur peut signaler un produit
Route::post('/signalements', [SignalementController::class, 'store']);

/*
|--------------------------------------------------------------------------
| Protected Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    // Utilisateur connecté
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/me', function (Request $request) {
        return $request->user();
    });

    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);

    // Gestion Boutiques
    Route::apiResource('boutiques', BoutiqueController::class)->except([
        'index',
        'show'
    ]);

    // Gestion Catégories
    Route::apiResource('categories', CategoryController::class)->except([
        'index',
        'show'
    ]);

    // Gestion Produits
    Route::apiResource('produits', ProduitController::class)->except([
        'index',
        'show'
    ]);

    // Images Produits
    Route::apiResource('images-produits', ImagesProduitController::class);

    // Horaires Boutique
    Route::apiResource('horaires-boutiques', HorairesBoutiqueController::class);

    // Journaux d'audit
    Route::apiResource('journaux-audit', JournalAuditController::class);

    // Gestion des demandes
    Route::apiResource('demandes', DemandeController::class)->except([
        'store'
    ]);

    // Gestion des signalements
    Route::apiResource('signalements', SignalementController::class)->except([
        'store'
    ]);
});