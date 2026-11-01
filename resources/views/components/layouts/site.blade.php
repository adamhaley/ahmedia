@props(['page' => null, 'title' => null, 'description' => null])
@php
  $settings = \App\Models\SiteSetting::current();
  $navPages = \App\Models\Page::query()
      ->where('show_in_nav', true)
      ->where('is_published', true)
      ->whereNull('parent_id')
      ->with('children')
      ->orderBy('sort_order')
      ->get();
  $ogTitle = $page?->meta_title ?: ($page?->title ?? $title).' | AH Media.ai';
  $metaDescription = $page?->meta_description ?? $description;
@endphp
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ $ogTitle }}</title>
    @if ($metaDescription)
    <meta name="description" content="{{ $metaDescription }}" />
    @endif
    <link rel="stylesheet" href="/styles.css?v={{ filemtime(public_path('styles.css')) }}" />
    <link rel="stylesheet" href="/doppler-rings.css?v={{ filemtime(public_path('doppler-rings.css')) }}" />
    @if ($settings->accent_color)
    <style>:root { --color-accent: {{ $settings->accent_color }}; }</style>
    @endif
  </head>
  <body>
    <div class="site-shell">
      <header class="site-header">
        <div class="container header-inner">
          <a class="site-brand" href="/">
            {{-- The Doppler rings, frozen small as the logo mark --}}
            <svg class="doppler-rings is-frozen brand-mark" viewBox="0 0 50 60" aria-hidden="true" style="--rings: 4">
              @for ($i = 0; $i < 4; $i++)
              <circle r="1" style="--i: {{ $i }}; --phase: 0; --size: 0"/>
              @endfor
            </svg>
            AH Media
          </a>
          <nav class="site-nav" aria-label="Primary">
            @foreach ($navPages as $navPage)
              @php($navHref = $navPage->nav_anchor ? ($page?->template === \App\Enums\PageTemplate::Home ? '#'.$navPage->nav_anchor : '/#'.$navPage->nav_anchor) : $navPage->url())
              @php($publishedChildren = $navPage->children->where('is_published', true))
              @if ($publishedChildren->isNotEmpty())
              <div class="nav-item has-dropdown">
                <a href="{{ $navHref }}">{{ $navPage->title }} <svg class="nav-caret" viewBox="0 0 20 20" aria-hidden="true"><path d="M4 7H16L10 15Z"/></svg></a>
                <ul class="nav-dropdown">@foreach ($publishedChildren as $child)<li><a href="{{ $child->url() }}">{{ $child->title }}</a></li>@endforeach</ul>
              </div>
              @else
              <a href="{{ $navHref }}">{{ $navPage->title }}</a>
              @endif
            @endforeach
          </nav>
        </div>
      </header>
      <main id="top">
        {{ $slot }}
      </main>
      <footer class="site-footer">
        <div class="container footer-inner">
          <div>
            <p class="footer-brand">AH Media</p>
            @if ($settings->footer_tagline)
            <p class="footer-copy">{{ $settings->footer_tagline }}</p>
            @endif
          </div>
          <div class="footer-links"></div>
        </div>
      </footer>
      <x-chat-widget />
      <div class="back-to-top-link" data-threshold="200" data-position="left">
        <a href="#top" aria-label="Back to top">
          <svg viewBox="0 0 20 20" aria-hidden="true"><path d="M10 3L17.5 11H2.5L10 3Z"/></svg>
        </a>
      </div>
    </div>
    <script src="/scripts.js?v={{ filemtime(public_path('scripts.js')) }}"></script>
    <script src="/doppler-rings.js?v={{ filemtime(public_path('doppler-rings.js')) }}"></script>
  </body>
</html>
