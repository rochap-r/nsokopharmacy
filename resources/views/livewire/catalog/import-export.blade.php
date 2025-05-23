<div class="max-w-7xl mx-auto py-8 sm:px-6 lg:px-8">
    <header class="mb-8">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center">
            <div>
                <div class="flex items-center mb-4">
                    <a href="{{ tenant_route('tenant.catalog.index') }}" class="inline-flex items-center text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-500 transition-colors duration-200" wire:navigate>
                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Retour au catalogue
                    </a>
                </div>
                <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white mb-4 md:mb-0">Import/Export Catalogue</h1>
            </div>
            <div class="flex items-center gap-4 flex-wrap">
                <span class="flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 rounded-lg text-gray-700 dark:text-gray-300 font-medium">
                    <svg class="w-5 h-5 mr-2 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <span class="font-medium">{{ $productCount }} produits</span>
                </span>
                <div class="flex flex-wrap gap-3">
                    @can('catalog.import')
                        <button wire:click="showImportForm" class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-blue-700 dark:hover:bg-blue-600 transition-colors duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                            </svg>
                            Importer
                        </button>
                    @endcan
                    @can('catalog.export')
                        <button wire:click="showExportForm" class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-green-700 dark:hover:bg-green-600 transition-colors duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            Exporter
                        </button>
                    @endcan
                </div>
            </div>
        </div>
    </header>

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-xl dark:border dark:border-gray-700 transition-all duration-200">

            @if($importMode)
                <div class="p-6">
                    <div class="flex items-center mb-6">
                        <svg class="w-6 h-6 text-blue-500 dark:text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                        </svg>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Importer des produits</h3>
                    </div>

                    @if(!$importSuccess)
                        <form wire:submit.prevent="import" class="space-y-6">
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
                                <div class="mb-4">
                                    <label for="file" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Fichier Excel (.xlsx, .xls, .csv)</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <input type="file" wire:model="file" id="file" class="block w-full text-sm text-gray-500 dark:text-gray-400
                                            file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0
                                            file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700
                                            dark:file:bg-blue-900/30 dark:file:text-blue-400
                                            hover:file:bg-blue-100 dark:hover:file:bg-blue-900/40 cursor-pointer
                                            transition-colors duration-200">
                                    </div>
                                    @error('file') <span class="text-red-500 dark:text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="bg-yellow-50 dark:bg-yellow-900/30 p-4 rounded-lg border border-yellow-200 dark:border-yellow-800">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-yellow-400 dark:text-yellow-300" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Instructions d'importation</h3>
                                            <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                                                <ul class="list-disc pl-5 space-y-1">
                                                    <li>Utilisez le modèle d'importation pour garantir la compatibilité</li>
                                                    <li>Assurez-vous que les catégories existent déjà dans le système</li>
                                                    <li>Les colonnes obligatoires sont : DCI, Nom, Catégorie</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end space-x-3">
                                <a href="{{ tenant_route('tenant.catalog.index') }}" class="px-4 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-300 dark:focus:ring-gray-600 font-medium transition-colors duration-200" wire:navigate>Retour au catalogue</a>
                                <button type="submit" class="px-4 py-2.5 bg-blue-600 dark:bg-blue-700 text-white rounded-lg hover:bg-blue-700 dark:hover:bg-blue-600 disabled:opacity-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 font-medium transition-colors duration-200" wire:loading.attr="disabled" wire:target="import">
                                    <span wire:loading.remove wire:target="import" class="flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                        </svg>
                                        Importer
                                    </span>
                                    <span wire:loading wire:target="import" class="inline-flex items-center">
                                        <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Traitement...
                                    </span>
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700 mb-6">
                            <div class="bg-green-50 dark:bg-green-900/30 p-4 rounded-lg border border-green-200 dark:border-green-800 mb-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-6 w-6 text-green-500 dark:text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-lg font-medium text-green-800 dark:text-green-300">Import réussi !</h3>
                                        <div class="mt-2 text-sm text-green-700 dark:text-green-400">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                                <div class="flex items-center">
                                                    <svg class="w-5 h-5 mr-2 text-green-600 dark:text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                    <p>{{ $importStats['products'] ?? 0 }} produits importés</p>
                                                </div>
                                                <div class="flex items-center">
                                                    <svg class="w-5 h-5 mr-2 text-green-600 dark:text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                    <p>{{ $importStats['categories'] ?? 0 }} catégories créées</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        @if(count($importErrors) > 0)
                            <div class="bg-red-50 dark:bg-red-900/30 p-4 rounded-lg border border-red-200 dark:border-red-800 mb-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-6 w-6 text-red-500 dark:text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-lg font-medium text-red-800 dark:text-red-300">Certaines lignes n'ont pas pu être importées</h3>
                                        <div class="mt-2 text-sm text-red-700 dark:text-red-400">
                                            <ul class="list-disc pl-5 space-y-1">
                                                @foreach($importErrors as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="flex justify-end space-x-3">
                            <a href="{{ tenant_route('tenant.catalog.index') }}" class="px-4 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-300 dark:focus:ring-gray-600 font-medium transition-colors duration-200" wire:navigate>Retour au catalogue</a>
                            <button type="button" wire:click="cancel" class="px-4 py-2.5 bg-gray-700 dark:bg-gray-600 text-white rounded-lg hover:bg-gray-600 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 dark:focus:ring-gray-400 font-medium transition-colors duration-200">Fermer</button>
                        </div>
                    @endif
                </div>
            @endif

            @if($exportMode)
                <div class="p-6">
                    <div class="flex items-center mb-6">
                        <svg class="w-6 h-6 text-green-500 dark:text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Exporter des produits</h3>
                    </div>

                    <form wire:submit.prevent="export" class="space-y-6">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                                <div>
                                    <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Catégorie</label>
                                    <select wire:model="category_id" id="category_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500 focus:ring-opacity-50 dark:focus:border-primary-400 dark:focus:ring-primary-400 transition-colors duration-200">
                                        <option value="">Toutes les catégories</option>
                                        @foreach($parentCategories as $parentCategory)
                                            <option value="{{ $parentCategory->id }}">{{ $parentCategory->name }}</option>
                                            @if($parentCategory->children->count() > 0)
                                                @foreach($parentCategory->children as $child)
                                                    <option value="{{ $child->id }}">&nbsp;&nbsp;&nbsp;{{ $child->name }}</option>
                                                    @if($child->children->count() > 0)
                                                        @foreach($child->children as $grandchild)
                                                            <option value="{{ $grandchild->id }}">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $grandchild->name }}</option>
                                                        @endforeach
                                                    @endif
                                                @endforeach
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Recherche</label>
                                    <div class="relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <input type="text" wire:model.defer="search" id="search" placeholder="Rechercher par DCI, dosage, etc." class="pl-10 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500 focus:ring-opacity-50 dark:focus:border-primary-400 dark:focus:ring-primary-400 transition-colors duration-200">
                                    </div>
                                </div>

                                <div>
                                    <label for="active" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Statut</label>
                                    <select wire:model="active" id="active" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500 focus:ring-opacity-50 dark:focus:border-primary-400 dark:focus:ring-primary-400 transition-colors duration-200">
                                        <option value="1">Produits actifs uniquement</option>
                                        <option value="0">Produits inactifs uniquement</option>
                                        <option value="">Tous les produits</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <a href="{{ tenant_route('tenant.catalog.index') }}" class="px-4 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-300 dark:focus:ring-gray-600 font-medium transition-colors duration-200" wire:navigate>Retour au catalogue</a>
                            <a href="{{ tenant_route('tenant.catalog.index') }}" class="px-4 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-300 dark:focus:ring-gray-600 font-medium transition-colors duration-200" wire:navigate>Annuler</a>
                            <button type="submit" class="px-4 py-2.5 bg-green-600 dark:bg-green-700 text-white rounded-lg hover:bg-green-700 dark:hover:bg-green-600 disabled:opacity-50 focus:outline-none focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400 font-medium transition-colors duration-200" wire:loading.attr="disabled" wire:target="export">
                                <span wire:loading.remove wire:target="export" class="flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                    Exporter
                                </span>
                                <span wire:loading wire:target="export" class="inline-flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Préparation...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            @if(!$importMode && !$exportMode)
                <div class="p-6">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
                        <div class="text-center max-w-3xl mx-auto">
                            <div class="flex justify-center mb-4">
                                <div class="rounded-full bg-blue-100 dark:bg-blue-900/30 p-3">
                                    <svg class="h-10 w-10 text-blue-500 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Gestion du catalogue</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-6">
                                Utilisez les boutons ci-dessus pour importer ou exporter des données du catalogue. Vous pouvez importer des nouveaux produits à partir d'un fichier Excel ou exporter le catalogue existant pour l'analyser ou le modifier.
                            </p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-blue-50 dark:bg-blue-900/30 rounded-lg p-4 text-left border border-blue-200 dark:border-blue-800">
                                    <h4 class="font-medium text-blue-800 dark:text-blue-300 mb-2 flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                                        </svg>
                                        Importer
                                    </h4>
                                    <p class="text-blue-700 dark:text-blue-300 text-sm">
                                        Importez vos produits depuis un fichier Excel (.xlsx, .xls) ou CSV. Vérifiez que votre fichier contient les colonnes requises.
                                    </p>
                                </div>
                                <div class="bg-green-50 dark:bg-green-900/30 rounded-lg p-4 text-left border border-green-200 dark:border-green-800">
                                    <h4 class="font-medium text-green-800 dark:text-green-300 mb-2 flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                        </svg>
                                        Exporter
                                    </h4>
                                    <p class="text-green-700 dark:text-green-300 text-sm">
                                        Exportez votre catalogue en filtrant par catégories, statut ou termes de recherche. Idéal pour la sauvegarde ou l'analyse.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
