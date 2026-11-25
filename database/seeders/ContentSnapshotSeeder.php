<?php

namespace Database\Seeders;

use App\Console\Commands\ExportContent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/**
 * Imports the CMS content snapshot (database/seeders/content/snapshot.json, written by
 * `php artisan content:export`) with its original ids. Each table is filled only when empty,
 * so re-running db:seed never overwrites CMS edits. The snapshot's files are copied to the
 * public disk at the same relative paths.
 */
class ContentSnapshotSeeder extends Seeder
{
    public function run(?string $base = null): void
    {
        $base ??= database_path('seeders/content');
        $snapshotFile = "{$base}/snapshot.json";

        if (! File::exists($snapshotFile)) {
            $this->command?->warn("No snapshot at {$snapshotFile}.");

            return;
        }

        /** @var array<string, mixed> $snapshot */
        $snapshot = json_decode(File::get($snapshotFile), true, flags: JSON_THROW_ON_ERROR);

        Schema::withoutForeignKeyConstraints(function () use ($snapshot): void {
            foreach (ExportContent::Tables as $table) {
                $rows = $snapshot[$table] ?? [];

                if (! is_array($rows) || $rows === []) {
                    continue;
                }

                if (! Schema::hasTable($table)) {
                    $this->command?->warn("Skipping {$table}: no such table.");

                    continue;
                }

                if (DB::table($table)->exists()) {
                    continue;
                }

                foreach (array_chunk($rows, 100) as $chunk) {
                    DB::table($table)->insert($chunk);
                }
            }
        });

        foreach (File::allFiles($base) as $file) {
            $path = $file->getRelativePathname();

            if ($path !== 'snapshot.json' && ! Storage::disk('public')->exists($path)) {
                Storage::disk('public')->put($path, $file->getContents());
            }
        }
    }
}
