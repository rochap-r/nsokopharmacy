<?php

namespace App\Livewire\Settings\Users;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Log;
use App\Http\Livewire\Traits\WithToast;

class Show extends Component
{
    use WithToast;

    public $userId;
    public $user;

    /**
     * Handle an incoming authentication request.
     */
    public $tenantParam;
    public $tenant_id;  // Propriété publique pour stocker l'ID du tenant

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
            $this->userId = $id;
            // Vérifier que l'utilisateur appartient bien au tenant courant
            $this->user = User::with('roles')
                ->where('id', $id)
                ->where('tenant_id', $this->tenant_id)
                ->firstOrFail();
        } catch (\Exception $e) {
            Log::error('Erreur dans Show::mount: ' . $e->getMessage());
            $this->error('Utilisateur non trouvé ou n\'appartient pas à cette pharmacie.');

            // Récupérer le tenant pour la redirection
            $tenantObj = \App\Models\Tenant::findOrFail($this->tenant_id);
            return $this->redirectRoute('tenant.settings.users.index', ['tenant' => $tenantObj->domain], navigate: true);
        }
    }

    public function back()
    {
        // Récupérer le tenant à partir de l'ID stocké dans la propriété publique
        $tenant = \App\Models\Tenant::findOrFail($this->tenant_id);
        return $this->redirectRoute('tenant.settings.users.index', ['tenant' => $tenant->domain], navigate: true);
    }

    public function render()
    {
        try {
            // Récupérer le tenant à partir de l'ID stocké dans la propriété publique
            $tenant = \App\Models\Tenant::findOrFail($this->tenant_id);

            return view('livewire.settings.users.show', [
                'title' => 'Détails de l\'utilisateur',
                'tenant' => $tenant
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur dans Show::render: ' . $e->getMessage());
            return view('livewire.settings.users.show', [
                'title' => 'Détails de l\'utilisateur'
            ]);
        }
    }
}
