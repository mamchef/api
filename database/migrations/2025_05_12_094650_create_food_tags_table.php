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
        Schema::create('food_tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId("tag_id")->constrained("tags");
            $table->foreignId("food_id")->constrained("foods");
            $table->timestamps();

            $table->unique(["tag_id", "food_id"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_tags');
    }
};
