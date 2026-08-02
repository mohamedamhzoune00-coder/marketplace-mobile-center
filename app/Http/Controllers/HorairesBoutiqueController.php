<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HorairesBoutique;

class HorairesBoutiqueController extends Controller
{
    // عرض جميع المواعيد
    public function index()
    {
        return HorairesBoutique::all();
    }

    // إضافة موعد جديد
    public function store(Request $request)
    {
        $request->validate([
            'boutique_id' => 'required|exists:boutiques,id',
            'jour' => 'required|string|max:20',
            'heure_ouverture' => 'nullable',
            'heure_fermeture' => 'nullable',
            'ferme' => 'boolean',
        ]);

        $horaire = HorairesBoutique::create($request->all());

        return response()->json([
            'message' => 'Horaire créé avec succès',
            'data' => $horaire
        ], 201);
    }

    // عرض موعد واحد
    public function show($id)
    {
        $horaire = HorairesBoutique::find($id);

        if (!$horaire) {
            return response()->json([
                'message' => 'Horaire introuvable'
            ], 404);
        }

        return response()->json($horaire);
    }

    // تعديل موعد
    public function update(Request $request, $id)
    {
        $horaire = HorairesBoutique::find($id);

        if (!$horaire) {
            return response()->json([
                'message' => 'Horaire introuvable'
            ], 404);
        }

        $request->validate([
            'boutique_id' => 'sometimes|exists:boutiques,id',
            'jour' => 'sometimes|required|string|max:20',
            'heure_ouverture' => 'nullable',
            'heure_fermeture' => 'nullable',
            'ferme' => 'boolean',
        ]);

        $horaire->update($request->all());

        return response()->json([
            'message' => 'Horaire mis à jour avec succès',
            'data' => $horaire
        ]);
    }

    // حذف موعد
    public function destroy($id)
    {
        $horaire = HorairesBoutique::find($id);

        if (!$horaire) {
            return response()->json([
                'message' => 'Horaire introuvable'
            ], 404);
        }

        $horaire->delete();

        return response()->json([
            'message' => 'Horaire supprimé avec succès'
        ]);
    }
}