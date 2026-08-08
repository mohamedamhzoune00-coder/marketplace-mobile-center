<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Boutique;

class BoutiqueController extends Controller
{
    // عرض جميع البوتيكات
    public function index()
    {
          $this->authorize('viewAny', Boutique::class);
        return Boutique::paginate(10);
    }

    // إنشاء بوتيك جديد
    public function store(Request $request)
    {
        // التحقق من الصلاحية
        $this->authorize('create', Boutique::class);

        // التحقق من صحة البيانات
        $request->validate([
            'nom'          => 'required|string|max:255',
            'description'  => 'nullable|string',
            'telephone'    => 'required|string|max:20',
            'email'        => 'nullable|email|max:255',
            'adresse'      => 'required|string|max:255',
            'emplacement'  => 'required|string|max:255',
            'logo'         => 'nullable|string',
            'couverture'   => 'nullable|string',
        ]);

        // إنشاء البوتيك وربطه بالمستخدم الحالي
        $boutique = Boutique::create([
            'user_id'      => auth()->id(),
            'nom'          => $request->nom,
            'description'  => $request->description,
            'telephone'    => $request->telephone,
            'email'        => $request->email,
            'adresse'      => $request->adresse,
            'emplacement'  => $request->emplacement,
            'logo'         => $request->logo,
            'couverture'   => $request->couverture,
            'actif'        => true,
        ]);

        return response()->json([
            'message' => 'Boutique créée avec succès',
            'data'    => $boutique
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

        $this->authorize('view', $boutique);

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

        // التحقق من الصلاحية
        $this->authorize('update', $boutique);

        // التحقق من صحة البيانات
        $request->validate([
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

        $boutique->nom = $request->nom ?? $boutique->nom;
        $boutique->description = $request->description ?? $boutique->description;
        $boutique->telephone = $request->telephone ?? $boutique->telephone;
        $boutique->email = $request->email ?? $boutique->email;
        $boutique->adresse = $request->adresse ?? $boutique->adresse;
        $boutique->emplacement = $request->emplacement ?? $boutique->emplacement;
        $boutique->logo = $request->logo ?? $boutique->logo;
        $boutique->couverture = $request->couverture ?? $boutique->couverture;
        $boutique->actif = $request->actif ?? $boutique->actif;

        $boutique->save();

        return response()->json([
            'message' => 'Boutique mise à jour avec succès',
            'data'    => $boutique
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

        // التحقق من الصلاحية
        $this->authorize('delete', $boutique);

        $boutique->delete();

        return response()->json([
            'message' => 'Boutique supprimée avec succès'
        ]);
    }
}
