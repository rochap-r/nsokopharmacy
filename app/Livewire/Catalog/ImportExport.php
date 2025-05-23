<?php

namespace App\Livewire\Catalog;

use App\Exports\CatalogExport;
use App\Imports\CatalogImport;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Http\Livewire\Traits\WithTenantContext;
use Maatwebsite\Excel\Facades\Excel;

class ImportExport extends Component
{
    use WithFileUploads, WithTenantContext;

    public $file;
    public $importMode = false;
    public $exportMode = false;
    public $importSuccess = false;
    public $exportSuccess = false;
    public $importErrors = [];
    public $importStats = [];
    public $processing = false;

    // Filtres pour l'export
    public $category_id = null;
    public $search = '';
    public $active = true;

    // Liste des catégories pour le filtre
    public $categories = [];



    protected $rules = [
        'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
    ];

    public function mount($tenant = null)
    {
        // Récupérer le tenant depuis le middleware
        $tenant = app('tenant');

        if (!$tenant) {
            return redirect()->route('identification');
        }

        // Utiliser la méthode du trait pour stocker le tenant
        $this->setTenant($tenant);

        // Vérification des permissions
        abort_unless(Auth::user()->can('catalog.view'), 403);

        // Charger les catégories pour le filtre d'export
        $this->loadCategories();
    }

    public function loadCategories()
    {
        // Récupérer toutes les catégories principales (sans parent)
        $this->categories = Category::whereNull('parent_id')
            ->with(['children', 'children.children'])
            ->get()
            ->toArray();
    }

    public function render()
    {
        // Charger les catégories parentes pour le filtre d'export
        $parentCategories = Category::whereNull('parent_id')
            ->with(['children', 'children.children'])
            ->get();

        return view('livewire.catalog.import-export', [
            'productCount' => Product::count(),
            'parentCategories' => $parentCategories,
        ]);
    }

    public function showImportForm()
    {
        // Vérification des permissions
        abort_unless(Auth::user()->can('catalog.import'), 403);

        $this->resetValidation();
        $this->reset('file', 'importSuccess', 'importErrors', 'importStats');
        $this->importMode = true;
        $this->exportMode = false;
    }

    public function showExportForm()
    {
        // Vérification des permissions
        abort_unless(Auth::user()->can('catalog.export'), 403);

        $this->exportMode = true;
        $this->importMode = false;
    }

    public function cancel()
    {
        $this->reset('file', 'importMode', 'exportMode', 'importSuccess', 'exportSuccess');

        return redirect()->route('tenant.catalog.index', ['tenant' => $this->getCurrentTenant()]);
    }

    public function import()
    {
        // Vérification des permissions
        abort_unless(Auth::user()->can('catalog.import'), 403);

        $this->validate();
        $this->processing = true;

        try {
            $import = new CatalogImport($this->tenant_id);
            Excel::import($import, $this->file);

            $this->importSuccess = true;
            $this->importStats = [
                'products' => $import->getImportedCount(),
                'categories' => $import->getCategoriesCreatedCount(),
            ];
            $this->importErrors = $import->getErrors();

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Import réalisé avec succès!'
            ]);
        } catch (\Exception $e) {
            $this->importErrors[] = ['line' => 0, 'message' => $e->getMessage()];
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Erreur lors de l\'import: ' . $e->getMessage()
            ]);
        }

        $this->processing = false;
        $this->reset('file');
    }

    public function export()
    {
        // Vérification des permissions
        abort_unless(Auth::user()->can('catalog.export'), 403);

        $this->processing = true;

        try {
            $filters = [
                'category_id' => $this->category_id,
                'search' => $this->search,
                'active' => $this->active,
            ];

            $this->exportSuccess = true;
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Téléchargement du catalogue en cours...'
            ]);

            $this->processing = false;

            return Excel::download(new CatalogExport($filters), 'catalogue-' . now()->format('Y-m-d') . '.xlsx');
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Erreur lors de l\'export: ' . $e->getMessage()
            ]);
            $this->processing = false;
        }
    }
}
