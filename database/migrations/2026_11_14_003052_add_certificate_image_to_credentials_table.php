<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/**
 * A full certificate image, shown as a wide card above the badges, and the n8n Foundations
 * Pathway certificate (which covers all three courses) for the existing pathway credential.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('credentials', function (Blueprint $table) {
            $table->string('certificate_image')->nullable()->after('badge_image');
        });

        $path = 'credentials/n8n-foundations-pathway-certificate.webp';
        $pathway = DB::table('credentials')->where('name', 'n8n Foundations Pathway')->whereNull('certificate_image');
        if ($pathway->exists()) {
            Storage::disk('public')->put($path, file_get_contents(database_path('seeders/content/'.$path)));
            $pathway->update(['certificate_image' => $path]);
        }
    }

    public function down(): void
    {
        Schema::table('credentials', function (Blueprint $table) {
            $table->dropColumn('certificate_image');
        });
    }
};
