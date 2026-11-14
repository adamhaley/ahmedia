@props(['credentials', 'eyebrow' => 'Credentials', 'heading' => 'Certifications'])
@php($disk = \Illuminate\Support\Facades\Storage::disk('public'))
<section class="section" id="credentials">
  <div class="container">
    <p class="eyebrow">{{ $eyebrow }}</p>
    <h2 class="section-title">{{ $heading }}</h2>
    <div class="credential-grid">
      @foreach ($credentials as $credential)
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
