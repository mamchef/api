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
        Schema::table('chef_stores', function (Blueprint $table) {
            $table->float('share_percent',2)->nullable()->default(20);
            $table->unsignedInteger('max_daily_order')->nullable()->default(20);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chef_stores', function (Blueprint $table) {
            $table->dropColumn(['share_percent','max_daily_order']);
        });
    }
};
