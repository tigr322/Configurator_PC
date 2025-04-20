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
        Schema::create('markets_urls', function (Blueprint $table) {
            $table->id();

            // Внешний ключ на таблицу categories
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');

            // Внешний ключ на таблицу markets
            $table->unsignedBigInteger('market_id');
            $table->foreign('market_id')->references('id')->on('markets')->onDelete('cascade');

            // URL
            $table->string('url');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('markets_urls');
    }
};
