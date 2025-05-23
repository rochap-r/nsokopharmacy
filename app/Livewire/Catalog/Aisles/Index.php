<?php

namespace App\Livewire\Catalog\Aisles;

use App\Models\Aisle;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Http\Livewire\Traits\WithTenantContext;

class Index extends Component
{
    use WithTenantContext;

    public $search = '';
    public $showGlobal = true;
    public $showTenant = true;

    public function mount()
    {
        // Vérification des permissions
        abort_unless(Auth::user()->can('catalog.view'), 403);
        // Set le tenant
        $this->setTenant(app('tenant'));
    }

    public function deleteAisle($id)
    {
        // Vérification des permissions
        abort_unless(Auth::user()->can('catalog.delete'), 403);

        try {
            $aisle = Aisle::findOrFail($id);

            // Vérifier si l'emplacement est global et si l'utilisateur a le droit de le modifier
            if ($aisle->is_global && !Auth::user()->can('catalog.manage')) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'Vous n\'avez pas les permissions nécessaires pour supprimer un emplacement global.'
                ]);
                return;
            }

            // Vérifier si l'emplacement est utilisé par des produits ou des inventaires
            if ($aisle->products()->count() > 0) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'Impossible de supprimer cet emplacement car il est associé à des produits.'
                ]);
                return;
            }

            if ($aisle->tenantInventories()->count() > 0) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'Impossible de supprimer cet emplacement car il est associé à des inventaires.'
                ]);
                return;
            }

            // Supprimer l'emplacement
            $aisle->delete();

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Emplacement supprimé avec succès!'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Erreur lors de la suppression de l\'emplacement: ' . $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        // Traitement global des données sans filtrer par tenant_id
        $query = Aisle::query();

        // Filtres par type (global ou tenant)
        if (!$this->showGlobal && !$this->showTenant) {
            // Si les deux sont désactivés, ne rien montrer
            $aisles = collect();
        } else {
            // Filtrer par type (global ou spécifique)
            if (!$this->showGlobal) {
                $query->where('is_global', false);
            }

            if (!$this->showTenant) {
                $query->where('is_global', true);
            }

            // Recherche
            if (!empty($this->search)) {
                $query->where(function($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                      ->orWhere('code', 'like', "%{$this->search}%");
                });
            }

            // Récupérer tous les emplacements sans restriction par tenant
            $aisles = $query->with(['category', 'tenant'])->get();
        }

        return view('livewire.catalog.aisles.index', [
            'aisles' => $aisles,
        ]);
    }
}
