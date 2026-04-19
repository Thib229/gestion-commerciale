<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->foreignId('entreprise_id')
                ->nullable()
                ->after('user_id')
                ->constrained('entreprise_profiles')
                ->cascadeOnDelete();
            $table->index('entreprise_id');
        });

        Schema::table('produits', function (Blueprint $table) {
            $table->foreignId('entreprise_id')
                ->nullable()
                ->after('user_id')
                ->constrained('entreprise_profiles')
                ->cascadeOnDelete();
            $table->index('entreprise_id');
        });

        Schema::table('factures', function (Blueprint $table) {
            $table->foreignId('entreprise_id')
                ->nullable()
                ->after('user_id')
                ->constrained('entreprise_profiles')
                ->cascadeOnDelete();
            $table->index('entreprise_id');
        });

        DB::table('clients')
            ->join('users', 'users.id', '=', 'clients.user_id')
            ->whereNull('clients.entreprise_id')
            ->update(['clients.entreprise_id' => DB::raw('users.entreprise_id')]);

        DB::table('produits')
            ->join('users', 'users.id', '=', 'produits.user_id')
            ->whereNull('produits.entreprise_id')
            ->update(['produits.entreprise_id' => DB::raw('users.entreprise_id')]);

        DB::table('factures')
            ->join('users', 'users.id', '=', 'factures.user_id')
            ->whereNull('factures.entreprise_id')
            ->update(['factures.entreprise_id' => DB::raw('users.entreprise_id')]);
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropIndex(['entreprise_id']);
            $table->dropConstrainedForeignId('entreprise_id');
        });

        Schema::table('produits', function (Blueprint $table) {
            $table->dropIndex(['entreprise_id']);
            $table->dropConstrainedForeignId('entreprise_id');
        });

        Schema::table('factures', function (Blueprint $table) {
            $table->dropIndex(['entreprise_id']);
            $table->dropConstrainedForeignId('entreprise_id');
        });
    }
};
