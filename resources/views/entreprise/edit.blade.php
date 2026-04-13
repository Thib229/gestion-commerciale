<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Profil entreprise</h2>
    </x-slot>

    <div class="py-8 px-4 max-w-3xl mx-auto">

        @if(session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if($profile && !$profile->isComplete())
            <div class="mb-6 bg-yellow-50 border border-yellow-400 text-yellow-800 px-4 py-3 rounded flex items-start gap-3">
                <span class="text-xl">⚠️</span>
                <div>
                    <p class="font-semibold">Profil incomplet</p>
                    <p class="text-sm">Complétez votre profil pour que vos informations apparaissent sur les factures PDF.</p>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white shadow rounded-2xl p-6">
            <form action="{{ route('entreprise.update') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @method('PUT')

                {{-- Logo --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Logo de l'entreprise</label>
                    @if($profile && $profile->logo_path)
                        <img src="{{ Storage::url($profile->logo_path) }}" alt="Logo" class="h-16 mb-2 rounded">
                    @endif
                    <input type="file" name="logo" accept="image/jpeg,image/png,image/webp"
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="text-xs text-gray-400 mt-1">JPEG, PNG ou WebP — max 2 Mo</p>
                </div>

                {{-- Nom --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom de l'entreprise <span class="text-red-500">*</span></label>
                    <input type="text" name="nom" value="{{ old('nom', $profile->nom ?? '') }}"
                        class="w-full border-gray-300 rounded-lg shadow-sm p-2" required>
                </div>

                {{-- Adresse --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                    <textarea name="adresse" rows="2" class="w-full border-gray-300 rounded-lg shadow-sm p-2">{{ old('adresse', $profile->adresse ?? '') }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Téléphone --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                        <input type="text" name="telephone" value="{{ old('telephone', $profile->telephone ?? '') }}"
                            class="w-full border-gray-300 rounded-lg shadow-sm p-2">
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email professionnel</label>
                        <input type="email" name="email" value="{{ old('email', $profile->email ?? '') }}"
                            class="w-full border-gray-300 rounded-lg shadow-sm p-2">
                    </div>
                </div>

                {{-- Numéro fiscal --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Numéro fiscal (IFU)</label>
                    <input type="text" name="numero_fiscal" value="{{ old('numero_fiscal', $profile->numero_fiscal ?? '') }}"
                        class="w-full border-gray-300 rounded-lg shadow-sm p-2">
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
