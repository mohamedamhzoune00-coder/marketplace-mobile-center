<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Signalement;
use App\Services\AuditLogger;
use App\Http\Resources\SignalementResource;

class SignalementController extends Controller
{
    // عرض جميع البلاغات
    public function index()
    {
        $this->authorize('viewAny', Signalement::class);

        return SignalementResource::collection(Signalement::with(['user', 'produit'])->paginate(10));
    }

    // إنشاء بلاغ جديد
    public function store(Request $request)
    {

        $this->authorize('create', Signalement::class);
        $request->validate([

            'produit_id' => 'required|exists:produits,id',
            'raison' => 'required|string',
        ]);

        $signalement = Signalement::create([
            'user_id' => auth()->id(),
            'produit_id' => $request->produit_id,
            'raison' => $request->raison,
            'statut' => 'en_attente',
        ]);

        return response()->json([
            'message' => 'Signalement créé avec succès',
            'data' => new SignalementResource($signalement->load(['user', 'produit']))
        ], 201);
    }

    // عرض بلاغ واحد
    public function show($id)
    {

        $signalement = Signalement::with(['user', 'produit'])->find($id);

        if (!$signalement) {
            return response()->json([
                'message' => 'Signalement introuvable'
            ], 404);
        }
        $this->authorize('view', $signalement);
        return response()->json(['data' => new SignalementResource($signalement)]);
    }

    // تعديل بلاغ
    public function update(Request $request, $id)
    {
        $signalement = Signalement::find($id);

        if (!$signalement) {
            return response()->json([
                'message' => 'Signalement introuvable'
            ], 404);
        }

        $this->authorize('update', $signalement);

        $request->validate([
            'raison' => 'nullable|string',
            'statut' => 'sometimes|in:en_attente,accepte,refuse',
        ]);

        $signalement->raison = $request->raison ?? $signalement->raison;
        $signalement->statut = $request->statut ?? $signalement->statut;

        $signalement->save();
        AuditLogger::log('update_signalement', 'signalements', $signalement->id, 'Statut: ' . $signalement->statut);
        return response()->json([
            'message' => 'Signalement mis à jour avec succès',
            'data' => new SignalementResource($signalement->load(['user', 'produit']))
        ]);
    }

    // حذف بلاغ
    public function destroy($id)
    {
        $signalement = Signalement::with(['user', 'produit'])->find($id);

        if (!$signalement) {
            return response()->json([
                'message' => 'Signalement introuvable'
            ], 404);
        }
        $this->authorize('delete', $signalement);
        AuditLogger::log('delete_signalement', 'signalements', $signalement->id, 'Signalement supprimé');
        $signalement->delete();

        return response()->json([
            'message' => 'Signalement supprimé avec succès'
        ]);
    }
}
