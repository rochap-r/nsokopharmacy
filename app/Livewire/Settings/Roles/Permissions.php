<?php

namespace App\Livewire\Settings\Roles;

use Livewire\Component;
use Spatie\Permission\Models\Role;
use App\Http\Livewire\Traits\WithToast;
use App\Http\Livewire\Traits\WithTenantContext;
use Illuminate\Support\Facades\Log;

class Permissions extends Component
{
    use WithToast, WithTenantContext;

    public $roleId;
    public $role;

    // Définition du layout à utiliser
    protected $layout = 'components.layouts.app';

    public function mount($id = null)
    {
        // Le tenant est disponible via le middleware à ce stade (requête GET initiale)
        $tenant = app('tenant');

        if (!$tenant) {
            return redirect()->route('identification');
        }

        // Utiliser la méthode du trait pour stocker le tenant
        $this->setTenant($tenant);

        try {
            $this->roleId = $id;
            // Vérifier que le rôle appartient bien au tenant courant
            $tenant = $this->getCurrentTenant();
            $this->role = Role::with('permissions')
                ->where('id', $id)
                ->where('tenant_id', $tenant->id)
                ->firstOrFail();
        } catch (\Exception $e) {
            Log::error('Erreur dans Permissions::mount: ' . $e->getMessage());
            $this->error('Erreur lors du chargement du rôle: ' . $e->getMessage());
            $this->redirectRoute('tenant.settings.roles.index', ['tenant'=>$this->getCurrentTenant()], navigate: true);
        }
    }

    public function backToList()
    {
        // Utiliser la méthode getCurrentTenant() du trait pour recuperer le tenant
        // et ne pas passer le tenant en paramètre car tenant_route() le récupère automatiquement
        $this->redirectRoute('tenant.settings.roles.index',['tenant'=>$this->getCurrentTenant()], navigate: true);
    }

    public function editPermissions()
    {
        // Ne pas passer le tenant en paramètre car tenant_route() le récupère automatiquement
        $this->redirectRoute('tenant.settings.roles.edit', ['id' => $this->role->id], navigate: true);
    }

    public function render()
    {
        try {
            $groupedPermissions = $this->role->permissions->groupBy(function ($permission) {
                $parts = explode('.', $permission->name);
                return $parts[0];
            });

            return view('livewire.settings.roles.permissions', [
                'groupedPermissions' => $groupedPermissions,
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
