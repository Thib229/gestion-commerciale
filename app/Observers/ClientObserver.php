<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;

class ClientObserver
{
    public function created(Client $client): void
    {
        $this->log('client.created', $client, "Client créé : {$client->nom}");
    }

    public function updated(Client $client): void
    {
        $this->log('client.updated', $client, "Client modifié : {$client->nom}");
    }

    public function deleted(Client $client): void
    {
        $this->log('client.deleted', $client, "Client supprimé : {$client->nom}");
    }

    private function log(string $action, Client $client, string $description): void
    {
        $userId = Auth::id() ?? $client->user_id;
        if (!$userId) return;

        ActivityLog::create([
            'user_id'      => $userId,
            'action'       => $action,
            'subject_type' => Client::class,
            'subject_id'   => $client->id,
            'description'  => $description,
        ]);
    }
}
