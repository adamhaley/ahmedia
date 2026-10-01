<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('case_studies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('summary', 500)->nullable();
            $table->text('narrative')->nullable();
            $table->string('hero_image')->nullable();
            $table->string('external_link')->nullable();
            $table->json('tags')->nullable();
            $table->string('client_name')->nullable();
            $table->date('started_at')->nullable();
            $table->date('completed_at')->nullable();
            $table->string('testimonial_quote', 1000)->nullable();
            $table->string('testimonial_author')->nullable();
            $table->string('testimonial_author_role')->nullable();
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('sort_order')->default(0);

            // Nullable, unique-when-set idempotency key for a future
            // adamhaley.com import -- deliberately not building that import
            // now (see the 2026-10-01 plan), but reserving the column means
            // it can be bolted on later without another migration. A
            // manually-created case study simply never sets this.
            $table->unsignedBigInteger('source_project_id')->nullable()->unique();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('case_studies');
    }
};
