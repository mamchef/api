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
            
            // Stripe Connect payment split fields
            $table->decimal('platform_fee', 8, 2)->default(0)->after('first_order_discount_applied')->comment('Platform commission amount');
            $table->decimal('chef_payout_amount', 10, 2)->default(0)->after('platform_fee')->comment('Amount that goes to chef');
            $table->string('discount_deduction_strategy', 20)->nullable()->after('chef_payout_amount')->comment('Strategy used for discount deduction');
            $table->string('stripe_payment_intent_id')->nullable()->after('discount_deduction_strategy')->comment('Stripe Payment Intent ID');
            $table->string('stripe_transfer_id')->nullable()->after('stripe_payment_intent_id')->comment('Stripe Transfer ID to chef');
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
                'platform_fee',
                'chef_payout_amount',
                'discount_deduction_strategy',
                'stripe_payment_intent_id',
                'stripe_transfer_id'
            ]);
        });
    }
};