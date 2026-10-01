{{-- Not used by any seeded page yet -- Contact currently stays a homepage
     anchor section (x-contact-form on pages.home), matching the live site.
     This template exists so the system enum case has a view if Contact is
     ever split into its own page later. --}}
<x-layouts.site :page="$page">
  <x-hero
    :eyebrow="$page->section_eyebrow"
    :title="$page->hero_heading ?: $page->title"
    :copy="$page->hero_subheading"
  />
  <x-contact-form />
</x-layouts.site>
