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
        Schema::create('chef_store_tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId("tag_id")->constrained("tags");
            $table->foreignId("chef_store_id")->constrained("chef_stores");
            $table->timestamps();
            $table->unique(["tag_id", "chef_store_id"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chef_store_tages');
    }
};
