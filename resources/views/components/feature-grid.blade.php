@props(['eyebrow' => null, 'title', 'copy' => null, 'items' => [], 'actions' => []])
<section class="section" id="services">
  <div class="container">
    @if ($eyebrow)
    <p class="eyebrow">{{ $eyebrow }}</p>
    @endif
    <div class="section-heading">
      <div>
        <h2 class="section-title">{{ $title }}</h2>
        @if ($copy)
        <p class="section-copy">{{ $copy }}</p>
        @endif
      </div>
      @if (count($actions))
      <div class="button-row">
        @foreach ($actions as $action)
        <a class="button button-{{ $action['variant'] ?? 'secondary' }}" href="{{ $action['href'] }}">{{ $action['label'] }}</a>
        @endforeach
      </div>
      @endif
    </div>
    <div class="feature-grid">
      @foreach ($items as $index => $item)
      <article class="surface-card feature-card">
        <p class="feature-index">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</p>
        <h3>{{ $item['title'] }}</h3>
        <p>{{ $item['copy'] }}</p>
        @if (! empty($item['href']))
        <a class="preview-link" href="{{ $item['href'] }}">{{ $item['linkLabel'] ?? 'Learn more' }}</a>
        @endif
      </article>
      @endforeach
    </div>
  </div>
</section>
