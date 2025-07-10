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
        Schema::create('foods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string("slug")->nullable();
            $table->text('description')->nullable();
            $table->string("image")->nullable();
            $table->float("price");
            $table->unsignedInteger("available_qty")->default(0);
            $table->unsignedInteger("display_order")->nullable();
            $table->foreignId("chef_store_id")->constrained("chef_stores");
            $table->decimal("rating", 2, 1,)->nullable();
            $table->boolean("status")->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('foods');
    }
};
