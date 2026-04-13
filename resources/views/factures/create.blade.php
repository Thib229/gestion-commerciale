<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Nouvelle facture
        </h2>
    </x-slot>

    <div class="py-6 px-6">

        @if ($errors->any())
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('factures.store') }}">
            @csrf

            <div class="mb-4">
                <label for="client_id" class="block text-gray-700">Client</label>
                <select name="client_id" class="w-full border rounded p-2">
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}">{{ $client->nom }}</option>
                    @endforeach
                </select>
            </div>

            <div id="produits-container">
                <div class="mb-4 produit-item">
                    <label class="block text-gray-700">Produit</label>
                    <select name="produits[0][id]" class="w-full border rounded p-2">
                        @foreach($produits as $produit)
                            <option value="{{ $produit->id }}">
                                {{ $produit->nom }} - {{ number_format($produit->prix_unitaire, 0, ',', ' ') }} F
                            </option>
                        @endforeach
                    </select>

                    <label class="block mt-2 text-gray-700">Quantité</label>
                    <input type="number" name="produits[0][quantite]" class="w-full border rounded p-2" min="1" value="1">
                </div>
            </div>

            <button type="button" onclick="ajouterProduit()" class="bg-blue-500 text-white px-4 py-2 rounded">
                + Ajouter un produit
            </button>

            <div class="mt-6">
                <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded">
                    Enregistrer la facture
                </button>
            </div>
        </form>
    </div>

    <script>
        let produitIndex = 1;
        function ajouterProduit() {
            const container = document.getElementById('produits-container');
            const newProduit = `
                <div class="mb-4 produit-item">
                    <label class="block text-gray-700">Produit</label>
                    <select name="produits[${produitIndex}][id]" class="w-full border rounded p-2">
                        @foreach($produits as $produit)
                            <option value="{{ $produit->id }}">{{ $produit->nom }} - {{ number_format($produit->prix_unitaire, 0, ',', ' ') }} F</option>
                        @endforeach
                    </select>

                    <label class="block mt-2 text-gray-700">Quantité</label>
                    <input type="number" name="produits[${produitIndex}][quantite]" class="w-full border rounded p-2" min="1" value="1">
                </div>
            `;
            container.insertAdjacentHTML('beforeend', newProduit);
            produitIndex++;
        }
    </script>
</x-app-layout>
