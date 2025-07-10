<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('chef_stores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chef_id')->constrained('chefs');
            $table->string("name")->nullable();
            $table->string("slug")->nullable();
            $table->string("short_description")->nullable();
            $table->string("profile_image")->nullable();

            $table->foreignId("city_id")->nullable()->constrained("cities");

            $table->string("main_street")->nullable();
            $table->text("address")->nullable();
            $table->string("building_details")->nullable();
            $table->string("zip")->nullable();
            $table->string('lat')->nullable();
            $table->string('lng')->nullable();

            $table->string("phone")->nullable();
            $table->decimal("rating", 2, 1,)->nullable();
            $table->string("status")->nullable();

            $table->string("estimated_time")->nullable();
            $table->string("start_daily_time")->nullable();
            $table->string("end_daily_time")->nullable();

            $table->string("delivery_method")->nullable();
            $table->float("delivery_cost")->nullable();

            $table->boolean("is_open")->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chef_stores');
    }
};
