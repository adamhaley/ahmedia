@props(['eyebrow' => null, 'heading', 'body' => null, 'bullets' => [], 'stacked' => false])
<section class="section">
  <div @class(['container', 'split-grid', 'split-grid-stacked' => $stacked])>
    <div>
      @if ($eyebrow)
      <p class="eyebrow">{{ $eyebrow }}</p>
      @endif
      <h2 class="section-title">{{ $heading }}</h2>
    </div>
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
