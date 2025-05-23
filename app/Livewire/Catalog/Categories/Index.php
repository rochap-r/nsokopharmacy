<?php

namespace App\Livewire\Catalog\Categories;

use Livewire\Component;
use App\Models\Category;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Livewire\Traits\WithToast;
use App\Http\Livewire\Traits\WithTenantContext;

class Index extends Component
{
    use WithPagination, WithToast, WithTenantContext;

    /**
     * Convert a hex color code to the closest Tailwind color class
     * This is used to dynamically apply Tailwind color classes based on the category color
     */
    public function colorToTailwind($hexColor)
    {
        // Remove # if present
        $hex = ltrim($hexColor, '#');

        // Convert to RGB
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        // Calculate color brightness (simple formula)
        $brightness = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;

        // Map to Tailwind color classes based on dominant color and brightness
        if (max($r, $g, $b) === $r) {
            // Red dominant
            if ($g > 150 && $b < 100) {
                return 'amber'; // Red-yellow mix
            } elseif ($b > 150) {
                return 'rose'; // Red-blue mix (pink/rose)
            } else {
                return 'red';
            }
        } elseif (max($r, $g, $b) === $g) {
            // Green dominant
            if ($r > 150 && $b < 100) {
                return 'lime'; // Green-red mix (lime)
            } elseif ($b > 150) {
                return 'emerald'; // Green-blue mix (teal/emerald)
            } else {
                return 'green';
            }
        } elseif (max($r, $g, $b) === $b) {
            // Blue dominant
            if ($r > 150 && $g < 100) {
                return 'violet'; // Blue-red mix (purple/violet)
            } elseif ($g > 150) {
                return 'sky'; // Blue-green mix (teal/cyan/sky)
            } else {
                return 'blue';
            }
        }

        // Handle specific color combinations
        if ($r > 180 && $g > 180 && $b < 100) {
            return 'yellow';
        } elseif ($r > 180 && $g > 130 && $g < 180 && $b < 100) {
            return 'orange';
        } elseif ($r > 130 && $g < 100 && $b > 130) {
            return 'purple';
        } elseif ($r < 100 && $g > 130 && $b > 130) {
            return 'cyan';
        }

        // Handle grayscale
        if (abs($r - $g) < 30 && abs($g - $b) < 30 && abs($r - $b) < 30) {
            if ($brightness > 200) {
                return 'gray';
            } elseif ($brightness > 100) {
                return 'slate';
            } else {
                return 'zinc';
            }
        }

        // Default fallback
        return 'indigo';
    }

    protected $paginationTheme = 'tailwind';

    // Propriétés pour la recherche
    public $search = '';

    public function mount()
    {
        // Vérification des permissions
        abort_unless(Auth::user()->can('catalog.view'), 403);

        // Utiliser la méthode du trait pour stocker le tenant
        $this->setTenant($this->getCurrentTenant());

        if (!$this->getCurrentTenant()) {
            return redirect()->route('identification');
        }

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

    // Nombre de catégories par page
    public $perPage = 9;

    // Les méthodes de gestion du tenant sont maintenant définies dans le trait WithTenantContext

    public function render()
    {
        try {
            // Récupérer le tenant en utilisant la méthode du trait
            $tenant = $this->getCurrentTenant();

            // Traitement global des données sans filtrer par tenant
            $query = Category::query();

            if (!empty($this->search)) {
                $query->where('name', 'like', "%{$this->search}%");
            }

            // Récupérer toutes les catégories dans une liste plate avec leurs relations
            $query->with(['parent', 'parent.parent', 'products']);

            // Trier les catégories par nom
            $query->orderBy('name', 'asc');

            // Paginer les résultats
            $categories = $query->paginate($this->perPage);

            return view('livewire.catalog.categories.index', [
                'categories' => $categories,
                'title' => 'Gestion des Catégories',
                'tenant' => $tenant,
                'colorToTailwind' => function($hex) {
                    return $this->colorToTailwind($hex);
                }
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur dans Categories\Index::render: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            $this->error('Erreur lors du chargement des catégories: ' . $e->getMessage());
            return view('livewire.catalog.categories.index', [
                'categories' => collect(),
                'title' => 'Gestion des Catégories',
                'colorToTailwind' => function($hex) {
                    return $this->colorToTailwind($hex);
                }
            ]);
        }
    }
}
