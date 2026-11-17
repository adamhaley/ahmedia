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
    <meta name="theme-color" content="#005e5e" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ $ogTitle }}</title>
    @if ($metaDescription)
    <meta name="description" content="{{ $metaDescription }}" />
    @endif
    <link rel="preload" href="/fonts/gantari-latin-wght-normal.woff2" as="font" type="font/woff2" crossorigin />
    <link rel="stylesheet" href="/styles.css?v={{ filemtime(public_path('styles.css')) }}" />
    <link rel="stylesheet" href="/doppler-rings.css?v={{ filemtime(public_path('doppler-rings.css')) }}" />
    @if ($settings->accent_color)
    <style>:root { --color-accent: {{ $settings->accent_color }}; }</style>
    @endif
  </head>
  <body>
    <a class="skip-link" href="#top">Skip to content</a>
    <div class="site-shell">
      <header class="site-header">
        <div class="container header-inner">
          <a class="site-brand" href="/" @if ($page?->template === \App\Enums\PageTemplate::Home) aria-current="page" @endif>
            {{-- The Doppler rings, frozen small as the logo mark --}}
            <svg class="doppler-rings is-frozen brand-mark" viewBox="0 0 50 60" aria-hidden="true" style="--rings: 4">
              @for ($i = 0; $i < 4; $i++)
              <circle r="1" style="--i: {{ $i }}; --phase: 0; --size: 0"/>
              @endfor
            </svg>
            AH Media
          </a>
          {{-- Mobile menu toggle (icons from monkmagazine.com); scripts.js shows it and wires it up --}}
          <button type="button" class="nav-toggle" aria-expanded="false" aria-controls="site-nav">
            <span class="sr-only">Menu</span>
            <svg class="nav-toggle-icon nav-toggle-open" viewBox="0 0 459 459" aria-hidden="true" focusable="false"><path d="M0 382.5h459v-51H0v51zM0 255h459v-51H0v51zM0 76.5v51h459v-51H0z"/></svg>
            <svg class="nav-toggle-icon nav-toggle-close" viewBox="0 0 28 28" aria-hidden="true" focusable="false"><path d="M27.123 25.002l-2.12 2.12-11.44-11.438-11.44 11.44L.003 25l11.438-11.44L.003 2.122l2.12-2.12 11.44 11.44L25.002 0l2.12 2.12-11.438 11.44 11.44 11.44z" fill-rule="evenodd"/></svg>
          </button>
          <nav class="site-nav" id="site-nav" aria-label="Primary">
            @foreach ($navPages as $navPage)
              @php($navHref = $navPage->nav_anchor ? ($page?->template === \App\Enums\PageTemplate::Home ? '#'.$navPage->nav_anchor : '/#'.$navPage->nav_anchor) : $navPage->url())
              @php($publishedChildren = $navPage->children->where('is_published', true))
              @php($isCurrent = $page && $navPage->is($page))
              @if ($publishedChildren->isNotEmpty())
              <div @class(['nav-item', 'has-dropdown', 'is-current-section' => $isCurrent || ($page && $page->parent_id === $navPage->id)])>
                <a href="{{ $navHref }}">{{ $navPage->title }} <svg class="nav-caret" viewBox="0 0 20 20" aria-hidden="true"><path d="M4 7H16L10 15Z"/></svg></a>
                {{-- Opens and closes this item's child pages: in the phone menu, and on tablets, which have no hover --}}
                <button type="button" class="nav-subtoggle" aria-expanded="true" aria-controls="nav-sub-{{ $loop->index }}">
                  <span class="sr-only">Open {{ str($navPage->title)->lower() }} menu</span>
                  <svg class="nav-caret" viewBox="0 0 20 20" aria-hidden="true" focusable="false"><path d="M4 7H16L10 15Z"/></svg>
                </button>
                <ul class="nav-dropdown" id="nav-sub-{{ $loop->index }}">@foreach ($publishedChildren as $child)<li><a href="{{ $child->url() }}" @if ($page && $child->is($page)) aria-current="page" @endif>{{ $child->title }}</a></li>@endforeach</ul>
              </div>
              @else
              <a href="{{ $navHref }}" @if ($isCurrent) aria-current="page" @endif>{{ $navPage->title }}</a>
              @endif
            @endforeach
          </nav>
        </div>
      </header>
      <main id="top" tabindex="-1">
        {{ $slot }}
      </main>
      <footer class="site-footer">
        <div class="container footer-grid">
          <div class="footer-id">
            <p class="footer-brand">AH Media</p>
            @if ($settings->footer_tagline)
            <p class="footer-copy">{{ $settings->footer_tagline }}</p>
            @endif
          </div>
          <nav class="footer-nav" aria-label="Footer">
            <p class="contact-kicker">Pages</p>
            <ul>
              <li><a href="/">Home</a></li>
              @foreach ($navPages as $navPage)
              <li><a href="{{ $navPage->url() }}" @if ($page && $navPage->is($page)) aria-current="page" @endif>{{ $navPage->title }}</a></li>
              @endforeach
            </ul>
          </nav>
          @foreach ($navPages as $navPage)
          @php($publishedChildren = $navPage->children->where('is_published', true))
          @if ($publishedChildren->isNotEmpty())
          <nav class="footer-nav" aria-label="{{ $navPage->title }}">
            <p class="contact-kicker">{{ $navPage->title }}</p>
            <ul>
              @foreach ($publishedChildren as $child)
              <li><a href="{{ $child->url() }}" @if ($page && $child->is($page)) aria-current="page" @endif>{{ $child->title }}</a></li>
              @endforeach
            </ul>
          </nav>
          @endif
          @endforeach
          <div class="footer-contact">
            <p class="contact-kicker">Contact</p>
            @if ($settings->contact_email)
            <p><a href="mailto:{{ $settings->contact_email }}">{{ $settings->contact_email }}</a></p>
            @endif
            @if ($settings->phone)
            <p><a href="tel:{{ preg_replace('/[^\d+]/', '', $settings->phone) }}">{{ $settings->phone }}</a></p>
            @endif
            @if ($settings->address)
            <address>{!! preg_replace('/, /', '<br />', e($settings->address), 1) !!}</address>
            @endif
            <div class="footer-social">
              @if ($settings->facebook_url)
              <a href="{{ $settings->facebook_url }}" aria-label="Facebook"><x-icon.facebook /></a>
              @endif
              @if ($settings->instagram_url)
              <a href="{{ $settings->instagram_url }}" aria-label="Instagram"><x-icon.instagram /></a>
              @endif
              @if ($settings->linkedin_url)
              <a href="{{ $settings->linkedin_url }}" aria-label="LinkedIn"><x-icon.linkedin /></a>
              @endif
              @if ($settings->contact_email)
              <a href="mailto:{{ $settings->contact_email }}" aria-label="Email {{ $settings->contact_email }}" title="{{ $settings->contact_email }}"><x-icon.email /></a>
              @endif
            </div>
          </div>
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
