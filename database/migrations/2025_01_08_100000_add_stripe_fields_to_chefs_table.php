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
        Schema::table('chefs', function (Blueprint $table) {
            // Stripe Connect account fields
            $table->string('stripe_account_id')->nullable()->after('email')->comment('Stripe Connect account ID');
            $table->string('stripe_account_status')->default('not_created')->after('stripe_account_id')->comment('Stripe account status: not_created, pending, active, error');
            $table->boolean('stripe_details_submitted')->default(false)->after('stripe_account_status')->comment('Whether chef completed Stripe KYC');
            $table->boolean('stripe_payouts_enabled')->default(false)->after('stripe_details_submitted')->comment('Whether payouts are enabled');
            $table->boolean('stripe_charges_enabled')->default(false)->after('stripe_payouts_enabled')->comment('Whether charges are enabled');
            $table->timestamp('stripe_onboarded_at')->nullable()->after('stripe_charges_enabled')->comment('When chef completed Stripe onboarding');
            
            // Additional fields for better chef management
            $table->string('country_code', 2)->default('LT')->after('stripe_onboarded_at')->comment('Chef country code for Stripe');
            $table->string('business_name')->nullable()->after('country_code')->comment('Business name for Stripe account');
            
            // Index for better performance
            $table->index('stripe_account_id', 'chefs_stripe_account_id_index');
            $table->index('stripe_account_status', 'chefs_stripe_status_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chefs', function (Blueprint $table) {
            $table->dropIndex('chefs_stripe_account_id_index');
            $table->dropIndex('chefs_stripe_status_index');
            
            $table->dropColumn([
                'stripe_account_id',
                'stripe_account_status',
                'stripe_details_submitted',
                'stripe_payouts_enabled', 
                'stripe_charges_enabled',
                'stripe_onboarded_at',
                'country_code',
                'business_name',
            ]);
        });
    }
};