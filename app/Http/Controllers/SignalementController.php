<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Signalement;

class SignalementController extends Controller
{
    // عرض جميع البلاغات
    public function index()
    {
        return Signalement::with(['user', 'produit'])->get();
    }

    // إنشاء بلاغ جديد
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'produit_id' => 'required|exists:produits,id',
            'raison' => 'required|string',
            'statut' => 'in:en_attente,accepte,refuse',
        ]);

        $signalement = Signalement::create($request->all());

        return response()->json([
            'message' => 'Signalement créé avec succès',
            'data' => $signalement->load(['user', 'produit'])
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

        return response()->json($signalement);
    }

    // تعديل بلاغ
    public function update(Request $request, $id)
    {
        $signalement = Signalement::with(['user', 'produit'])->find($id);

        if (!$signalement) {
            return response()->json([
                'message' => 'Signalement introuvable'
            ], 404);
        }

        $request->validate([
            'user_id' => 'sometimes|exists:users,id',
            'produit_id' => 'sometimes|exists:produits,id',
            'raison' => 'nullable|string',
            'statut' => 'sometimes|in:en_attente,accepte,refuse',
        ]);

        $signalement->update($request->all());

        return response()->json([
            'message' => 'Signalement mis à jour avec succès',
            'data' => $signalement->fresh()->load(['user', 'produit'])
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

        $signalement->delete();

        return response()->json([
            'message' => 'Signalement supprimé avec succès'
        ]);
    }
}
