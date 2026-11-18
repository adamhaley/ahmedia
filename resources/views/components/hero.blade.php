@props(['eyebrow' => null, 'title', 'accent' => null, 'tagline' => null, 'copy' => null, 'stance' => null, 'actions' => null, 'points' => [], 'ringStyle' => null, 'compact' => false, 'back' => true])
@php
  // Every hero offers "Start a Project" unless a page passes its own actions ([] for none)
  $actions ??= [['label' => 'Start a Project', 'href' => '/#contact', 'variant' => 'primary']];
  $rings = $ringStyle?->settings;
  $pin = $rings?->motion === \App\Enums\HeroRingsMotion::Scroll;
@endphp
@if ($pin)
<div class="hero-pin" data-hero-pin data-start="{{ $rings->scrollStart }}" data-end="{{ $rings->scrollEnd }}" style="--pin-length: {{ $rings->pinLength }}svh">
@endif
<section @class(['section', 'hero-section', 'hero-has-rings' => $rings, 'hero-compact' => $compact])>
  @if ($rings)
  <div class="hero-rings">
    <div class="container hero-rings-frame">
      <x-doppler-rings :settings="$rings" />
    </div>
  </div>
  @endif
  <div class="container hero-grid hero-grid-solo hero-align-right">
    <div class="hero-copy">
      @if ($eyebrow)
      <p class="eyebrow">{{ $eyebrow }}</p>
      @endif
      <h1 class="display-title">{{ $title }}@if ($accent)<span class="text-accent">{{ $accent }}</span>@endif</h1>
      @if ($tagline)
      <p class="hero-tagline">{{ $tagline }}</p>
      @endif
      @if ($copy)
      <p class="lede">{{ $copy }}</p>
      @endif
      @if ($stance)
      <p class="hero-stance">{{ $stance }}</p>
      @endif
      @if ($back || count($actions))
      <div @class(['button-row', 'button-row-end' => ! count($actions)])>
        @if ($back)
        <a class="hero-back" href="{{ route('page.home') }}" aria-label="Back to home">
          <svg viewBox="0 0 20 20" focusable="false" aria-hidden="true"><path d="M17 10H5M9 5l-5 5 5 5"/></svg>
        </a>
        @endif
        @foreach ($actions as $action)
        <a class="button button-{{ $action['variant'] ?? 'secondary' }}" href="{{ $action['href'] }}">{{ $action['label'] }}</a>
        @endforeach
      </div>
      @endif
      @if (count($points ?? []))
      <ul class="proof-line" aria-label="Why work with us">
        @foreach ($points as $point)
        <li>{{ str($point)->inlineMarkdown(['html_input' => 'escape', 'allow_unsafe_links' => false])->toHtmlString() }}</li>
        @endforeach
      </ul>
      @endif
    </div>
  </div>
</section>
@if ($pin)
</div>
@endif
