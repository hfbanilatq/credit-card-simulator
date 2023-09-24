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
        Schema::create('simulations', function (Blueprint $table) {
            $table->id();
            $table->integer('number_of_installments');
            $table->unsignedBigInteger('credit_card_id');
            $table->timestamps();

            $table->foreign('credit_card_id')->references('id')->on('credit_cards');
        });

        Schema::create('product_has_simulation', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('simulation_id');
            $table->primary(['product_id', 'simulation_id']);

            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('simulation_id')->references('id')->on('simulations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_has_simulation');
        Schema::dropIfExists('simulations');
    }
};
