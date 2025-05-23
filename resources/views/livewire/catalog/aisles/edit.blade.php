<div class="max-w-6xl mx-auto">
    <header class="mb-8 flex justify-between items-center">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white">Modifier un emplacement</h1>
        <a href="{{ tenant_route('tenant.catalog.aisles.index', ['tenant' => request()->route('tenant')]) }}" wire:navigate class="inline-flex items-center text-gray-600 hover:text-gray-800 dark:text-gray-300 dark:hover:text-gray-100 transition-colors duration-200">
            <svg class="h-5 w-5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Retour
        </a>
    </header>

    <div class="bg-white rounded-xl shadow-md p-8 dark:bg-gray-800/90 dark:backdrop-blur-sm dark:border dark:border-gray-700 transition-all duration-200 hover:shadow-lg">

            <form wire:submit.prevent="update" class="space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    <!-- Code -->
                    <div class="space-y-2">
                        <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-200 flex items-center">
                            Code <span class="text-red-500 ml-1">*</span>
                        </label>
                        <div class="group relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none z-10">
                                <svg class="h-5 w-5 text-gray-400 group-focus-within:text-primary-500 transition-colors duration-200 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4 2a1 1 0 011-1h10a1 1 0 011 1v1H4V2zm-1 4v10a2 2 0 002 2h10a2 2 0 002-2V6H3zm4 4a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input wire:model="code" type="text" id="code" 
                                class="pl-11 w-full bg-gray-50 border-0 text-gray-900 rounded-lg py-3 ring-1 ring-inset ring-gray-300 
                                placeholder:text-gray-400 outline-none 
                                focus:ring-2 focus:ring-inset focus:ring-primary-500 
                                dark:bg-gray-800 dark:text-white dark:ring-gray-600 dark:placeholder-gray-500 
                                dark:focus:ring-primary-500 transition-all duration-200"
                                placeholder="Entrez le code de l'emplacement">
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Code unique pour identifier l'emplacement</p>
                        @error('code')
                            <p class="mt-1.5 text-sm text-red-600 dark:text-red-400 flex items-center">
                                <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Nom -->
                    <div class="space-y-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-200 flex items-center">
                            Nom <span class="text-red-500 ml-1">*</span>
                        </label>
                        <div class="group relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none z-10">
                                <svg class="h-5 w-5 text-gray-400 group-focus-within:text-primary-500 transition-colors duration-200 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zm-1 9a1 1 0 11-2 0 1 1 0 012 0z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input wire:model="name" type="text" id="name" 
                                class="pl-11 w-full bg-gray-50 border-0 text-gray-900 rounded-lg py-3 ring-1 ring-inset ring-gray-300 
                                placeholder:text-gray-400 outline-none 
                                focus:ring-2 focus:ring-inset focus:ring-primary-500 
                                dark:bg-gray-800 dark:text-white dark:ring-gray-600 dark:placeholder-gray-500 
                                dark:focus:ring-primary-500 transition-all duration-200"
                                placeholder="Entrez le nom de l'emplacement">
                        </div>
                        @error('name')
                            <p class="mt-1.5 text-sm text-red-600 dark:text-red-400 flex items-center">
                                <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Catégorie -->
                    <div class="space-y-2">
                        <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200 flex items-center">
                            Catégorie
                            <span class="text-gray-500 text-xs ml-2 dark:text-gray-400">(Optionnel)</span>
                        </label>
                        <div class="group relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none z-10">
                                <svg class="h-5 w-5 text-gray-400 group-focus-within:text-primary-500 transition-colors duration-200 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" />
                                </svg>
                            </div>
                            <select wire:model="category_id" id="category_id" 
                                class="pl-11 w-full bg-gray-50 border-0 text-gray-900 rounded-lg py-3 ring-1 ring-inset ring-gray-300 
                                placeholder:text-gray-400 outline-none appearance-none
                                focus:ring-2 focus:ring-inset focus:ring-primary-500 
                                dark:bg-gray-800 dark:text-white dark:ring-gray-600 dark:placeholder-gray-500 
                                dark:focus:ring-primary-500 transition-all duration-200">
                                <option value="">-- Sélectionner une catégorie --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category['id'] }}">{{ $category['name'] }}</option>
                                    @foreach($category['children'] as $subCategory)
                                        <option value="{{ $subCategory['id'] }}">-- {{ $subCategory['name'] }}</option>
                                        @foreach($subCategory['children'] as $subSubCategory)
                                            <option value="{{ $subSubCategory['id'] }}">---- {{ $subSubCategory['name'] }}</option>
                                        @endforeach
                                    @endforeach
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Catégorie associée à cet emplacement</p>
                        @error('category_id')
                            <p class="mt-1.5 text-sm text-red-600 dark:text-red-400 flex items-center">
                                <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Type d'emplacement -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Type d'emplacement</label>
                        <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg ring-1 ring-gray-300 dark:ring-gray-600 space-y-4">
                            <div class="flex flex-col space-y-2">
                                <label class="inline-flex items-center">
                                    <input type="radio" wire:model="is_global" value="0" 
                                        class="h-4 w-4 text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-400 border-gray-300 dark:border-gray-600">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-200">Spécifique à ce tenant</span>
                                </label>
                                @can('catalog.manage')
                                    <label class="inline-flex items-center">
                                        <input type="radio" wire:model="is_global" value="1" 
                                            class="h-4 w-4 text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-400 border-gray-300 dark:border-gray-600">
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-200">Global (tous les tenants)</span>
                                    </label>
                                @endcan
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                @if(Auth::user()->can('catalog.manage'))
                                    Les emplacements globaux sont partagés entre tous les tenants
                                @else
                                    Vous ne pouvez créer que des emplacements spécifiques à votre tenant
                                @endif
                            </p>
                        </div>
                        @error('is_global')
                            <p class="mt-1.5 text-sm text-red-600 dark:text-red-400 flex items-center">
                                <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <div class="pt-5 flex justify-end space-x-4">
                    <a href="{{ tenant_route('tenant.catalog.aisles.index', ['tenant' => request()->route('tenant')]) }}" wire:navigate 
                        class="inline-flex justify-center py-3 px-5 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 
                        bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 
                        dark:text-gray-200 dark:bg-gray-700 dark:border-gray-600 dark:hover:bg-gray-600 dark:focus:ring-offset-gray-800 
                        transition-colors duration-200 ease-in-out">
                        <svg class="mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Annuler
                    </a>
                    <button type="submit" 
                        class="inline-flex justify-center py-3 px-5 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white 
                        bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 
                        dark:focus:ring-offset-gray-800 transition-colors duration-200 ease-in-out">
                        <span wire:loading.remove wire:target="update" class="flex items-center">
                            <svg class="mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Mettre à jour
                        </span>
                        <span wire:loading wire:target="update" class="inline-flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Traitement...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
