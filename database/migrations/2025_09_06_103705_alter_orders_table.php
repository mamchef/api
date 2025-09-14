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
            // Stripe Connect payment split fields
            $table->decimal('platform_fee', 8, 2)->default(0)->comment('Platform commission amount');
            $table->decimal('chef_payout_amount', 10, 2)->default(0)->comment('Amount that goes to chef');
            $table->string('discount_deduction_strategy', 20)->nullable()->comment('Strategy used for discount deduction');
            $table->string('stripe_payment_intent_id')->nullable()->comment('Stripe Payment Intent ID');
            $table->string('stripe_transfer_id')->nullable()->comment('Stripe Transfer ID to chef');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'platform_fee',
                'chef_payout_amount',
                'discount_deduction_strategy',
                'stripe_payment_intent_id',
                'stripe_transfer_id'
            ]);
        });
    }
};