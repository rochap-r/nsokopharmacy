<div class="max-w-7xl mx-auto py-8 sm:px-6 lg:px-8" x-data="{ showDeleteModal: false, categoryIdToDelete: null }">
    <header class="mb-8">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center">
            <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white mb-4 md:mb-0">
                {{ $title ?? 'Catégories de produits' }}</h1>
            <div class="flex flex-wrap gap-3">
                @can('catalog.import')
                    <a href="{{ tenant_route('tenant.catalog.import-export', ['tenant' => request()->route('tenant')]) }}"
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-blue-700 dark:hover:bg-blue-600 transition-colors duration-200"
                        wire:navigate>
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10">
                            </path>
                        </svg>
                        Import/Export
                    </a>
                @endcan
                <a href="{{ tenant_route('tenant.catalog.index', ['tenant' => request()->route('tenant')]) }}"
                    class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg shadow-sm text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-400 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 transition-colors duration-200"
                    wire:navigate>
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                    </svg>
                    Retour au catalogue
                </a>
                @can('catalog.create')
                    <a href="{{ tenant_route('tenant.catalog.categories.create', ['tenant' => request()->route('tenant')]) }}"
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 dark:bg-primary-700 dark:hover:bg-primary-600 transition-colors duration-200"
                        wire:navigate>
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Nouvelle catégorie
                    </a>
                @endcan
            </div>
        </div>
    </header>

    <div
        class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden border border-gray-100 dark:border-gray-700 transition-all duration-300">
        <div class="p-6">
            <div class="max-w-lg mx-auto mb-6">
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Rechercher
                    une catégorie</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" wire:model.live.debounce.300ms="search" id="search"
                        class="pl-10 w-full bg-white border-0 text-gray-900 rounded-lg py-3 ring-1 ring-inset ring-gray-300
                        placeholder:text-gray-400 outline-none shadow-sm
                        focus:ring-2 focus:ring-inset focus:ring-primary-500 focus:shadow-md
                        dark:bg-gray-800 dark:text-white dark:ring-gray-600 dark:placeholder-gray-500
                        dark:focus:ring-primary-500 transition-all duration-200"
                        placeholder="Nom de la catégorie...">
                    @if (!empty($search))
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <button wire:click="resetSearch"
                                class="text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400 focus:outline-none"
                                title="Effacer la recherche">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-6">
            @if ($categories->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5 lg:gap-6 p-2 sm:p-4">
                    @foreach ($categories as $category)
                        @php
                            // Déterminer la couleur à utiliser (hériter du parent si disponible)
                            $displayColor = $category->color_code;
                            $colorClass = $colorToTailwind($displayColor);
                            $isChild = false;
                            $hierarchyLevel = 0;
                            $hierarchyIndicator = '';

                            // Si c'est une catégorie enfant, utiliser une variante de la couleur du parent
                            if ($category->parent) {
                                $isChild = true;
                                $hierarchyLevel = 1;
                                $parentColor = $category->parent->color_code;
                                $parentColorClass = $colorToTailwind($parentColor);

                                // Utiliser la couleur du parent pour les enfants
                                $colorClass = $parentColorClass;

                                // Pour les petits-enfants, ajouter un indicateur supplémentaire
                                if ($category->parent->parent) {
                                    $hierarchyLevel = 2;
                                }

                                // Créer un indicateur visuel de la hiérarchie
                                if ($hierarchyLevel === 1) {
                                    $hierarchyIndicator = '<span class="mr-1 text-'.$colorClass.'-500">└</span>';
                                } elseif ($hierarchyLevel === 2) {
                                    $hierarchyIndicator = '<span class="mr-1 text-'.$colorClass.'-500">└─</span>';
                                }

                                // Utiliser une variante de la couleur du parent pour l'affichage
                                $displayColor = $category->color_code;
                            }
                        @endphp
                        <div class="group relative bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden transition-all duration-300 transform hover:-translate-y-1"
                            style="border-top: 4px solid {{ $displayColor }}">
                            <!-- Category header with color accent -->
                            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-transparent via-{{ $colorClass }}-500 to-transparent opacity-75"></div>

                            <!-- Main content area -->
                            <div class="p-4 sm:p-5 md:p-6">
                                <div class="flex items-start space-x-3 sm:space-x-4">
                                    <!-- Category icon -->
                                    <div class="flex-shrink-0">
                                        <div class="h-12 w-12 sm:h-14 sm:w-14 rounded-lg flex items-center justify-center shadow-md transform transition-transform group-hover:scale-110"
                                            style="background-color: {{ $category->color_code }}; color: white;">
                                            <span class="text-lg sm:text-xl font-semibold">{{ substr($category->name, 0, 1) }}</span>
                                        </div>
                                    </div>

                                    <!-- Category details -->
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white break-words group-hover:text-{{ $colorClass }}-600 dark:group-hover:text-{{ $colorClass }}-400 transition-colors">
                                            @if ($isChild)
                                                <span class="inline-flex items-start flex-wrap">
                                                    <span class="inline-block mr-1">
                                                        {!! $hierarchyIndicator !!}
                                                    </span>
                                                    <span class="break-all">{{ $category->name }}</span>
                                                </span>
                                            @else
                                                <span class="break-all">{{ $category->name }}</span>
                                            @endif
                                        </h3>

                                        <!-- Product count badge -->
                                        <div class="mt-2 inline-flex items-center px-2.5 sm:px-3 py-1 rounded-full text-sm font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                            <svg class="-ml-0.5 mr-1.5 sm:mr-2 h-4 w-4 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"></path>
                                            </svg>
                                            <span class="whitespace-nowrap">{{ $category->products->count() }} produits</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Hiérarchie de catégories -->
                                @if ($isChild)
                                    <div class="mt-2 sm:mt-3">
                                        <div class="inline-flex items-center px-2 sm:px-3 py-0.5 sm:py-1 rounded text-xs sm:text-sm font-medium bg-{{ $colorClass }}-50 text-{{ $colorClass }}-700 dark:bg-gray-700 dark:text-{{ $colorClass }}-300 border border-{{ $colorClass }}-200 dark:border-gray-600 max-w-full overflow-hidden">
                                            <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-1.5 flex-shrink-0 text-{{ $colorClass }}-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                            </svg>
                                            <span class="break-all">
                                                @if ($hierarchyLevel === 2)
                                                    <span class="hidden sm:inline">{{ $category->parent->parent->name }} &rsaquo; </span>{{ $category->parent->name }}
                                                @else
                                                    {{ $category->parent->name }}
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                @endif

                                <!-- Action buttons -->
                                <div class="mt-4 sm:mt-5 pt-3 sm:pt-4 border-t border-gray-100 dark:border-gray-700 flex flex-wrap sm:flex-nowrap justify-end gap-3 sm:gap-4">
                                    @can('catalog.edit')
                                        <a href="{{ tenant_route('tenant.catalog.categories.edit', ['tenant' => request()->route('tenant'), 'id' => $category->id]) }}"
                                            class="flex-1 sm:flex-initial inline-flex items-center justify-center px-3 sm:px-4 py-2 text-sm font-medium rounded-md text-{{ $colorClass }}-700 bg-{{ $colorClass }}-50 hover:bg-{{ $colorClass }}-100 dark:text-{{ $colorClass }}-400 dark:bg-gray-700 dark:hover:bg-gray-600 transition-colors duration-200 min-w-[100px]"
                                            wire:navigate>
                                            <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1.5 sm:mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                            <span class="whitespace-nowrap">Editer</span>
                                        </a>
                                    @endcan
                                    @can('catalog.delete')
                                        <button @click="showDeleteModal = true; categoryIdToDelete = {{ $category->id }}"
                                            class="flex-1 sm:flex-initial inline-flex items-center justify-center px-3 sm:px-4 py-2 text-sm font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100 dark:text-red-400 dark:bg-gray-700 dark:hover:bg-gray-600 transition-colors duration-200 min-w-[100px]">
                                            <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1.5 sm:mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                            <span class="whitespace-nowrap">Supprimer</span>
                                        </button>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z">
                        </path>
                    </svg>
                    <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-gray-100">Aucun résultat</h3>
                    <p class="mt-1 text-gray-500 dark:text-gray-400">Aucune catégorie ne correspond à votre recherche.
                    </p>
                </div>
            @endif

            @if ($categories->hasPages())
                <div class="mt-8 px-4">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4">
                        {{ $categories->onEachSide(1)->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal de confirmation de suppression -->
    <div x-show="showDeleteModal"
        class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div @click.away="showDeleteModal = false"
            class="bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-xl transform transition-all max-w-lg w-full p-6"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
            <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                    </path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mt-4">Confirmation de suppression</h3>
                <p class="text-gray-500 dark:text-gray-400 mt-2">Êtes-vous sûr de vouloir supprimer cette catégorie?
                    Cette
                    action est irréversible et pourrait affecter les produits associés.</p>
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <button @click="showDeleteModal = false"
                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    Annuler
                </button>
                <button wire:click="deleteCategory(categoryIdToDelete)" @click="showDeleteModal = false"
                    class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                    Supprimer
                </button>
            </div>
        </div>
    </div>
</div>
