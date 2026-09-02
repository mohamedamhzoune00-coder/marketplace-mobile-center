<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produit;
use App\Models\Boutique;
use App\Services\AuditLogger;
use App\Http\Resources\ProduitResource;

class ProduitController extends Controller
{
    // عرض جميع المنتجات
    public function index()
    {
        $this->authorize('viewAny', Produit::class);

        return ProduitResource::collection(
            Produit::with(['boutique', 'category'])
                ->orderBy('id', 'desc')
                ->paginate(10)
        );
    }

    // إنشاء منتج جديد
    public function store(Request $request)
    {
        // التحقق من الصلاحية
        $this->authorize('create', Produit::class);

        // التحقق من صحة البيانات
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'prix' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'marque' => 'nullable|string|max:255',
            'modele' => 'nullable|string|max:255',
            'disponible' => 'boolean',
        ]);

        // جلب Boutique ديال المستخدم الحالي
        $boutique = Boutique::where('user_id', auth()->id())->first();

        // إلى ما عندوش Boutique
        // machi trusted mn client, kayjbed mn authenticated vendeur
        if (!$boutique) {
            return response()->json([
                'message' => 'Vous n\'avez pas encore de boutique'
            ], 422);
        }

        // إنشاء المنتج
        $produit = Produit::create([
            'boutique_id' => $boutique->id,
            'category_id' => $request->category_id,
            'nom' => $request->nom,
            'description' => $request->description,
            'prix' => $request->prix,
            'stock' => $request->stock,
            'marque' => $request->marque,
            'modele' => $request->modele,
            'disponible' => $request->disponible ?? true,
            'vues' => 0,
        ]);
        AuditLogger::log('create_produit', 'produits', $produit->id, 'Produit créé');
        // إرجاع النتيجة
        return response()->json([
            'message' => 'Produit créé avec succès',
            'data' => new ProduitResource($produit)
        ], 201);
    }

    // عرض منتج واحد
    public function show($id)
    {
        $produit = Produit::with(['boutique', 'category'])->find($id);

        if (!$produit) {
            return response()->json([
                'message' => 'Produit introuvable'
            ], 404);
        }

        $this->authorize('view', $produit);

        return response()->json([
            'data' => new ProduitResource($produit->load(['boutique', 'category', 'images']))
        ]);
    }

    // تعديل منتج
    public function update(Request $request, $id)
    {
        $produit = Produit::find($id);

        if (!$produit) {
            return response()->json([
                'message' => 'Produit introuvable'
            ], 404);
        }

        // التحقق من الصلاحية
        $this->authorize('update', $produit);

        // التحقق من صحة البيانات
        $request->validate([
            'category_id' => 'sometimes|exists:categories,id',
            'nom' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'prix' => 'sometimes|required|numeric|min:0',
            'stock' => 'sometimes|required|integer|min:0',
            'marque' => 'nullable|string|max:255',
            'modele' => 'nullable|string|max:255',
            'disponible' => 'boolean',
        ]);

        // تحديث المنتج
        $produit->category_id = $request->category_id ?? $produit->category_id;
        $produit->nom = $request->nom ?? $produit->nom;
        $produit->description = $request->description ?? $produit->description;
        $produit->prix = $request->prix ?? $produit->prix;
        $produit->stock = $request->stock ?? $produit->stock;
        $produit->marque = $request->marque ?? $produit->marque;
        $produit->modele = $request->modele ?? $produit->modele;
        $produit->disponible = $request->disponible ?? $produit->disponible;

        $produit->save();
        AuditLogger::log('update_produit', 'produits', $produit->id, 'Produit mis à jour');
        return response()->json([
            'message' => 'Produit mis à jour avec succès',
            'data' => new ProduitResource($produit)
        ]);
    }
    // حذف منتج
    public function destroy($id)
    {
        $produit = Produit::find($id);

        if (!$produit) {
            return response()->json([
                'message' => 'Produit introuvable'
            ], 404);
        }

        // التحقق من الصلاحية
        $this->authorize('delete', $produit);
        AuditLogger::log('delete_produit', 'produits', $produit->id, 'Produit supprimé');
        // حذف المنتج
        $produit->delete();

        return response()->json([
            'message' => 'Produit supprimé avec succès'
        ]);
    }
}
