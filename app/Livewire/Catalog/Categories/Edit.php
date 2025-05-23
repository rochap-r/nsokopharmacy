<?php

namespace App\Livewire\Catalog\Categories;

use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use App\Http\Livewire\Traits\WithToast;

class Edit extends Component
{
    use WithToast;
    
    public $category_id;
    public $name;
    public $slug;
    public $color_code;
    public $parent_id;
    
    public $parentCategories = [];
    
    // Pour gérer le contexte tenant
    public $tenantParam;
    public $tenant_id;
    
    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('categories', 'slug')->ignore($this->category_id)
            ],
            'color_code' => 'nullable|string|max:20',
            'parent_id' => [
                'nullable',
                'exists:categories,id',
                function ($attribute, $value, $fail) {
                    // Éviter qu'une catégorie soit sa propre parente ou grand-parente
                    if ($value == $this->category_id) {
                        $fail('Une catégorie ne peut pas être sa propre parente.');
                    }
                    
                    // Vérifier que le parent n'est pas un descendant de cette catégorie
                    if ($value) {
                        $descendants = $this->getDescendants($this->category_id);
                        if (in_array($value, $descendants)) {
                            $fail('Une catégorie ne peut pas avoir un de ses descendants comme parent.');
                        }
                    }
                },
            ],
        ];
    }
    
    public function mount($id, $tenant = null)
    {
        // Vérification des permissions
        abort_unless(Auth::user()->can('catalog.edit'), 403);
        
        // Récupérer le paramètre tenant depuis la route
        $this->tenantParam = $tenant;

        // Le tenant est disponible via le middleware à ce stade (requête GET initiale)
        $tenant = app('tenant');

        if (!$tenant) {
            return redirect()->route('identification');
        }

        // Stocker l'ID du tenant dans une propriété publique pour les actions Livewire
        $this->tenant_id = $tenant->id;
        
        try {
            // Charger la catégorie
            $category = Category::findOrFail($id);
            $this->category_id = $category->id;
            $this->name = $category->name;
            $this->slug = $category->slug;
            $this->color_code = $category->color_code;
            $this->parent_id = $category->parent_id;
            
            // Charger les catégories parentes possibles
            $this->loadParentCategories();
            
            // Vérification des messages flash pour les afficher en toast
            if (session()->has('success')) {
                $this->success(session('success'), 6000);
                session()->forget('success'); // Reset pour éviter des affichages multiples
            }
    
            if (session()->has('error')) {
                $this->error(session('error'), 6000);
                session()->forget('error'); // Reset pour éviter des affichages multiples
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement de la catégorie: ' . $e->getMessage());
            $this->error('Erreur lors du chargement de la catégorie: ' . $e->getMessage());
            return redirect()->route('tenant.catalog.categories.index', ['tenant' => $tenant->domain]);
        }
    }
    
    public function loadParentCategories()
    {
        // Récupérer toutes les catégories principales (sans parent) et les sous-catégories
        // en excluant cette catégorie et ses descendants pour éviter les boucles
        $descendants = $this->getDescendants($this->category_id);
        $descendants[] = $this->category_id;
        
        $this->parentCategories = Category::whereNull('parent_id')
            ->where(function ($query) use ($descendants) {
                $query->whereNotIn('id', $descendants);
            })
            ->with(['children' => function($query) use ($descendants) {
                $query->whereNotIn('id', $descendants);
            }])
            ->get()
            ->toArray();
    }
    
    protected function getDescendants($categoryId)
    {
        $descendants = [];
        $children = Category::where('parent_id', $categoryId)->get();
        
        foreach ($children as $child) {
            $descendants[] = $child->id;
            $childDescendants = $this->getDescendants($child->id);
            $descendants = array_merge($descendants, $childDescendants);
        }
        
        return $descendants;
    }
    
    public function updatedName()
    {
        // Générer automatiquement le slug à partir du nom si le champ slug est vide
        if (empty($this->slug)) {
            $this->slug = Str::slug($this->name);
        }
    }
    
    public function update()
    {
        try {
            // Récupérer le tenant
            $tenant = \App\Models\Tenant::findOrFail($this->tenant_id);
            
            // Si le slug est vide, générer à partir du nom
            if (empty($this->slug)) {
                $this->slug = Str::slug($this->name);
            }
            
            $this->validate();
            
            $category = Category::findOrFail($this->category_id);
            $category->update([
                'name' => $this->name,
                'slug' => $this->slug,
                'color_code' => $this->color_code,
                'parent_id' => $this->parent_id,
            ]);
            
            $this->success('Catégorie mise à jour avec succès!');
            
            return redirect()->route('tenant.catalog.categories.index', ['tenant' => $tenant->domain]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour de la catégorie: ' . $e->getMessage());
            $this->error('Erreur lors de la mise à jour de la catégorie: ' . $e->getMessage());
        }
    }
    
    public function render()
    {
        try {
            // Récupérer le tenant
            $tenant = \App\Models\Tenant::findOrFail($this->tenant_id);
            
            return view('livewire.catalog.categories.edit', [
                'tenant' => $tenant,
                'title' => 'Modifier la Catégorie'
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur dans Categories\Edit::render: ' . $e->getMessage());
            
            $this->error('Erreur lors du chargement du formulaire: ' . $e->getMessage());
            return view('livewire.catalog.categories.edit', [
                'title' => 'Modifier la Catégorie'
            ]);
        }
    }
}
