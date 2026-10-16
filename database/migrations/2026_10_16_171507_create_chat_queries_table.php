<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_queries', function (Blueprint $table) {
            $table->id();
            $table->text('question');
            $table->string('outcome');
            $table->decimal('score', 5, 4)->nullable();
            $table->foreignId('matched_faq_id')->nullable()->constrained('faq_items')->nullOnDelete();
            $table->foreignId('contact_submission_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_queries');
    }
};
