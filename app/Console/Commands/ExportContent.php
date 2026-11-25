<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/**
 * Exports the CMS content to a JSON snapshot plus the public-disk files its rows reference,
 * which ContentSnapshotSeeder imports into an empty database. Run on demand; the snapshot is
 * not kept in sync with the CMS.
 */
#[Signature('content:export {--path= : Base directory to write to instead of database/seeders/content}')]
#[Description('Export CMS content to database/seeders/content/snapshot.json and copy its files')]
class ExportContent extends Command
{
    /**
     * The exported tables, in an order that lets foreign keys resolve on import.
     *
     * @var list<string>
     */
    public const Tables = [
        'doppler_ring_styles',
        'testimonials',
        'pages',
        'packages',
        'case_studies',
        'case_study_images',
        'projects',
        'project_images',
        'credentials',
        'faq_items',
        'site_settings',
        'posts',
    ];

    public function handle(): int
    {
        $base = rtrim((string) ($this->option('path') ?: database_path('seeders/content')), '/');
        $disk = Storage::disk('public');

        $snapshot = [];
        $referenced = [];

        foreach (self::Tables as $table) {
            if (! Schema::hasTable($table)) {
                $this->warn("Skipping {$table}: no such table.");

                continue;
            }

            $query = DB::table($table)->orderBy('id');

            if ($table === 'posts') {
                $query->whereNotNull('published_at')->where('published_at', '<=', now());
            }

            $snapshot[$table] = $query->get()->map(fn (object $row): array => (array) $row)->all();

            foreach ($snapshot[$table] as $row) {
                foreach ($row as $value) {
                    if (is_string($value) && preg_match('#^[\w.\-]+(/[\w.\-]+)+$#', $value) && ! str_contains($value, '..') && $disk->exists($value)) {
                        $referenced[$value] = true;
                    }
                }
            }
        }

        $snapshot['exported_at'] = now()->toIso8601String();

        File::ensureDirectoryExists($base);

        foreach (array_keys($referenced) as $file) {
            File::ensureDirectoryExists(dirname("{$base}/{$file}"));
            File::put("{$base}/{$file}", $disk->get($file));
        }

        $removed = 0;

        foreach (File::directories($base) as $directory) {
            foreach (File::allFiles($directory) as $file) {
                $relative = basename($directory).'/'.$file->getRelativePathname();

                if (! isset($referenced[$relative])) {
                    File::delete($file->getPathname());
                    $removed++;
                }
            }

            if (File::allFiles($directory) === []) {
                File::deleteDirectory($directory);
            }
        }

        File::put("{$base}/snapshot.json", json_encode($snapshot, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)."\n");

        $this->info(sprintf('Exported %d tables and %d files to %s (%d stale files removed).', count($snapshot) - 1, count($referenced), $base, $removed));

        return self::SUCCESS;
    }
}
