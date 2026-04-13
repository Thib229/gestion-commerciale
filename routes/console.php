<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Envoyer les rappels d'expiration chaque jour à 8h
Schedule::command('subscriptions:remind')->dailyAt('08:00');

// Purger les logs d'activité de plus de 90 jours chaque nuit à 2h
Schedule::command('activity-logs:purge')->dailyAt('02:00');
