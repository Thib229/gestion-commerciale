<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // basique, pro, premium
            $table->integer('price'); // FCFA / mois
            $table->integer('client_limit_per_day')->nullable();
            $table->boolean('pdf_enabled')->default(false);
            $table->boolean('statistics_enabled')->default(false);
            $table->boolean('multi_users')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
