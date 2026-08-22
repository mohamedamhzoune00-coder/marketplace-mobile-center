<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Demande;
use App\Models\Produit;

class DemandeController extends Controller
{
    // عرض الطلبات (vendeur/admin فقط)
    public function index()
    {
        $this->authorize('viewAny', Demande::class);

        $user = auth()->user();

        if ($user->role === 'super_admin') {
            return Demande::with(['produit', 'boutique'])
                ->orderBy('id', 'desc')
                ->paginate(10);
        }

        return Demande::with(['produit', 'boutique'])
            ->where('boutique_id', $user->boutique->id)
            ->orderBy('id', 'desc')
            ->paginate(10);
    }

    // إنشاء طلب جديد — مجهول بالكامل، بلا تسجيل دخول
    public function store(Request $request)
    {
        $request->validate([
            'produit_id' => 'required|exists:produits,id',
            'nom_client' => 'required|string|max:255',
            'telephone'  => 'required|string|max:20',
            'email'      => 'nullable|email|max:255',
            'quantite'   => 'required|integer|min:1',
            'message'    => 'nullable|string',
        ]);

        $produit = Produit::findOrFail($request->produit_id);

        $demande = Demande::create([
            'produit_id'  => $produit->id,
            'boutique_id' => $produit->boutique_id,
            'nom_client'  => $request->nom_client,
            'telephone'   => $request->telephone,
            'email'       => $request->email,
            'quantite'    => $request->quantite,
            'message'     => $request->message,
            'statut'      => 'en_attente',
        ]);

        return response()->json([
            'message' => 'Demande créée avec succès. Gardez ce token pour annuler votre demande si besoin.',
            'token'   => $demande->token,
            'data'    => $demande->load(['produit', 'boutique']),
        ], 201);
    }

    // إلغاء الطلب عبر الـ token (بلا تسجيل دخول)
    public function cancel(Request $request, $token)
    {
        $demande = Demande::where('token', $token)->firstOrFail();

        if ($demande->statut !== 'en_attente') {
            return response()->json([
                'message' => 'Impossible d\'annuler : la demande est déjà traitée.'
            ], 422);
        }

        $demande->delete();

        return response()->json([
            'message' => 'Demande annulée avec succès.'
        ]);
    }

    public function show($id)
    {
        $demande = Demande::with(['produit', 'boutique'])->findOrFail($id);
        $this->authorize('view', $demande);
        return response()->json($demande);
    }

    // تعديل حالة الطلب (vendeur/admin: accepter/refuser)
    public function update(Request $request, $id)
    {
        $demande = Demande::findOrFail($id);
        $this->authorize('update', $demande);

        $request->validate([
            'statut' => 'required|in:en_attente,acceptee,refusee',
        ]);

        $demande->statut = $request->statut;
        $demande->save();

        return response()->json([
            'message' => 'Demande mise à jour avec succès',
            'data' => $demande->fresh()->load(['produit', 'boutique'])
        ]);
    }

    public function destroy($id)
    {
        $demande = Demande::findOrFail($id);
        $this->authorize('delete', $demande);
        $demande->delete();

        return response()->json(['message' => 'Demande supprimée avec succès']);
    }
}