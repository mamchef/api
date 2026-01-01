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
            $table->boolean('use_credit')->default(false);
            $table->timestamp('chef_payout_transferred_at')->nullable()->after('chef_payout_amount');
            $table->string('chef_payout_transfer_id')->nullable()->after('chef_payout_transferred_at');
            $table->longText('chef_payout_error')->nullable()->after('chef_payout_transfer_id');
            $table->boolean('need_review')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'use_credit',
                'chef_payout_transferred_at',
                'chef_payout_transfer_id',
                'chef_payout_error',
            ]);
        });
    }
};
