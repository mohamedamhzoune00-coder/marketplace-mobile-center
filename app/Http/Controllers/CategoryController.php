<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Services\AuditLogger;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    // عرض جميع التصنيفات
    public function index()
    {
        $this->authorize('viewAny', Category::class);

        return CategoryResource::collection(Category::orderBy('id', 'desc')->paginate(10));
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
        AuditLogger::log('create_category', 'categories', $category->id, 'Catégorie créée');
        return response()->json([
            'message' => 'Catégorie créée avec succès',
           'data' => new CategoryResource($category)
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

        return response()->json(['data' => new CategoryResource($category)]);
    }

    // تعديل تصنيف
    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Catégorie introuvable'], 404);
        }

        $this->authorize('update', $category);

        $request->validate([
            'nom' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'ordre' => 'nullable|integer',
            'actif' => 'boolean',
        ]);

        // manmn3ouch category tkon parent dyal rassha
        if ($request->has('parent_id') && $request->parent_id == $category->id) {
            return response()->json([
                'message' => 'Une catégorie ne peut pas être son propre parent.'
            ], 422);
        }

        // manmn3ouch circular hierarchy: parent_id li 3tawh ma ykonch wa7d mn les enfants dyal had category
        if ($request->has('parent_id') && $request->parent_id) {
            $enfantsIds = Category::where('parent_id', $category->id)->pluck('id')->toArray();
            if (in_array($request->parent_id, $enfantsIds)) {
                return response()->json([
                    'message' => 'Impossible : cela créerait une hiérarchie circulaire.'
                ], 422);
            }
        }

        $category->nom = $request->nom ?? $category->nom;
        $category->description = $request->description ?? $category->description;
        $category->parent_id = $request->parent_id ?? $category->parent_id;
        $category->ordre = $request->ordre ?? $category->ordre;
        $category->actif = $request->actif ?? $category->actif;

        $category->save();
        AuditLogger::log('update_category', 'categories', $category->id, 'Catégorie modifiée');
        return response()->json([
            'message' => 'Catégorie mise à jour avec succès',
            'data' => new CategoryResource($category)
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
        AuditLogger::log('delete_category', 'categories', $category->id, 'Catégorie supprimée');
        $category->delete();

        return response()->json([
            'message' => 'Catégorie supprimée avec succès'
        ]);
    }
}
