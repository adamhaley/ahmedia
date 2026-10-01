@props(['eyebrow' => null, 'title', 'accent' => null, 'copy' => null, 'actions' => []])
<section class="section hero-section">
  <div class="container hero-grid hero-grid-solo hero-align-right">
    <div class="hero-copy">
      @if ($eyebrow)
      <p class="eyebrow">{{ $eyebrow }}</p>
      @endif
      <h1 class="display-title">{{ $title }}@if ($accent)<span class="text-accent">{{ $accent }}</span>@endif</h1>
      @if ($copy)
      <p class="lede">{{ $copy }}</p>
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
