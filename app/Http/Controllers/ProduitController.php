<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produit;

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
            'boutique_id' => 'required|exists:boutiques,id',
            'category_id' => 'required|exists:categories,id',
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'prix' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'marque' => 'nullable|string|max:255',
            'modele' => 'nullable|string|max:255',
            'disponible' => 'boolean',
            'vues' => 'integer|min:0',
        ]);

        // إنشاء المنتج
        $produit = Produit::create($request->all());

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
            'boutique_id' => 'sometimes|exists:boutiques,id',
            'category_id' => 'sometimes|exists:categories,id',
            'nom' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'prix' => 'sometimes|required|numeric|min:0',
            'stock' => 'sometimes|required|integer|min:0',
            'marque' => 'nullable|string|max:255',
            'modele' => 'nullable|string|max:255',
            'disponible' => 'boolean',
            'vues' => 'integer|min:0',
        ]);

        // تحديث المنتج
        $produit->update($request->all());

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