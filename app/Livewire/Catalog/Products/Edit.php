<?php

namespace App\Livewire\Catalog\Products;

use App\Models\Product;
use App\Models\Category;
use App\Models\Aisle;
use App\Enums\ProductType;
use App\Enums\PersonType;
use App\Http\Livewire\Traits\WithTenantContext;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Edit extends Component
{
    use WithTenantContext;
    public $product_id;
    public $dci;
    public $dosage;
    public $forme_galenique;
    public $color_code;
    public $type;
    public $personne;
    public $category_id;
    public $aisle_id;
    public $active;

    public $categories = [];
    public $aisles = [];

    protected $rules = [
        'dci' => 'required|string|max:255',
        'dosage' => 'nullable|string|max:255',
        'forme_galenique' => 'nullable|string|max:255',
        'color_code' => 'nullable|string|max:20',
        'category_id' => 'nullable|exists:categories,id',
        'aisle_id' => 'nullable|exists:aisles,id',
        'active' => 'boolean',
    ];

    //adapte cette fonction par rapport à edit
    public function boot()
    {
        // Add dynamic validation rules that can't be in property initialization
        $this->rules['type'] = 'nullable|string';
        $this->rules['personne'] = 'nullable|string';
    }

    public function mount($id)
    {
        // Vérification des permissions
        abort_unless(Auth::user()->can('catalog.edit'), 403);

        // Le tenant est disponible via le middleware à ce stade (requête GET initiale)
        $tenant = app('tenant');

        if (!$tenant) {
            return redirect()->route('identification');
        }

        // Utiliser la méthode du trait pour stocker le tenant
        $this->setTenant($tenant);

        // Charger le produit
        $product = Product::findOrFail($id);
        $this->product_id = $product->id;
        $this->dci = $product->dci;
        $this->dosage = $product->dosage;
        $this->forme_galenique = $product->forme_galenique;
        $this->color_code = $product->color_code;
        $this->type = $product->type;
        $this->personne = $product->personne;
        $this->category_id = $product->category_id;
        $this->aisle_id = $product->aisle_id;
        $this->active = $product->active;

        // Charger les catégories et les emplacements
        $this->loadCategories();
        $this->loadAisles();
    }

    public function loadCategories()
    {
        // Récupérer toutes les catégories principales (sans parent)
        $this->categories = Category::whereNull('parent_id')
            ->with(['children', 'children.children'])
            ->get()
            ->toArray();
    }

    public function loadAisles()
    {
        // si les emplacements sont disponibles pour le tenant, sinon les emplacements globaux
        $aisles = Aisle::availableFor($this->getCurrentTenant()->id)->get();
        $this->aisles = $aisles->toArray();
    }

    public function update()
    {
        // Vérifier si les propriétés sont déjà des instances d'enum
        // et les convertir en valeurs string pour la validation si nécessaire
        if ($this->type instanceof ProductType) {
            $this->type = $this->type->value;
        }

        if ($this->personne instanceof PersonType) {
            $this->personne = $this->personne->value;
        }

        $this->validate();

        try {
            $product = Product::findOrFail($this->product_id);
            $product->update([
                'dci' => $this->dci,
                'dosage' => $this->dosage,
                'forme_galenique' => $this->forme_galenique,
                'color_code' => $this->color_code,
                'type' => $this->type,
                'personne' => $this->personne,
                'category_id' => $this->category_id,
                'aisle_id' => $this->aisle_id,
                'active' => $this->active
            ]);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Produit mis à jour avec succès!'
            ]);

            return redirect()->route('tenant.catalog.index', ['tenant' => $this->getCurrentTenant()]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Erreur lors de la mise à jour du produit: ' . $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        return view('livewire.catalog.products.edit');
    }
}
