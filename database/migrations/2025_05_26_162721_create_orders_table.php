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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('chef_store_id')->constrained('chef_stores')->onDelete('cascade');
            $table->foreignId('user_address_id')->nullable()->constrained('user_addresses')->onDelete('set null');

            $table->string('order_number')->unique();
            $table->string('status')->default('pending');
            $table->string('delivery_type');
            $table->string('original_delivery_type')->nullable();

            $table->decimal('delivery_cost', 8, 2)->default(0);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('total_amount', 10, 2);

            $table->timestamp('estimated_ready_time')->nullable();
            $table->text('chef_notes')->nullable();
            $table->text('user_notes')->nullable();
            $table->json('delivery_address_snapshot')->nullable();
            $table->text('refused_reason')->nullable();
            $table->timestamp('delivery_change_requested_at')->nullable();
            $table->timestamp('accept_at')->nullable();
            $table->text('delivery_change_reason')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('completed_by')->nullable();

            $table->unsignedInteger("rating")->nullable();
            $table->text('rating_review')->nullable();

            $table->softDeletes();
            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'status']);
            $table->index(['chef_store_id', 'status']);
            $table->index('order_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};