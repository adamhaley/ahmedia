<?php

namespace Database\Seeders;

use App\Enums\ProjectType;
use App\Models\Page;
use App\Models\Project;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Seeds the n8n workflow cards on the Automations page from
 * database/seeders/data/projects.php, copying their images from
 * database/seeders/content/projects/ to the public disk. Like the other
 * content seeders, it only runs against an empty table so CMS edits are
 * never overwritten.
 */
class ProjectContentSeeder extends Seeder
{
    public function run(): void
    {
        if (Project::query()->exists()) {
            $this->command?->warn('Projects already exist; skipping so CMS edits are never overwritten.');

            return;
        }

        $automations = Page::query()->where('path', 'services/automations')->first();
        if (! $automations) {
            $this->command?->warn('No services/automations page; skipping the workflow projects.');

            return;
        }

        foreach (require __DIR__.'/data/projects.php' as $sortOrder => $attributes) {
            $images = $attributes['images'] ?? [];
            unset($attributes['images']);

            $project = Project::query()->create([
                ...$attributes,
                'page_id' => $automations->id,
                'type' => ProjectType::Workflow,
                'cover_image' => $this->publish($attributes['cover_image']),
                'is_published' => true,
                'sort_order' => $sortOrder,
            ]);

            foreach ($images as $index => [$file, $altText]) {
                $project->images()->create(['image' => $this->publish($file), 'alt_text' => $altText, 'sort_order' => $index]);
            }
        }
    }

    private function publish(string $file): string
    {
        $path = 'projects/'.$file;
        Storage::disk('public')->put($path, file_get_contents(database_path('seeders/content/'.$path)));

        return $path;
    }
}
