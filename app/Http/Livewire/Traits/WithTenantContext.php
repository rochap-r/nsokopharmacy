<?php

namespace App\Http\Livewire\Traits;

use App\Models\Tenant;

trait WithTenantContext
{
    /**
     * Propriété pour stocker l'ID du tenant
     */
    public $tenant_id;

    /**
     * Définit le tenant pour le composant
     *
     * @param \App\Models\Tenant $tenant
     * @return void
     */
    public function setTenant(Tenant $tenant)
    {
        $this->tenant_id = $tenant->id;
        app()->instance('tenant', $tenant);
        view()->share('currentTenant', $tenant);
    }

    /**
     * Hook Livewire exécuté après l'hydratation du composant
     */
    public function hydrate()
    {
        $this->hydrateTenant();
    }

    /**
     * Hook Livewire exécuté avant la pagination
     */
    public function updatingPage()
    {
        $this->hydrateTenant();
    }

    /**
     * Hook Livewire exécuté avant la mise à jour du filtre de recherche
     */
    public function updatingSearch()
    {
        $this->hydrateTenant();
        // Réinitialiser la pagination si nécessaire
        if (method_exists($this, 'resetPage')) {
            $this->resetPage();
        }
    }

    /**
     * Restaure le contexte du tenant pour les requêtes Livewire
     */
    protected function hydrateTenant()
    {
        if ($this->tenant_id) {
            $tenant = Tenant::find($this->tenant_id);
            if ($tenant) {
                // Rétablir le contexte du tenant pour cette requête
                app()->instance('tenant', $tenant);
                view()->share('currentTenant', $tenant);
            }
        }
    }

    /**
     * Récupère le tenant actuel
     *
     * @return \App\Models\Tenant|null
     */
    protected function getCurrentTenant()
    {
        return current_tenant() ?? Tenant::find($this->tenant_id);
    }
}
