<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    // عرض جميع التصنيفات
public function index()
{
    return Category::all();
}
// إنشاء تصنيف جديد
public function store(Request $request)
{
    // التحقق من صحة البيانات
    $request->validate([
        'nom' => 'required|string|max:255',
        'description' => 'nullable|string',
    ]);

    // إنشاء التصنيف
    $category = Category::create($request->all());

    // إرجاع النتيجة
    return response()->json([
        'message' => 'Catégorie créée avec succès',
        'data' => $category
    ], 201);
}
// عرض تصنيف واحد
public function show($id)
{
    // البحث عن التصنيف
    $category = Category::find($id);

    // إذا ما لقيناهش
    if (!$category) {
        return response()->json([
            'message' => 'Catégorie introuvable'
        ], 404);
    }

    // إرجاع التصنيف
    return response()->json($category);
}
// تعديل تصنيف
public function update(Request $request, $id)
{
    // البحث عن التصنيف
    $category = Category::find($id);

    // إذا ما لقيناهش
    if (!$category) {
        return response()->json([
            'message' => 'Catégorie introuvable'
        ], 404);
    }

    // التحقق من صحة البيانات
    $request->validate([
        'nom' => 'sometimes|required|string|max:255',
        'description' => 'nullable|string',
    ]);

    // تحديث التصنيف
    $category->update($request->all());

    // إرجاع النتيجة
    return response()->json([
        'message' => 'Catégorie mise à jour avec succès',
        'data' => $category
    ]);
}
// حذف تصنيف
public function destroy($id)
{
    // البحث عن التصنيف
    $category = Category::find($id);

    // إذا ما لقيناهش
    if (!$category) {
        return response()->json([
            'message' => 'Catégorie introuvable'
        ], 404);
    }

    // حذف التصنيف
    $category->delete();

    // إرجاع رسالة نجاح
    return response()->json([
        'message' => 'Catégorie supprimée avec succès'
    ]);
}
}
