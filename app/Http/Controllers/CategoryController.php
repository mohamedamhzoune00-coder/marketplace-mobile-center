<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    // عرض جميع التصنيفات
    public function index()
    {
        $this->authorize('viewAny', Category::class);

        return Category::orderBy('id', 'desc')->paginate(10);
    }

    // إنشاء تصنيف جديد
   public function store(Request $request)
{
    $this->authorize('create', Category::class);

    $request->validate([
        'nom' => 'required|string|max:255',
        'description' => 'nullable|string',
        'parent_id' => 'nullable|exists:categories,id',
        'ordre' => 'nullable|integer',
        'actif' => 'boolean',
    ]);

    $category = Category::create([
        'nom' => $request->nom,
        'description' => $request->description,
        'parent_id' => $request->parent_id,
        'ordre' => $request->ordre ?? 0,
        'actif' => $request->actif ?? true,
    ]);

    return response()->json([
        'message' => 'Catégorie créée avec succès',
        'data' => $category
    ], 201);
}
    // عرض تصنيف واحد
    public function show($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Catégorie introuvable'
            ], 404);
        }

        $this->authorize('view', $category);

        return response()->json($category);
    }

    // تعديل تصنيف
    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Catégorie introuvable'
            ], 404);
        }

        $this->authorize('update', $category);

        $request->validate([
            'nom' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'ordre' => 'nullable|integer',
            'actif' => 'boolean',
        ]);

        $category->nom = $request->nom ?? $category->nom;
        $category->description = $request->description ?? $category->description;
        $category->parent_id = $request->parent_id ?? $category->parent_id;
        $category->ordre = $request->ordre ?? $category->ordre;
        $category->actif = $request->actif ?? $category->actif;

        $category->save();

        return response()->json([
            'message' => 'Catégorie mise à jour avec succès',
            'data' => $category
        ]);
    }

    // حذف تصنيف
    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Catégorie introuvable'
            ], 404);
        }

        $this->authorize('delete', $category);

        $category->delete();

        return response()->json([
            'message' => 'Catégorie supprimée avec succès'
        ]);
    }
}
