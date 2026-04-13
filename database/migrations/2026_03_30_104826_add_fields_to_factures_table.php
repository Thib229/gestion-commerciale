<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('factures', function (Blueprint $table) {
            // numero_facture et statut existent déjà
            if (!Schema::hasColumn('factures', 'public_token')) {
                $table->char('public_token', 36)->nullable()->unique()->after('statut');
            }
            if (!Schema::hasColumn('factures', 'conditions_paiement')) {
                $table->text('conditions_paiement')->nullable()->after('public_token');
            }
            // Modifier statut pour s'assurer que l'enum est correct
            $table->enum('statut', ['impayée', 'partiellement payée', 'payée'])
                  ->default('impayée')->change();
        });
    }

    public function down(): void
    {
        Schema::table('factures', function (Blueprint $table) {
            $table->dropColumn(['public_token', 'conditions_paiement']);
        });
    }
};
