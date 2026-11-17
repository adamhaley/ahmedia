<x-layouts.site :page="$page">
  <x-hero
    :back="false"
    :title="$page->hero_heading ?: 'AH Media'"
    accent=".ai"
    :tagline="$tagline"
    :copy="$page->hero_subheading"
    :stance="$services?->body"
    :points="$page->hero_points"
    :ring-style="$page->heroRingStyle()"
    :actions="[
        ['label' => 'Start a Project', 'href' => '#contact', 'variant' => 'primary'],
    ]"
  />

  @if ($services)
  <x-service-rows
    eyebrow="What we do"
    :title="$services->section_heading ?? 'Three ways we help businesses run on AI'"
    :services="$services->children"
    :link="['label' => 'View All Services', 'href' => $services->url()]"
  />
  @endif

  @if ($page->section_heading)
  <x-spotlight
    :eyebrow="$page->section_eyebrow"
    :heading="$page->section_heading"
    :body="$page->body"
    :bullets="$page->bullets"
    :steps="['Discover what works', 'Remove what\'s in the way', 'Add the multiplier']"
    :actions="collect($page->buttons ?? [])->map(fn ($button) => ['label' => $button['label'], 'href' => $button['url'], 'variant' => $button['style'] ?? 'secondary'])->all()"
  />
  @endif

  <!--
    @if ($caseStudiesPage && $caseStudies->isNotEmpty())
  <x-feature-grid
    id="case-studies"
    :eyebrow="$caseStudiesPage->section_eyebrow ?: 'Case Studies'"
    :title="$caseStudiesPage->hero_heading ?: $caseStudiesPage->title"
    :copy="$caseStudiesPage->hero_subheading"
    :items="$caseStudies->map(fn ($caseStudy) => [
        'title' => $caseStudy->title,
        'copy' => $caseStudy->summary,
        'href' => $caseStudy->narrative ? $caseStudy->url() : $caseStudy->external_link,
        'linkLabel' => $caseStudy->narrative ? 'Read the case study' : ($caseStudy->external_link ? 'View project' : null),
    ])->all()"
    :actions="[['label' => 'View All Case Studies', 'href' => $caseStudiesPage->url(), 'variant' => 'secondary']]"
  />
  @endif
-->
  @if ($testimonials->isNotEmpty())
  <x-testimonials :testimonials="$testimonials" />
  @endif

  <x-contact-form
    :eyebrow="$contactPage?->section_eyebrow"
    :heading="$contactPage?->hero_heading ?: 'What\'s getting in the way?'"
    :copy="$contactPage?->hero_subheading"
  />
</x-layouts.site>
