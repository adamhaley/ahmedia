@props(['eyebrow' => null, 'heading', 'body' => null, 'bullets' => [], 'actions' => []])
<section class="section" id="approach">
  <div class="container spotlight-grid ">
    <figure class="spotlight-media media-placeholder"></figure>
    <div class="spotlight-copy">
      @if ($eyebrow)
      <p class="eyebrow">{{ $eyebrow }}</p>
      @endif
      <h2 class="section-title">{{ $heading }}</h2>
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
      @if (count($actions))
      <div class="button-row">
        @foreach ($actions as $action)
        <a class="button button-{{ $action['variant'] ?? 'secondary' }}" href="{{ $action['href'] }}">{{ $action['label'] }}</a>
        @endforeach
      </div>
      @endif
    </div>
  </div>
</section>
