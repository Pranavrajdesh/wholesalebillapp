<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('brand_id')->constrained();
            $table->foreignId('category_id')->constrained();
            $table->string('barcode')->nullable()->unique();
            $table->decimal('mrp', 10, 2);
            $table->string('hsn_code', 20)->nullable();
            $table->decimal('tax_percent', 5, 2)->default(0);
            $table->boolean('tax_inclusive')->default(true);
            $table->boolean('track_stock')->default(false);
            $table->integer('stock_qty')->default(0);
            $table->string('image_path')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->boolean('rate_visible')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};