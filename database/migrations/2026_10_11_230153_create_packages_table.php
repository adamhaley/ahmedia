<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->string('summary', 500)->nullable();
            $table->unsignedInteger('price');
            $table->unsignedSmallInteger('delivery_days')->nullable();
            $table->json('includes')->nullable();
            $table->json('add_ons')->nullable();
            $table->string('sample_document')->nullable();
            $table->string('upwork_url')->nullable();
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['page_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
