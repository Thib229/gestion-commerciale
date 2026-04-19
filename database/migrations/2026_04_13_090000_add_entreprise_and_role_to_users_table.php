<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('entreprise_id')
                ->nullable()
                ->after('id')
                ->constrained('entreprise_profiles')
                ->nullOnDelete();

            $table->enum('role', ['admin', 'staff'])
                ->default('admin')
                ->after('password');

            $table->index(['entreprise_id', 'role']);
        });

        // Associer les utilisateurs existants à leur entreprise propriétaire
        DB::table('users')
            ->join('entreprise_profiles', 'entreprise_profiles.user_id', '=', 'users.id')
            ->whereNull('users.entreprise_id')
            ->update(['users.entreprise_id' => DB::raw('entreprise_profiles.id')]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['entreprise_id', 'role']);
            $table->dropConstrainedForeignId('entreprise_id');
            $table->dropColumn('role');
        });
    }
};
