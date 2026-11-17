@props(['eyebrow' => null, 'heading', 'body' => null, 'bullets' => [], 'actions' => [], 'steps' => []])
@php
  // Each step adds one ring, offset along the axis in proportion to its radius: the logo's
  // propagating geometry, never concentric.
  $stepRings = [[91.2, 6], [84.8, 14], [78.4, 22], [73.6, 28]];
@endphp
<section class="section" id="approach">
  <div class="container spotlight-grid ">
    @if (count($steps))
    <figure class="spotlight-media approach-diagram">
      <ol class="approach-steps" aria-label="How a project runs">
        @foreach ($steps as $step)
        <li class="approach-step">
          <svg class="approach-mark" viewBox="0 0 120 60" preserveAspectRatio="xMinYMid meet" aria-hidden="true" focusable="false">@foreach (array_slice($stepRings, 0, min($loop->iteration + 1, count($stepRings))) as [$cx, $radius])<circle cx="{{ $cx }}" cy="30" r="{{ $radius }}"/>@endforeach</svg>
          <span class="approach-step-index" aria-hidden="true">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
          <span class="approach-step-label">{{ $step }}</span>
        </li>
        @endforeach
      </ol>
    </figure>
    @endif
    <div class="spotlight-copy">
      @if ($eyebrow)
      <p class="eyebrow">{{ $eyebrow }}</p>
      @endif
      <h2 class="section-title">{{ $heading }}</h2>
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
