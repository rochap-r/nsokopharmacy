<?php

namespace App\Livewire\Catalog;

use App\Models\Category;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Livewire\Traits\WithToast;
use App\Http\Livewire\Traits\WithTenantContext;

class Index extends Component
{
    use WithPagination, WithToast, WithTenantContext;

    protected $paginationTheme = 'tailwind';

    // Propriétés pour la recherche et le filtrage
    public $search = '';
    public $category_id = null;
    public $active = true;

    // Liste des catégories pour le filtre
    public $categories = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'category_id' => ['except' => ''],
        'active' => ['except' => true],
    ];

    public function mount()
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

        // Charger les catégories pour le filtre
        $this->loadCategories();

        // Vérification des messages flash pour les afficher en toast
        if (session()->has('success')) {
            $this->success(session('success'), 6000);
            session()->forget('success'); // Reset pour éviter des affichages multiples
        }

        if (session()->has('error')) {
            $this->error(session('error'), 6000);
            session()->forget('error'); // Reset pour éviter des affichages multiples
        }
    }

    public function loadCategories()
    {
        // Récupérer toutes les catégories principales (sans parent)
        $this->categories = Category::whereNull('parent_id')
            ->with(['children', 'children.children'])
            ->get()
            ->toArray();
    }

    public function resetSearch()
    {
        $this->reset('search');
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

    public function render()
    {
        try {
            // Utiliser la méthode du trait pour récupérer le tenant
            $tenant = $this->getCurrentTenant();

            // Construction de la requête de produits - traitement global des données
            $query = Product::query();

            // Filtrage par recherche
            if (!empty($this->search)) {
                $query->where(function($q) {
                    $q->where('dci', 'like', "%{$this->search}%")
                      ->orWhere('dosage', 'like', "%{$this->search}%")
                      ->orWhere('forme_galenique', 'like', "%{$this->search}%");
                });
            }

            // Filtrage par catégorie
            if (!empty($this->category_id)) {
                $query->where('category_id', $this->category_id);
            }

            // Filtrage par statut
            if ($this->active !== null) {
                $query->where('active', $this->active);
            }

            // Pagination des résultats avec chargement des relations
            $products = $query->with('category')->paginate(10);

            // Rendu de la vue avec passage des données
            return view('livewire.catalog.index', [
                'products' => $products,
                'title' => 'Catalogue des Produits',
                'tenant' => $tenant
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur dans Catalog\Index::render: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            $this->error('Erreur lors du chargement des produits: ' . $e->getMessage());
            return view('livewire.catalog.index', [
                'products' => collect(),
                'title' => 'Catalogue des Produits'
            ]);
        }
    }

    // Méthode pour supprimer un produit
    public function deleteProduct($id)
    {
        try {
            // Vérification des permissions
            if (!Auth::user()->can('catalog.delete')) {
                $this->error('Vous n\'avez pas les droits pour supprimer un produit.');
                return;
            }

            $product = Product::findOrFail($id);

            // Vérifier si le produit est utilisé dans des inventaires
            if ($product->tenantInventories()->count() > 0) {
                $this->error('Ce produit est utilisé dans des inventaires et ne peut pas être supprimé.');
                return;
            }

            // Supprimer le produit
            $product->delete();

            $this->success('Produit supprimé avec succès!');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression du produit: ' . $e->getMessage());
            $this->error('Erreur lors de la suppression du produit: ' . $e->getMessage());
        }
    }
}
