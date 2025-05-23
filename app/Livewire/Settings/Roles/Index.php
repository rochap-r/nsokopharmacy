<?php

namespace App\Livewire\Settings\Roles;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Role;
use App\Http\Livewire\Traits\WithToast;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class Index extends Component
{
    use WithPagination, WithToast;

    protected $paginationTheme = 'tailwind';

    public $search = '';
    public $showDeleteModal = false;
    public $roleIdToDelete = null;
    public $roleName = '';

    /**
     * Handle an incoming authentication request.
     */
    public $tenantParam;
    public $tenant_id;  // Propriété publique pour stocker l'ID du tenant

    // Définition du layout à utiliser
    protected $layout = 'components.layouts.app';

    public function resetSearch()
    {
        $this->reset('search');
    }

    public function mount($tenant = null)
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

        // Vérification des messages flash pour les afficher en toast
        if (session()->has('success')) {
            $this->success(session('success'), 6000);
            session()->forget('success'); // Reset pour éviter des affichages multiples
        }

        if (session()->has('error')) {
            $this->error(session('error'), 6000);
            session()->forget('error'); // Reset pour éviter des affichages multiples
        }

        // Vérifier s'il y a un message toast dans la session et l'afficher
        if (session()->has('toast_message')) {
            $message = session('toast_message');
            $type = session('toast_type', 'success'); // Par défaut, type success

            // Appeler la méthode appropriée du trait WithToast en fonction du type
            if ($type === 'success') {
                $this->success($message);
            } elseif ($type === 'error') {
                $this->error($message);
            } elseif ($type === 'info') {
                $this->info($message);
            } elseif ($type === 'warning') {
                $this->warning($message);
            }

            // Supprimer les messages de la session pour éviter qu'ils ne s'affichent à nouveau
            session()->forget(['toast_message', 'toast_type']);
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function navigateToCreateRole()
    {
        // Récupérer le tenant à partir de l'ID stocké dans la propriété publique
        $tenant = \App\Models\Tenant::findOrFail($this->tenant_id);
        $this->redirectRoute('tenant.settings.roles.create', ['tenant' => $tenant->domain], navigate: true);
    }

    public function navigateToEditRole($roleId)
    {
        // Récupérer le tenant à partir de l'ID stocké dans la propriété publique
        $tenant = \App\Models\Tenant::findOrFail($this->tenant_id);
        $this->redirectRoute('tenant.settings.roles.edit', ['tenant' => $tenant->domain, 'id' => $roleId], navigate: true);
    }

    public function navigateToViewPermissions($roleId)
    {
        // Récupérer le tenant à partir de l'ID stocké dans la propriété publique
        $tenant = \App\Models\Tenant::findOrFail($this->tenant_id);
        $this->redirectRoute('tenant.settings.roles.permissions', ['tenant' => $tenant->domain, 'id' => $roleId], navigate: true);
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

                        // Récupérer le tenant pour la redirection
                        $tenant = \App\Models\Tenant::findOrFail($this->tenant_id);
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
            // Récupérer le tenant à partir de l'ID stocké dans la propriété publique
            $tenant = \App\Models\Tenant::findOrFail($this->tenant_id);

            $roles = Role::query()
                ->where('tenant_id', $this->tenant_id) // Utiliser la propriété tenant_id stockée
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
