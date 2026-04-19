<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Gestion des utilisateurs
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded">
                    <ul class="list-disc ml-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white shadow rounded p-6">
                <h3 class="text-lg font-semibold mb-4">Ajouter un employé</h3>
                <form method="POST" action="{{ route('users.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Nom</label>
                        <input id="name" name="name" type="text" required class="mt-1 block w-full border-gray-300 rounded-md" value="{{ old('name') }}">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input id="email" name="email" type="email" required class="mt-1 block w-full border-gray-300 rounded-md" value="{{ old('email') }}">
                    </div>
                    <div>
                        <label for="staff_role" class="block text-sm font-medium text-gray-700">Fonction</label>
                        <select id="staff_role" name="staff_role" required class="mt-1 block w-full border-gray-300 rounded-md">
                            <option value="">-- Choisir --</option>
                            <option value="comptable" @selected(old('staff_role') === 'comptable')>Comptable</option>
                            <option value="secretaire" @selected(old('staff_role') === 'secretaire')>Secrétaire</option>
                            <option value="caissier" @selected(old('staff_role') === 'caissier')>Caissier</option>
                            <option value="commercial" @selected(old('staff_role') === 'commercial')>Commercial</option>
                            <option value="autre" @selected(old('staff_role') === 'autre')>Autre</option>
                        </select>
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe</label>
                        <input id="password" name="password" type="password" required class="mt-1 block w-full border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmation</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required class="mt-1 block w-full border-gray-300 rounded-md">
                    </div>
                    <div class="md:col-span-2 pt-3 border-t border-gray-100">
                        <button
                            type="submit"
                            class="inline-flex items-center justify-center w-full md:w-auto px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-md shadow-sm transition"
                            style="background:#4f46e5;color:#ffffff;border:1px solid #4338ca;min-height:44px;"
                        >
                            Ajouter l'employe
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white shadow rounded p-6">
                <h3 class="text-lg font-semibold mb-4">Utilisateurs de l'entreprise</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Rôle</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fonction</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($users as $user)
                                <tr>
                                    <td class="px-3 py-2">{{ $user->name }}</td>
                                    <td class="px-3 py-2">{{ $user->email }}</td>
                                    <td class="px-3 py-2">{{ $user->role }}</td>
                                    <td class="px-3 py-2">{{ $user->staff_role ?? '-' }}</td>
                                    <td class="px-3 py-2 text-right">
                                        @if ($user->role === 'staff')
                                            <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Supprimer cet employé ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800">Supprimer</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
