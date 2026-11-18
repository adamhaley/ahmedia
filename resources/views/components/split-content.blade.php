@props(['eyebrow' => null, 'heading' => null, 'body' => null, 'bullets' => [], 'stacked' => false, 'image' => null, 'imageAlt' => '', 'card' => null])
<section class="section">
@php
  $card = filled($card['title'] ?? null) ? $card : null;
@endphp
  <div @class(['container', 'split-grid', 'split-grid-stacked' => $stacked, 'split-grid-image' => $image && ! $card, 'split-grid-aside' => $card])>
    @if ($eyebrow || $heading)
    <div>
      @if ($eyebrow)
      <p class="eyebrow">{{ $eyebrow }}</p>
      @endif
      @if ($heading)
      <h2 class="section-title">{{ $heading }}</h2>
      @endif
    </div>
    @endif
    <div class="split-body">
      @if ($body)
      <x-markdown class="section-copy" :text="$body" />
      @endif
      @if (count($bullets ?? []))
      <ul class="bullet-list">
        @foreach ($bullets as $bullet)
        <li>{{ $bullet }}</li>
        @endforeach
      </ul>
      @endif
    </div>
    @if ($image || $card)
    <aside @class(['split-image' => ! $card, 'split-aside' => $card, 'split-image-headless' => ! ($eyebrow || $heading)])>
      @if ($image)
      <img src="{{ $image }}" alt="{{ $imageAlt }}" loading="lazy">
      @endif
      @if ($card)
      <div class="surface-card demo-card">
        <p class="demo-card-title">{{ $card['title'] }}</p>
        @if (filled($card['link_label'] ?? null) && filled($card['link_url'] ?? null))
        <a class="demo-card-number" href="{{ $card['link_url'] }}">{{ $card['link_label'] }}</a>
        @endif
        @if (filled($card['note'] ?? null))
        <p class="demo-card-note">{{ $card['note'] }}</p>
        @endif
      </div>
      @endif
    </aside>
    @endif
  </div>
</section>
