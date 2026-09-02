<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ImagesProduit;
use App\Models\Produit;
use App\Http\Resources\ImageResource;

class ImagesProduitController extends Controller
{
    public function index()
    {
        return ImageResource::collection(ImagesProduit::with('produit')->paginate(20));
    }

    // إضافة صورة جديدة (upload 7a9i9i)
    public function store(Request $request)
    {
        $this->authorize('create', ImagesProduit::class);

        $request->validate([
            'produit_id' => 'required|exists:produits,id',
            'image'      => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'principale' => 'boolean',
            'ordre'      => 'integer|min:0',
        ]);

        $produit = Produit::findOrFail($request->produit_id);

        // n verifiw l ownership: produit -> boutique -> user
        if (auth()->user()->role !== 'super_admin' && $produit->boutique->user_id !== auth()->id()) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        // save l image f storage/app/public/products
        $path = $request->file('image')->store('products', 'public');

        $principale = $request->boolean('principale');

        // ila jdida hiya principale, khass les others ywliw false
        if ($principale) {
            ImagesProduit::where('produit_id', $produit->id)->update(['principale' => false]);
        }

        $image = ImagesProduit::create([
            'produit_id' => $produit->id,
            'chemin'     => $path,
            'principale' => $principale,
            'ordre'      => $request->ordre ?? 0,
        ]);

        return response()->json([
            'message' => 'Image ajoutée avec succès',
            'data' => new ImageResource($image)
        ], 201);
    }

    public function show($id)
    {
        $image = ImagesProduit::with('produit')->find($id);

        if (!$image) {
            return response()->json(['message' => 'Image introuvable'], 404);
        }

        return response()->json(['data' => new ImageResource($image)]);
    }

    // تعديل صورة (تبديل الملف اختياري)
    public function update(Request $request, $id)
    {
        $image = ImagesProduit::with('produit')->find($id);

        if (!$image) {
            return response()->json(['message' => 'Image introuvable'], 404);
        }

        $this->authorize('update', $image);

        $request->validate([
            'image'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'principale' => 'boolean',
            'ordre'      => 'integer|min:0',
        ]);

        // ila jab image jdida, khass n7ido lqdima o nzido jdida
        if ($request->hasFile('image')) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($image->chemin);
            $image->chemin = $request->file('image')->store('products', 'public');
        }

        if ($request->has('principale') && $request->boolean('principale')) {
            ImagesProduit::where('produit_id', $image->produit_id)
                ->where('id', '!=', $image->id)
                ->update(['principale' => false]);
            $image->principale = true;
        } elseif ($request->has('principale')) {
            $image->principale = false;
        }

        $image->ordre = $request->ordre ?? $image->ordre;
        $image->save();

        return response()->json([
            'message' => 'Image mise à jour avec succès',
            'data' => new ImageResource($image)
        ]);
    }

    // حذف صورة (كتحيد الملف من storage بحال)
    public function destroy($id)
    {
        $image = ImagesProduit::find($id);

        if (!$image) {
            return response()->json(['message' => 'Image introuvable'], 404);
        }

        $this->authorize('delete', $image);

        \Illuminate\Support\Facades\Storage::disk('public')->delete($image->chemin);
        $image->delete();

        return response()->json(['message' => 'Image supprimée avec succès']);
    }
}
