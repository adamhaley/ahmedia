<x-layouts.site :page="$page">
  <x-hero
    :eyebrow="$page->section_eyebrow"
    :title="$page->hero_heading ?: $page->title"
    :copy="$page->hero_subheading"
  />

  <x-feature-grid
    :title="$page->section_heading ?: 'Case Studies'"
    :copy="$page->body"
    :items="$caseStudies->map(fn ($caseStudy) => [
        'title' => $caseStudy->title,
        'copy' => $caseStudy->summary,
        'href' => $caseStudy->narrative ? $caseStudy->url() : $caseStudy->external_link,
        'linkLabel' => $caseStudy->narrative ? 'Read the case study' : ($caseStudy->external_link ? 'View project' : null),
    ])->all()"
  />

  <x-cta-band
    :eyebrow="$page->cta_eyebrow"
    :heading="$page->cta_heading"
    copy="Tell us what you're building - we'll tell you honestly whether AI is the right tool for it."
    :buttonLabel="$page->cta_button_label"
    buttonHref="/#contact"
  />
</x-layouts.site>
