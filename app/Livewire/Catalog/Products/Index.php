<?php

namespace App\Livewire\Catalog\Products;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    
    public $search = '';
    public $category_id = null;
    public $active = true;
    public $categories = [];
    
    protected $queryString = [
        'search' => ['except' => ''],
        'category_id' => ['except' => ''],
        'active' => ['except' => true],
    ];
    
    public function mount()
    {    
        // Vu00e9rification des permissions
        abort_unless(Auth::user()->can('catalog.view'), 403);
        
        // Charger les catu00e9gories pour le filtre
        $this->loadCategories();
    }
    
    public function loadCategories()
    {        
        // Ru00e9cupu00e9rer toutes les catu00e9gories principales (sans parent)
        $this->categories = Category::whereNull('parent_id')
            ->with(['children', 'children.children'])
            ->get()
            ->toArray();
    }
    
    public function deleteProduct($id)
    {        
        // Vu00e9rification des permissions
        abort_unless(Auth::user()->can('catalog.delete'), 403);
        
        try {
            $product = Product::findOrFail($id);
            
            // Vu00e9rifier si le produit est utilisu00e9 dans des inventaires
            if ($product->tenantInventories()->count() > 0) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'Impossible de supprimer ce produit car il est associu00e9 u00e0 des inventaires.'
                ]);
                return;
            }
            
            // Supprimer le produit
            $product->delete();
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Produit supprimu00e9 avec succu00e8s!'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Erreur lors de la suppression du produit: ' . $e->getMessage()
            ]);
        }
    }
    
    public function render()
    {        
        // Traitement global des donnu00e9es sans filtrer par tenant
        $query = Product::query();
        
        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('dci', 'like', "%{$this->search}%")
                  ->orWhere('dosage', 'like', "%{$this->search}%")
                  ->orWhere('forme_galenique', 'like', "%{$this->search}%");
            });
        }
        
        if (!empty($this->category_id)) {
            $query->where('category_id', $this->category_id);
        }
        
        if ($this->active !== null) {
            $query->where('active', $this->active);
        }
        
        $products = $query->with('category')->paginate(10);
        
        return view('livewire.catalog.products.index', [
            'products' => $products,
        ]);
    }
    
    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    public function updatingCategoryId()
    {
        $this->resetPage();
    }
    
    public function updatingActive()
    {
        $this->resetPage();
    }
}
