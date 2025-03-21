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
        // Таблица categories
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // Таблица components
        Schema::create('components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('name');
            $table->string('brand')->nullable();
            $table->decimal('price', 10, 2);
            $table->text('image_url')->nullable();
            $table->text('shop_url')->nullable();
            $table->json('compatibility_data');
            $table->timestamps();
        });

        // Таблица configurations
        Schema::create('configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->decimal('total_price', 10, 2)->default(0);
            $table->timestamps();
        });

        // Таблица configuration_components
        Schema::create('configuration_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('configuration_id')->constrained('configurations')->onDelete('cascade');
            $table->foreignId('component_id')->constrained('components')->onDelete('cascade');
            $table->timestamps();
        });

        // Таблица compatibility_rules
        Schema::create('compatibility_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category1_id')->constrained('categories')->onDelete('cascade');
            $table->foreignId('category2_id')->constrained('categories')->onDelete('cascade');
            $table->json('condition');
            $table->timestamps();
        });

        // Таблица parsed_data
        Schema::create('parsed_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('component_id')->constrained('components')->onDelete('cascade');
            $table->string('source');
            $table->decimal('price', 10, 2)->nullable();
            $table->boolean('availability')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('parsed_data');
        Schema::dropIfExists('compatibility_rules');
        Schema::dropIfExists('configuration_components');
        Schema::dropIfExists('configurations');
        Schema::dropIfExists('components');
        Schema::dropIfExists('categories');
    }
};
