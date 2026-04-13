<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Tableau de bord</h2>
                <p class="text-sm text-gray-500 mt-1">Bienvenue, {{ Auth::user()->name }}</p>
            </div>
            <a href="{{ route('factures.create') }}"
               class="inline-flex items-center gap-2 text-white px-4 py-2 rounded-lg text-sm font-semibold shadow transition"
               style="background:#2563eb;">
                <i class="fas fa-plus"></i> Nouvelle facture
            </a>
        </div>
    </x-slot>

    <div class="py-6 px-4 md:px-8 space-y-6">

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        {{-- KPI Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-3 gap-6">

            <div class="bg-white shadow rounded p-4 flex items-center space-x-4">
                <div class="text-blue-500 text-3xl"><i class="fas fa-users"></i></div>
                <div>
                    <h3 class="text-gray-700 text-sm">Nombre de clients</h3>
                    <p class="text-2xl font-bold">{{ $nbClients }}</p>
                    <a href="{{ route('clients.index') }}" class="text-xs text-blue-500">Voir tous →</a>
                </div>
            </div>

            <div class="bg-white shadow rounded p-4 flex items-center space-x-4">
                <div class="text-green-500 text-3xl"><i class="fas fa-boxes"></i></div>
                <div>
                    <h3 class="text-gray-700 text-sm">Produits en stock</h3>
                    <p class="text-2xl font-bold">{{ $nbProduits }}</p>
                    <a href="{{ route('produits.index') }}" class="text-xs text-green-500">Voir tous →</a>
                </div>
            </div>

            <div class="bg-white shadow rounded p-4 flex items-center space-x-4">
                <div class="text-yellow-500 text-3xl"><i class="fas fa-file-invoice"></i></div>
                <div>
                    <h3 class="text-gray-700 text-sm">Factures émises</h3>
                    <p class="text-2xl font-bold">{{ $nbFactures }}</p>
                    <a href="{{ route('factures.index') }}" class="text-xs text-yellow-500">Voir toutes →</a>
                </div>
            </div>

            <div class="bg-white shadow rounded p-4 flex items-center space-x-4">
                <div class="text-green-700 text-3xl"><i class="fas fa-money-bill-wave"></i></div>
                <div>
                    <h3 class="text-gray-700 text-sm">Chiffre d'affaires</h3>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($chiffreAffaires, 0, ',', ' ') }} F</p>
                </div>
            </div>

            <div class="bg-white shadow rounded p-4 flex items-center space-x-4">
                <div class="text-blue-700 text-3xl"><i class="fas fa-credit-card"></i></div>
                <div>
                    <h3 class="text-gray-700 text-sm">Total des paiements</h3>
                    <p class="text-2xl font-bold text-blue-600">{{ number_format($totalPaiements, 0, ',', ' ') }} F</p>
                    <a href="{{ route('paiements.index') }}" class="text-xs text-blue-500">Voir tous →</a>
                </div>
            </div>

        </div>

        {{-- Accès rapide + Top produits --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="lg:col-span-2">
                <div class="bg-white shadow rounded p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Gestion rapide</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <a href="{{ route('clients.create') }}"
                           class="flex items-center gap-3 p-4 rounded-xl border transition hover:shadow-md"
                           style="border-color:#dbeafe;background:#eff6ff;">
                            <div class="text-white rounded-lg p-2" style="background:#2563eb;">
                                <i class="fas fa-user-plus text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">Nouveau client</p>
                                <p class="text-xs text-gray-400">Ajouter</p>
                            </div>
                        </a>
                        <a href="{{ route('produits.create') }}"
                           class="flex items-center gap-3 p-4 rounded-xl border transition hover:shadow-md"
                           style="border-color:#d1fae5;background:#ecfdf5;">
                            <div class="text-white rounded-lg p-2" style="background:#059669;">
                                <i class="fas fa-plus-circle text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">Nouveau produit</p>
                                <p class="text-xs text-gray-400">Ajouter</p>
                            </div>
                        </a>
                        <a href="{{ route('factures.create') }}"
                           class="flex items-center gap-3 p-4 rounded-xl border transition hover:shadow-md"
                           style="border-color:#fde68a;background:#fffbeb;">
                            <div class="text-white rounded-lg p-2" style="background:#d97706;">
                                <i class="fas fa-file-invoice text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">Nouvelle facture</p>
                                <p class="text-xs text-gray-400">Créer</p>
                            </div>
                        </a>
                        <a href="{{ route('paiements.index') }}"
                           class="flex items-center gap-3 p-4 rounded-xl border transition hover:shadow-md"
                           style="border-color:#ede9fe;background:#f5f3ff;">
                            <div class="text-white rounded-lg p-2" style="background:#7c3aed;">
                                <i class="fas fa-money-bill-wave text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">Paiements</p>
                                <p class="text-xs text-gray-400">Enregistrer</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-base font-semibold text-gray-800">Top produits</h3>
                    <i class="fas fa-trophy" style="color:#f59e0b;"></i>
                </div>
                @if(Auth::user()->canViewStatistics())
                    @forelse ($produitsPopulaires as $index => $produit)
                        <div class="flex items-center gap-3 py-3 {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
                            <span class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold"
                                style="{{ $index === 0 ? 'background:#fef3c7;color:#d97706;' : ($index === 1 ? 'background:#f3f4f6;color:#6b7280;' : 'background:#fff7ed;color:#ea580c;') }}">
                                {{ $index + 1 }}
                            </span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ $produit->nom }}</p>
                                <p class="text-xs text-gray-400">{{ $produit->factures_count }} vente(s)</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <i class="fas fa-box-open text-3xl mb-2" style="color:#d1d5db;"></i>
                            <p class="text-sm text-gray-400">Aucune vente enregistrée</p>
                        </div>
                    @endforelse
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-lock text-3xl mb-2" style="color:#d1d5db;"></i>
                        <p class="text-sm text-gray-400">Plan Pro requis</p>
                    </div>
                @endif
            </div>

        </div>

        {{-- Graphique chiffre d'affaires (en bas) --}}
        @if(Auth::user()->canViewStatistics())
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-base font-semibold text-gray-800">Chiffre d'affaires mensuel</h3>
                <span class="text-xs text-gray-400 bg-gray-100 px-3 py-1 rounded-full">6 derniers mois</span>
            </div>
            <canvas id="chiffreAffairesChart" height="80"></canvas>
        </div>
        @else
        <div class="bg-white rounded-2xl border border-blue-100 p-8 flex flex-col items-center justify-center text-center" style="background:#eff6ff;">
            <div class="rounded-full p-4 mb-4" style="background:#dbeafe;">
                <i class="fas fa-chart-bar text-2xl" style="color:#3b82f6;"></i>
            </div>
            <h3 class="font-semibold text-gray-800 mb-2">Statistiques avancées</h3>
            <p class="text-sm text-gray-500 mb-4">Disponibles à partir du plan Pro</p>
            <a href="{{ route('subscriptions.choose') }}" class="text-white px-4 py-2 rounded-lg text-sm font-semibold" style="background:#2563eb;">
                Passer au plan Pro →
            </a>
        </div>
        @endif

    </div>

    @if(Auth::user()->canViewStatistics())
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('chiffreAffairesChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [
                    @foreach ($stats as $stat)
                        "{{ \Carbon\Carbon::createFromDate($stat->year, $stat->month, 1)->format('M Y') }}",
                    @endforeach
                ],
                datasets: [{
                    label: "Chiffre d'affaires (F)",
                    data: [
                        @foreach ($stats as $stat)
                            {{ $stat->total }},
                        @endforeach
                    ],
                    backgroundColor: 'rgba(37,99,235,0.15)',
                    borderColor: 'rgba(37,99,235,1)',
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ctx.parsed.y.toLocaleString('fr-FR') + ' F'
                        }
                    }
                },
                scales: {
                    x: { grid: { display: false } },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.04)' },
                        ticks: { callback: v => v.toLocaleString('fr-FR') + ' F' }
                    }
                }
            }
        });
    </script>
    @endif

</x-app-layout>
