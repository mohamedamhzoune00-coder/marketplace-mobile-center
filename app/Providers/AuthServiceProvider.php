<?php

namespace App\Providers;

use App\Models\Boutique;
use App\Policies\BoutiquePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Produit;
use App\Policies\ProduitPolicy;
use App\Models\ImagesProduit;
use App\Policies\ImagesProduitPolicy;
use App\Models\HorairesBoutique;
use App\Policies\HoraireBoutiquePolicy;
use App\Models\Demande;
use App\Policies\DemandePolicy;
use App\Models\Signalement;
use App\Policies\SignalementPolicy;
use App\Models\JournalAudit;
use App\Policies\JournalAuditPolicy;
use App\Models\Category;
use App\Policies\CategoryPolicy;

class AuthServiceProvider extends ServiceProvider

{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Boutique::class => BoutiquePolicy::class,
        Produit::class => ProduitPolicy::class,
        ImagesProduit::class => ImagesProduitPolicy::class,
        HorairesBoutique::class => HoraireBoutiquePolicy::class,
        Demande::class => DemandePolicy::class,
        Signalement::class => SignalementPolicy::class,
        JournalAudit::class => JournalAuditPolicy::class,
        Category::class => CategoryPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    }
}