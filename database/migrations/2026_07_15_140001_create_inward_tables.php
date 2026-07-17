<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inward_entries', function (Blueprint $table) {
            $table->id();
            $table->date('inward_date');
            $table->foreignId('supplier_id')->nullable()->constrained();
            $table->string('note')->nullable();
            $table->timestamps();
        });

        Schema::create('inward_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inward_entry_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained();
            $table->string('name');
            $table->string('brand');
            $table->string('category');
            $table->unsignedInteger('qty');
            $table->decimal('purchase_rate', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inward_lines');
        Schema::dropIfExists('inward_entries');
    }
};