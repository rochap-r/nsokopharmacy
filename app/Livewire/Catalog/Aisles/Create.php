<?php

namespace App\Livewire\Catalog\Aisles;

use App\Models\Aisle;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Create extends Component
{
    public $code;
    public $name;
    public $category_id;
    public $is_global = false;
    
    public $categories = [];
    
    protected $rules = [
        'code' => 'required|string|max:50|unique:aisles,code',
        'name' => 'required|string|max:255',
        'category_id' => 'nullable|exists:categories,id',
        'is_global' => 'boolean',
    ];
    
    public function mount()
    {
        // Vérification des permissions
        abort_unless(Auth::user()->can('catalog.create'), 403);
        
        // Si l'utilisateur veut créer un emplacement global, vérifier qu'il a les permissions nécessaires
        if ($this->is_global) {
            abort_unless(Auth::user()->can('catalog.manage'), 403, 'Vous n\'avez pas les permissions nécessaires pour créer un emplacement global.');
        }
        
        // Charger les catégories pour le formulaire
        $this->loadCategories();
    }
    
    public function loadCategories()
    {
        // Récupérer toutes les catégories
        $this->categories = Category::with(['children', 'children.children'])
            ->whereNull('parent_id')
            ->get()
            ->toArray();
    }
    
    public function updatedIsGlobal()
    {
        // Si l'utilisateur veut créer un emplacement global, vérifier qu'il a les permissions nécessaires
        if ($this->is_global && !Auth::user()->can('catalog.manage')) {
            $this->is_global = false;
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Vous n\'avez pas les permissions nécessaires pour créer un emplacement global.'
            ]);
        }
    }
    
    public function create()
    {
        $this->validate();
        
        try {
            // Création globale mais avec référence au tenant pour les emplacements non-globaux
            // L'interface utilisateur traite par tenant (route) mais les données sont globales
            Aisle::create([
                'code' => $this->code,
                'name' => $this->name,
                'category_id' => $this->category_id,
                'tenant_id' => $this->is_global ? null : Auth::user()->tenant_id,
                'is_global' => $this->is_global,
            ]);
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Emplacement créé avec succès!'
            ]);
            
            return redirect()->route('tenant.catalog.aisles.index', ['tenant' => request()->route('tenant')]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Erreur lors de la création de l\'emplacement: ' . $e->getMessage()
            ]);
        }
    }
    
    public function render()
    {
        return view('livewire.catalog.aisles.create');
    }
}
