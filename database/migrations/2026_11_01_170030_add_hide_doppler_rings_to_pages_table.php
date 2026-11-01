<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            // Child pages inherit their parent's rings unless they pick a style or turn rings off
            $table->boolean('hide_doppler_rings')->default(false)->after('doppler_ring_style_id');
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('hide_doppler_rings');
        });
    }
};
