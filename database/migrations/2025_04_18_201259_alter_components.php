<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
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
};
