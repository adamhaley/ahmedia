@props(['page'])
@php
  $settings = \App\Models\SiteSetting::current();
  $navPages = \App\Models\Page::query()
      ->where('show_in_nav', true)
      ->where('is_published', true)
      ->whereNull('parent_id')
      ->with('children')
      ->orderBy('sort_order')
      ->get();
  $ogTitle = $page->meta_title ?: $page->title.' | AH Media.ai';
@endphp
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ $ogTitle }}</title>
    @if ($page->meta_description)
    <meta name="description" content="{{ $page->meta_description }}" />
    @endif
    <link rel="stylesheet" href="/styles.css?v={{ filemtime(public_path('styles.css')) }}" />
    @if ($settings->accent_color)
    <style>:root { --color-accent: {{ $settings->accent_color }}; }</style>
    @endif
  </head>
  <body>
    <div class="site-shell">
      <header class="site-header">
        <div class="container header-inner">
          <a class="site-brand" href="/">AH Media</a>
          <nav class="site-nav" aria-label="Primary">
            @foreach ($navPages as $navPage)
              @php($navHref = $navPage->nav_anchor ? ($page->template === \App\Enums\PageTemplate::Home ? '#'.$navPage->nav_anchor : '/#'.$navPage->nav_anchor) : $navPage->url())
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
            <p class="footer-copy">AI systems and automation for teams that want to ship, not just plan.</p>
          </div>
          <div class="footer-links"></div>
        </div>
      </footer>
      <x-chat-widget />
      <div class="back-to-top-link" data-threshold="200" data-position="left">
        <a href="#top" aria-label="Back to top">
          <svg viewBox="0 0 20 20" aria-hidden="true"><path d="M7.5 3L15 11H0L7.5 3Z"/></svg>
        </a>
      </div>
    </div>
    <script src="/scripts.js?v={{ filemtime(public_path('scripts.js')) }}"></script>
  </body>
</html>
