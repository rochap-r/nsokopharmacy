<?php

namespace App\Livewire\Tenants;

use App\Models\Tenant;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.auth.auth')]
class Identificator extends Component
{
    #[Validate('required|string')]
    public string $name = '';

    public $tenants = [];

    public function mount()
    {
        // Récupérer les tenants actifs pour affichage dans la vue
        $this->tenants = Tenant::where('active', true)
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'domain']);
    }

    public function identify()
    {
        $this->validate();

        // Recherche plus flexible du tenant (par domain, nom ou ID)
        $searchTerm = Str::lower($this->name);
        $tenant = Tenant::where(function($query) use ($searchTerm) {
                $query->where('domain', $searchTerm)
                      ->orWhere('name', 'like', '%' . $searchTerm . '%')
                      ->orWhere('id', $searchTerm);
            })
            ->where('active', true)
            ->first();

        // Vérifier si le tenant existe
        if (!$tenant) {
            // Journaliser la tentative d'accès à un tenant inexistant
            Log::warning("Tentative d'accès à un tenant inexistant", [
                'tenant_id' => $this->name,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            // Ajouter un message d'erreur à l'utilisateur
            $this->addError('name', "L'établissement spécifié n'existe pas. Veuillez vérifier l'identifiant et réessayer.");
            return;
        }

        // Redirection vers la route tenant.login avec le paramètre tenant
        return redirect()->route('tenant.login', ['tenant' => $tenant->domain]);
    }

    public function selectTenant($tenantId)
    {
        $tenant = Tenant::findOrFail($tenantId);
        return redirect()->route('tenant.login', ['tenant' => $tenant->domain]);
    }

    public function render()
    {
        return view('livewire.tenants.identificator');
    }
}
