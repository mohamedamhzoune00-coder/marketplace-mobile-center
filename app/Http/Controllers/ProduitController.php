<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produit;
use App\Models\Boutique;

class ProduitController extends Controller
{
    // عرض جميع المنتجات
    public function index()
    {
        return Produit::all();
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
        if (!$boutique) {
            return response()->json([
                'message' => 'Vous devez créer une boutique avant d’ajouter un produit.'
            ], 403);
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

        // إرجاع النتيجة
        return response()->json([
            'message' => 'Produit créé avec succès',
            'data' => $produit
        ], 201);
    }

    // عرض منتج واحد
    public function show($id)
    {
        $produit = Produit::find($id);

        if (!$produit) {
            return response()->json([
                'message' => 'Produit introuvable'
            ], 404);
        }

        return response()->json($produit);
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
            //'boutique_id' => 'sometimes|exists:boutiques,id',
            'category_id' => 'sometimes|exists:categories,id',
            'nom' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'prix' => 'sometimes|required|numeric|min:0',
            'stock' => 'sometimes|required|integer|min:0',
            'marque' => 'nullable|string|max:255',
            'modele' => 'nullable|string|max:255',
            'disponible' => 'boolean',
           // 'vues' => 'integer|min:0',
        ]);

        // تحديث المنتج
        $produit->update([
            'category_id' => $request->category_id ?? $produit->category_id,
            'nom' => $request->nom ?? $produit->nom,
            'description' => $request->description ?? $produit->description,
            'prix' => $request->prix ?? $produit->prix,
            'stock' => $request->stock ?? $produit->stock,
            'marque' => $request->marque ?? $produit->marque,
            'modele' => $request->modele ?? $produit->modele,
            'disponible' => $request->disponible ?? $produit->disponible,
        ]);

        return response()->json([
            'message' => 'Produit mis à jour avec succès',
            'data' => $produit
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

        // حذف المنتج
        $produit->delete();

        return response()->json([
            'message' => 'Produit supprimé avec succès'
        ]);
    }
}
