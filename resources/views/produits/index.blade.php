<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Liste des produits</h2>
    </x-slot>

    <div class="py-6 px-8">
        @if (session('success'))
            <div class="mb-4 text-green-600">
                {{ session('success') }}
            </div>
        @endif

        <a href="{{ route('produits.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4 inline-block">
            Ajouter un produit
        </a>

        <table class="min-w-full bg-white border rounded shadow">
            <thead>
                <tr>
                    <th class="border px-4 py-2">Nom</th>
                    <th class="border px-4 py-2">Prix Unitaire</th>
                    <th class="border px-4 py-2">Stock</th>
                    <th class="border px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($produits as $produit)
                    <tr>
                        <td class="border px-4 py-2">{{ $produit->nom }}</td>
                        <td class="border px-4 py-2">{{ number_format($produit->prix_unitaire, 2, ',', ' ') }} F</td>
                        <td class="border px-4 py-2">{{ $produit->stock }}</td>
                        <td class="border px-4 py-2">
                            <a href="{{ route('produits.edit', $produit) }}" class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded text-sm shadow">Modifier</a>

                            <form action="{{ route('produits.destroy', $produit) }}" method="POST" class="inline-block" onsubmit="return confirm('Confirmer la suppression ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm shadow">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="border px-4 py-2 text-center">Aucun produit trouvé.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>
