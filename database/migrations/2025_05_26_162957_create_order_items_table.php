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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('food_id')->constrained('foods')->onDelete('cascade');

            // Snapshot data for historical integrity
            $table->string('food_name'); // Food name at time of order
            $table->decimal('food_price', 8, 2); // Food price at time of order

            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('item_subtotal', 8, 2); // food_price * quantity (before options)
            $table->decimal('item_total', 8, 2); // item_subtotal + options total

            $table->timestamps();

            $table->string('note')->nullable(); // Food name at time of order

            // Indexes
            $table->index('order_id');
            $table->index('food_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};