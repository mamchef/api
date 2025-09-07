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
        Schema::table('orders', function (Blueprint $table) {
            // First order discount fields (temporary - will be removed in 6 months)
            $table->decimal('discount_amount', 8, 2)->default(0)->after('subtotal')->comment('First order discount amount (temporary field)');
            $table->unsignedTinyInteger('discount_percentage')->default(0)->after('discount_amount')->comment('First order discount percentage (temporary field)');
            $table->boolean('first_order_discount_applied')->default(false)->after('discount_percentage')->comment('Whether first order discount was applied (temporary field)');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'discount_amount', 
                'discount_percentage', 
                'first_order_discount_applied',
            ]);
        });
    }
};