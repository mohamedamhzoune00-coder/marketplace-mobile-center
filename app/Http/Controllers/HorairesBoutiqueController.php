<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HorairesBoutique;

class HorairesBoutiqueController extends Controller
{
    // عرض جميع المواعيد
    public function index()
    {
        $this->authorize('viewAny', HorairesBoutique::class);

        return HorairesBoutique::paginate(10);
    }
    // إضافة موعد جديد
    public function store(Request $request)
    {
        $this->authorize('create', HorairesBoutique::class);

        $request->validate([
            'boutique_id' => 'required|exists:boutiques,id',
            'jour' => 'required|string|max:20',
            'heure_ouverture' => 'nullable',
            'heure_fermeture' => 'nullable',
            'ferme' => 'boolean',
        ]);

        $horaire = HorairesBoutique::create([
            'boutique_id' => $request->boutique_id,
            'jour' => $request->jour,
            'heure_ouverture' => $request->heure_ouverture,
            'heure_fermeture' => $request->heure_fermeture,
            'ferme' => $request->ferme ?? false,
        ]);

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

        $this->authorize('view', $horaire);

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

        $this->authorize('update', $horaire);

        $request->validate([
            'jour' => 'sometimes|string|max:20',
            'heure_ouverture' => 'nullable',
            'heure_fermeture' => 'nullable',
            'ferme' => 'boolean',
        ]);

        $horaire->jour = $request->jour ?? $horaire->jour;
        $horaire->heure_ouverture = $request->heure_ouverture ?? $horaire->heure_ouverture;
        $horaire->heure_fermeture = $request->heure_fermeture ?? $horaire->heure_fermeture;
        $horaire->ferme = $request->ferme ?? $horaire->ferme;

        $horaire->save();

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

        $this->authorize('delete', $horaire);

        $horaire->delete();

        return response()->json([
            'message' => 'Horaire supprimé avec succès'
        ]);
    }
}
