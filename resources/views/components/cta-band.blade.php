@props(['eyebrow' => null, 'heading', 'copy' => null, 'buttonLabel' => null, 'buttonHref' => '/#contact', 'buttonVariant' => 'primary', 'id' => null])
@if ($heading)
<section class="section" @if ($id) id="{{ $id }}" @endif>
  <div class="container">
    <div class="surface-card cta-band">
      <div>
        @if ($eyebrow)
        <p class="eyebrow">{{ $eyebrow }}</p>
        @endif
        <h2 class="section-title">{{ $heading }}</h2>
        @if ($copy)
        <p class="section-copy">{{ $copy }}</p>
        @endif
      </div>
      <div class="button-row">
        <a class="button button-{{ $buttonVariant }}" href="{{ $buttonHref }}">{{ $buttonLabel ?: 'Start a Project' }}</a>
      </div>
    </div>
  </div>
</section>
@endif
