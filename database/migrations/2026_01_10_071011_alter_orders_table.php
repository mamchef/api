<?php

use App\Enums\Order\OrderPayoutStatusEnum;
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
        Schema::table("orders", function (Blueprint $table) {
           $table->string('payout_status')->default(OrderPayoutStatusEnum::NO_PAYOUT->value);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
