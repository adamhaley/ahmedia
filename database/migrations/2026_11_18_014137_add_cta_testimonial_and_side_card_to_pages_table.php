<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->foreignId('cta_testimonial_id')->nullable()->after('cta_copy')->constrained('testimonials')->nullOnDelete();
            $table->json('side_card')->nullable()->after('hero_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropConstrainedForeignId('cta_testimonial_id');
            $table->dropColumn('side_card');
        });
    }
};
