<x-layouts.site :page="$page">
  <x-hero
    :ring-style="$page->heroRingStyle()"
    :eyebrow="$page->section_eyebrow"
    :title="$page->hero_heading ?: $page->title"
    :copy="$page->hero_subheading"
  />

  @if ($posts->isNotEmpty())
  <x-feature-grid
    id="posts"
    :title="$page->section_heading ?: 'Latest posts'"
    :copy="$page->body"
    :items="$posts->map(fn ($post) => [
        'label' => $post->published_at->format('M j, Y'),
        'title' => $post->title,
        'copy' => $post->excerpt,
        'href' => $post->url(),
        'linkLabel' => 'Read the post',
    ])->all()"
  />
  @else
  <section class="section">
    <div class="container">
      <p class="section-copy">The first posts are on their way.</p>
    </div>
  </section>
  @endif

  <x-cta-band
    :eyebrow="$page->cta_eyebrow"
    :heading="$page->cta_heading"
    copy="Tell us what you're building - we'll tell you honestly whether AI is the right tool for it."
    :buttonLabel="$page->cta_button_label"
    buttonHref="/#contact"
  />
</x-layouts.site>
