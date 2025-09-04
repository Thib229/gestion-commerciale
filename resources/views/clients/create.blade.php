<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 leading-tight">➕ Nouveau client</h2>
    </x-slot>

    <div class="py-6 px-8 max-w-xl mx-auto">
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                <strong>Oups !</strong> Corrige les erreurs suivantes :
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('clients.store') }}" method="POST" class="bg-white p-6 rounded shadow space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700">Nom</label>
                <input type="text" name="nom" class="w-full border-gray-300 rounded p-2" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" class="w-full border-gray-300 rounded p-2" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Téléphone</label>
                <input type="text" name="telephone" class="w-full border-gray-300 rounded p-2" required>
            </div>

            <div class="flex justify-between pt-4">
                <a href="{{ route('clients.index') }}" class="text-blue-600 hover:underline">← Retour</a>
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded shadow hover:bg-green-700">Enregistrer</button>
            </div>
        </form>
    </div>
</x-app-layout>
