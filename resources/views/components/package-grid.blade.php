@props(['eyebrow' => 'Fixed-price packages', 'heading' => 'Packages', 'packages'])
<section class="section section-major" id="packages">
  <div class="container">
    <p class="eyebrow">{{ $eyebrow }}</p>
    <h2 class="section-title">{{ $heading }}</h2>
    {{-- Fixed-scope offers sold on Upwork stand apart from the ladder of build packages --}}
    @foreach (['package-grid package-ladder' => $packages->whereNull('upwork_url'), 'package-standalone' => $packages->whereNotNull('upwork_url')] as $groupClass => $group)
    @if ($group->isNotEmpty())
    <div class="{{ $groupClass }}">
      @foreach ($group as $package)
      <article class="surface-card package-card" id="{{ $package->slug }}">
        <header class="package-header">
          <h3>{{ $package->title }}</h3>
          <p class="package-price">${{ number_format($package->price) }}</p>
          @if ($package->delivery_days)
          <p class="package-meta">Delivered in {{ $package->delivery_days }} {{ str('day')->plural($package->delivery_days) }}</p>
          @endif
        </header>
        @if ($package->summary)
        <p class="package-summary">{{ $package->summary }}</p>
        @endif
        @if (count($package->includes ?? []))
        <ul class="bullet-list package-includes">
          @foreach ($package->includes as $item)
          <li>{{ $item }}</li>
          @endforeach
        </ul>
        @endif
        @if (count($package->add_ons ?? []))
        <div class="package-add-ons">
          <p class="eyebrow">Add-ons</p>
          <ul>
            @foreach ($package->add_ons as $addOn)
            <li>
              <span class="package-add-on-title">{{ $addOn['title'] }}</span>
              <span class="package-add-on-price">+${{ number_format((int) $addOn['price']) }}@if (! empty($addOn['days'])) · +{{ $addOn['days'] }} {{ str('day')->plural((int) $addOn['days']) }}@endif</span>
              @if (! empty($addOn['description']))
              <span class="package-add-on-description">{{ $addOn['description'] }}</span>
              @endif
            </li>
            @endforeach
          </ul>
        </div>
        @endif
        <div class="button-row package-actions">
          @if ($package->upwork_url)
          <a class="button button-primary" href="{{ $package->upwork_url }}" rel="noopener" target="_blank">Order on Upwork</a>
          @else
          <a class="button button-primary" href="/#contact">Start a Project</a>
          @endif
          @if ($package->sample_document)
          <a class="button button-secondary package-sample" href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($package->sample_document) }}" target="_blank">Download a sample report (PDF)</a>
          @endif
        </div>
      </article>
      @endforeach
    </div>
    @endif
    @endforeach
  </div>
</section>
