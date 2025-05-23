<?php

namespace App\Livewire\Catalog\Products;

use App\Models\Product;
use App\Models\Category;
use App\Models\Aisle;
use App\Enums\ProductType;
use App\Enums\PersonType;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Edit extends Component
{
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
        'type' => 'nullable|string|in:medicament,dispositif',
        'personne' => 'nullable|string|in:adulte,enfant',
        'category_id' => 'nullable|exists:categories,id',
        'aisle_id' => 'nullable|exists:aisles,id',
        'active' => 'boolean',
    ];
    
    public function mount($id)
    {
        // Vu00e9rification des permissions
        abort_unless(Auth::user()->can('catalog.edit'), 403);
        
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
        
        // Charger les catu00e9gories et les emplacements
        $this->loadCategories();
        $this->loadAisles();
    }
    
    public function loadCategories()
    {
        // Ru00e9cupu00e9rer toutes les catu00e9gories principales (sans parent)
        $this->categories = Category::whereNull('parent_id')
            ->with(['children', 'children.children'])
            ->get()
            ->toArray();
    }
    
    public function loadAisles()
    {
        // Ru00e9cupu00e9rer les emplacements disponibles pour ce tenant (globaux et spu00e9cifiques)
        $tenantId = Auth::user()->tenant_id;
        $this->aisles = Aisle::availableFor($tenantId)->get()->toArray();
    }
    
    public function update()
    {
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
                'message' => 'Produit mis u00e0 jour avec succu00e8s!'
            ]);
            
            return redirect()->route('tenant.catalog.index', ['tenant' => request()->route('tenant')]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Erreur lors de la mise u00e0 jour du produit: ' . $e->getMessage()
            ]);
        }
    }
    
    public function render()
    {
        return view('livewire.catalog.products.edit');
    }
}
