@php($disk = \Illuminate\Support\Facades\Storage::disk('public'))
@php([$stats, $certifications] = $credentials->partition(fn ($credential) => $credential->is_featured))
<x-layouts.site :page="$page">
  <x-hero
    :ring-style="$page->heroRingStyle()"
    :eyebrow="$page->section_eyebrow"
    :title="$page->hero_heading ?: $page->title"
    :copy="$page->hero_subheading"
  />

  @if ($stats->isNotEmpty())
  <section class="section" id="track-record">
    <div class="container">
      <p class="eyebrow">Track record</p>
      <div class="stat-grid">
        @foreach ($stats as $stat)
        <figure class="surface-card stat-tile">
          {{-- The frozen rings mark from the header, scaled up --}}
          <svg class="doppler-rings is-frozen stat-mark" viewBox="0 0 50 60" aria-hidden="true" style="--rings: 4">
            @for ($i = 0; $i < 4; $i++)
            <circle r="1" style="--i: {{ $i }}; --phase: 0; --size: 0"/>
            @endfor
          </svg>
          <p @class(['stat-value', 'is-long' => mb_strlen($stat->value ?: $stat->name) > 5])>{{ $stat->value ?: $stat->name }}</p>
          <figcaption class="stat-label">
            @if ($stat->value){{ $stat->name }}@endif
            @if ($stat->url)
            <a class="stat-source" href="{{ $stat->url }}" rel="noopener" target="_blank">{{ $stat->issuer }}</a>
            @elseif ($stat->issuer)
            <span class="stat-source">{{ $stat->issuer }}</span>
            @endif
          </figcaption>
        </figure>
        @endforeach
      </div>
    </div>
  </section>
  @endif

  @if ($page->section_heading || $page->body)
  <section class="section">
    <div class="container split-grid">
      <div>
        @if ($page->circle_image)
        <figure class="spotlight-media-circle about-portrait">
          <img src="{{ $disk->url($page->circle_image) }}" alt="{{ $page->circle_image_alt }}" />
        </figure>
        @endif
        @if ($page->section_heading)
        <h2 class="section-title">{{ $page->section_heading }}</h2>
        @endif
      </div>
      <div class="split-body">
        @if ($page->body)
        <x-markdown class="section-copy" :text="$page->body" />
        @endif
        @if (count($page->bullets ?? []))
        <ul class="bullet-list">
          @foreach ($page->bullets as $bullet)
          <li>{{ $bullet }}</li>
          @endforeach
        </ul>
        @endif
      </div>
    </div>
  </section>
  @endif

  @if ($certifications->isNotEmpty())
  <section class="section" id="credentials">
    <div class="container">
      <p class="eyebrow">Credentials</p>
      <h2 class="section-title">Certifications</h2>
      <div class="credential-grid">
        @foreach ($certifications as $credential)
        <article class="surface-card credential-card">
          @if ($credential->badge_image)
          <img class="credential-badge" src="{{ $disk->url($credential->badge_image) }}" alt="" loading="lazy" />
          @endif
          <div>
            <h3 class="credential-name">{{ $credential->name }}</h3>
            <p class="credential-meta">{{ $credential->issuer }}@if ($credential->issued_on) · {{ $credential->issued_on->format('M Y') }}@endif</p>
            @if ($credential->url)
            <a class="preview-link" href="{{ $credential->url }}" rel="noopener" target="_blank">Verify</a>
            @endif
          </div>
        </article>
        @endforeach
      </div>
    </div>
  </section>
  @endif

  <x-cta-band
    :eyebrow="$page->cta_eyebrow"
    :heading="$page->cta_heading"
    copy="Tell us what you're building - we'll tell you honestly whether AI is the right tool for it."
    :buttonLabel="$page->cta_button_label"
    buttonHref="/#contact"
  />
</x-layouts.site>
