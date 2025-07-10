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
        Schema::create('food_option_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId("food_id")->constrained("foods")->onDelete("cascade");
            $table->string("name");
            $table->string("slug")->nullable();
            $table->string("selection_type"); //  ["single", "multiple"]
            $table->integer("max_selections")->nullable();
            $table->boolean("is_required")->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_option_groups');
    }
};
