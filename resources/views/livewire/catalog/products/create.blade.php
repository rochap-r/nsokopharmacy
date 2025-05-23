<div class="max-w-7xl mx-auto py-8 sm:px-6 lg:px-8">
    <header class="mb-8 flex justify-between items-center">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white">Créer un nouveau produit</h1>
        <a href="{{ tenant_route('tenant.catalog.index') }}" wire:navigate class="inline-flex items-center text-gray-600 hover:text-gray-800 dark:text-gray-300 dark:hover:text-gray-100 transition-colors duration-200">
            <svg class="h-5 w-5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Retour au catalogue
        </a>
    </header>

    <div class="bg-white rounded-xl shadow-md p-8 dark:bg-gray-800/90 dark:backdrop-blur-sm dark:border dark:border-gray-700 transition-all duration-200 hover:shadow-lg">

            <form wire:submit.prevent="create" class="space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    <!-- DCI (Dénomination Commune Internationale) -->
                    <div class="space-y-2">
                        <label for="dci" class="block text-sm font-medium text-gray-700 dark:text-gray-200 flex items-center">
                            DCI <span class="text-red-500 ml-1">*</span>
                        </label>
                        <div class="group relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none z-10">
                                <svg class="h-5 w-5 text-gray-400 group-focus-within:text-primary-500 transition-colors duration-200 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z" />
                                </svg>
                            </div>
                            <input wire:model="dci" type="text" id="dci" 
                                class="pl-11 w-full bg-gray-50 border-0 text-gray-900 rounded-lg py-3 ring-1 ring-inset ring-gray-300 
                                placeholder:text-gray-400 outline-none 
                                focus:ring-2 focus:ring-inset focus:ring-primary-500 
                                dark:bg-gray-800 dark:text-white dark:ring-gray-600 dark:placeholder-gray-500 
                                dark:focus:ring-primary-500 transition-all duration-200"
                                placeholder="Entrez la DCI du produit">
                        </div>
                        @error('dci')
                            <p class="mt-1.5 text-sm text-red-600 dark:text-red-400 flex items-center">
                                <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Dosage -->
                    <div class="space-y-2">
                        <label for="dosage" class="block text-sm font-medium text-gray-700 dark:text-gray-200 flex items-center">
                            Dosage
                            <span class="text-gray-500 text-xs ml-2 dark:text-gray-400">(Optionnel)</span>
                        </label>
                        <div class="group relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none z-10">
                                <svg class="h-5 w-5 text-gray-400 group-focus-within:text-primary-500 transition-colors duration-200 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 01.894 1.79l-1.233.616 1.738 5.42a1 1 0 01-.285 1.05A3.989 3.989 0 0115 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.715-5.349L11 6.477V16h2a1 1 0 110 2H7a1 1 0 110-2h2V6.477L6.237 7.582l1.715 5.349a1 1 0 01-.285 1.05A3.989 3.989 0 015 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.738-5.42-1.233-.617a1 1 0 01.894-1.788l1.599.799L9 4.323V3a1 1 0 011-1zm-5 8.274l-.818 2.552c.25.112.526.174.818.174.292 0 .569-.062.818-.174L5 10.274zm10 0l-.818 2.552c.25.112.526.174.818.174.292 0 .569-.062.818-.174L15 10.274z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input wire:model="dosage" type="text" id="dosage" 
                                class="pl-11 w-full bg-gray-50 border-0 text-gray-900 rounded-lg py-3 ring-1 ring-inset ring-gray-300 
                                placeholder:text-gray-400 outline-none 
                                focus:ring-2 focus:ring-inset focus:ring-primary-500 
                                dark:bg-gray-800 dark:text-white dark:ring-gray-600 dark:placeholder-gray-500 
                                dark:focus:ring-primary-500 transition-all duration-200"
                                placeholder="Ex: 500mg, 10mg/ml">
                        </div>
                        @error('dosage')
                            <p class="mt-1.5 text-sm text-red-600 dark:text-red-400 flex items-center">
                                <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Forme Galénique -->
                    <div class="space-y-2">
                        <label for="forme_galenique" class="block text-sm font-medium text-gray-700 dark:text-gray-200 flex items-center">
                            Forme Galénique
                            <span class="text-gray-500 text-xs ml-2 dark:text-gray-400">(Optionnel)</span>
                        </label>
                        <div class="group relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none z-10">
                                <svg class="h-5 w-5 text-gray-400 group-focus-within:text-primary-500 transition-colors duration-200 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input wire:model="forme_galenique" type="text" id="forme_galenique" 
                                class="pl-11 w-full bg-gray-50 border-0 text-gray-900 rounded-lg py-3 ring-1 ring-inset ring-gray-300 
                                placeholder:text-gray-400 outline-none 
                                focus:ring-2 focus:ring-inset focus:ring-primary-500 
                                dark:bg-gray-800 dark:text-white dark:ring-gray-600 dark:placeholder-gray-500 
                                dark:focus:ring-primary-500 transition-all duration-200"
                                placeholder="Ex: comprimé, sirop, injection">
                        </div>
                        @error('forme_galenique')
                            <p class="mt-1.5 text-sm text-red-600 dark:text-red-400 flex items-center">
                                <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Code Couleur -->
                    <div class="space-y-2">
                        <label for="color_code" class="block text-sm font-medium text-gray-700 dark:text-gray-200 flex items-center">
                            Code Couleur
                            <span class="text-gray-500 text-xs ml-2 dark:text-gray-400">(Optionnel)</span>
                        </label>
                        <div class="flex items-center gap-3">
                            <input type="color" wire:model="color_code" id="color_code" 
                                class="h-10 w-12 rounded-md border-0 ring-1 ring-inset ring-gray-300 dark:ring-gray-600 shadow-sm focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-500">
                            <div class="group relative flex-1">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none z-10">
                                    <svg class="h-5 w-5 text-gray-400 group-focus-within:text-primary-500 transition-colors duration-200 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4 2a2 2 0 00-2 2v11a3 3 0 106 0V4a2 2 0 00-2-2H4zm1 14a1 1 0 100-2 1 1 0 000 2zm5-1.757l4.9-4.9a2 2 0 000-2.828L13.485 5.1a2 2 0 00-2.828 0L10 5.757v8.486zM16 18H9.071l6-6H16a2 2 0 012 2v2a2 2 0 01-2 2z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input type="text" wire:model="color_code" 
                                    class="pl-11 w-full bg-gray-50 border-0 text-gray-900 rounded-lg py-3 ring-1 ring-inset ring-gray-300 
                                    placeholder:text-gray-400 outline-none 
                                    focus:ring-2 focus:ring-inset focus:ring-primary-500 
                                    dark:bg-gray-800 dark:text-white dark:ring-gray-600 dark:placeholder-gray-500 
                                    dark:focus:ring-primary-500 transition-all duration-200"
                                    placeholder="Ex: #3b82f6">
                            </div>
                        </div>
                        @error('color_code')
                            <p class="mt-1.5 text-sm text-red-600 dark:text-red-400 flex items-center">
                                <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Type -->
                    <div class="space-y-2">
                        <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-200 flex items-center">
                            Type de produit
                            <span class="text-gray-500 text-xs ml-2 dark:text-gray-400">(Optionnel)</span>
                        </label>
                        <div class="group relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none z-10">
                                <svg class="h-5 w-5 text-gray-400 group-focus-within:text-primary-500 transition-colors duration-200 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a1 1 0 011-1h5.586a.997.997 0 01.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <select wire:model="type" id="type" 
                                class="pl-11 w-full bg-gray-50 border-0 text-gray-900 rounded-lg py-3 ring-1 ring-inset ring-gray-300 
                                placeholder:text-gray-400 outline-none appearance-none
                                focus:ring-2 focus:ring-inset focus:ring-primary-500 
                                dark:bg-gray-800 dark:text-white dark:ring-gray-600 dark:placeholder-gray-500 
                                dark:focus:ring-primary-500 transition-all duration-200">
                                <option value="">-- Sélectionner un type --</option>
                                @foreach(\App\Enums\ProductType::cases() as $enum)
                                    <option value="{{ $enum->value }}">{{ $enum->label() }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                        @error('type')
                            <p class="mt-1.5 text-sm text-red-600 dark:text-red-400 flex items-center">
                                <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Personne -->
                    <div class="space-y-2">
                        <label for="personne" class="block text-sm font-medium text-gray-700 dark:text-gray-200 flex items-center">
                            Public cible
                            <span class="text-gray-500 text-xs ml-2 dark:text-gray-400">(Optionnel)</span>
                        </label>
                        <div class="group relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none z-10">
                                <svg class="h-5 w-5 text-gray-400 group-focus-within:text-primary-500 transition-colors duration-200 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                                </svg>
                            </div>
                            <select wire:model="personne" id="personne" 
                                class="pl-11 w-full bg-gray-50 border-0 text-gray-900 rounded-lg py-3 ring-1 ring-inset ring-gray-300 
                                placeholder:text-gray-400 outline-none appearance-none
                                focus:ring-2 focus:ring-inset focus:ring-primary-500 
                                dark:bg-gray-800 dark:text-white dark:ring-gray-600 dark:placeholder-gray-500 
                                dark:focus:ring-primary-500 transition-all duration-200">
                                <option value="">-- Sélectionner une option --</option>
                                @foreach(\App\Enums\PersonType::cases() as $enum)
                                    <option value="{{ $enum->value }}">{{ $enum->label() }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                        @error('personne')
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
                                <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                        @error('category_id')
                            <p class="mt-1.5 text-sm text-red-600 dark:text-red-400 flex items-center">
                                <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Rayon / Emplacement -->
                    <div class="space-y-2">
                        <label for="aisle_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200 flex items-center">
                            Rayon / Emplacement
                            <span class="text-gray-500 text-xs ml-2 dark:text-gray-400">(Optionnel)</span>
                        </label>
                        <div class="group relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none z-10">
                                <svg class="h-5 w-5 text-gray-400 group-focus-within:text-primary-500 transition-colors duration-200 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <select wire:model="aisle_id" id="aisle_id" 
                                class="pl-11 w-full bg-gray-50 border-0 text-gray-900 rounded-lg py-3 ring-1 ring-inset ring-gray-300 
                                placeholder:text-gray-400 outline-none appearance-none
                                focus:ring-2 focus:ring-inset focus:ring-primary-500 
                                dark:bg-gray-800 dark:text-white dark:ring-gray-600 dark:placeholder-gray-500 
                                dark:focus:ring-primary-500 transition-all duration-200">
                                <option value="">-- Sélectionner un emplacement --</option>
                                @foreach($aisles as $aisle)
                                    <option value="{{ $aisle['id'] }}">{{ $aisle['name'] }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                        @error('aisle_id')
                            <p class="mt-1.5 text-sm text-red-600 dark:text-red-400 flex items-center">
                                <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2 space-y-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-200 flex items-center">
                            Description
                            <span class="text-gray-500 text-xs ml-2 dark:text-gray-400">(Optionnel)</span>
                        </label>
                        <div class="relative">
                            <textarea wire:model="description" id="description" rows="3" 
                                class="w-full bg-gray-50 border-0 text-gray-900 rounded-lg py-3 px-4 ring-1 ring-inset ring-gray-300 
                                placeholder:text-gray-400 outline-none resize-none
                                focus:ring-2 focus:ring-inset focus:ring-primary-500 
                                dark:bg-gray-800 dark:text-white dark:ring-gray-600 dark:placeholder-gray-500 
                                dark:focus:ring-primary-500 transition-all duration-200"
                                placeholder="Décrivez le produit en détail..."></textarea>
                        </div>
                        @error('description')
                            <p class="mt-1.5 text-sm text-red-600 dark:text-red-400 flex items-center">
                                <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>



                    <div class="bg-gray-50 dark:bg-gray-800/70 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-5 flex items-center">
                            <svg class="h-5 w-5 mr-2 text-primary-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd" />
                            </svg>
                            Statut
                        </h3>

                        <div class="space-y-6">
                            <!-- Statut -->
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0 flex h-6 items-center">
                                    <input wire:model="status" id="status" type="checkbox" 
                                        class="h-5 w-5 rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 dark:focus:ring-primary-500 transition-colors duration-200">
                                </div>
                                <div class="text-sm leading-6">
                                    <label for="status" class="font-medium text-gray-900 dark:text-gray-100">Actif</label>
                                    <p class="text-gray-500 dark:text-gray-400 mt-1">Cochez cette case si le produit est actif et disponible à la vente.</p>
                                </div>
                            </div>

                            <!-- Featured -->
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0 flex h-6 items-center">
                                    <input wire:model="featured" id="featured" type="checkbox" 
                                        class="h-5 w-5 rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 dark:focus:ring-primary-500 transition-colors duration-200">
                                </div>
                                <div class="text-sm leading-6">
                                    <label for="featured" class="font-medium text-gray-900 dark:text-gray-100">Mis en avant</label>
                                    <p class="text-gray-500 dark:text-gray-400 mt-1">Cochez cette case pour mettre ce produit en avant sur la page d'accueil.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="submit" class="px-5 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 font-medium transition-all duration-200 flex items-center">
                        <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Créer le produit
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
