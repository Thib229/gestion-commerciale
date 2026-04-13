<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Liste des clients
        </h2>
    </x-slot>

    <div class="py-6 px-8 max-w-5xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('clients.create') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow">
                + Ajouter un client
            </a>
        </div>

        <!-- Formulaire de recherche -->
        <form method="GET" action="{{ route('clients.index') }}" class="mb-4 flex gap-2 items-center">
            <input type="text" name="search" value="{{ $search ?? '' }}"
                   placeholder="Rechercher par nom ou email..."
                   class="border border-gray-300 rounded px-3 py-2 text-sm w-64 focus:outline-none focus:ring-2 focus:ring-blue-300">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
                Rechercher
            </button>
            @if(!empty($search))
                <a href="{{ route('clients.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded text-sm">
                    Réinitialiser
                </a>
            @endif
        </form>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white shadow rounded p-6 overflow-x-auto">
            @if ($clients->count() > 0)
                <table class="w-full table-auto border-collapse">
                    <thead>
                        <tr class="bg-gray-100 text-left text-gray-700 uppercase text-sm">
                            <th class="p-3 border-b">Nom</th>
                            <th class="p-3 border-b">Email</th>
                            <th class="p-3 border-b">Téléphone</th>
                            <th class="p-3 border-b">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($clients as $client)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-3">{{ $client->nom }}</td>
                                <td class="p-3">{{ $client->email }}</td>
                                <td class="p-3">{{ $client->telephone }}</td>
                                <td class="p-3 flex gap-2">
                                    <a href="{{ route('clients.edit', $client) }}"
                                       class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded text-sm shadow">
                                        Modifier
                                    </a>
                                    @if (!$client->factures()->exists())
                                        <form action="{{ route('clients.destroy', $client) }}" method="POST"
                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce client ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm shadow">
                                                Supprimer
                                            </button>
                                        </form>
                                    @else
                                        <span class="bg-gray-300 text-gray-500 px-3 py-1 rounded text-sm cursor-not-allowed" title="Client avec factures, suppression impossible">
                                            Supprimer
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $clients->links() }}
                </div>
            @else
                <p class="text-gray-600 italic">Aucun client enregistré pour le moment.</p>
            @endif
        </div>
    </div>
</x-app-layout>
