<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 leading-tight">
            🧾 Détails de la facture #{{ $facture->id }}
        </h2>
    </x-slot>

    <div class="py-6 px-8 max-w-4xl mx-auto">
        <div class="bg-white p-6 shadow rounded space-y-4">
            <h3 class="text-lg font-bold">Informations client</h3>
            <p><strong>Nom :</strong> {{ $facture->client->nom }}</p>
            <p><strong>Email :</strong> {{ $facture->client->email }}</p>
            <p><strong>Téléphone :</strong> {{ $facture->client->telephone }}</p>

            <hr>

            <h3 class="text-lg font-bold">Produits achetés</h3>
            <table class="w-full table-auto border border-gray-300">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-4 py-2 text-left">Produit</th>
                        <th class="border px-4 py-2 text-left">Quantité</th>
                        <th class="border px-4 py-2 text-left">Prix unitaire (F)</th>
                        <th class="border px-4 py-2 text-left">Total (F)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($facture->produits as $produit)
                        <tr>
                            <td class="border px-4 py-2">{{ $produit->nom }}</td>
                            <td class="border px-4 py-2">{{ $produit->pivot->quantite }}</td>
                            <td class="border px-4 py-2">{{ number_format($produit->pivot->prix, 0, ',', ' ') }}</td>
                            <td class="border px-4 py-2">
                                {{ number_format($produit->pivot->prix * $produit->pivot->quantite, 0, ',', ' ') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <hr>

            <h3 class="text-lg font-bold">Paiements</h3>
            @if ($facture->paiements->isEmpty())
                <p class="text-gray-500">Aucun paiement enregistré.</p>
            @else
                <table class="w-full table-auto border border-gray-300">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-4 py-2 text-left">Montant (F)</th>
                            <th class="border px-4 py-2 text-left">Date de paiement</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($facture->paiements as $paiement)
                            <tr>
                                <td class="border px-4 py-2 text-blue-700">
                                    {{ number_format($paiement->montant, 0, ',', ' ') }}
                                </td>
                                <td class="border px-4 py-2">{{ $paiement->date_paiement }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            <hr>

            <h3 class="text-lg font-bold">Résumé</h3>
            <p><strong>Total facture :</strong> {{ number_format($facture->total, 0, ',', ' ') }} F</p>
            <p><strong>Montant payé :</strong> {{ number_format($facture->montant_paye, 0, ',', ' ') }} F</p>
            <p><strong>Reste à payer :</strong> <span class="text-red-600 font-semibold">{{ number_format($facture->reste_a_regler, 0, ',', ' ') }} F</span></p>

            <div class="pt-6 flex items-center gap-4 flex-wrap">
                <a href="{{ route('factures.index') }}" class="text-blue-600 hover:underline">&larr; Retour à la liste</a>

                @if(Auth::user()->canExportPdf())
                    <a href="{{ route('factures.exportPdf', $facture->id) }}"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded shadow">
                        📄 Exporter en PDF
                    </a>
                @else
                    <span class="text-sm text-gray-400 italic" title="Disponible à partir du plan Pro">
                        📄 Export PDF (plan Pro requis)
                    </span>
                @endif

                @if($facture->public_token)
                    <button
                        onclick="navigator.clipboard.writeText('{{ route('factures.public', $facture->public_token) }}').then(() => { this.textContent = '✅ Lien copié !'; setTimeout(() => { this.textContent = '🔗 Copier le lien public'; }, 2000); })"
                        class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded shadow text-sm border border-gray-300">
                        🔗 Copier le lien public
                    </button>
                @endif
            </div>
        </div>
    </div>

</x-app-layout>
