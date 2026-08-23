<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Demande;
use App\Models\Produit;

class DemandeController extends Controller
{
    // عرض الطلبات: admin كيشوف الكل، vendeur كيشوف غير ديال البوتيك ديالو
    public function index()
    {
        $this->authorize('viewAny', Demande::class);
        $user = auth()->user();

        if ($user->role === 'super_admin') {
            return Demande::with(['produit', 'boutique', 'user'])
                ->orderBy('id', 'desc')->paginate(10);
        }

        // vendeur ma3ndouch boutique bade7 -> ma3ndouch demandes ychouf
        if (!$user->boutique) {
            return response()->json(['message' => 'Vous n\'avez pas encore de boutique'], 422);
        }

        return Demande::with(['produit', 'boutique', 'user'])
            ->where('boutique_id', $user->boutique->id)
            ->orderBy('id', 'desc')->paginate(10);
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
            'data' => $demande->load(['produit', 'boutique']),
        ], 201);
    }

    // عرض طلب واحد: Policy howa li kayqarr chkoun 3ndo l7a9
    public function show($id)
    {
        $demande = Demande::with(['produit', 'boutique', 'user'])->findOrFail($id);
        $this->authorize('view', $demande);
        return response()->json($demande);
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
            'data' => $demande->fresh()->load(['produit', 'boutique'])
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
        $demande->statut = 'acceptee';
        $demande->save();
        return response()->json(['message' => 'Demande acceptée', 'data' => $demande]);
    }

    // vendeur/admin kayrfd talab
    public function refuse($id)
    {
        $demande = Demande::findOrFail($id);
        $this->authorize('refuse', $demande);
        $demande->statut = 'refusee';
        $demande->save();
        return response()->json(['message' => 'Demande refusée', 'data' => $demande]);
    }
}