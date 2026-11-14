<?php

use App\Models\Page;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Lets a credential also appear on a service page, and puts the n8n certifications on
 * Automations with n8n, where they back up the workflow examples.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('credentials', function (Blueprint $table) {
            $table->foreignId('page_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });

        $automations = Page::query()->where('path', 'services/automations')->value('id');
        if ($automations) {
            DB::table('credentials')->where('issuer', 'n8n')->where('is_featured', false)->update(['page_id' => $automations]);
        }
    }

    public function down(): void
    {
        Schema::table('credentials', function (Blueprint $table) {
            $table->dropConstrainedForeignId('page_id');
        });
    }
};
