<div class="max-w-7xl mx-auto py-8 sm:px-6 lg:px-8" x-data="{ showDeleteModal: false, productIdToDelete: null }">
    <header class="mb-8">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center">
            <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white mb-4 md:mb-0">{{ $title ?? 'Catalogue des Produits' }}</h1>
            <div class="flex flex-wrap gap-3">
                @can('catalog.import')
                    <a href="{{ tenant_route('tenant.catalog.import-export', ['tenant' => request()->route('tenant')]) }}"
                       class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-blue-700 dark:hover:bg-blue-600 transition-colors duration-200"
                       wire:navigate>
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                        </svg>
                        Import/Export
                    </a>
                @endcan
                @can('catalog.view')
                    <a href="{{ tenant_route('tenant.catalog.aisles.index', ['tenant' => request()->route('tenant')]) }}"
                       class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-indigo-700 dark:hover:bg-indigo-600 transition-colors duration-200"
                       wire:navigate>
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                        </svg>
                        Emplacements / Rayons
                    </a>
                @endcan
                @can('catalog.create')
                    <a href="{{ tenant_route('tenant.catalog.products.create', ['tenant' => request()->route('tenant')]) }}"
                       class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 dark:bg-primary-700 dark:hover:bg-primary-600 transition-colors duration-200"
                       wire:navigate>
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Nouveau Produit
                    </a>
                @endcan
                @can('catalog.view')
                    <a href="{{ tenant_route('tenant.catalog.categories.index', ['tenant' => request()->route('tenant')]) }}"
                       class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg shadow-sm text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 dark:bg-purple-700 dark:hover:bg-purple-600 transition-colors duration-200"
                       wire:navigate>
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        Catégories
                    </a>
                @endcan
            </div>
        </div>
    </header>
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-xl dark:border dark:border-gray-700 transition-all duration-200">
        <!-- Filtres de recherche -->
        <div class="p-6 bg-gray-50 dark:bg-gray-700/50 rounded-xl border-b border-gray-200 dark:border-gray-600/50">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div class="space-y-2">
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Rechercher un produit</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                     fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                          d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                          clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <input wire:model.live.debounce.300ms="search" type="text" id="search"
                                   class="bg-white dark:bg-gray-800 block w-full pl-10 pr-3 py-2.5 border-0 text-gray-900 dark:text-white rounded-lg ring-1 ring-inset ring-gray-300 dark:ring-gray-600
                                   placeholder-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-500
                                   dark:focus:ring-primary-500 transition-all duration-200"
                                   placeholder="Rechercher par nom, DCI...">
                            @if(!empty($search))
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <button wire:click="resetSearch" class="text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400 focus:outline-none" title="Effacer la recherche">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Catégorie</label>
                        <select wire:model.live="category_id" id="category_id"
                                class="w-full bg-white dark:bg-gray-800 border-0 text-gray-900 rounded-lg py-2.5 ring-1 ring-inset ring-gray-300
                                outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500
                                dark:text-white dark:ring-gray-600 dark:focus:ring-primary-500 transition-all duration-200">
                            <option value="">Toutes les catégories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category['id'] }}">{{ $category['name'] }}</option>
                            @if(isset($category['children']) && count($category['children']) > 0)
                                @foreach($category['children'] as $child)
                                    <option value="{{ $child['id'] }}">&nbsp;&nbsp;└ {{ $child['name'] }}</option>
                                    @if(isset($child['children']) && count($child['children']) > 0)
                                        @foreach($child['children'] as $grandchild)
                                            <option value="{{ $grandchild['id'] }}">&nbsp;&nbsp;&nbsp;&nbsp;└ {{ $grandchild['name'] }}</option>
                                        @endforeach
                                    @endif
                                @endforeach
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="space-y-2">
                    <label for="active" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Statut</label>
                    <select wire:model.live="active" id="active"
                            class="w-full bg-white dark:bg-gray-800 border-0 text-gray-900 rounded-lg py-2.5 ring-1 ring-inset ring-gray-300
                            outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500
                            dark:text-white dark:ring-gray-600 dark:focus:ring-primary-500 transition-all duration-200">
                        <option value="1">Produits actifs</option>
                        <option value="0">Produits inactifs</option>
                        <option value="">Tous les produits</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">DCI</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Dosage</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Forme Galénique</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Catégorie</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Personne</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Statut</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($products as $product)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $product->dci }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $product->dosage }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $product->forme_galenique }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    @if($product->category)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background-color: {{ $product->category->color_code ?? '#e5e7eb' }}; color: {{ $product->category->color_code ? '#ffffff' : '#374151' }}">
                                            {{ $product->category->name }}
                                        </span>
                                    @else
                                        Non catégorisé
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $product->type }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $product->personne }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    @if($product->active)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-100">Actif</span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 dark:bg-red-800 text-red-800 dark:text-red-100">Inactif</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex items-center justify-end space-x-2">
                                    <div class="flex space-x-2">
                                        @can('catalog.view')
                                            <a href="{{ tenant_route('tenant.catalog.products.show', ['tenant' => request()->route('tenant'), 'product' => $product->id]) }}"
                                               class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 transition-colors duration-200"
                                               wire:navigate>
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>
                                        @endcan
                                        @can('catalog.edit')
                                            <a href="{{ tenant_route('tenant.catalog.products.edit', ['tenant' => request()->route('tenant'), 'product' => $product->id]) }}"
                                               class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 transition-colors duration-200"
                                               wire:navigate>
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>
                                        @endcan
                                        @can('catalog.delete')
                                            <button @click="showDeleteModal = true; productIdToDelete = {{ $product->id }}" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 transition-colors duration-200">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-8 whitespace-nowrap text-center">
                                    <div class="flex flex-col items-center justify-center space-y-3">
                                        <svg class="w-12 h-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <p class="text-gray-500 dark:text-gray-400 text-lg">Aucun produit trouvé</p>
                                        @can('catalog.create')
                                            <a href="{{ tenant_route('tenant.catalog.products.create', ['tenant' => request()->route('tenant')]) }}"
                                               class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 dark:bg-primary-700 dark:hover:bg-primary-600 transition-colors duration-200"
                                               wire:navigate>
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                                Ajouter un produit
                                            </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-6 border-t border-gray-200 dark:border-gray-700">
                {{ $products->links() }}
            </div>

        </div>

        <!-- Delete Confirmation Modal -->
        <div x-show="showDeleteModal"
             class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            <div @click.away="showDeleteModal = false"
                 class="bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-xl transform transition-all max-w-lg w-full p-6"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">
                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mt-4">Confirmation de suppression</h3>
                    <p class="text-gray-500 dark:text-gray-400 mt-2">Êtes-vous sûr de vouloir supprimer ce produit? Cette action est irréversible.</p>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button @click="showDeleteModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        Annuler
                    </button>
                    <button wire:click="deleteProduct(productIdToDelete)" @click="showDeleteModal = false" class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                        Supprimer
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
