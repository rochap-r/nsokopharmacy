<?php

namespace App\Livewire\Settings\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Livewire\Traits\WithToast;
use App\Http\Livewire\Traits\WithTenantContext;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;

class Show extends Component
{
    use WithToast, WithTenantContext;

    public $userId;
    public $user;


    protected $layout = 'components.layouts.app';

    public function mount($id = null)
    {
        $tenant = app('tenant');

        if (!$tenant) {
            return redirect()->route('identification');
        }

        $this->setTenant($tenant);

        try {
            $this->userId = $id;
            $this->user = User::with('roles')
                ->where('id', $id)
                ->where('tenant_id', $tenant->id)
                ->firstOrFail();
        } catch (\Exception $e) {
            Log::error('Erreur dans Show::mount: ' . $e->getMessage());
            $this->error('Utilisateur non trouvé ou n\'appartient pas à cette pharmacie.');

            return $this->redirectRoute('tenant.settings.users.index', ['tenant' => $tenant], navigate: true);
        }
    }

    public function back()
    {
        return $this->redirectRoute('tenant.settings.users.index', ['tenant' => $this->getCurrentTenant()], navigate: true);
    }

    public function render()
    {
        try {
            return view('livewire.settings.users.show', [
                'title' => 'Détails de l\'utilisateur',
                'tenant' => $this->getCurrentTenant()
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur dans Show::render: ' . $e->getMessage());
            return view('livewire.settings.users.show', [
                'title' => 'Détails de l\'utilisateur'
            ]);
        }
    }
}
