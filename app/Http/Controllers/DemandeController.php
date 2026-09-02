<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Demande;
use Illuminate\Support\Facades\DB;
use App\Models\Produit;
use App\Services\AuditLogger;
use App\Http\Resources\DemandeResource;

class DemandeController extends Controller
{
    // عرض الطلبات: admin كيشوف الكل، vendeur كيشوف غير ديال البوتيك ديالو
    public function index()
    {
        $this->authorize('viewAny', Demande::class);
        $user = auth()->user();

        if ($user->role === 'super_admin') {
            return DemandeResource::collection(
                Demande::with(['produit', 'boutique', 'user'])
                    ->orderBy('id', 'desc')->paginate(10)
            );
        }

        // vendeur ma3ndouch boutique bade7 -> ma3ndouch demandes ychouf
        if (!$user->boutique) {
            return response()->json(['message' => 'Vous n\'avez pas encore de boutique'], 422);
        }

        return DemandeResource::collection(
            Demande::with(['produit', 'boutique', 'user'])
                ->where('boutique_id', $user->boutique->id)
                ->orderBy('id', 'desc')->paginate(10)
        );
    }

    // إنشاء طلب: khass visiteur ykon connecté (auth), user_id kaykhrej mn token, ma tathiqch f input
    public function store(Request $request)
    {
        $this->authorize('create', Demande::class);

        $request->validate([
            'produit_id' => 'required|exists:produits,id',
            'nom_client' => 'required|string|max:255',
            'telephone'  => 'required|string|max:20',
            'email'      => 'nullable|email|max:255',
            'quantite'   => 'required|integer|min:1',
            'message'    => 'nullable|string',
        ]);

        // njibo produit bach n3rfo l boutique_id (ma nthiqch fih men client)
        $produit = Produit::findOrFail($request->produit_id);

        // n verifiw: produit disponible, boutique active, stock kafi
        if (!$produit->disponible) {
            return response()->json(['message' => 'Ce produit n\'est plus disponible.'], 422);
        }
        if (!$produit->boutique->actif) {
            return response()->json(['message' => 'Cette boutique n\'est plus active.'], 422);
        }
        if ($request->quantite > $produit->stock) {
            return response()->json(['message' => 'Stock insuffisant.'], 422);
        }

        $demande = Demande::create([
            'user_id'     => auth()->id(), // dima mn authenticated user, machi mn request
            'produit_id'  => $produit->id,
            'boutique_id' => $produit->boutique_id,
            'nom_client'  => $request->nom_client,
            'telephone'   => $request->telephone,
            'email'       => $request->email,
            'quantite'    => $request->quantite,
            'message'     => $request->message,
            'statut'      => 'en_attente', // dima kaybda b had l7ala
        ]);

        return response()->json([
            'message' => 'Demande créée avec succès',
            'data' => new DemandeResource($demande->load(['produit', 'boutique'])),
        ], 201);
    }

    // عرض طلب واحد: Policy howa li kayqarr chkoun 3ndo l7a9
    public function show($id)
    {
        $demande = Demande::with(['produit', 'boutique', 'user'])->findOrFail($id);
        $this->authorize('view', $demande);
        return response()->json(['data' => new DemandeResource($demande)]);
    }

    // تعديل الطلب: ghi visiteur (mol demande) o ghi ila mazal en_attente
    public function update(Request $request, $id)
    {
        $demande = Demande::findOrFail($id);
        $this->authorize('update', $demande);

        $request->validate([
            'nom_client' => 'sometimes|string|max:255',
            'telephone'  => 'sometimes|string|max:20',
            'email'      => 'nullable|email|max:255',
            'quantite'   => 'sometimes|integer|min:1',
            'message'    => 'nullable|string',
        ]);

        $demande->nom_client = $request->nom_client ?? $demande->nom_client;
        $demande->telephone  = $request->telephone ?? $demande->telephone;
        $demande->email      = $request->email ?? $demande->email;
        $demande->quantite   = $request->quantite ?? $demande->quantite;
        $demande->message    = $request->message ?? $demande->message;
        $demande->save();

        return response()->json([
            'message' => 'Demande mise à jour avec succès',
            'data' => new DemandeResource($demande->fresh()->load(['produit', 'boutique']))
        ]);
    }

    // حذف الطلب: ghi visiteur (mol demande) o ghi ila mazal en_attente
    public function destroy($id)
    {
        $demande = Demande::findOrFail($id);
        $this->authorize('delete', $demande);
        $demande->delete();
        return response()->json(['message' => 'Demande supprimée avec succès']);
    }

    // vendeur/admin kayqbel talab
    public function accept($id)
    {
        $demande = Demande::findOrFail($id);
        $this->authorize('accept', $demande);

        // transaction: bach machi 2 demandes yban9so f nfs stock f nfs lwe9t
        return DB::transaction(function () use ($demande) {
            $produit = Produit::lockForUpdate()->findOrFail($demande->produit_id);

            if ($produit->stock < $demande->quantite) {
                return response()->json([
                    'message' => 'Stock insuffisant pour accepter cette demande.'
                ], 422);
            }

            $produit->stock -= $demande->quantite;
            $produit->save();

            $demande->statut = 'acceptee';
            $demande->save();

            // n sajlo l'audit ghi mnin l3amaliya nja7at b7al
            AuditLogger::log('accept_demande', 'demandes', $demande->id, 'Demande acceptée');

            return response()->json(['message' => 'Demande acceptée', 'data' => new DemandeResource($demande)]);
        });
    }

    // vendeur/admin kayrfd talab
    public function refuse($id)
    {
        $demande = Demande::findOrFail($id);
        $this->authorize('refuse', $demande);

        $demande->statut = 'refusee';
        $demande->save();

        AuditLogger::log('refuse_demande', 'demandes', $demande->id, 'Demande refusée');

        return response()->json(['message' => 'Demande refusée', 'data' => new DemandeResource($demande)]);
    }
}