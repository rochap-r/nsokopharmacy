<?php

namespace App\Livewire\Catalog\Aisles;

use App\Models\Aisle;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Edit extends Component
{
    public $aisle_id;
    public $code;
    public $name;
    public $category_id;
    public $is_global;
    public $tenant_id;
    
    public $categories = [];
    
    protected function rules()
    {
        return [
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('aisles', 'code')->ignore($this->aisle_id)
            ],
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'is_global' => 'boolean',
        ];
    }
    
    public function mount($id)
    {
        // Vérification des permissions
        abort_unless(Auth::user()->can('catalog.edit'), 403);
        
        // Charger l'emplacement
        $aisle = Aisle::findOrFail($id);
        
        // Vérifier si l'emplacement est global et si l'utilisateur a le droit de le modifier
        if ($aisle->is_global && !Auth::user()->can('catalog.manage')) {
            abort(403, 'Vous n\'avez pas les permissions nécessaires pour modifier un emplacement global.');
        }
        
        // Pas de restriction par tenant car les données sont traitées globalement
        // La restriction se fait seulement par les permissions et pour les emplacements globaux
        
        $this->aisle_id = $aisle->id;
        $this->code = $aisle->code;
        $this->name = $aisle->name;
        $this->category_id = $aisle->category_id;
        $this->is_global = $aisle->is_global;
        $this->tenant_id = $aisle->tenant_id;
        
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
        // Si l'utilisateur veut transformer l'emplacement en global, vérifier qu'il a les permissions nécessaires
        if ($this->is_global && !Auth::user()->can('catalog.manage')) {
            $this->is_global = false;
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Vous n\'avez pas les permissions nécessaires pour créer un emplacement global.'
            ]);
        }
    }
    
    public function update()
    {
        $this->validate();
        
        try {
            $aisle = Aisle::findOrFail($this->aisle_id);
            
            $aisle->update([
                'code' => $this->code,
                'name' => $this->name,
                'category_id' => $this->category_id,
                'tenant_id' => $this->is_global ? null : Auth::user()->tenant_id,
                'is_global' => $this->is_global,
            ]);
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Emplacement mis à jour avec succès!'
            ]);
            
            return redirect()->route('tenant.catalog.aisles.index', ['tenant' => request()->route('tenant')]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Erreur lors de la mise à jour de l\'emplacement: ' . $e->getMessage()
            ]);
        }
    }
    
    public function render()
    {
        return view('livewire.catalog.aisles.edit');
    }
}
