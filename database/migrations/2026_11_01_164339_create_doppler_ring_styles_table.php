<?php

use App\Data\DopplerRingSettings;
use App\Enums\PageTemplate;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doppler_ring_styles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('settings');
            $table->timestamps();
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->foreignId('doppler_ring_style_id')->nullable()->after('template')->constrained()->nullOnDelete();
        });

        // Carries the homepage's live rings over, so nothing changes on deploy
        $siteSettings = DB::table('site_settings')->first();
        $styleId = DB::table('doppler_ring_styles')->insertGetId([
            'name' => 'Homepage',
            'settings' => json_encode(DopplerRingSettings::fromArray([
                'motion' => $siteSettings->hero_rings_motion ?? null,
                'scrollStart' => $siteSettings->hero_rings_scroll_start ?? null,
                'scrollEnd' => $siteSettings->hero_rings_scroll_end ?? null,
                'pinLength' => $siteSettings->hero_rings_pin_length ?? null,
            ])->toArray()),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('pages')->where('template', PageTemplate::Home->value)->update(['doppler_ring_style_id' => $styleId]);
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropConstrainedForeignId('doppler_ring_style_id');
        });

        Schema::dropIfExists('doppler_ring_styles');
    }
};
