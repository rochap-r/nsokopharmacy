<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-800">Du00e9tails du produit</h2>
                <div class="flex space-x-2">
                    <a href="{{ tenant_route('tenant.catalog.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50 flex items-center" wire:navigate>
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                        </svg>
                        Retour au catalogue
                    </a>
                    @can('catalog.edit')
                        <a href="{{ tenant_route('tenant.catalog.products.edit', ['tenant' => request()->route('tenant'), 'id' => $product->id]) }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 flex items-center" wire:navigate>
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Modifier
                        </a>
                    @endcan
                </div>
            </div>

            <div class="bg-gray-50 p-6 rounded-lg border border-gray-200 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Informations principales -->
                    <div class="md:col-span-2 space-y-4">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">{{ $product->dci }}</h3>
                            <p class="text-sm text-gray-600">{{ $product->dosage }} {{ $product->forme_galenique }}</p>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <h4 class="text-sm font-medium text-gray-700">Type</h4>
                                <p class="text-sm text-gray-600">{{ $product->type ?: 'Non spu00e9cifiu00e9' }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-700">Public cible</h4>
                                <p class="text-sm text-gray-600">{{ $product->personne ?: 'Non spu00e9cifiu00e9' }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-700">Statut</h4>
                                @if($product->active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Actif</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Inactif</span>
                                @endif
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-700">Code couleur</h4>
                                <div class="flex items-center space-x-2">
                                    <div class="w-6 h-6 rounded-md" style="background-color: {{ $product->color_code }}"></div>
                                    <span class="text-sm text-gray-600">{{ $product->color_code }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Catu00e9gorie et Emplacement -->
                    <div class="space-y-4 border-l border-gray-200 pl-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-700">Catu00e9gorie</h4>
                            @if($product->category)
                                <div class="space-y-1">
                                    @if($product->category->parent && $product->category->parent->parent)
                                        <p class="text-sm text-gray-600">
                                            <span class="font-medium">Principale:</span> {{ $product->category->parent->parent->name }}
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            <span class="font-medium">Sous-catu00e9gorie:</span> {{ $product->category->parent->name }}
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            <span class="font-medium">Sous-sous-catu00e9gorie:</span> {{ $product->category->name }}
                                        </p>
                                    @elseif($product->category->parent)
                                        <p class="text-sm text-gray-600">
                                            <span class="font-medium">Principale:</span> {{ $product->category->parent->name }}
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            <span class="font-medium">Sous-catu00e9gorie:</span> {{ $product->category->name }}
                                        </p>
                                    @else
                                        <p class="text-sm text-gray-600">
                                            <span class="font-medium">Principale:</span> {{ $product->category->name }}
                                        </p>
                                    @endif
                                </div>
                            @else
                                <p class="text-sm text-gray-600">Aucune catu00e9gorie assignu00e9e</p>
                            @endif
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-700">Emplacement recommandu00e9</h4>
                            @if($product->aisle)
                                <p class="text-sm text-gray-600">
                                    {{ $product->aisle->name }} ({{ $product->aisle->code }})
                                    @if($product->aisle->is_global)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 ml-2">Global</span>
                                    @endif
                                </p>
                            @else
                                <p class="text-sm text-gray-600">Aucun emplacement assignu00e9</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inventaires par tenant -->
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Inventaires par tenant</h3>
                @if($product->tenantInventories->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tenant</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantitu00e9</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Emplacement spu00e9cifique</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dernier mouvement</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($product->tenantInventories as $inventory)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $inventory->tenant->name ?? 'Inconnu' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $inventory->quantity ?? 0 }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($inventory->aisle)
                                                {{ $inventory->aisle->name }} ({{ $inventory->aisle->code }})
                                            @else
                                                Non du00e9fini
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $inventory->updated_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="bg-gray-50 p-4 rounded-md">
                        <p class="text-sm text-gray-600">Aucun inventaire enregistru00e9 pour ce produit.</p>
                    </div>
                @endif
            </div>

            <!-- Actions -->
            <div class="border-t border-gray-200 pt-6 flex justify-between">
                <div>
                    @can('catalog.delete')
                        <button class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Supprimer le produit
                        </button>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>
