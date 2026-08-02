<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ImagesProduit;

class ImagesProduitController extends Controller
{
    // عرض جميع الصور
    public function index()
    {
        return ImagesProduit::with('produit')->get();
    }

    // إضافة صورة جديدة
    public function store(Request $request)
    {
        $request->validate([
            'produit_id' => 'required|exists:produits,id',
            'chemin' => 'required|string',
            'principale' => 'boolean',
            'ordre' => 'integer|min:0',
        ]);

        $image = ImagesProduit::create($request->all());

        return response()->json([
            'message' => 'Image ajoutée avec succès',
            'data' => $image
        ], 201);
    }

    // عرض صورة واحدة
    public function show($id)
    {
        $image = ImagesProduit::with('produit')->find($id);

        if (!$image) {
            return response()->json([
                'message' => 'Image introuvable'
            ], 404);
        }

        return response()->json($image);
    }

    // تعديل صورة
    public function update(Request $request, $id)
    {
        $image = ImagesProduit::with('produit')->find($id);

        if (!$image) {
            return response()->json([
                'message' => 'Image introuvable'
            ], 404);
        }

        $request->validate([
            'produit_id' => 'sometimes|exists:produits,id',
            'chemin' => 'nullable|string',
            'principale' => 'boolean',
            'ordre' => 'integer|min:0',
        ]);

        $image->update($request->all());

        return response()->json([
            'message' => 'Image mise à jour avec succès',
            'data' => $image
        ]);
    }

    // حذف صورة
    public function destroy($id)
    {
        $image = ImagesProduit::find($id);

        if (!$image) {
            return response()->json([
                'message' => 'Image introuvable'
            ], 404);
        }

        $image->delete();

        return response()->json([
            'message' => 'Image supprimée avec succès'
        ]);
    }
}