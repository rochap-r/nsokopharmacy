<?php

namespace App\Livewire\Settings\Roles;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Role;
use App\Http\Livewire\Traits\WithToast;
use App\Http\Livewire\Traits\WithTenantContext;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class Index extends Component
{
    use WithPagination, WithToast, WithTenantContext;

    protected $paginationTheme = 'tailwind';

    public $search = '';
    public $showDeleteModal = false;
    public $roleIdToDelete = null;
    public $roleName = '';


    // Définition du layout à utiliser
    protected $layout = 'components.layouts.app';

    public function resetSearch()
    {
        $this->reset('search');
    }

    public function mount()
    {
        $tenant = app('tenant');

        if (!$tenant) {
            return redirect()->route('identification');
        }
        $this->setTenant($tenant);

        if (session()->has('success')) {
            $this->success(session('success'), 6000);
            session()->forget('success');
        }

        if (session()->has('error')) {
            $this->error(session('error'), 6000);
            session()->forget('error');
        }

        if (session()->has('toast_message')) {
            $message = session('toast_message');
            $type = session('toast_type', 'success');

            if ($type === 'success') {
                $this->success($message);
            } elseif ($type === 'error') {
                $this->error($message);
            } elseif ($type === 'info') {
                $this->info($message);
            } elseif ($type === 'warning') {
                $this->warning($message);
            }

            session()->forget(['toast_message', 'toast_type']);
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function navigateToCreateRole()
    {
        $this->redirectRoute('tenant.settings.roles.create', navigate: true);
    }

    public function navigateToEditRole($roleId)
    {
        $this->redirectRoute('tenant.settings.roles.edit', ['id' => $roleId], navigate: true);
    }

    public function navigateToViewPermissions($roleId)
    {
        $this->redirectRoute('tenant.settings.roles.permissions', ['tenant' => $this->tenant, 'id' => $roleId], navigate: true);
    }

    public function confirmDelete($roleId)
    {
        $role = Role::find($roleId);
        if ($role) {
            // Vérifier si c'est le rôle de l'utilisateur connecté
            $currentUser = Auth::user();
            $currentUserRole = $currentUser->roles->first();

            if ($currentUserRole && $currentUserRole->id == $roleId) {
                $this->error('Vous ne pouvez pas supprimer le rôle auquel vous êtes assigné.');
                return;
            }

            $this->roleIdToDelete = $roleId;
            $this->roleName = $role->name;
            $this->showDeleteModal = true;
            $this->dispatch('open-modal', ['name' => 'confirm-delete-role']);
        }
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->roleIdToDelete = null;
        $this->roleName = '';
        $this->dispatch('close-modal', ['name' => 'confirm-delete-role']);
    }

    public function deleteRole()
    {
        try {
            if ($this->roleIdToDelete) {
                $role = Role::find($this->roleIdToDelete);

                if ($role) {
                    // Vérifier si c'est le rôle de l'utilisateur connecté
                    $currentUser = Auth::user();
                    $currentUserRole = $currentUser->roles->first();

                    if ($currentUserRole && $currentUserRole->id == $this->roleIdToDelete) {
                        $this->error('Vous ne pouvez pas supprimer le rôle auquel vous êtes assigné.');
                        $this->closeDeleteModal();
                        return;
                    }

                    // Vérifier si des utilisateurs sont assignés à ce rôle
                    $usersWithRole = User::role($role->name)->count();
                    if ($usersWithRole > 0) {
                        $this->error("Ce rôle est assigné à {$usersWithRole} utilisateur(s). Veuillez d'abord changer leur rôle.");
                        $this->closeDeleteModal();
                        return;
                    }

                    DB::beginTransaction();
                    try {
                        // Détacher toutes les permissions associées au rôle
                        // Ne pas utiliser syncPermissions([]) qui supprime les permissions
                        $permissions = $role->permissions;
                        foreach ($permissions as $permission) {
                            $role->revokePermissionTo($permission);
                        }

                        // Puis supprimer le rôle
                        $role->delete();
                        DB::commit();
                        $this->success('Rôle supprimé avec succès.');

                        // Utiliser la méthode du trait pour récupérer le tenant
                        $tenant = $this->getCurrentTenant();
                        return $this->redirectRoute('tenant.settings.roles.index', ['tenant' => $tenant->domain], navigate: true);
                    } catch (\Exception $e) {
                        DB::rollBack();
                        throw $e;
                    }
                } else {
                    $this->error('Rôle non trouvé.');
                }
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression du rôle: ' . $e->getMessage());
            $this->error('Erreur lors de la suppression du rôle: ' . $e->getMessage());
        }

        $this->closeDeleteModal();
    }

    public function render()
    {
        try {
            // Utiliser la méthode du trait pour récupérer le tenant
            $tenant = $this->getCurrentTenant();

            $roles = Role::query()
                ->where('tenant_id', $tenant->id)
                ->when($this->search, function ($query) {
                    return $query->where('name', 'like', '%' . $this->search . '%');
                })
                ->paginate(10);

            return view('livewire.settings.roles.index', [
                'roles' => $roles,
                'tenant' => $tenant,
                'title' => 'Rôles et Permissions'
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur dans Roles\Index::render: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            $this->error('Erreur lors du chargement des rôles: ' . $e->getMessage());
            return view('livewire.settings.roles.index', [
                'roles' => collect(),
                'title' => 'Rôles et Permissions'
            ]);
        }
    }
}
