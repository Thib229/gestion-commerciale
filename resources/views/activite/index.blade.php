<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Journal d'activité
        </h2>
    </x-slot>

    <div class="py-6 px-8 max-w-5xl mx-auto">
        <div class="bg-white shadow rounded p-6">
            @if($logs->isEmpty())
                <p class="text-gray-500 italic text-center py-8">Aucune activité enregistrée.</p>
            @else
                <table class="min-w-full table-auto border-collapse">
                    <thead>
                        <tr class="bg-gray-100 text-left text-gray-700 text-sm uppercase">
                            <th class="px-4 py-3 border-b">Date</th>
                            <th class="px-4 py-3 border-b">Action</th>
                            <th class="px-4 py-3 border-b">Entité</th>
                            <th class="px-4 py-3 border-b">Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-500 whitespace-nowrap">
                                    {{ $log->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $actionParts = explode('.', $log->action);
                                        $verb = $actionParts[1] ?? $log->action;
                                        $badgeClass = match($verb) {
                                            'created' => 'bg-green-100 text-green-700',
                                            'updated' => 'bg-blue-100 text-blue-700',
                                            'deleted' => 'bg-red-100 text-red-700',
                                            default   => 'bg-gray-100 text-gray-600',
                                        };
                                        $verbLabel = match($verb) {
                                            'created' => 'Création',
                                            'updated' => 'Modification',
                                            'deleted' => 'Suppression',
                                            default   => $verb,
                                        };
                                    @endphp
                                    <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold {{ $badgeClass }}">
                                        {{ $verbLabel }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm font-mono text-gray-600">
                                    {{ class_basename($log->subject_type ?? '') }}
                                    @if($log->subject_id)
                                        <span class="text-gray-400">#{{ $log->subject_id }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    {{ $log->description }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
