<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('facebook_url')->nullable()->after('phone');
            $table->string('instagram_url')->nullable()->after('facebook_url');
            $table->string('linkedin_url')->nullable()->after('instagram_url');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn(['facebook_url', 'instagram_url', 'linkedin_url']);
        });
    }
};
