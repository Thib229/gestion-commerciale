<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">✏️ Modifier le client</h2>
    </x-slot>

    <div class="py-6 px-8 max-w-xl mx-auto">
        <!-- Message d'erreurs -->
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                <strong>Oups !</strong> Veuillez corriger les erreurs suivantes :
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Formulaire -->
        <form action="{{ route('clients.update', $client) }}" method="POST" class="space-y-4 bg-white p-6 shadow rounded">
            @csrf
            @method('PUT')

            <div>
                <label for="nom" class="block text-sm font-medium text-gray-700">Nom complet*</label>
                <input type="text" name="nom" id="nom" value="{{ old('nom', $client->nom) }}"
                    class="w-full border-gray-300 rounded shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 p-2" placeholder="Entrez le nom complet du client"
                    required>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email*</label>
                <input type="email" name="email" id="email" value="{{ old('email', $client->email) }}"
                    class="w-full border-gray-300 rounded shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 p-2" placeholder="Entrez son email"
                    required>
            </div>

            <div>
                <label for="telephone" class="block text-sm font-medium text-gray-700">Téléphone*</label>
                <input type="text" name="telephone" id="telephone" value="{{ old('telephone', $client->telephone) }}"
                    class="w-full border-gray-300 rounded shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 p-2" placeholder="Entrez son numéro de téléphone"
                    required>
            </div>

            <div class="flex justify-between items-center pt-4">
                <a href="{{ route('clients.index') }}"
                   class="text-sm text-blue-600 hover:underline">&larr; Retour à la liste</a>

                <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow">
                    ✅ Mettre à jour
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
