<?php

namespace App\Livewire\Catalog\Products;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Show extends Component
{
    public $product;
    
    public function mount($id)
    {
        // Vérification des permissions
        abort_unless(Auth::user()->can('catalog.view'), 403);
        
        // Charger le produit avec ses relations
        $this->product = Product::with(['category', 'category.parent', 'category.parent.parent', 'aisle'])
            ->findOrFail($id);
    }
    
    public function render()
    {
        return view('livewire.catalog.products.show');
    }
}
