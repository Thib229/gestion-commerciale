<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link
            href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
            rel="stylesheet"
        />


        <title>{{ config('app.name', 'GestCom') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Bandeau vérification email -->
            @auth
                @if(!auth()->user()->hasVerifiedEmail())
                    <div class="bg-yellow-50 border-b border-yellow-200 px-4 py-3">
                        <div class="max-w-7xl mx-auto flex items-center justify-between flex-wrap gap-2">
                            <p class="text-sm text-yellow-800">
                                ⚠️ Votre adresse email n'est pas encore vérifiée. Certaines fonctionnalités peuvent être limitées.
                            </p>
                            <form method="POST" action="{{ route('verification.send') }}">
                                @csrf
                                <button type="submit" class="text-sm text-yellow-700 underline hover:text-yellow-900 font-medium">
                                    Renvoyer l'email de vérification
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            @endauth

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
