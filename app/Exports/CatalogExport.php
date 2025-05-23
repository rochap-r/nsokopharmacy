<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Database\Eloquent\Builder;

class CatalogExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * @return Builder
     */
    public function query()
    {
        $query = Product::query();

        // Appliquer les filtres si pru00e9sents
        if (!empty($this->filters['category_id'])) {
            $query->where('category_id', $this->filters['category_id']);
        }

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('dci', 'like', "%{$search}%")
                  ->orWhere('dosage', 'like', "%{$search}%")
                  ->orWhere('forme_galenique', 'like', "%{$search}%");
            });
        }

        if (isset($this->filters['active'])) {
            $query->where('active', $this->filters['active']);
        }

        return $query->with(['category', 'category.parent', 'category.parent.parent']);
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'DCI',
            'DOSAGE',
            'FORME GALENIQUE',
            'SOUS SOUS CATEGORIE',
            'SOUS CATEGORIE',
            'CATEGORIE',
            'type',
            'personne'
        ];
    }

    /**
     * @param mixed $product
     * @return array
     */
    public function map($product): array
    {
        // Du00e9terminer la hiu00e9rarchie des catu00e9gories
        $category = $product->category;
        $subSubCategory = '';
        $subCategory = '';
        $mainCategory = '';

        if ($category) {
            if ($category->parent && $category->parent->parent) {
                // C'est une sous-sous-catu00e9gorie
                $subSubCategory = $category->name;
                $subCategory = $category->parent->name;
                $mainCategory = $category->parent->parent->name;
            } elseif ($category->parent) {
                // C'est une sous-catu00e9gorie
                $subCategory = $category->name;
                $mainCategory = $category->parent->name;
            } else {
                // C'est une catu00e9gorie principale
                $mainCategory = $category->name;
            }
        }

        return [
            $product->dci,
            $product->dosage,
            $product->forme_galenique,
            $subSubCategory,
            $subCategory,
            $mainCategory,
            $product->type,
            $product->personne
        ];
    }

    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style pour l'en-tu00eate
            1 => ['font' => ['bold' => true]],
        ];
    }
}
