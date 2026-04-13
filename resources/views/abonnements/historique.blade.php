<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Historique des paiements d'abonnement
        </h2>
    </x-slot>

    <div class="py-6 px-8 max-w-5xl mx-auto">
        <div class="bg-white shadow rounded p-6">
            @if($paiements->isEmpty())
                <p class="text-gray-500 italic text-center py-8">Aucun paiement d'abonnement enregistré.</p>
            @else
                <table class="min-w-full table-auto border-collapse">
                    <thead>
                        <tr class="bg-gray-100 text-left text-gray-700 text-sm uppercase">
                            <th class="px-4 py-3 border-b">Date</th>
                            <th class="px-4 py-3 border-b">Plan</th>
                            <th class="px-4 py-3 border-b">Montant</th>
                            <th class="px-4 py-3 border-b">Référence</th>
                            <th class="px-4 py-3 border-b">Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($paiements as $paiement)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm">
                                    {{ $paiement->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-4 py-3 text-sm font-medium">
                                    {{ $paiement->plan->name ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-sm font-semibold text-gray-800">
                                    {{ number_format($paiement->montant, 0, ',', ' ') }} {{ $paiement->devise }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500 font-mono">
                                    {{ $paiement->reference_fedapay ?? '—' }}
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $badgeClass = match($paiement->statut) {
                                            'réussie'    => 'bg-green-100 text-green-700',
                                            'échouée'    => 'bg-red-100 text-red-700',
                                            'en attente' => 'bg-yellow-100 text-yellow-700',
                                            default      => 'bg-gray-100 text-gray-600',
                                        };
                                    @endphp
                                    <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold {{ $badgeClass }}">
                                        {{ ucfirst($paiement->statut) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $paiements->links() }}
                </div>
            @endif
        </div>

        <div class="mt-4">
            <a href="{{ route('subscriptions.choose') }}" class="text-blue-600 hover:underline text-sm">
                &larr; Retour aux abonnements
            </a>
        </div>
    </div>
</x-app-layout>
