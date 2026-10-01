{{-- Note: dreamworldcirque's Page schema (matched here) has only one eyebrow
     field, shared between hero and content -- unlike Codette's original
     two-section ahmedia design (distinct "Approach" hero eyebrow vs. "How We
     Work" content eyebrow). Shown once, on the hero; the content block below
     doesn't repeat it. A deliberate simplification from the live site, not
     an oversight. --}}
<x-layouts.site :page="$page">
  <x-hero
    :eyebrow="$page->section_eyebrow"
    :title="$page->hero_heading ?: $page->title"
    :copy="$page->hero_subheading"
    :actions="collect($page->buttons ?? [])->map(fn ($button) => ['label' => $button['label'], 'href' => $button['url'], 'variant' => $button['style'] ?? 'secondary'])->all()"
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
