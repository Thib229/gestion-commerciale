<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Liste des paiements & Ajouter un paiement
        </h2>
    </x-slot>

    <div class="py-6 px-8 max-w-7xl mx-auto space-y-6">

        <!-- Formulaire d'ajout de paiement -->
        <div class="bg-white shadow rounded p-6">
            @if(session('success'))
                <div class="mb-4 text-green-600 font-semibold">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('paiements.store') }}" method="POST" class="flex flex-col md:flex-row md:items-center md:space-x-4">
                @csrf

                <div class="flex-1 mb-4 md:mb-0">
                    <label for="facture_id" class="block text-sm font-medium text-gray-700">Facture</label>
                    <select name="facture_id" id="facture_id" class="mt-1 block w-full border-gray-300 rounded-md">
                        <option value="">-- Choisir une facture --</option>
                        @foreach ($factures as $facture)
                            <option value="{{ $facture->id }}">
                                #{{ $facture->id }} - {{ $facture->client->nom ?? 'Client' }} - {{ number_format($facture->total, 0, ',', ' ') }} F
                            </option>
                        @endforeach
                    </select>
                    @error('facture_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex-1 mb-4 md:mb-0">
                    <label for="montant" class="block text-sm font-medium text-gray-700">Montant (F)</label>
                    <input type="number" step="0.01" min="1" name="montant" id="montant" value="{{ old('montant') }}" class="mt-1 block w-full border-gray-300 rounded-md" required>
                    @error('montant')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <button type="submit" class="mt-6 md:mt-0 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow">
                        Ajouter paiement
                    </button>
                </div>
            </form>
        </div>

        <!-- Liste des paiements -->
        <div class="bg-white shadow rounded p-6 overflow-x-auto">
            <table class="min-w-full table-auto border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-300 px-4 py-2 text-left">ID</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Facture ID</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Montant</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Date paiement</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($paiements as $paiement)
                        <tr>
                            <td class="border border-gray-300 px-4 py-2">{{ $paiement->id }}</td>
                            <td class="border border-gray-300 px-4 py-2">#{{ $paiement->facture->id }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ number_format($paiement->montant, 0, ',', ' ') }} F</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $paiement->date_paiement }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-gray-500">Aucun paiement trouvé.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</x-app-layout>
