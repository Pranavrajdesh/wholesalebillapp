<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('partners', function (Blueprint $table) {
            $table->string('gst_number', 15)->nullable()->after('mobile');
            $table->string('alt_mobile', 15)->nullable()->after('gst_number');
            $table->string('address', 500)->nullable()->after('alt_mobile');
        });
    }

    public function down(): void
    {
        Schema::table('partners', function (Blueprint $table) {
            $table->dropColumn(['gst_number', 'alt_mobile', 'address']);
        });
    }
};