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
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->text("description")->nullable();
            $table->string("icon_type")->nullable();
            $table->string("icon")->nullable();
            $table->foreignId("tag_id")->nullable()->constrained("tags")->onDelete("cascade");
            $table->boolean("status")->default(false);
            $table->boolean("homepage")->default(false);
            $table->integer("priority")->default(1000);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tags');
    }
};
