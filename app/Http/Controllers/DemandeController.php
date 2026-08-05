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
        return Demande::with(['produit', 'boutique'])->paginate(10);
    }

    // إنشاء طلب جديد
    public function store(Request $request)
    {
        $this->authorize('create', Demande::class);
        $request->validate([
            'produit_id' => 'required|exists:produits,id',
            'nom_client' => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'quantite' => 'required|integer|min:1',
            'message' => 'nullable|string',
            // 'statut' => 'in:en_attente,acceptee,refusee',
        ]);

        // جلب المنتج
        $produit = Produit::findOrFail($request->produit_id);

        $demande = Demande::create([
            'produit_id'  => $produit->id,
            'boutique_id' => $produit->boutique_id,
            'nom_client'  => $request->nom_client,
            'telephone'   => $request->telephone,
            'email'       => $request->email,
            'quantite'    => $request->quantite,
            'message'     => $request->message,
            'statut'      => 'en_attente',
        ]);

        return response()->json([
            'message' => 'Demande créée avec succès',
            'data' => $demande->load(['produit', 'boutique'])
        ], 201);
    }

    // عرض طلب واحد
    public function show($id)
    {
        $demande = Demande::with(['produit', 'boutique'])->findOrFail($id);

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
        $this->authorize('update', $demande);

        $request->validate([
            'produit_id' => 'sometimes|exists:produits,id',
            'nom_client' => 'sometimes|string|max:255',
            'telephone' => 'sometimes|string|max:20',
            'email' => 'nullable|email|max:255',
            'quantite' => 'sometimes|integer|min:1',
            'message' => 'nullable|string',
            // الزائر ما خاصوش يحدد الحالة
            // الحالة غادي تكون دائما en_attente
        ]);

        if ($request->has('produit_id')) {
            $produit = Produit::findOrFail($request->produit_id);

            $demande->produit_id = $produit->id;
            $demande->boutique_id = $produit->boutique_id;
        }

        $demande->nom_client = $request->nom_client ?? $demande->nom_client;
        $demande->telephone = $request->telephone ?? $demande->telephone;
        $demande->email = $request->email ?? $demande->email;
        $demande->quantite = $request->quantite ?? $demande->quantite;
        $demande->message = $request->message ?? $demande->message;

        $demande->save();

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
        $this->authorize('delete', $demande);
        $demande->delete();

        return response()->json([
            'message' => 'Demande supprimée avec succès'
        ]);
    }
}
