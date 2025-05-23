<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class CatalogImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $tenantId;
    protected $errors = [];
    protected $imported = 0;
    protected $categoriesCreated = 0;

    public function __construct($tenantId)
    {
        $this->tenantId = $tenantId;
    }

    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            try {
                // Création ou récupération de la hiérarchie des catégories
                $mainCategory = null;
                $subCategory = null;
                $subSubCategory = null;

                // Création ou récupération de la catégorie principale
                if (!empty($row['categorie'])) {
                    $mainCategory = Category::firstOrCreate(
                        ['name' => $row['categorie'], 'parent_id' => null],
                        ['slug' => Str::slug($row['categorie'])]
                    );
                    
                    $this->categoriesCreated += $mainCategory->wasRecentlyCreated ? 1 : 0;

                    // Création ou récupération de la sous-catégorie
                    if (!empty($row['sous_categorie'])) {
                        $subCategory = Category::firstOrCreate(
                            ['name' => $row['sous_categorie'], 'parent_id' => $mainCategory->id],
                            ['slug' => Str::slug($row['sous_categorie'])]
                        );
                        
                        $this->categoriesCreated += $subCategory->wasRecentlyCreated ? 1 : 0;

                        // Création ou récupération de la sous-sous-catégorie
                        if (!empty($row['sous_sous_categorie'])) {
                            $subSubCategory = Category::firstOrCreate(
                                ['name' => $row['sous_sous_categorie'], 'parent_id' => $subCategory->id],
                                ['slug' => Str::slug($row['sous_sous_categorie'])]
                            );
                            
                            $this->categoriesCreated += $subSubCategory->wasRecentlyCreated ? 1 : 0;
                        }
                    }
                }

                // Déterminer la catégorie à laquelle associer le produit
                $categoryId = null;
                if ($subSubCategory) {
                    $categoryId = $subSubCategory->id;
                } elseif ($subCategory) {
                    $categoryId = $subCategory->id;
                } elseif ($mainCategory) {
                    $categoryId = $mainCategory->id;
                }

                // Déterminer le color_code en fonction d'une règle métier
                $colorCode = $this->determineColorCode($row);

                // Création du produit
                $product = Product::updateOrCreate(
                    [
                        'dci' => $row['dci'],
                        'dosage' => $row['dosage'],
                        'forme_galenique' => $row['forme_galenique'],
                    ],
                    [
                        'color_code' => $colorCode,
                        'type' => $row['type'] ?? null,
                        'personne' => $row['personne'] ?? null,
                        'category_id' => $categoryId,
                        'active' => true
                    ]
                );

                $this->imported++;
            } catch (\Exception $e) {
                $this->errors[] = ["line" => $row->getIndex() + 2, "message" => $e->getMessage()];
            }
        }
    }

    /**
     * Déterminer la couleur en fonction des caractéristiques du produit
     */
    protected function determineColorCode($row)
    {
        // Exemple de règle métier pour déterminer la couleur
        // Cette logique peut être modifiée selon les besoins spécifiques
        if (strtolower($row['personne'] ?? '') === 'enfant') {
            return '#4299e1'; // Bleu pour les médicaments enfants
        } elseif (strtolower($row['type'] ?? '') === 'antibiotique') {
            return '#68d391'; // Vert pour les antibiotiques
        } elseif (strtolower($row['type'] ?? '') === 'analgesique') {
            return '#fc8181'; // Rouge pour les analgésiques
        }

        return '#a0aec0'; // Gris par défaut
    }

    /**
     * Règles de validation pour l'import
     */
    public function rules(): array
    {
        return [
            'dci' => 'required|string',
            'dosage' => 'nullable|string',
            'forme_galenique' => 'nullable|string',
            'categorie' => 'nullable|string',
            'sous_categorie' => 'nullable|string',
            'sous_sous_categorie' => 'nullable|string',
            'type' => 'nullable|string',
            'personne' => 'nullable|string',
        ];
    }

    /**
     * Messages d'erreur personnalisés pour la validation
     */
    public function customValidationMessages()
    {
        return [
            'dci.required' => 'La colonne DCI est obligatoire.',
        ];
    }

    /**
     * Récupérer les erreurs d'importation
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Récupérer le nombre d'éléments importés
     */
    public function getImportedCount(): int
    {
        return $this->imported;
    }

    /**
     * Récupérer le nombre de catégories créées
     */
    public function getCategoriesCreatedCount(): int
    {
        return $this->categoriesCreated;
    }
}
