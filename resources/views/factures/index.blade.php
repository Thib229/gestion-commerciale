<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Liste des factures
        </h2>
    </x-slot>

    <div class="py-6 px-8 max-w-7xl mx-auto">
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white shadow rounded p-6">
            <table class="min-w-full table-auto border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-300 px-4 py-2 text-left">ID</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Client</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Total (F)</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Payé (F)</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Reste (F)</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Date</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($factures as $facture)
                        <tr>
                            <td class="border border-gray-300 px-4 py-2">{{ $facture->id }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $facture->client->nom }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-green-700 font-semibold">
                                {{ number_format($facture->total, 0, ',', ' ') }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2 text-blue-700">
                                {{ number_format($facture->montant_paye, 0, ',', ' ') }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2 text-red-600 font-semibold">
                                {{ number_format($facture->reste_a_regler, 0, ',', ' ') }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2">{{ $facture->date }}</td>
                            <td class="border border-gray-300 px-4 py-2">
                                <a href="{{ route('factures.show', $facture) }}" class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded text-sm shadow">Voir</a>
                                <a href="{{ route('factures.edit', $facture) }}" class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded text-sm shadow">Modifier</a>
                                <form action="{{ route('factures.destroy', $facture) }}" method="POST" class="inline-block" onsubmit="return confirm('Voulez-vous vraiment supprimer cette facture ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm shadow">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-gray-500">Aucune facture trouvée.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-4">
                <a href="{{ route('factures.create') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
                    ➕ Nouvelle facture
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
