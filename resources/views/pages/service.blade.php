<x-layouts.site :page="$page">
  <x-hero
    :eyebrow="$page->section_eyebrow"
    :title="$page->hero_heading ?: $page->title"
    :copy="$page->hero_subheading"
    :actions="[['label' => 'Start a Project', 'href' => '/#contact', 'variant' => 'primary']]"
  />

  @if ($page->section_heading)
  <x-split-content
    :heading="$page->section_heading"
    :body="$page->body"
    :bullets="$page->bullets"
  />
  @endif

  <x-cta-band
    :eyebrow="$page->cta_eyebrow"
    :heading="$page->cta_heading"
    copy="Tell us what you're trying to build — we'll tell you honestly whether AI is the right tool for it."
    :buttonLabel="$page->cta_button_label"
    buttonHref="/#contact"
  />
</x-layouts.site>
