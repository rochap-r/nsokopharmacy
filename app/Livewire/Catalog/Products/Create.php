<?php

namespace App\Livewire\Catalog\Products;

use App\Models\Product;
use App\Models\Category;
use App\Models\Aisle;
use App\Enums\ProductType;
use App\Enums\PersonType;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Create extends Component
{
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
        'type' => 'nullable|string|in:medicament,dispositif',
        'personne' => 'nullable|string|in:adulte,enfant',
        'category_id' => 'nullable|exists:categories,id',
        'aisle_id' => 'nullable|exists:aisles,id',
        'active' => 'boolean',
    ];

    public function mount()
    {
        // Vérification des permissions
        abort_unless(Auth::user()->can('catalog.create'), 403);

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
        // Traitement global des données : récupérer tous les emplacements sans restriction par tenant
        // Pour l'interface utilisateur, on affichera quand même les emplacements globaux et ceux du tenant courant
        $this->aisles = Aisle::all()->toArray();
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
                'type' => $this->type,
                'personne' => $this->personne,
                'category_id' => $this->category_id,
                'aisle_id' => $this->aisle_id,
                'active' => $this->active
            ]);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Produit créé avec succès!'
            ]);

            return redirect()->route('tenant.catalog.index', ['tenant' => request()->route('tenant')]);
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
