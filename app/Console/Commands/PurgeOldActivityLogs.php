<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use Illuminate\Console\Command;

class PurgeOldActivityLogs extends Command
{
    protected $signature = 'activity-logs:purge {--days=90 : Nombre de jours à conserver}';

    protected $description = 'Supprime les logs d\'activité de plus de 90 jours';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $cutoff = now()->subDays($days);

        $count = ActivityLog::where('created_at', '<', $cutoff)->delete();

        $this->info("Purge terminée : {$count} log(s) supprimé(s) (antérieurs au {$cutoff->format('d/m/Y')}).");

        return self::SUCCESS;
    }
}
