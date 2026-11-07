<x-layouts.site :title="$post->title" :description="$post->meta_description ?: $post->excerpt">
  <x-hero
    :eyebrow="$post->published_at?->format('F j, Y') ?? 'Draft'"
    :title="$post->title"
    :copy="$post->excerpt"
  />

  <section class="section">
    <div class="container">
      @if ($post->cover_image)
      <img class="case-study-hero-image post-cover" src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($post->cover_image) }}" alt="" />
      @endif
      @if ($post->body)
      <x-markdown class="case-study-prose" :text="$post->body" />
      @endif
      <p><a class="preview-link" href="/blog">All posts</a></p>
    </div>
  </section>

  <x-cta-band
    heading="Let's talk about your project"
    copy="Tell us what you're building - we'll tell you honestly whether AI is the right tool for it."
    buttonLabel="Start a Project"
    buttonHref="/#contact"
  />
</x-layouts.site>
