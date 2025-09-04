<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-blue-100 min-h-screen flex items-center justify-center">

    <div class="w-full h-full flex flex-col items-center justify-center">
        <div class="bg-white px-8 py-10 rounded-lg shadow-lg text-center max-w-lg w-full mx-4">
            <h1 class="text-2xl font-bold text-gray-800 mb-4">Bienvenue sur notre plateforme</h1>
            <p class="mb-6 text-gray-700">Veuillez vous connecter ou créer un compte pour continuer.</p>
            <div class="flex justify-center space-x-4">
                <a href="{{ route('login') }}" class="px-5 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">Connexion</a>
                <a href="{{ route('register') }}" class="px-5 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition">Inscription</a>
            </div>
        </div>
    </div>

</body>
</html>
