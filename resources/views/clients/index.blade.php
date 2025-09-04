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

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
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
                                    <form action="{{ route('clients.destroy', $client) }}" method="POST"
                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce client ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm shadow">
                                            Supprimer
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-gray-600 italic">Aucun client enregistré pour le moment.</p>
            @endif
        </div>
    </div>
</x-app-layout>
