<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Demande;
use App\Models\Produit;

class DemandeController extends Controller
{
    // عرض جميع الطلبات
    public function index()
    {
        return Demande::with(['produit', 'boutique'])->get();
    }

    // إنشاء طلب جديد
    public function store(Request $request)
    {
        $request->validate([
            'produit_id' => 'required|exists:produits,id',
            'nom_client' => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'quantite' => 'required|integer|min:1',
            'message' => 'nullable|string',
            'statut' => 'in:en_attente,acceptee,refusee',
        ]);

        // جلب المنتج
        $produit = Produit::findOrFail($request->produit_id);

        // البيانات المرسلة
        $data = $request->all();

        // جلب البوتيك تلقائياً من المنتج
        $data['boutique_id'] = $produit->boutique_id;

        // إنشاء الطلب
        $demande = Demande::create($data);

        return response()->json([
            'message' => 'Demande créée avec succès',
            'data' => $demande->load(['produit', 'boutique'])
        ], 201);
    }

    // عرض طلب واحد
    public function show($id)
    {
        $demande = Demande::with(['produit', 'boutique'])->find($id);

        if (!$demande) {
            return response()->json([
                'message' => 'Demande introuvable'
            ], 404);
        }

        return response()->json($demande);
    }

    // تعديل طلب
    public function update(Request $request, $id)
    {
        $demande = Demande::find($id);

        if (!$demande) {
            return response()->json([
                'message' => 'Demande introuvable'
            ], 404);
        }

        $request->validate([
            'produit_id' => 'sometimes|exists:produits,id',
            'nom_client' => 'sometimes|string|max:255',
            'telephone' => 'sometimes|string|max:20',
            'email' => 'nullable|email|max:255',
            'quantite' => 'sometimes|integer|min:1',
            'message' => 'nullable|string',
            'statut' => 'sometimes|in:en_attente,acceptee,refusee',
        ]);

        $data = $request->all();

        // إذا تبدل المنتج، تبدل البوتيك تلقائياً
        if ($request->has('produit_id')) {
            $produit = Produit::findOrFail($request->produit_id);
            $data['boutique_id'] = $produit->boutique_id;
        }

        $demande->update($data);

        return response()->json([
            'message' => 'Demande mise à jour avec succès',
            'data' => $demande->fresh()->load(['produit', 'boutique'])
        ]);
    }

    // حذف طلب
    public function destroy($id)
    {
        $demande = Demande::find($id);

        if (!$demande) {
            return response()->json([
                'message' => 'Demande introuvable'
            ], 404);
        }

        $demande->delete();

        return response()->json([
            'message' => 'Demande supprimée avec succès'
        ]);
    }
}