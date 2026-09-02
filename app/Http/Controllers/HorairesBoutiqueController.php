<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HorairesBoutique;

class HorairesBoutiqueController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', HorairesBoutique::class);
        return HorairesBoutique::paginate(10);
    }

    // إضافة موعد جديد
    public function store(Request $request)
    {
        $this->authorize('create', HorairesBoutique::class);
        $user = auth()->user();

        $request->validate([
            'jour'            => 'required|string|max:20',
            'heure_ouverture' => 'nullable|date_format:H:i',
            'heure_fermeture' => 'nullable|date_format:H:i',
            'ferme'           => 'boolean',
        ]);

        // admin y9dr ydir boutique_id, vendeur dima min l boutique dyalo
        if ($user->role === 'super_admin' && $request->has('boutique_id')) {
            $boutiqueId = $request->boutique_id;
        } else {
            if (!$user->boutique) {
                return response()->json(['message' => 'Vous n\'avez pas encore de boutique'], 422);
            }
            $boutiqueId = $user->boutique->id;
        }

        $ferme = $request->boolean('ferme');

        if (!$ferme) {
            if (!$request->heure_ouverture || !$request->heure_fermeture) {
                return response()->json([
                    'message' => 'Les heures d\'ouverture et de fermeture sont requises si le magasin n\'est pas fermé.'
                ], 422);
            }
            if ($request->heure_ouverture >= $request->heure_fermeture) {
                return response()->json([
                    'message' => 'L\'heure d\'ouverture doit être avant l\'heure de fermeture.'
                ], 422);
            }
        }

        // manmn3ouch 2 lignes lnfs jour lnfs boutique
        $existant = HorairesBoutique::where('boutique_id', $boutiqueId)
            ->where('jour', $request->jour)
            ->first();
        if ($existant) {
            return response()->json(['message' => 'Un horaire existe déjà pour ce jour.'], 422);
        }

        $horaire = HorairesBoutique::create([
            'boutique_id'     => $boutiqueId,
            'jour'            => $request->jour,
            'heure_ouverture' => $ferme ? null : $request->heure_ouverture,
            'heure_fermeture' => $ferme ? null : $request->heure_fermeture,
            'ferme'           => $ferme,
        ]);

        return response()->json(['message' => 'Horaire créé avec succès', 'data' => $horaire], 201);
    }

    public function show($id)
    {
        $horaire = HorairesBoutique::find($id);

        if (!$horaire) {
            return response()->json(['message' => 'Horaire introuvable'], 404);
        }

        $this->authorize('view', $horaire);
        return response()->json($horaire);
    }

    // تعديل موعد
    public function update(Request $request, $id)
    {
        $horaire = HorairesBoutique::find($id);

        if (!$horaire) {
            return response()->json(['message' => 'Horaire introuvable'], 404);
        }

        $this->authorize('update', $horaire);

        $request->validate([
            'jour'            => 'sometimes|string|max:20',
            'heure_ouverture' => 'nullable|date_format:H:i',
            'heure_fermeture' => 'nullable|date_format:H:i',
            'ferme'           => 'boolean',
        ]);

        $ferme = $request->has('ferme') ? $request->boolean('ferme') : $horaire->ferme;
        $ouverture = $request->heure_ouverture ?? $horaire->heure_ouverture;
        $fermeture = $request->heure_fermeture ?? $horaire->heure_fermeture;

        if (!$ferme) {
            if (!$ouverture || !$fermeture) {
                return response()->json([
                    'message' => 'Les heures d\'ouverture et de fermeture sont requises si le magasin n\'est pas fermé.'
                ], 422);
            }
            if ($ouverture >= $fermeture) {
                return response()->json([
                    'message' => 'L\'heure d\'ouverture doit être avant l\'heure de fermeture.'
                ], 422);
            }
        }

        // manmn3ouch duplicate jour (ghi ila bdlna jour) - manhsabch f rassha
        if ($request->has('jour')) {
            $existant = HorairesBoutique::where('boutique_id', $horaire->boutique_id)
                ->where('jour', $request->jour)
                ->where('id', '!=', $horaire->id)
                ->first();
            if ($existant) {
                return response()->json(['message' => 'Un horaire existe déjà pour ce jour.'], 422);
            }
        }

        $horaire->jour            = $request->jour ?? $horaire->jour;
        $horaire->heure_ouverture = $ferme ? null : $ouverture;
        $horaire->heure_fermeture = $ferme ? null : $fermeture;
        $horaire->ferme           = $ferme;
        $horaire->save();

        return response()->json(['message' => 'Horaire mis à jour avec succès', 'data' => $horaire]);
    }

    public function destroy($id)
    {
        $horaire = HorairesBoutique::find($id);

        if (!$horaire) {
            return response()->json(['message' => 'Horaire introuvable'], 404);
        }

        $this->authorize('delete', $horaire);
        $horaire->delete();

        return response()->json(['message' => 'Horaire supprimé avec succès']);
    }
}