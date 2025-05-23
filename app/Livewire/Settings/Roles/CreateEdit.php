<?php

namespace App\Livewire\Settings\Roles;

use Livewire\Component;
use App\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Http\Livewire\Traits\WithToast;
use App\Http\Livewire\Traits\WithTenantContext;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CreateEdit extends Component
{
    use WithToast, WithTenantContext;

    public $roleId = null;
    public $roleName = '';
    public $selectedPermissions = [];
    public $editMode = false;
    public $showConfirmDeselectModal = false;

    // Définition du layout à utiliser
    protected $layout = 'components.layouts.app';

    protected function rules()
    {
        return [
            'roleName' => [
                'required',
                'min:3',
                'max:50',
                Rule::unique('roles', 'name')->ignore($this->roleId)
            ],
            'selectedPermissions' => 'array'
        ];
    }

    protected $messages = [
        'roleName.required' => 'Le nom du rôle est obligatoire.',
        'roleName.min' => 'Le nom du rôle doit contenir au moins 3 caractères.',
        'roleName.max' => 'Le nom du rôle ne peut pas dépasser 50 caractères.',
        'roleName.unique' => 'Ce nom de rôle existe déjà.'
    ];

    public function mount($id = null)
    {
        // Le tenant est disponible via le middleware à ce stade (requête GET initiale)
        $tenant = app('tenant');

        if (!$tenant) {
            return redirect()->route('identification');
        }

        // Utiliser la méthode du trait pour stocker le tenant
        $this->setTenant($tenant);

        if ($id) {
            try {
                // Vérifier que le rôle appartient bien au tenant courant
                $role = Role::where('id', $id)
                    ->where('tenant_id', $this->getCurrentTenant()->id)
                    ->firstOrFail();

                $this->roleId = $id;
                $this->roleName = $role->name;
                $this->selectedPermissions = $role->permissions->pluck('id')->toArray();
                $this->editMode = true;
            } catch (\Exception $e) {
                Log::error('Erreur dans CreateEdit::mount pour le rôle ID ' . $id . ': ' . $e->getMessage());
                $this->error('Erreur lors du chargement du rôle: ' . $e->getMessage());


                return $this->redirectRoute('tenant.settings.roles.index', ['tenant' => $this->getCurrentTenant()], navigate: true);
            }
        }
    }

    public function resetRoleForm()
    {
        $this->roleName = '';
        $this->selectedPermissions = [];
        $this->resetValidation();
    }

    public function saveRole()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            if ($this->editMode) {
                // Mise à jour d'un rôle existant
                $role = Role::findOrFail($this->roleId);
                $role->name = $this->roleName;
                $role->save();

                // Récupérer les objets Permission à partir des IDs
                $permissions = Permission::whereIn('id', $this->selectedPermissions)->get();

                // Synchroniser les permissions en utilisant la collection d'objets
                $role->syncPermissions($permissions);

                $message='Rôle mis à jour avec succès!';
            } else {
                // Vérifier si le rôle existe déjà pour éviter l'erreur de duplicat
                if (Role::where('name', $this->roleName.'-'.$this->getCurrentTenant()->id)->exists()) {
                    $this->error('Ce nom de rôle existe déjà.');
                    DB::rollBack();
                    return;
                }

                // Création d'un nouveau rôle
                $role = Role::create(['name' => $this->roleName.'-'.$this->getCurrentTenant()->id, 'tenant_id' => $this->getCurrentTenant()->id]);

                // Récupérer les objets Permission à partir des IDs
                if (!empty($this->selectedPermissions)) {
                    $permissions = Permission::whereIn('id', $this->selectedPermissions)->get();

                    // Attribuer les permissions en utilisant la collection d'objets
                    $role->syncPermissions($permissions);
                }

                $message='Rôle créé avec succès!';
            }

            DB::commit();

            session()->flash('success', $message);

            // Rediriger vers la liste des rôles
            $this->redirectRoute('tenant.settings.roles.index', ['tenant' => $this->getCurrentTenant()], navigate:false);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur dans CreateEdit::saveRole pour "' . $this->roleName . '": ' . $e->getMessage());
            $this->error('Erreur lors de la sauvegarde du rôle: ' . $e->getMessage());
        }
    }

    public function cancelEdit()
    {
        $this->resetValidation();

        $this->redirectRoute('tenant.settings.roles.index', ['tenant' => $this->getCurrentTenant()], navigate: true);
    }

    public function selectAllPermissions()
    {
        $this->selectedPermissions = Permission::pluck('id')->toArray();
        $this->success('Toutes les permissions ont été sélectionnées.');
    }

    public function confirmDeselectAllPermissions()
    {
        // Ouvrir la modale de confirmation
        $this->showConfirmDeselectModal = true;
        $this->dispatch('open-modal', ['name' => 'confirm-deselect']);
    }

    public function cancelDeselectAllPermissions()
    {
        $this->showConfirmDeselectModal = false;
        $this->dispatch('close-modal', ['name' => 'confirm-deselect']);
    }

    public function deselectAllPermissions()
    {
        $this->selectedPermissions = [];
        $this->showConfirmDeselectModal = false;
        $this->success('Toutes les permissions ont été désélectionnées.');
        $this->dispatch('close-modal', ['name' => 'confirm-deselect']);
    }

    public function render()
    {
        try {
            $permissions = Permission::all();
            $permissionsByGroup = $permissions->groupBy(function ($permission) {
                // Grouper les permissions par leur préfixe (avant le premier point)
                $parts = explode('.', $permission->name);
                return $parts[0];
            });

            $title = $this->editMode ? 'Modifier le rôle' : 'Créer un rôle';

            return view('livewire.settings.roles.create-edit', [
                'permissions' => $permissions,
                'permissionsByGroup' => $permissionsByGroup,
                'tenant' => $this->getCurrentTenant(),
                'title' => $title
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur dans CreateEdit::render: ' . $e->getMessage());
            $this->error('Erreur lors du chargement des permissions: ' . $e->getMessage());

            $title = $this->editMode ? 'Modifier le rôle' : 'Créer un rôle';

            return view('livewire.settings.roles.create-edit', [
                'permissions' => collect(),
                'permissionsByGroup' => collect(),
                'title' => $title
            ]);
        }
    }
}
