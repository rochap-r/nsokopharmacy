<?php

namespace App\Livewire\Catalog\Categories;

use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Livewire\Traits\WithToast;

class Index extends Component
{
    use WithPagination, WithToast;

    protected $paginationTheme = 'tailwind';

    // Propriétés pour la recherche
    public $search = '';

    // Pour gérer le contexte tenant
    public $tenantParam;
    public $tenant_id;

    public function mount($tenant = null)
    {
        // Vérification des permissions
        abort_unless(Auth::user()->can('catalog.view'), 403);

        // Récupérer le paramètre tenant depuis la route
        $this->tenantParam = $tenant;

        // Le tenant est disponible via le middleware à ce stade (requête GET initiale)
        $tenant = app('tenant');

        if (!$tenant) {
            return redirect()->route('identification');
        }

        // Stocker l'ID du tenant dans une propriété publique pour les actions Livewire
        $this->tenant_id = $tenant->id;

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

    public function resetSearch()
    {
        $this->reset('search');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function deleteCategory($id)
    {
        try {
            // Vérification des permissions
            if (!Auth::user()->can('catalog.delete')) {
                $this->error('Vous n\'avez pas les droits pour supprimer une catégorie.');
                return;
            }

            $category = Category::findOrFail($id);

            // Vérifier si la catégorie a des sous-catégories ou des produits
            if ($category->children()->count() > 0) {
                $this->error('Impossible de supprimer cette catégorie car elle contient des sous-catégories.');
                return;
            }

            if ($category->products()->count() > 0) {
                $this->error('Impossible de supprimer cette catégorie car elle est associée à des produits.');
                return;
            }

            // Supprimer la catégorie
            $category->delete();

            $this->success('Catégorie supprimée avec succès!');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de la catégorie: ' . $e->getMessage());
            $this->error('Erreur lors de la suppression de la catégorie: ' . $e->getMessage());
        }
    }

    public function render()
    {
        try {
            // Récupérer le tenant à partir de l'ID stocké dans la propriété publique
            $tenant = \App\Models\Tenant::findOrFail($this->tenant_id);

            // Traitement global des données sans filtrer par tenant
            $query = Category::query();

            if (!empty($this->search)) {
                $query->where('name', 'like', "%{$this->search}%");
            }

            // Récupérer toutes les catégories principales (sans parent)
            if (empty($this->search)) {
                $categories = $query->whereNull('parent_id')
                    ->with(['children.children', 'children.products', 'children.children.products', 'products'])
                    ->get();
            } else {
                // Si une recherche est active, on récupère toutes les catégories qui correspondent
                $categories = $query->with(['parent.parent', 'children.children', 'products'])
                    ->get();
            }

            return view('livewire.catalog.categories.index', [
                'categories' => $categories,
                'title' => 'Gestion des Catégories',
                'tenant' => $tenant
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur dans Categories\Index::render: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            $this->error('Erreur lors du chargement des catégories: ' . $e->getMessage());
            return view('livewire.catalog.categories.index', [
                'categories' => collect(),
                'title' => 'Gestion des Catégories'
            ]);
        }
    }
}
