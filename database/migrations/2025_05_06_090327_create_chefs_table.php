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
        Schema::create('chefs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('id_number')->nullable();
            $table->string('first_name')->nullable();
            $table->string("last_name")->nullable();
            $table->string("email")->unique();
            $table->dateTime("email_verified_at")->nullable();
            $table->string("register_source")->nullable();
            $table->string("password")->nullable();
            $table->string("phone")->nullable();

            $table->foreignId("city_id")->nullable()->constrained("cities");
            $table->text("main_street")->nullable();

            $table->text("address")->nullable();
            $table->string("zip")->nullable();
            $table->string("status")->nullable()->default("registered");

            $table->text("document_1")->nullable();
            $table->text("document_2")->nullable();

            $table->string("contract_id")->nullable();
            $table->text("contract")->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chefs');
        Schema::dropIfExists('chef_password_reset_tokens');
    }
};
