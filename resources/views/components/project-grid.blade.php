@props(['type', 'projects'])
@php
  $disk = \Illuminate\Support\Facades\Storage::disk('public');
@endphp
<section class="section" id="{{ str($type->getLabel())->slug() }}">
  <div class="container">
    <p class="eyebrow">Examples</p>
    <h2 class="section-title">{{ $type->getLabel() }}</h2>
    <div class="package-grid project-grid">
      @foreach ($projects as $project)
      @php
        $slides = collect($project->cover_image ? [['image' => $project->cover_image, 'alt' => $project->title]] : [])
            ->concat($project->images->map(fn ($image) => ['image' => $image->image, 'alt' => $image->alt_text ?: $project->title]));
      @endphp
      <article class="surface-card package-card project-card" id="{{ $project->slug }}">
        @if ($slides->isNotEmpty())
        {{-- Swipe or scroll sideways through the cover and gallery; each image opens full size --}}
        @if ($slides->count() > 1)
        <div class="project-carousel" data-carousel role="region" aria-roledescription="carousel" aria-label="{{ $project->title }} images">
          <div class="project-carousel-stage">
            <div class="project-slides" data-carousel-track tabindex="0" aria-label="{{ $project->title }} images, scroll sideways for more">
              @foreach ($slides as $slide)
              <a class="project-slide" href="{{ $disk->url($slide['image']) }}" target="_blank">
                <img src="{{ $disk->url($slide['image']) }}" alt="{{ $slide['alt'] }}" loading="lazy" />
              </a>
              @endforeach
            </div>
            <button type="button" class="project-carousel-button project-carousel-prev" data-carousel-prev aria-label="Previous image" disabled>&#8249;</button>
            <button type="button" class="project-carousel-button project-carousel-next" data-carousel-next aria-label="Next image">&#8250;</button>
          </div>
          <div class="project-carousel-dots">
            @foreach ($slides as $slide)
            <button type="button" class="project-carousel-dot" data-carousel-dot aria-label="Show image {{ $loop->iteration }} of {{ $slides->count() }}"{!! $loop->first ? ' aria-current="true"' : '' !!}></button>
            @endforeach
          </div>
        </div>
        @else
        <div class="project-slides">
          @foreach ($slides as $slide)
          <a class="project-slide" href="{{ $disk->url($slide['image']) }}" target="_blank">
            <img src="{{ $disk->url($slide['image']) }}" alt="{{ $slide['alt'] }}" loading="lazy" />
          </a>
          @endforeach
        </div>
        @endif
        @endif
        <header class="package-header">
          <h3>{{ $project->title }}</h3>
        </header>
        @if ($project->description)
        <x-markdown class="package-summary" :text="$project->description" />
        @endif
        @if (count($project->tags ?? []))
        <ul class="case-study-tags feature-tags">
          @foreach ($project->tags as $tag)
          <li>{{ $tag }}</li>
          @endforeach
        </ul>
        @endif
        @if ($project->download_file || count($project->links ?? []))
        <div class="button-row package-actions">
          @if ($project->download_file)
          <a class="button button-primary" href="{{ $disk->url($project->download_file) }}" download>Download free</a>
          @endif
          @foreach ($project->links ?? [] as $link)
          <a class="button {{ $loop->first && ! $project->download_file ? 'button-primary' : 'button-secondary' }}" href="{{ $link['url'] }}" rel="noopener" target="_blank">{{ $link['label'] }}</a>
          @endforeach
        </div>
        @endif
        @if ($project->caseStudy?->is_published)
        <a class="preview-link" href="{{ $project->caseStudy->url() }}">Read the case study</a>
        @endif
      </article>
      @endforeach
    </div>
  </div>
</section>
