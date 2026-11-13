@props(['id' => 'services', 'eyebrow' => null, 'title', 'copy' => null, 'items' => [], 'actions' => [], 'linkCards' => false])
<section class="section" id="{{ $id }}">
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
      @php($linked = $linkCards && ! empty($item['href']))
      <article @class(['surface-card', 'feature-card', 'feature-card-linked' => $linked])>
        <p class="feature-index">{{ $item['label'] ?? str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</p>
        <h3>{{ $item['title'] }}</h3>
        <p>{{ $item['copy'] }}</p>
        @if (count($item['tags'] ?? []))
        <ul class="case-study-tags feature-tags">
          @foreach ($item['tags'] as $tag)
          <li>{{ $tag }}</li>
          @endforeach
        </ul>
        @endif
        @if (! empty($item['href']))
        <a @class(['preview-link', 'feature-card-link' => $linked]) href="{{ $item['href'] }}">{{ $item['linkLabel'] ?? 'Learn more' }}</a>
        @endif
      </article>
      @endforeach
    </div>
  </div>
</section>
