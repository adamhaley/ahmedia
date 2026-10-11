@php($disk = \Illuminate\Support\Facades\Storage::disk('public'))
<x-layouts.site :title="$caseStudy->title" :description="$caseStudy->summary">
  <x-hero
    eyebrow="Case Study"
    :title="$caseStudy->title"
    :copy="$caseStudy->summary"
  />

  <section class="section">
    <div class="container case-study-layout">
      <aside class="case-study-facts">
        @if ($caseStudy->client_name)
        <p class="eyebrow">Client</p>
        <p class="case-study-fact">{{ $caseStudy->client_name }}</p>
        @endif
        @if (count($caseStudy->tags ?? []))
        <p class="eyebrow">Focus</p>
        <ul class="case-study-tags">
          @foreach ($caseStudy->tags as $tag)
          <li>{{ $tag }}</li>
          @endforeach
        </ul>
        @endif
        @if ($caseStudy->external_link)
        <a class="preview-link" href="{{ $caseStudy->external_link }}" rel="noopener" target="_blank">Visit the site</a>
        @endif
      </aside>
      <div class="case-study-body">
        @if ($caseStudy->hero_image)
        <img class="case-study-hero-image" src="{{ $disk->url($caseStudy->hero_image) }}" alt="{{ $caseStudy->title }}" />
        @endif
        @if ($caseStudy->narrative)
        <div class="case-study-prose">
          {!! \Illuminate\Support\Str::markdown($caseStudy->narrative, ['html_input' => 'escape', 'allow_unsafe_links' => false]) !!}
        </div>
        @endif
      </div>
    </div>
  </section>

  @if ($caseStudy->images->isNotEmpty())
  <section class="section">
    <div class="container">
      <p class="eyebrow">The work</p>
      <div class="case-study-gallery">
        @foreach ($caseStudy->images as $image)
        <figure class="case-study-figure">
          <a href="{{ $disk->url($image->image) }}" target="_blank"><img src="{{ $disk->url($image->image) }}" alt="{{ $image->alt_text }}" loading="lazy" /></a>
          @if ($image->alt_text)
          <figcaption>{{ $image->alt_text }}</figcaption>
          @endif
        </figure>
        @endforeach
      </div>
    </div>
  </section>
  @endif

  @if ($caseStudy->testimonial_quote)
  <section class="section">
    <div class="container">
      <blockquote class="surface-card case-study-quote">
        <p>“{{ $caseStudy->testimonial_quote }}”</p>
        @if ($caseStudy->testimonial_author)
        <footer>{{ $caseStudy->testimonial_author }}@if ($caseStudy->testimonial_author_role), {{ $caseStudy->testimonial_author_role }}@endif</footer>
        @endif
      </blockquote>
    </div>
  </section>
  @endif

  @if ($caseStudy->package?->is_published)
  <x-cta-band
    eyebrow="Want this for your site?"
    :heading="$caseStudy->package->title"
    :copy="$caseStudy->package->summary"
    buttonLabel="See the package"
    :buttonHref="$caseStudy->package->url()"
  />
  @else
  <x-cta-band
    eyebrow="Start a project"
    heading="Have something like this in mind?"
    copy="Tell us what you're trying to build — we'll tell you honestly whether AI is the right tool for it."
  />
  @endif
</x-layouts.site>
