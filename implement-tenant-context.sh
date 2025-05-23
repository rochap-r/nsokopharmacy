#!/bin/bash

# Script pour implémenter le trait WithTenantContext dans tous les composants Livewire spécifiés

# Fonction pour ajouter le trait à un fichier
implementTrait() {
    local file=$1
    echo "Traitement du fichier: $file"
    
    # Vérifier si le trait est déjà importé
    if ! grep -q "use App\\\\Http\\\\Livewire\\\\Traits\\\\WithTenantContext;" "$file"; then
        # Ajouter l'import du trait après le dernier import
        sed -i '/^use /a use App\\Http\\Livewire\\Traits\\WithTenantContext;' "$file"
        echo "  - Import du trait ajouté"
    else
        echo "  - Import du trait déjà présent"
    fi
    
    # Vérifier si le trait est déjà utilisé dans la classe
    if ! grep -q "use WithTenantContext" "$file"; then
        # Ajouter l'utilisation du trait à la classe
        sed -i 's/use \([^;]*\);/use \1, WithTenantContext;/' "$file"
        echo "  - Utilisation du trait ajoutée"
    else
        echo "  - Utilisation du trait déjà présente"
    fi
    
    # Remplacer les références à app('tenant') ou \App\Models\Tenant::findOrFail($this->tenant_id)
    sed -i 's/\\App\\Models\\Tenant::findOrFail($this->tenant_id)/$this->getCurrentTenant()/g' "$file"
    sed -i 's/$tenant = app("tenant")/$tenant = $this->getCurrentTenant()/g' "$file"
    
    echo "  - Références au tenant mises à jour"
}

# Traiter tous les fichiers dans les dossiers spécifiés
echo "=== Implémentation du trait WithTenantContext ==="

# Composants Users
for file in /home/rochap/development/nsokopharmacy/app/Livewire/Settings/Users/*.php; do
    implementTrait "$file"
done

# Composants Roles
for file in /home/rochap/development/nsokopharmacy/app/Livewire/Settings/Roles/*.php; do
    implementTrait "$file"
done

# Composants Catalog (racine)
for file in /home/rochap/development/nsokopharmacy/app/Livewire/Catalog/*.php; do
    implementTrait "$file"
done

# Composants Catalog/Categories
for file in /home/rochap/development/nsokopharmacy/app/Livewire/Catalog/Categories/*.php; do
    implementTrait "$file"
done

# Composants Catalog/Products
for file in /home/rochap/development/nsokopharmacy/app/Livewire/Catalog/Products/*.php; do
    implementTrait "$file"
done

# Composants Catalog/Aisles
for file in /home/rochap/development/nsokopharmacy/app/Livewire/Catalog/Aisles/*.php; do
    implementTrait "$file"
done

echo "=== Implémentation terminée ==="
