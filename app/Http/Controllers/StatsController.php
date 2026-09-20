<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Boutique;
use App\Models\Produit;
use App\Models\Demande;
use App\Models\Signalement;
use Carbon\Carbon;

class StatsController extends Controller
{
    /**
     * GET /api/stats/dashboard
     * إحصائيات Dashboard للـ super_admin و vendeur
     */
    public function dashboard(Request $request)
    {
        $user = $request->user();
        $isAdmin = $user->role === 'super_admin';
        $boutiqueId = $isAdmin ? null : ($user->boutique->id ?? 0);

        // ============ Chiffre d'Affaires (demandes acceptées) ============
        $caTotal = $this->calcCA(null, $boutiqueId);
        $caLastWeek = $this->calcCA(
            [Carbon::now()->subDays(14), Carbon::now()->subDays(7)],
            $boutiqueId
        );
        $caDelta = $caLastWeek > 0
            ? round((($caTotal - $caLastWeek) / $caLastWeek) * 100, 1)
            : 0;

        // ============ Utilisateurs ============
        if ($isAdmin) {
            $usersTotal = User::count();
        } else {
            $usersTotal = User::whereIn('id', function ($q) use ($boutiqueId) {
                $q->select('user_id')->from('demandes')->where('boutique_id', $boutiqueId);
            })->count();
        }

        // ============ Commandes (demandes en attente) ============
        $commandesQuery = Demande::where('statut', 'en_attente');
        if ($boutiqueId !== null) {
            $commandesQuery->where('boutique_id', $boutiqueId);
        }
        $commandesTotal = $commandesQuery->count();

        // ============ Alertes Stock (produits < 5 unités) ============
        $stockQuery = Produit::where('stock', '<', 5);
        if ($boutiqueId !== null) {
            $stockQuery->where('boutique_id', $boutiqueId);
        }
        $alertesStock = $stockQuery->count();

        // ============ Ventes 7 derniers jours ============
        $ventes7jours = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->startOfDay();
            $nextDay = $date->copy()->addDay();

            $ventes = $this->calcCA([$date, $nextDay], $boutiqueId);

            $ventes7jours[] = [
                'jour'   => $date->translatedFormat('d M'),
                'ventes' => (float) $ventes,
            ];
        }

        // ============ Signalements récents (5 derniers) ============
        $signalementsQuery = Signalement::with(['produit.boutique', 'user'])
            ->orderByDesc('created_at')
            ->limit(5);

        if ($boutiqueId !== null) {
            $signalementsQuery->whereHas('produit', function ($q) use ($boutiqueId) {
                $q->where('boutique_id', $boutiqueId);
            });
        }

        $signalementsRecents = $signalementsQuery->get()->map(function ($s) {
            return [
                'id'       => $s->id,
                'boutique' => $s->produit->boutique->nom ?? '—',
                'type'     => $s->raison ?? '—',
                'statut'   => $this->mapStatut($s->statut),
                'priorite' => 'Moyenne', // ما كاينش priorite فـ DB
            ];
        });

        // ============ Boutiques populaires (Top 5) ============
        $boutiquesQuery = Boutique::with('user')
            ->select('boutiques.*')
            ->selectSub(function ($q) {
                $q->selectRaw('COALESCE(SUM(produits.prix * demandes.quantite), 0)')
                    ->from('demandes')
                    ->join('produits', 'demandes.produit_id', '=', 'produits.id')
                    ->whereColumn('demandes.boutique_id', 'boutiques.id')
                    ->where('demandes.statut', 'acceptee');
            }, 'ventes_total')
            ->orderByDesc('ventes_total')
            ->limit(5);

        if ($boutiqueId !== null) {
            $boutiquesQuery->where('id', $boutiqueId);
        }

        $boutiquesPopulaires = $boutiquesQuery->get()->map(function ($b) {
            return [
                'id'           => $b->id,
                'nom'          => $b->nom,
                'proprietaire' => $b->user->name ?? '—',
                'ventes'       => (float) $b->ventes_total,
                'actif'        => (bool) $b->actif,
            ];
        });

        return response()->json([
            'chiffre_affaires' => [
                'valeur' => (float) $caTotal,
                'delta'  => $caDelta,
            ],
            'utilisateurs' => [
                'total' => $usersTotal,
                'delta' => 0,
            ],
            'commandes' => [
                'total' => $commandesTotal,
                'delta' => 0,
            ],
            'alertes_stock' => [
                'total' => $alertesStock,
            ],
            'ventes_7_jours'       => $ventes7jours,
            'signalements_recents' => $signalementsRecents,
            'boutiques_populaires' => $boutiquesPopulaires,
        ]);
    }

    /**
     * Helper: حساب CA
     */
    private function calcCA($dateRange = null, $boutiqueId = null)
    {
        $q = Demande::where('demandes.statut', 'acceptee')
            ->join('produits', 'demandes.produit_id', '=', 'produits.id')
            ->selectRaw('COALESCE(SUM(produits.prix * demandes.quantite), 0) as total');

        if ($dateRange) {
            $q->whereBetween('demandes.created_at', $dateRange);
        }

        if ($boutiqueId !== null) {
            $q->where('demandes.boutique_id', $boutiqueId);
        }

        return (float) $q->value('total');
    }

    /**
     * Helper: mapping statut signalement → français
     */
    private function mapStatut($statut)
    {
        return match ($statut) {
            'en_attente' => 'Nouveau',
            'accepte'    => 'En cours',
            'refuse'     => 'Résolu',
            default      => 'Nouveau',
        };
    }
}