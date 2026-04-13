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

        <!-- Formulaire de recherche -->
        <form method="GET" action="{{ route('produits.index') }}" class="mb-4 flex gap-2 items-center">
            <input type="text" name="search" value="{{ $search ?? '' }}"
                   placeholder="Rechercher par nom..."
                   class="border border-gray-300 rounded px-3 py-2 text-sm w-64 focus:outline-none focus:ring-2 focus:ring-blue-300">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
                Rechercher
            </button>
            @if(!empty($search))
                <a href="{{ route('produits.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded text-sm">
                    Réinitialiser
                </a>
            @endif
        </form>

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

        <div class="mt-4">
            {{ $produits->links() }}
        </div>
    </div>
</x-app-layout>
