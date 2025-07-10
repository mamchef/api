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
        Schema::create('food_options', function (Blueprint $table) {
            $table->id();

            $table->foreignId("food_option_group_id")->constrained("food_option_groups")->onDelete("cascade");
            $table->string("type"); // ["quantitative", "qualitative"]
            $table->string("name");
            $table->string("description")->nullable();
            $table->float("price")->default(0);
            $table->integer("sort_order")->default(0);
            $table->integer("maximum_allowed")->default(1);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_options');
    }
};
