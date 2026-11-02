<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_submissions', function (Blueprint $table) {
            $table->boolean('email_mx_valid')->nullable()->after('email');
            $table->string('classification')->nullable()->after('read_at');
            $table->unsignedTinyInteger('classification_confidence')->nullable()->after('classification');
            $table->text('classification_reason')->nullable()->after('classification_confidence');
            $table->timestamp('classified_at')->nullable()->after('classification_reason');
            $table->string('human_classification')->nullable()->after('classified_at');
            $table->foreignId('reviewed_by_user_id')->nullable()->after('human_classification')->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by_user_id');
        });
    }

    public function down(): void
    {
        Schema::table('contact_submissions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('reviewed_by_user_id');
            $table->dropColumn(['email_mx_valid', 'classification', 'classification_confidence', 'classification_reason', 'classified_at', 'human_classification', 'reviewed_at']);
        });
    }
};
