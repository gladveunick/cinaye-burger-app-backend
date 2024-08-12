<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('commandes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('burger_id');
            $table->string('nom');
            $table->string('email');
            $table->integer('quantite');
            $table->integer('prix_total');
            $table->enum('status', ['en cours', 'terminé', 'annulé', 'payé'])->default('en cours');
            $table->timestamp('date_paiement')->nullable();
            $table->integer('montant')->nullable();
            $table->timestamps();

            $table->foreign('burger_id')->references('id')->on('burgers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commandes');
    }
};
