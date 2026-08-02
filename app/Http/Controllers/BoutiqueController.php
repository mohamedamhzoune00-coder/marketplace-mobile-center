<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Boutique;

class BoutiqueController extends Controller
{
    // عرض جميع البوتيكات
    public function index()
    {
        return Boutique::all();
    }

    // إنشاء بوتيك جديد
    public function store(Request $request)
    {
        // التحقق من صحة البيانات
        $request->validate([
            'user_id'      => 'required|exists:users,id',
            'nom'          => 'required|string|max:255',
            'description'  => 'nullable|string',
            'telephone'    => 'required|string|max:20',
            'email'        => 'nullable|email|max:255',
            'adresse'      => 'required|string|max:255',
            'emplacement'  => 'required|string|max:255',
            'logo'         => 'nullable|string',
            'couverture'   => 'nullable|string',
            'actif'        => 'boolean',
        ]);

        $boutique = Boutique::create($request->all());

        return response()->json([
            'message' => 'Boutique créée avec succès',
            'data' => $boutique
        ], 201);
    }

    // عرض بوتيك واحد
    public function show($id)
    {
        $boutique = Boutique::find($id);

        if (!$boutique) {
            return response()->json([
                'message' => 'Boutique introuvable'
            ], 404);
        }

        return response()->json($boutique);
    }

    // تعديل بوتيك
    public function update(Request $request, $id)
    {
        $boutique = Boutique::find($id);

        if (!$boutique) {
            return response()->json([
                'message' => 'Boutique introuvable'
            ], 404);
        }

        // التحقق من صحة البيانات
        $request->validate([
            'user_id'      => 'sometimes|exists:users,id',
            'nom'          => 'sometimes|required|string|max:255',
            'description'  => 'nullable|string',
            'telephone'    => 'sometimes|required|string|max:20',
            'email'        => 'nullable|email|max:255',
            'adresse'      => 'sometimes|required|string|max:255',
            'emplacement'  => 'sometimes|required|string|max:255',
            'logo'         => 'nullable|string',
            'couverture'   => 'nullable|string',
            'actif'        => 'boolean',
        ]);

        $boutique->update($request->all());

        return response()->json([
            'message' => 'Boutique mise à jour avec succès',
            'data' => $boutique
        ]);
    }

    // حذف بوتيك
    public function destroy($id)
    {
        $boutique = Boutique::find($id);

        if (!$boutique) {
            return response()->json([
                'message' => 'Boutique introuvable'
            ], 404);
        }

        $boutique->delete();

        return response()->json([
            'message' => 'Boutique supprimée avec succès'
        ]);
    }
}