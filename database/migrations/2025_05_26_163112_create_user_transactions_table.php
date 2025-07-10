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
        Schema::create('user_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('set null');

            $table->string('type');

            $table->decimal('amount', 10, 2); // Positive for credit, negative for debit
            $table->text('description'); // Human readable description
            $table->string('status')->default('completed');

            // Payment gateway fields
            $table->string('payment_method')->nullable(); // 'apple_pay', 'stripe', etc.
            $table->string('external_transaction_id')->nullable(); // Payment gateway transaction ID
            $table->json('gateway_response')->nullable(); // Full gateway response for debugging

            $table->timestamps();

            // Indexes for balance calculation performance
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'created_at']);
            $table->index('order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_transactions');
    }
};