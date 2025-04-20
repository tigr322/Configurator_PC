<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('markets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('markets');
    }
    /*
    public function up()
    {
        Schema::table('components', function (Blueprint $table) {
            // Add the market_id column as foreign key
            $table->foreignId('market_id')
                  ->nullable()
                  ->constrained('markets')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('components', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['market_id']);
            // Then drop the column
            $table->dropColumn('market_id');
        });
    }
        */
};