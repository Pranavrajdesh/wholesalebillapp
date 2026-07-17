<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('invoice_no')->unique();
            $table->foreignId('partner_id')->constrained();
            $table->date('invoice_date');
            $table->decimal('subtotal', 12, 2);
            $table->string('discount_type', 10)->nullable();
            $table->decimal('discount_value', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->string('discount_note')->nullable();
            $table->decimal('round_off', 6, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->timestamps();
        });

        Schema::create('invoice_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained();
            $table->string('name');
            $table->string('brand');
            $table->string('category');
            $table->string('hsn_code', 20)->nullable();
            $table->decimal('mrp', 10, 2);
            $table->unsignedInteger('qty');
            $table->unsignedInteger('free_qty')->default(0);
            $table->decimal('rate', 10, 2);
            $table->decimal('scheme_percent', 5, 2)->default(0);
            $table->decimal('tax_percent', 5, 2)->default(0);
            $table->boolean('tax_inclusive')->default(true);
            $table->decimal('amount', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_lines');
        Schema::dropIfExists('invoices');
    }
};