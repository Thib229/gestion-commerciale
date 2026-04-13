<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entreprise_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->string('nom');
            $table->string('logo_path', 500)->nullable();
            $table->text('adresse')->nullable();
            $table->string('telephone', 50)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('numero_fiscal', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entreprise_profiles');
    }
};
