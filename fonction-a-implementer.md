### Contexte Général

Développez une fonctionnalité d'import/export dans le module **Catalogue** d'une application multi-tenant de gestion de produits pharmaceutiques. Les modèles et leurs migrations existent déjà. La gestion des rôles et permissions est assurée via **spatie/permissions** et le système de feedback (notifications/messages flash) est intégré. L'interface doit utiliser **Livewire 3** pour la logique côté serveur, **Alpine JS** pour les interactions client et **Tailwind CSS** pour le design.

## Pas mal de s'inspirer de module déjà implementé comme la gestion de roles

> **Note importante :** Pour toutes les fonctionnalités développées (import, export, CRUD pour produits, catégories et rayons), créez et enregistrez les permissions correspondantes dans le `PermissionSeeder`. Par exemple : `catalog.import`, `catalog.export`, `catalog.create`, `catalog.update`, `catalog.delete`.

---

### Étape 1 : Modèles et Structure de Données

1. **Product (Produit pharmaceutique)**
   - **Champs à mapper lors de l’import :**
     - `dci` ← Colonne "DCI"
     - `dosage` ← Colonne "DOSAGE"
     - `forme_galenique` ← Colonne "FORME GALÉNIQUE"
     - `color_code` ← À déterminer dynamiquement (via une règle métier)
     - `type` ← Colonne "type"
     - `personne` ← Colonne "personne"
     - `category_id` ← Doit être associé à la catégorie résultant de la hiérarchie (voir ci-dessous)
     - `aisle_id` ← Optionnel, à définir selon la logique du projet
     - `active` ← Défini sur `true` par défaut

2. **Category (Catégorie)**
   - **Champs pour la hiérarchie :**
     - `name` ← Issue des colonnes "CATEGORIE", "SOUS CATEGORIE" et "SOUS SOUS CATEGORIE"
     - `slug` ← Généré à partir du nom pour créer une URL-friendly
     - `color_code` ← Optionnel
     - `parent_id` ← Pour structurer la hiérarchie (CATEGORIE → SOUS CATEGORIE → SOUS SOUS CATEGORIE)

3. **Aisle (Rayon / Emplacement)**
   - **Champs principaux :**
     - `code`, `name`, `category_id`, `tenant_id`, `is_global`
   - Ce modèle sera intégré ultérieurement dans la logique d’assignation si nécessaire, mais il n'est pas au cœur du processus d'import initial.

---

### Étape 2 : Fonctionnalités d'Import/Export

#### Importation des Données

- **Interface d'Import :**
  - Créez un composant Livewire accessible depuis le module Catalogue (via un item dans la sidebar) qui propose un formulaire pour sélectionner et uploader un fichier Excel.

- **Lecture et Traitement du Fichier :**
  - Utilisez le package **maatwebsite/excel** pour lire le fichier Excel.
  - Le fichier doit respecter l’ordre et les colonnes suivants :
    - `DCI`, `DOSAGE`, `FORME GALÉNIQUE`, `SOUS SOUS CATEGORIE`, `SOUS CATEGORIE`, `CATEGORIE`, `type`, `personne`
  - Pour chaque ligne du fichier :
    - Mappez les colonnes aux attributs du modèle **Product** (avec `active` par défaut à `true`).
    - **Gestion hiérarchique des Catégories :**
      - Vérifiez et/ou créez la catégorie définie par la colonne "CATEGORIE".
      - Si présente, rattachez "SOUS CATEGORIE" en tant qu'enfant de "CATEGORIE".
      - Si présente, rattachez "SOUS SOUS CATEGORIE" en tant qu'enfant de "SOUS CATEGORIE".
      - Associez le produit à la catégorie la plus précise (privilégiez "SOUS SOUS CATEGORIE", sinon "SOUS CATEGORIE", sinon "CATEGORIE").

- **Gestion des Erreurs et Feedback :**
  - Validez la structure et le contenu du fichier avant l'import (colonnes obligatoires, format, etc.).
  - Gérez les erreurs (doublons, données manquantes, format incorrect) et affichez des notifications via le système de feedback existant.

#### Exportation des Données

- **Interface d’Export :**
  - Proposez à l’utilisateur la possibilité d’exporter l’ensemble (ou une sélection filtrée) des produits en un fichier Excel.

- **Traitement de l'Export :**
  - Utilisez **Laravel Excel** pour générer et télécharger le fichier.
  - Intégrez la gestion des droits d’accès et des filtres pour déterminer si l’utilisateur peut exporter l'intégralité ou une partie des données.

---

### Étape 3 : Interface Utilisateur et Gestion des Interactions

- **Utilisation de Livewire et Alpine JS :**
  - Le composant Livewire gèrera l’upload du fichier, le traitement en temps réel et la validation côté serveur.
  - Utilisez Alpine JS pour gérer les interactions côté client : animations, modales de confirmation, menus déroulants, etc.

- **Design et Responsivité :**
  - Appliquez **Tailwind CSS** pour offrir une interface claire, moderne et responsive.

- **Feedback Utilisateur :**
  - Affichez immédiatement des notifications ou messages flash pour informer l'utilisateur du succès ou des erreurs lors des opérations d'import/export.

---

### Étape 4 : Sécurité et Contrôle d’Accès

- **Vérification des Permissions :**
  - Avant toute opération (import ou export), vérifiez que l’utilisateur dispose des permissions requises (par exemple, `catalog.manage`).
  
- **Création des Permissions dans le PermissionSeeder :**
  - Pour chaque fonctionnalité développée (import, export, création, modification, suppression), créez les permissions correspondantes dans le `PermissionSeeder` :
    - Par exemple : `catalog.import`, `catalog.export`, `catalog.create`, `catalog.update`, `catalog.delete`.

- **Validation des Données :**
  - Implémentez des règles de validation pour vous assurer que les données du fichier importé respectent la structure attendue et évitez les incohérences dans la base.

---

### Étape 5 : Technologies et Packages Techniques

- **Laravel Excel (maatwebsite/excel) :**
  - Pour l'import/export de fichiers Excel.
  - Installation :
    ```bash
    composer require maatwebsite/excel
    ```

- **Livewire 3 :**
  - Pour la gestion des interactions dynamiques côté serveur.

- **Alpine JS :**
  - Pour des interactions légères et réactives côté client.

- **Tailwind CSS :**
  - Pour assurer une interface moderne et responsive.

- **spatie/permissions :**
  - Pour gérer la sécurité et les rôles/permissions dans le projet. N’oubliez pas d’y ajouter les permissions spécifiques dans le `PermissionSeeder`.

---

### Étape 6 : Documentation et Livrables

- **Code Source Complet :**
  - Composant Livewire dédié pour l'import/export, avec ses vues Blade formatées avec Tailwind CSS et enrichies par Alpine JS.
  - Contrôleur ou service dédié pour le traitement du fichier Excel (lecture, mapping et création d’enregistrements pour **Product** et **Category**).
  - Mise à jour du `PermissionSeeder` pour inclure toutes les permissions (ex. `catalog.import`, `catalog.export`, etc.).
  - Tests unitaires et fonctionnels (si possible) pour garantir la robustesse de la fonctionnalité.

- **Documentation Technique :**
  - Schéma de l’architecture du module et du flux de données pendant l'import/export.
  - Instructions détaillées pour la configuration des permissions via spatie/permissions.
  - Guide d’utilisation pour l’interface d'import/export et les étapes à suivre par l’utilisateur.

- **Listes de fonctionnalités :**
réalistes toutes les fonctionnalités : importantes, critiques et necessaires pour ce catalogue par rapport au contexte du projet et des modeles à ta disposition.
