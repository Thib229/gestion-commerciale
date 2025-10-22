<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tableau de bord
        </h2>
    </x-slot>

    <div class="py-6 px-8 space-y-6">
        <!-- Cartes de résumé -->
        <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
            <div class="bg-white shadow rounded p-4 flex items-center space-x-4">
                <div class="text-blue-500 text-3xl">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <h3 class="text-gray-700 text-sm">Nombre de clients</h3>
                    <p class="text-2xl font-bold">{{ $nbClients }}</p>
                </div>
            </div>

            <div class="bg-white shadow rounded p-4 flex items-center space-x-4">
                <div class="text-green-500 text-3xl">
                    <i class="fas fa-boxes"></i>
                </div>
                <div>
                    <h3 class="text-gray-700 text-sm">Produits en stock</h3>
                    <p class="text-2xl font-bold">{{ $nbProduits }}</p>
                </div>
            </div>

            <div class="bg-white shadow rounded p-4 flex items-center space-x-4">
                <div class="text-yellow-500 text-3xl">
                    <i class="fas fa-file-invoice"></i>
                </div>
                <div>
                    <h3 class="text-gray-700 text-sm">Factures émises</h3>
                    <p class="text-2xl font-bold">{{ $nbFactures }}</p>
                </div>
            </div>

            <div class="bg-white shadow rounded p-4 flex items-center space-x-4">
                <div class="text-green-700 text-3xl">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div>
                    <h3 class="text-gray-700 text-sm">Chiffre d'affaires</h3>
                    <p class="text-2xl font-bold text-green-600">
                        {{ number_format($chiffreAffaires, 0, ',', ' ') }} F
                    </p>
                </div>
            </div>

            <div class="bg-white shadow rounded p-4 flex items-center space-x-4">
                <div class="text-blue-700 text-3xl">
                    <i class="fas fa-credit-card"></i>
                </div>
                <div>
                    <h3 class="text-gray-700 text-sm">Total des paiements</h3>
                    <p class="text-2xl font-bold text-blue-600">
                        {{ number_format($totalPaiements, 0, ',', ' ') }} F
                    </p>
                </div>
            </div>
        </div>

        <!-- Gestion rapide -->
        <div class="bg-white shadow rounded p-6 mt-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Gestion rapide</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('clients.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white rounded p-4 text-center shadow">
                    <i class="fas fa-users fa-2x mb-2"></i>
                    <div>Clients</div>
                </a>
                <a href="{{ route('produits.index') }}" class="bg-green-600 hover:bg-green-700 text-white rounded p-4 text-center shadow">
                    <i class="fas fa-boxes fa-2x mb-2"></i>
                    <div>Produits</div>
                </a>
                <a href="{{ route('factures.index') }}" class="bg-yellow-600 hover:bg-yellow-700 text-white rounded p-4 text-center shadow">
                    <i class="fas fa-file-invoice fa-2x mb-2"></i>
                    <div>Factures</div>
                </a>
                <a href="{{ route('paiements.index') }}" class="bg-purple-600 hover:bg-purple-700 text-white rounded p-4 text-center shadow">
                    <i class="fas fa-money-bill-wave fa-2x mb-2"></i>
                    <div>Paiements</div>
                </a>
            </div>
        </div>

        <!-- Liste des produits populaires -->
        <div class="bg-white shadow rounded p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Top 5 produits les plus vendus</h3>
            <ul class="list-disc list-inside text-gray-800">
                @forelse ($produitsPopulaires as $produit)
                    <li>{{ $produit->nom }} (vendu {{ $produit->factures_count }} fois)</li>
                @empty
                    <li>Aucun produit vendu pour le moment.</li>
                @endforelse
            </ul>
        </div>

        <!-- Graphique chiffre d'affaires -->
        <div class="bg-white shadow rounded p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Chiffre d'affaires des 6 derniers mois</h3>
            <canvas id="chiffreAffairesChart" width="400" height="200"></canvas>
        </div>
    </div>

    <!-- Chart.js depuis CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const ctx = document.getElementById('chiffreAffairesChart').getContext('2d');

        const labels = [
            @foreach ($stats as $stat)
                "{{ $stat->month }}/{{ $stat->year }}",
            @endforeach
        ];

        const data = {
            labels: labels,
            datasets: [{
                label: 'Chiffre d\'affaires (F)',
                data: [
                    @foreach ($stats as $stat)
                        {{ $stat->total }},
                    @endforeach
                ],
                backgroundColor: 'rgba(34, 197, 94, 0.5)',
                borderColor: 'rgba(34, 197, 94, 1)', 
                borderWidth: 1
            }]
        };

        const config = {
            type: 'bar',
            data: data,
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString() + ' F';
                            }
                        }
                    }
                }
            }
        };

        new Chart(ctx, config);
    </script>
</x-app-layout>
