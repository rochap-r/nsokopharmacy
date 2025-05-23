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

class Create extends Component
{
    use WithTenantContext;
    public $dci;
    public $dosage;
    public $forme_galenique;
    public $color_code = '#a0aec0';
    public $type;
    public $personne;
    public $category_id;
    public $aisle_id;
    public $active = true;

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

    public function boot()
    {
        // Add dynamic validation rules that can't be in property initialization
        $this->rules['type'] = 'nullable|string|in:' . implode(',', array_column(ProductType::cases(), 'value'));
        $this->rules['personne'] = 'nullable|string|in:' . implode(',', array_column(PersonType::cases(), 'value'));
    }

    public function mount()
    {
        // Vérification des permissions
        abort_unless(Auth::user()->can('catalog.create'), 403);


        // Le tenant est disponible via le middleware à ce stade (requête GET initiale)
        $tenant = app('tenant');

        if (!$tenant) {
            return redirect()->route('identification');
        }

        // Utiliser la méthode du trait pour stocker le tenant
        $this->setTenant($tenant);

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
        //recuperer les emplacements en priorité specifique sinon global
        $this->aisles = Aisle::availableFor($this->getCurrentTenant()->id)->get()->toArray();
    }

    public function create()
    {
        $this->validate();

        try {
            Product::create([
                'dci' => $this->dci,
                'dosage' => $this->dosage,
                'forme_galenique' => $this->forme_galenique,
                'color_code' => $this->color_code,
                'type' => ProductType::from($this->type),
                'personne' => PersonType::from($this->personne),
                'category_id' => $this->category_id,
                'aisle_id' => $this->aisle_id,
                'active' => $this->active
            ]);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Produit créé avec succès!'
            ]);

            return redirect()->route('tenant.catalog.index', ['tenant' => $this->getCurrentTenant()]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Erreur lors de la création du produit: ' . $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        return view('livewire.catalog.products.create');
    }
}
