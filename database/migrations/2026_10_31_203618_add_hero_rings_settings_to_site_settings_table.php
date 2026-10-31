<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('hero_rings_motion')->default('scroll')->after('accent_color');
            $table->decimal('hero_rings_scroll_start', 4, 2)->default(0)->after('hero_rings_motion');
            $table->decimal('hero_rings_scroll_end', 4, 2)->default(1)->after('hero_rings_scroll_start');
            $table->unsignedSmallInteger('hero_rings_pin_length')->default(220)->after('hero_rings_scroll_end');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn(['hero_rings_motion', 'hero_rings_scroll_start', 'hero_rings_scroll_end', 'hero_rings_pin_length']);
        });
    }
};
