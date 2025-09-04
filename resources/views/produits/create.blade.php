<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Ajouter un produit</h2>
    </x-slot>

    <div class="py-6 px-8 max-w-lg mx-auto">
        @if ($errors->any())
            <div class="mb-4 text-red-600">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('produits.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="nom" class="block font-medium text-gray-700">Nom</label>
                <input type="text" name="nom" id="nom" value="{{ old('nom') }}" required class="mt-1 block w-full border rounded px-3 py-2" />
            </div>

            <div class="mb-4">
                <label for="prix_unitaire" class="block font-medium text-gray-700">Prix Unitaire (F)</label>
                <input type="number" step="0.01" name="prix_unitaire" id="prix_unitaire" value="{{ old('prix_unitaire') }}" required class="mt-1 block w-full border rounded px-3 py-2" />
            </div>

            <div class="mb-4">
                <label for="stock" class="block font-medium text-gray-700">Stock</label>
                <input type="number" name="stock" id="stock" value="{{ old('stock') }}" required class="mt-1 block w-full border rounded px-3 py-2" />
            </div>

            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Ajouter</button>
        </form>
    </div>
</x-app-layout>
