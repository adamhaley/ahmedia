@props(['eyebrow' => null, 'heading' => null, 'body' => null, 'bullets' => [], 'stacked' => false])
<section class="section">
  <div @class(['container', 'split-grid', 'split-grid-stacked' => $stacked])>
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
  </div>
</section>
