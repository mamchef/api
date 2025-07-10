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
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string("description")->nullable();
            $table->foreignId("country_id")->constrained("countries");
            $table->boolean("status")->default(true);
            $table->timestamps();
        });

        $file = file_get_contents(__DIR__ . "/data/lt.json");
        $cities = json_decode($file, true);
        foreach ($cities as $city) {
            \App\Models\City::query()->create([
                "name" => $city["city"],
                "description" => $city["admin_name"],
                "country_id" => 1,
                "status" => $city["status"] ?? false,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
