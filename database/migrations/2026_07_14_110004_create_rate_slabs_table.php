<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rate_slabs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('rate_group_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('min_qty');
            $table->decimal('rate', 10, 2);
            $table->timestamps();
            $table->unique(['product_id', 'rate_group_id', 'min_qty']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rate_slabs');
    }
};