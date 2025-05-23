<?php

namespace App\Livewire\Settings\Roles;

use Livewire\Component;
use Spatie\Permission\Models\Role;
use App\Http\Livewire\Traits\WithToast;
use Illuminate\Support\Facades\Log;

class Permissions extends Component
{
    use WithToast;

    public $roleId;
    public $role;
    
    /**
     * Handle an incoming authentication request.
     */
    public $tenantParam;
    public $tenant_id;  // Propriété publique pour stocker l'ID du tenant

    // Définition du layout à utiliser
    protected $layout = 'components.layouts.app';

    public function mount($tenant = null, $id = null)
    {
        // Récupérer le paramètre tenant depuis la route
        $this->tenantParam = $tenant;

        // Le tenant est disponible via le middleware à ce stade (requête GET initiale)
        $tenant = app('tenant');

        if (!$tenant) {
            return redirect()->route('identification');
        }

        // Stocker l'ID du tenant dans une propriété publique pour les actions Livewire
        $this->tenant_id = $tenant->id;
        
        try {
            $this->roleId = $id;
            // Vérifier que le rôle appartient bien au tenant courant
            $this->role = Role::with('permissions')
                ->where('id', $id)
                ->where('tenant_id', $this->tenant_id)
                ->firstOrFail();
        } catch (\Exception $e) {
            Log::error('Erreur dans Permissions::mount: ' . $e->getMessage());
            $this->error('Erreur lors du chargement du rôle: ' . $e->getMessage());
            // Récupérer le tenant pour la redirection
            $tenantObj = \App\Models\Tenant::findOrFail($this->tenant_id);
            return $this->redirectRoute('tenant.settings.roles.index', ['tenant' => $tenantObj->domain], navigate: true);
        }
    }

    public function backToList()
    {
        // Récupérer le tenant à partir de l'ID stocké dans la propriété publique
        $tenant = \App\Models\Tenant::findOrFail($this->tenant_id);
        return $this->redirectRoute('tenant.settings.roles.index', ['tenant' => $tenant->domain], navigate: true);
    }

    public function editPermissions()
    {
        // Récupérer le tenant à partir de l'ID stocké dans la propriété publique
        $tenant = \App\Models\Tenant::findOrFail($this->tenant_id);
        return $this->redirectRoute('tenant.settings.roles.edit', ['tenant' => $tenant->domain, 'id' => $this->role->id], navigate: true);
    }

    public function render()
    {
        try {
            // Récupérer le tenant à partir de l'ID stocké dans la propriété publique
            $tenant = \App\Models\Tenant::findOrFail($this->tenant_id);
            
            $groupedPermissions = $this->role->permissions->groupBy(function ($permission) {
                $parts = explode('.', $permission->name);
                return $parts[0];
            });

            return view('livewire.settings.roles.permissions', [
                'groupedPermissions' => $groupedPermissions,
                'tenant' => $tenant,
                'title' => 'Permissions du rôle : ' . $this->role->name
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur dans Permissions::render: ' . $e->getMessage());
            $this->error('Erreur lors du chargement des permissions: ' . $e->getMessage());
            return view('livewire.settings.roles.permissions', [
                'groupedPermissions' => collect(),
                'title' => 'Permissions du rôle'
            ]);
        }
    }
}
