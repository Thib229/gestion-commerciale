<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('plan_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('montant', 10, 2);
            $table->string('devise', 10)->default('XOF');
            $table->enum('statut', ['réussie', 'échouée', 'en attente'])->default('en attente');
            $table->string('reference_fedapay', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_payments');
    }
};
