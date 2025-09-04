<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            ✏️ Modifier la facture #{{ $facture->id }}
        </h2>
    </x-slot>

    <div class="py-6 px-8 max-w-4xl mx-auto">
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                <strong>Erreurs :</strong>
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('factures.update', $facture->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="client_id" class="block text-sm font-medium text-gray-700">Client</label>
                <select name="client_id" id="client_id" class="w-full mt-1 border-gray-300 rounded shadow-sm p-2">
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ $facture->client_id == $client->id ? 'selected' : '' }}>
                            {{ $client->nom }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div id="produits-container" class="space-y-4">
                @foreach ($facture->produits as $index => $produit)
                    <div class="flex space-x-2">
                        <select name="produits[{{ $index }}][id]" class="w-2/3 border-gray-300 rounded p-2">
                            @foreach ($produits as $prod)
                                <option value="{{ $prod->id }}" {{ $produit->id == $prod->id ? 'selected' : '' }}>
                                    {{ $prod->nom }} ({{ number_format($prod->prix_unitaire, 0, ',', ' ') }} F)
                                </option>
                            @endforeach
                        </select>

                        <input type="number" name="produits[{{ $index }}][quantite]" min="1"
                               value="{{ $produit->pivot->quantite }}"
                               class="w-1/3 border-gray-300 rounded p-2" />
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                <button type="button" onclick="ajouterProduit()"
                        class="text-blue-600 hover:underline text-sm">+ Ajouter un produit</button>
            </div>

            <div class="mt-6 flex justify-between items-center">
                <a href="{{ route('factures.index') }}" class="text-sm text-gray-600 hover:underline">← Retour</a>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow">
                    💾 Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>

    <script>
        let index = {{ $facture->produits->count() }};

        function ajouterProduit() {
            const container = document.getElementById('produits-container');
            const produitHtml = `
                <div class="flex space-x-2 mt-2">
                    <select name="produits[\${index}][id]" class="w-2/3 border-gray-300 rounded p-2">
                        @foreach ($produits as $prod)
                            <option value="{{ $prod->id }}">{{ $prod->nom }} ({{ number_format($prod->prix_unitaire, 0, ',', ' ') }} F)</option>
                        @endforeach
                    </select>

                    <input type="number" name="produits[\${index}][quantite]" min="1"
                           class="w-1/3 border-gray-300 rounded p-2" />
                </div>
            `;
            container.insertAdjacentHTML('beforeend', produitHtml);
            index++;
        }
    </script>
</x-app-layout>
