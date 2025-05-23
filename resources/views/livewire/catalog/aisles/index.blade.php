<div class="max-w-6xl mx-auto">
    <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white">Emplacements / Rayons</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Gérez les emplacements et rayons pour vos produits</p>
        </div>
        <div class="flex flex-col md:flex-row gap-3">
            <a href="{{ tenant_route('tenant.catalog.index') }}" wire:navigate class="inline-flex items-center text-gray-600 hover:text-gray-800 dark:text-gray-300 dark:hover:text-gray-100 transition-colors duration-200">
                <svg class="h-5 w-5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Retour au catalogue
            </a>
                    @can('catalog.create')
                        <a href="{{ tenant_route('tenant.catalog.aisles.create', ['tenant' => request()->route('tenant')]) }}" 
                            class="inline-flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-gray-800 transition-colors duration-200" wire:navigate>
                            <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Nouvel emplacement
                        </a>
                    @endcan
                </div>
    </header>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-8 dark:backdrop-blur-sm dark:border dark:border-gray-700 transition-all duration-200">
        <div class="grid gap-6 md:grid-cols-5">
            <div class="md:col-span-3">
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Rechercher un emplacement</label>
                <div class="group relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none z-10">
                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-primary-500 transition-colors duration-200 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input type="text" wire:model.live.debounce.300ms="search" id="search"
                        class="pl-11 w-full bg-gray-50 border-0 text-gray-900 rounded-lg py-3 ring-1 ring-inset ring-gray-300 
                        placeholder:text-gray-400 outline-none
                        focus:ring-2 focus:ring-inset focus:ring-primary-500 
                        dark:bg-gray-800 dark:text-white dark:ring-gray-600 dark:placeholder-gray-500 
                        dark:focus:ring-primary-500 transition-all duration-200"
                        placeholder="Rechercher par nom ou code...">
                </div>
            </div>
            
            <div class="md:col-span-2 flex flex-col">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Type d'emplacements</label>
                <div class="flex flex-wrap gap-4 mt-2">
                    <label class="inline-flex items-center">
                        <input type="checkbox" wire:model.live="showGlobal" 
                            class="rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-500 focus:ring-opacity-50 transition-colors duration-200">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Globaux</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" wire:model.live="showTenant" 
                            class="rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-500 focus:ring-opacity-50 transition-colors duration-200">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Spécifiques au tenant</span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden dark:backdrop-blur-sm dark:border dark:border-gray-700 transition-all duration-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50">
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Code</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nom</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Catégorie</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tenant</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($aisles as $aisle)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $aisle->code }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">{{ $aisle->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                @if($aisle->category)
                                    {{ $aisle->category->name }}
                                @else
                                    <span class="text-gray-400 dark:text-gray-500">Non définie</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($aisle->is_global)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-primary-100 dark:bg-primary-900 text-primary-800 dark:text-primary-100">Global</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-100">Spécifique</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                @if($aisle->is_global)
                                    <span class="text-gray-400 dark:text-gray-500">Tous</span>
                                @elseif($aisle->tenant)
                                    {{ $aisle->tenant->name }}
                                @else
                                    <span class="text-gray-400 dark:text-gray-500">Non défini</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex gap-2">
                                    @can('catalog.edit')
                                        @if(!$aisle->is_global || Auth::user()->can('catalog.manage'))
                                            <a href="{{ tenant_route('tenant.catalog.aisles.edit', ['tenant' => request()->route('tenant'), 'id' => $aisle->id]) }}" 
                                               class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300 transition-colors duration-150" 
                                               wire:navigate
                                               title="Modifier">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                        @endif
                                    @endcan
                                    @can('catalog.delete')
                                        @if(!$aisle->is_global || Auth::user()->can('catalog.manage'))
                                            <button wire:click="deleteAisle({{ $aisle->id }})" 
                                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 transition-colors duration-150"
                                                    title="Supprimer">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">
                                <div class="flex flex-col items-center justify-center space-y-2">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Aucun emplacement trouvé.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
