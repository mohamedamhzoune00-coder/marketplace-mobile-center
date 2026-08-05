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
        // التحقق من الصلاحية
        $this->authorize('create', ImagesProduit::class);

        // التحقق من صحة البيانات
        $request->validate([
            'produit_id' => 'required|exists:produits,id',
            'chemin' => 'required|string',
            'principale' => 'boolean',
            'ordre' => 'integer|min:0',
        ]);

        // إنشاء الصورة
        $image = ImagesProduit::create([
            'produit_id' => $request->produit_id,
            'chemin' => $request->chemin,
            'principale' => $request->principale ?? false,
            'ordre' => $request->ordre ?? 0,
        ]);

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

        // التحقق من الصلاحية
        $this->authorize('update', $image);

        // التحقق من صحة البيانات
        $request->validate([
            'chemin' => 'nullable|string',
            'principale' => 'boolean',
            'ordre' => 'integer|min:0',
        ]);

        // تحديث الصورة
        $image->update([
            'chemin' => $request->chemin ?? $image->chemin,
            'principale' => $request->principale ?? $image->principale,
            'ordre' => $request->ordre ?? $image->ordre,
        ]);

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
        $this->authorize('delete', $image);
        $image->delete();

        return response()->json([
            'message' => 'Image supprimée avec succès'
        ]);
    }
}
