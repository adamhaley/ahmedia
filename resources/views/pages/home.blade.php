<x-layouts.site :page="$page">
  <x-hero
    eyebrow="AI Systems & Automation"
    title="AH Media"
    accent=".ai"
    copy="We design, build, and ship the AI systems businesses actually run on — from custom chat and RAG assistants to end-to-end automation with n8n."
    :actions="[
        ['label' => 'Start a Project', 'href' => '#contact', 'variant' => 'primary'],
        ['label' => 'What We Do', 'href' => '#services', 'variant' => 'secondary'],
    ]"
  />

  @if ($services)
  <x-feature-grid
    eyebrow="What We Do"
    :title="$services->section_heading ?? 'Three ways we help businesses run on AI'"
    :copy="$services->body"
    :items="$services->children->map(fn ($child) => [
        'title' => $child->title,
        'copy' => $child->card_excerpt,
        'href' => $child->url(),
        'linkLabel' => 'Learn more',
    ])->all()"
    :actions="[['label' => 'View All Services', 'href' => $services->url(), 'variant' => 'secondary']]"
  />
  @endif

  @if ($page->section_heading)
  <x-spotlight
    :eyebrow="$page->section_eyebrow"
    :heading="$page->section_heading"
    :body="$page->body"
    :bullets="$page->bullets"
    :actions="[['label' => 'More on Our Approach', 'href' => '/approach/', 'variant' => 'secondary']]"
  />
  @endif

  <x-cta-band
    id="case-studies"
    :eyebrow="$page->cta_eyebrow"
    :heading="$page->cta_heading"
    copy="We're building out detailed case studies from current engagements. Take a look, or check back soon."
    buttonLabel="View Case Studies"
    buttonHref="/case-studies/"
    buttonVariant="secondary"
  />

  <x-contact-form />
</x-layouts.site>
