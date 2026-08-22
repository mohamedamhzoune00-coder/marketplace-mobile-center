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

// Test API
Route::get('/test', function () {
    return 'API fonctionne';
});

// Authentication
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Consultation
// Public
Route::get('/boutiques', [BoutiqueController::class, 'index']);
Route::get('/boutiques/{boutique}', [BoutiqueController::class, 'show']);


Route::apiResource('categories', CategoryController::class)->only([
    'index',
    'show'
]);

Route::apiResource('produits', ProduitController::class)->only([
    'index',
    'show'
]);

// Demandes (Visitor)
Route::post('/demandes', [DemandeController::class, 'store']);
Route::delete('/demandes/{token}/annuler', [DemandeController::class, 'cancel']);

// Signalements (Visitor)
Route::post('/signalements', [SignalementController::class, 'store']);


/*
|--------------------------------------------------------------------------
| Protected Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    // Current user
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);

    // Boutiques
    Route::post('/boutiques', [BoutiqueController::class, 'store']);
    Route::put('/boutiques/{boutique}', [BoutiqueController::class, 'update']);
    Route::patch('/boutiques/{boutique}', [BoutiqueController::class, 'update']);
    Route::delete('/boutiques/{boutique}', [BoutiqueController::class, 'destroy']);

    // Categories
    Route::apiResource('categories', CategoryController::class)->except([
        'index',
        'show'
    ]);

    // Produits
    Route::apiResource('produits', ProduitController::class)->except([
        'index',
        'show'
    ]);

    // Images Produits
    Route::apiResource('images-produits', ImagesProduitController::class);

    // Horaires
    Route::apiResource('horaires-boutiques', HorairesBoutiqueController::class);

    // Demandes
    Route::apiResource('demandes', DemandeController::class)->except([
        'store'
    ]);

    // Signalements
    Route::apiResource('signalements', SignalementController::class)->except([
        'store'
    ]);

    // Journaux Audit
    Route::get('/journaux-audit', [JournalAuditController::class, 'index']);
    Route::get('/journaux-audit/{journalAudit}', [JournalAuditController::class, 'show']);
});
