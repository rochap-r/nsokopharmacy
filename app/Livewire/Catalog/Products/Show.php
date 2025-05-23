<?php

namespace App\Livewire\Catalog\Products;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Http\Livewire\Traits\WithTenantContext;

class Show extends Component
{
    use WithTenantContext;

    public $product;

    public function mount($id)
    {
        // Vérification des permissions
        abort_unless(Auth::user()->can('catalog.view'), 403);

        // Le tenant est disponible via le middleware à ce stade (requête GET initiale)
        $tenant = app('tenant');

        if (!$tenant) {
            return redirect()->route('identification');
        }

        // Utiliser la méthode du trait pour stocker le tenant
        $this->setTenant($tenant);

        // Charger le produit avec ses relations
        $this->product = Product::with(['category', 'category.parent', 'category.parent.parent', 'aisle'])
            ->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.catalog.products.show');
    }
}
