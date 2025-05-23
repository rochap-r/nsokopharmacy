<?php

namespace App\Livewire\Catalog\Categories;

use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Component;
use App\Http\Livewire\Traits\WithToast;
use App\Http\Livewire\Traits\WithTenantContext;

class Create extends Component
{
    use WithToast, WithTenantContext;

    public $name;
    public $slug;
    public $color_code = '#a0aec0';
    public $parent_id = null;

    public $parentCategories = [];

    protected $rules = [
        'name' => 'required|string|max:255',
        'slug' => 'nullable|string|max:255|unique:categories,slug',
        'color_code' => 'nullable|string|max:20',
        'parent_id' => 'nullable|exists:categories,id',
    ];

    public function mount()
    {
        // Vérification des permissions
        abort_unless(Auth::user()->can('catalog.create'), 403);

        // Le tenant est disponible via le middleware à ce stade (requête GET initiale)
        $tenant = app('tenant');

        if (!$tenant) {
            return redirect()->route('identification');
        }

        // Utiliser la méthode du trait pour stocker le tenant
        $this->setTenant($tenant);

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
    }

    public function loadParentCategories()
    {
        // Récupérer toutes les catégories principales (sans parent) et les sous-catégories (pas les sous-sous-catégories)
        $this->parentCategories = Category::whereNull('parent_id')
            ->with(['children'])
            ->get()
            ->toArray();
    }

    public function updatedName()
    {
        // Générer automatiquement le slug à partir du nom si le champ slug est vide
        if (empty($this->slug)) {
            $this->slug = Str::slug($this->name);
        }
    }

    public function create()
    {
        // Si le slug est vide, générer à partir du nom
        if (empty($this->slug)) {
            $this->slug = Str::slug($this->name);
        }

        $this->validate();

        try {
            // Récupérer le tenant
            $tenant = $this->getCurrentTenant();

            //si parent_id est differente de null selectionne le parent et utilise la couleur du parent
            //pour créer une variante pour le fils sinon utilise la couleur choisie par l'utilisateur
            if ($this->parent_id) {
                $parent = Category::findOrFail($this->parent_id);
                $this->color_code = $this->getChildColorVariant($parent->color_code);
            } else {
                $this->color_code = $this->color_code;
            }

            Category::create([
                'name' => $this->name,
                'slug' => $this->slug,
                'color_code' => $this->color_code,
                'parent_id' => $this->parent_id,
            ]);

            $this->success('Catégorie créée avec succès!');

            return redirect()->route('tenant.catalog.categories.index', ['tenant' => $tenant]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de la catégorie: ' . $e->getMessage());
            $this->error('Erreur lors de la création de la catégorie: ' . $e->getMessage());
        }
    }

    public function getChildColorVariant($parentHexColor)
    {
        // Suppression du # si présent
        $hex = ltrim($parentHexColor, '#');

        // Conversion en RGB
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        // Création d'une variante plus claire (augmentation de la luminosité)
        $r = min(255, intval($r * 1.2));
        $g = min(255, intval($g * 1.2));
        $b = min(255, intval($b * 1.2));

        // Conversion en hex
        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }

    public function render()
    {
        try {
            // Récupérer le tenant
            $tenant = $this->getCurrentTenant();

            return view('livewire.catalog.categories.create', [
                'tenant' => $tenant,
                'title' => 'Créer une Catégorie'
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur dans Categories\Create::render: ' . $e->getMessage());

            $this->error('Erreur lors du chargement du formulaire: ' . $e->getMessage());
            return view('livewire.catalog.categories.create', [
                'title' => 'Créer une Catégorie'
            ]);
        }
    }
}
