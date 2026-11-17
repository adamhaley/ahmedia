{{-- Services as a numbered list of full-width rows. The service name is the row's link
     (stretched over the whole row by styles.css), so each link's text is the service name. --}}
@props(['eyebrow' => null, 'title', 'copy' => null, 'services', 'link' => null])
<section class="section section-major" id="services">
  <div class="container">
    @if ($eyebrow)
    <p class="eyebrow">{{ $eyebrow }}</p>
    @endif
    <div @class(['section-heading', 'section-heading-split' => $link])>
      <div>
        <h2 class="section-title">{{ $title }}</h2>
        @if ($copy)
        <p class="section-copy">{{ $copy }}</p>
        @endif
      </div>
      @if ($link)
      <a class="section-heading-link" href="{{ $link['href'] }}">{{ $link['label'] }}<svg viewBox="0 0 20 20" aria-hidden="true" focusable="false"><path d="M3 10h12M11 5l5 5-5 5"/></svg></a>
      @endif
    </div>
    <ol class="service-rows">
      @foreach ($services as $service)
      <li class="service-row">
        <span class="service-row-index" aria-hidden="true">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
        <h3 class="service-row-name"><a href="{{ $service->url() }}">{{ $service->title }}</a></h3>
        @if ($service->card_excerpt)
        <p class="service-row-line">{{ $service->card_excerpt }}</p>
        @endif
        <span class="service-row-arrow" aria-hidden="true"><svg viewBox="0 0 20 20" focusable="false"><path d="M3 10h12M11 5l5 5-5 5"/></svg></span>
      </li>
      @endforeach
    </ol>
  </div>
</section>
