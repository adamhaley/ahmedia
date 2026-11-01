<x-layouts.site :page="$page">
  <x-hero
    :ring-style="$page->dopplerRingStyle"
    :eyebrow="$page->section_eyebrow"
    :title="$page->hero_heading ?: $page->title"
    :copy="$page->hero_subheading"
  />

  <section class="section">
    <div class="container">
      <div class="faq-list">
        @foreach ($faqItems as $item)
        <details class="surface-card faq-item">
          <summary>{{ $item->question }}</summary>
          <div class="faq-answer">{!! $item->answer !!}</div>
        </details>
        @endforeach
      </div>
    </div>
  </section>

  <x-cta-band
    :eyebrow="$page->cta_eyebrow"
    :heading="$page->cta_heading"
    copy="Tell us what you're building - we'll tell you honestly whether AI is the right tool for it."
    :buttonLabel="$page->cta_button_label"
    buttonHref="/#contact"
  />
</x-layouts.site>
