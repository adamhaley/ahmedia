{{-- The homepage #contact section also takes its eyebrow/heading/intro
     from this page's fields. --}}
<x-layouts.site :page="$page">
  <x-hero
    :ring-style="$page->heroRingStyle()"
    :eyebrow="$page->section_eyebrow"
    :title="$page->hero_heading ?: $page->title"
    :copy="$page->hero_subheading"
  />
  <x-contact-form eyebrow="Send a message" heading="Tell us about your project" copy="" />

  @if ($address)
  <section class="section" id="location">
    <div class="container split-grid">
      <div>
        <p class="eyebrow">Visit</p>
        <h2 class="section-title">Find us in North Hollywood</h2>
        <address class="section-copy contact-address">{{ $address }}</address>
        <a class="preview-link" href="https://www.google.com/maps/search/?api=1&amp;query={{ urlencode($address) }}" rel="noopener" target="_blank">Get directions</a>
      </div>
      <figure class="split-body contact-map">
        <a href="https://www.openstreetmap.org/search?query={{ urlencode($address) }}" rel="noopener" target="_blank">
          <img src="/images/contact-map.jpg" width="1200" height="600" alt="Map of {{ $address }}" loading="lazy" />
        </a>
        <figcaption>Map data © <a href="https://www.openstreetmap.org/copyright" rel="noopener" target="_blank">OpenStreetMap</a> contributors</figcaption>
      </figure>
    </div>
  </section>
  @endif
</x-layouts.site>
