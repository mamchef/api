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
        Schema::create('order_item_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->constrained('order_items')->onDelete('cascade');
            $table->foreignId('food_option_group_id')->constrained('food_option_groups')->onDelete('cascade');
            $table->foreignId('food_option_id')->constrained('food_options')->onDelete('cascade');

            // Snapshot data for historical integrity
            $table->string('option_group_name'); // Group name at time of order
            $table->string('option_name'); // Option name at time of order
            $table->decimal('option_price', 8, 2); // Option price at time of order
            $table->string('option_type'); // Option type at time of order

            $table->unsignedInteger('quantity')->default(1); // For quantitative options
            $table->decimal('option_total', 8, 2); // option_price * quantity

            $table->timestamps();

            // Indexes
            $table->index('order_item_id');
            $table->index('food_option_group_id');
            $table->index('food_option_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_item_options');
    }
};