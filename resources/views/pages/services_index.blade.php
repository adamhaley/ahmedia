<x-layouts.site :page="$page">
  <x-hero
    :ring-style="$page->heroRingStyle()"
    :eyebrow="$page->section_eyebrow"
    :title="$page->hero_heading ?: $page->title"
    :copy="$page->hero_subheading"
    :actions="[['label' => 'Start a Project', 'href' => '/#contact', 'variant' => 'primary']]"
  />

  <x-service-rows
    eyebrow="Capabilities"
    :title="$page->section_heading"
    :copy="$page->body"
    :services="$page->children"
  />

  <x-cta-band
    :eyebrow="$page->cta_eyebrow"
    :heading="$page->cta_heading"
    :copy="$page->cta_copy"
    :buttonLabel="$page->cta_button_label"
    buttonHref="/#contact"
  />
</x-layouts.site>
