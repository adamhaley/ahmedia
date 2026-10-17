@props(['testimonials'])
<section class="section" id="testimonials">
  <div class="container">
    <p class="eyebrow">Testimonials</p>
    <h2 class="section-title">What clients say</h2>
    <div class="testimonial-grid">
      @foreach ($testimonials as $testimonial)
      <figure class="surface-card testimonial-card">
        @if ($testimonial->rating)
        <div class="testimonial-stars" role="img" aria-label="Rated {{ $testimonial->rating }} out of 5">
          @for ($star = 1; $star <= 5; $star++)
          <svg @class(['testimonial-star', 'is-filled' => $star <= $testimonial->rating]) viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2.5l2.94 5.96 6.56.95-4.75 4.63 1.12 6.54L12 17.5l-5.87 3.08 1.12-6.54L2.5 9.41l6.56-.95z"/></svg>
          @endfor
        </div>
        @endif
        <blockquote class="testimonial-quote">“{!! nl2br(e($testimonial->body)) !!}”</blockquote>
        <figcaption class="testimonial-attribution">
          <span class="testimonial-name">{{ $testimonial->name }}@if ($testimonial->affiliation), {{ $testimonial->affiliation }}@endif</span>
          @if ($testimonial->project)
          <span class="testimonial-project">{{ $testimonial->project }}</span>
          @endif
        </figcaption>
      </figure>
      @endforeach
    </div>
  </div>
</section>
