<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ip_locations', function (Blueprint $table) {
            $table->string('ip', 45)->primary();
            $table->string('city')->nullable();
            $table->string('region')->nullable();
            $table->string('country', 5)->nullable();
            $table->string('org')->nullable();      // ISP, e.g. Jio/Airtel
            $table->timestamp('looked_up_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ip_locations');
    }
};