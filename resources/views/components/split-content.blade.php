@props(['eyebrow' => null, 'heading', 'body' => null, 'bullets' => []])
<section class="section">
  <div class="container split-grid">
    <div>
      @if ($eyebrow)
      <p class="eyebrow">{{ $eyebrow }}</p>
      @endif
      <h2 class="section-title">{{ $heading }}</h2>
    </div>
    <div class="split-body">
      @if ($body)
      <p class="section-copy">{{ $body }}</p>
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
