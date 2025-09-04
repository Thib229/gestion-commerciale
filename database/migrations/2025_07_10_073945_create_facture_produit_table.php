<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFactureProduitTable extends Migration
{
    public function up()
    {
        Schema::create('facture_produit', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facture_id')->constrained()->onDelete('cascade');
            $table->foreignId('produit_id')->constrained()->onDelete('cascade');
            $table->integer('quantite');
            $table->decimal('prix', 10, 2);
            $table->timestamps();

            $table->unique(['facture_id', 'produit_id']); // éviter doublons
        });
    }

    public function down()
    {
        Schema::dropIfExists('facture_produit');
    }
}
