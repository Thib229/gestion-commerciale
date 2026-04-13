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
            <!-- Formulaire de filtres -->
            <form method="GET" action="{{ route('factures.index') }}" class="mb-4 flex flex-wrap gap-3 items-end">
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Client</label>
                    <input type="text" name="search" value="{{ $search ?? '' }}"
                           placeholder="Nom du client..."
                           class="border border-gray-300 rounded px-3 py-2 text-sm w-44 focus:outline-none focus:ring-2 focus:ring-blue-300">
                </div>
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Date début</label>
                    <input type="date" name="date_from" value="{{ $dateFrom ?? '' }}"
                           class="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                </div>
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Date fin</label>
                    <input type="date" name="date_to" value="{{ $dateTo ?? '' }}"
                           class="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                </div>
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Statut</label>
                    <select name="statut" class="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                        <option value="">Tous</option>
                        <option value="impayée" {{ ($statut ?? '') === 'impayée' ? 'selected' : '' }}>Impayée</option>
                        <option value="partiellement payée" {{ ($statut ?? '') === 'partiellement payée' ? 'selected' : '' }}>Partiellement payée</option>
                        <option value="payée" {{ ($statut ?? '') === 'payée' ? 'selected' : '' }}>Payée</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
                        Filtrer
                    </button>
                    @if(!empty($search) || !empty($dateFrom) || !empty($dateTo) || !empty($statut))
                        <a href="{{ route('factures.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded text-sm">
                            Réinitialiser
                        </a>
                    @endif
                </div>
            </form>

            <table class="min-w-full table-auto border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-300 px-4 py-2 text-left">N° Facture</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Client</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Total (F)</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Payé (F)</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Reste (F)</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Statut</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Date</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($factures as $facture)
                        <tr>
                            <td class="border border-gray-300 px-4 py-2 font-mono text-sm">{{ $facture->numero_facture ?? '#'.$facture->id }}</td>
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
                            <td class="border border-gray-300 px-4 py-2">
                                @php
                                    $badgeClass = match($facture->statut ?? 'impayée') {
                                        'payée' => 'bg-green-100 text-green-700',
                                        'partiellement payée' => 'bg-yellow-100 text-yellow-700',
                                        default => 'bg-red-100 text-red-700',
                                    };
                                @endphp
                                <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold {{ $badgeClass }}">
                                    {{ $facture->statut ?? 'impayée' }}
                                </span>
                            </td>
                            <td class="border border-gray-300 px-4 py-2">{{ $facture->date }}</td>
                            <td class="border border-gray-300 px-4 py-2">
                                <a href="{{ route('factures.show', $facture) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm shadow">Voir</a>
                                <a href="{{ route('factures.edit', $facture) }}" class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded text-sm shadow">Modifier</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-gray-500">Aucune facture trouvée.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-4">
                <a href="{{ route('factures.create') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
                    ➕ Nouvelle facture
                </a>
            </div>

            <div class="mt-4">
                {{ $factures->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
