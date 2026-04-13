<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Choisir un abonnement</h2>
    </x-slot>

    <div class="py-10 px-4 max-w-6xl mx-auto" x-data="{ duration: 1 }">

        @if(session('error'))
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded text-center">{{ session('error') }}</div>
        @endif
        @if(session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded text-center">{{ session('success') }}</div>
        @endif

        {{-- Titre --}}
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Choisissez votre abonnement</h1>
            @if($trialDaysLeft !== null && $trialDaysLeft > 0)
                <p class="text-blue-600 font-medium">Il vous reste {{ $trialDaysLeft }} jour(s) d'essai gratuit.</p>
            @else
                <p class="text-gray-500">Votre période d'essai est terminée. Choisissez un plan pour continuer.</p>
            @endif
        </div>

        {{-- Sélecteur de durée --}}
        <div class="flex justify-center mb-10">
            <div class="inline-flex bg-gray-100 rounded-xl p-1 gap-1 flex-wrap justify-center">
                @foreach([
                    1  => ['label' => '1 mois',   'discount' => null],
                    3  => ['label' => '3 mois',   'discount' => '-10%'],
                    6  => ['label' => '6 mois',   'discount' => '-20%'],
                    12 => ['label' => '1 an',     'discount' => '-30%'],
                ] as $months => $info)
                <button @click="duration = {{ $months }}"
                    :class="duration === {{ $months }}
                        ? 'bg-white shadow text-blue-600 font-semibold'
                        : 'text-gray-500 hover:text-gray-700'"
                    class="relative px-5 py-2 rounded-lg text-sm transition-all duration-200 flex flex-col items-center min-w-[80px]">
                    <span>{{ $info['label'] }}</span>
                    @if($info['discount'])
                        <span class="text-xs text-green-600 font-semibold">{{ $info['discount'] }}</span>
                    @endif
                </button>
                @endforeach
            </div>
        </div>

        {{-- Grille des plans --}}
        @php
            $basique  = $plans->firstWhere('name', 'Basique');
            $pro      = $plans->firstWhere('name', 'Pro');
            $premium  = $plans->firstWhere('name', 'Premium');

            $durations = [
                1  => ['multiplier' => 1,    'discount' => 1.00],
                3  => ['multiplier' => 3,    'discount' => 0.90],
                6  => ['multiplier' => 6,    'discount' => 0.80],
                12 => ['multiplier' => 12,   'discount' => 0.70],
            ];
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            {{-- Basique --}}
            @if($basique)
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 flex flex-col">
                <div class="mb-4">
                    <span class="inline-block bg-green-100 text-green-700 text-xs font-semibold px-3 py-1 rounded-full mb-3">Basique</span>
                    @foreach($durations as $months => $d)
                        @php $price = round($basique->price * $d['multiplier'] * $d['discount']); @endphp
                        <div x-show="duration === {{ $months }}">
                            <p class="text-4xl font-bold text-gray-900">
                                {{ number_format($price, 0, ',', ' ') }}
                                <span class="text-base font-normal text-gray-400">FCFA</span>
                            </p>
                            <p class="text-sm text-gray-400 mt-1">
                                @if($months === 1) par mois
                                @elseif($months === 12) par an
                                @else pour {{ $months }} mois
                                @endif
                                @if($d['discount'] < 1)
                                    &nbsp;<span class="text-green-600 font-semibold">(-{{ round((1 - $d['discount']) * 100) }}%)</span>
                                @endif
                            </p>
                        </div>
                    @endforeach
                </div>

                <hr class="my-4 border-gray-100">

                <ul class="space-y-3 mb-6 flex-1 text-sm text-gray-600">
                    <li class="flex items-center gap-2"><span class="text-green-500">✓</span> Gestion clients & produits</li>
                    <li class="flex items-center gap-2"><span class="text-green-500">✓</span> Facturation</li>
                    <li class="flex items-center gap-2"><span class="text-red-400">✗</span> <span class="text-gray-400">Export PDF</span></li>
                    <li class="flex items-center gap-2"><span class="text-red-400">✗</span> <span class="text-gray-400">Statistiques</span></li>
                    <li class="flex items-center gap-2"><span class="text-red-400">✗</span> <span class="text-gray-400">Multi-utilisateurs</span></li>
                    <li class="flex items-center gap-2"><span class="text-orange-400">⚠</span> Max {{ $basique->client_limit_per_day }} clients/jour</li>
                </ul>

                <form action="{{ route('subscriptions.subscribe') }}" method="POST">
                    @csrf
                    <input type="hidden" name="plan_id" value="{{ $basique->id }}">
                    <input type="hidden" name="duration_months" x-bind:value="duration">
                    <button type="submit" class="w-full py-3 rounded-xl font-semibold text-white bg-green-500 hover:bg-green-600 transition text-sm">
                        Choisir Basique
                    </button>
                </form>
            </div>
            @endif

            {{-- Pro --}}
            @if($pro)
            <div class="bg-blue-600 rounded-2xl shadow-lg p-6 flex flex-col relative">
                <div class="mb-4 mt-2">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="inline-block bg-blue-500 text-white text-xs font-semibold px-3 py-1 rounded-full">Pro</span>
                        <span class="bg-yellow-400 text-yellow-900 text-xs font-bold px-3 py-1 rounded-full">⭐ Populaire</span>
                    </div>
                    @foreach($durations as $months => $d)
                        @php $price = round($pro->price * $d['multiplier'] * $d['discount']); @endphp
                        <div x-show="duration === {{ $months }}">
                            <p class="text-4xl font-bold text-white">
                                {{ number_format($price, 0, ',', ' ') }}
                                <span class="text-base font-normal text-blue-200">FCFA</span>
                            </p>
                            <p class="text-sm text-blue-200 mt-1">
                                @if($months === 1) par mois
                                @elseif($months === 12) par an
                                @else pour {{ $months }} mois
                                @endif
                                @if($d['discount'] < 1)
                                    &nbsp;<span class="text-yellow-300 font-semibold">(-{{ round((1 - $d['discount']) * 100) }}%)</span>
                                @endif
                            </p>
                        </div>
                    @endforeach
                </div>

                <hr class="my-4 border-blue-500">

                <ul class="space-y-3 mb-6 flex-1 text-sm text-blue-100">
                    <li class="flex items-center gap-2"><span class="text-green-300">✓</span> Gestion clients & produits</li>
                    <li class="flex items-center gap-2"><span class="text-green-300">✓</span> Facturation</li>
                    <li class="flex items-center gap-2"><span class="text-green-300">✓</span> Export PDF</li>
                    <li class="flex items-center gap-2"><span class="text-green-300">✓</span> Statistiques</li>
                    <li class="flex items-center gap-2"><span class="text-blue-300">✗</span> <span class="text-blue-300">Multi-utilisateurs</span></li>
                    <li class="flex items-center gap-2"><span class="text-green-300">✓</span> Clients illimités</li>
                </ul>

                <form action="{{ route('subscriptions.subscribe') }}" method="POST">
                    @csrf
                    <input type="hidden" name="plan_id" value="{{ $pro->id }}">
                    <input type="hidden" name="duration_months" x-bind:value="duration">
                    <button type="submit" class="w-full py-3 rounded-xl font-semibold text-blue-600 bg-white hover:bg-blue-50 transition text-sm">
                        Choisir Pro
                    </button>
                </form>
            </div>
            @endif

            {{-- Premium --}}
            @if($premium)
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 flex flex-col">
                <div class="mb-4">
                    <span class="inline-block bg-purple-100 text-purple-700 text-xs font-semibold px-3 py-1 rounded-full mb-3">Premium</span>
                    @foreach($durations as $months => $d)
                        @php $price = round($premium->price * $d['multiplier'] * $d['discount']); @endphp
                        <div x-show="duration === {{ $months }}">
                            <p class="text-4xl font-bold text-gray-900">
                                {{ number_format($price, 0, ',', ' ') }}
                                <span class="text-base font-normal text-gray-400">FCFA</span>
                            </p>
                            <p class="text-sm text-gray-400 mt-1">
                                @if($months === 1) par mois
                                @elseif($months === 12) par an
                                @else pour {{ $months }} mois
                                @endif
                                @if($d['discount'] < 1)
                                    &nbsp;<span class="text-green-600 font-semibold">(-{{ round((1 - $d['discount']) * 100) }}%)</span>
                                @endif
                            </p>
                        </div>
                    @endforeach
                </div>

                <hr class="my-4 border-gray-100">

                <ul class="space-y-3 mb-6 flex-1 text-sm text-gray-600">
                    <li class="flex items-center gap-2"><span class="text-green-500">✓</span> Gestion clients & produits</li>
                    <li class="flex items-center gap-2"><span class="text-green-500">✓</span> Facturation</li>
                    <li class="flex items-center gap-2"><span class="text-green-500">✓</span> Export PDF</li>
                    <li class="flex items-center gap-2"><span class="text-green-500">✓</span> Statistiques</li>
                    <li class="flex items-center gap-2"><span class="text-green-500">✓</span> Multi-utilisateurs</li>
                    <li class="flex items-center gap-2"><span class="text-green-500">✓</span> Clients illimités</li>
                </ul>

                <form action="{{ route('subscriptions.subscribe') }}" method="POST">
                    @csrf
                    <input type="hidden" name="plan_id" value="{{ $premium->id }}">
                    <input type="hidden" name="duration_months" x-bind:value="duration">
                    <button type="submit" class="w-full py-3 rounded-xl font-semibold text-white bg-purple-600 hover:bg-purple-700 transition text-sm">
                        Choisir Premium
                    </button>
                </form>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
