<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rate_slabs', function (Blueprint $table) {
            $table->decimal('scheme_percent', 5, 2)->default(0)->after('rate');
            $table->unsignedInteger('offer_buy_qty')->nullable()->after('scheme_percent');
            $table->unsignedInteger('offer_free_qty')->nullable()->after('offer_buy_qty');
        });
    }

    public function down(): void
    {
        Schema::table('rate_slabs', function (Blueprint $table) {
            $table->dropColumn(['scheme_percent', 'offer_buy_qty', 'offer_free_qty']);
        });
    }
};