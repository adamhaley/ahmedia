@props(['eyebrow' => 'Fixed-price packages', 'heading' => 'Packages', 'packages'])
<section class="section" id="packages">
  <div class="container">
    <p class="eyebrow">{{ $eyebrow }}</p>
    <h2 class="section-title">{{ $heading }}</h2>
    <div class="package-grid">
      @foreach ($packages as $package)
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
          <a class="button button-primary" href="/#contact">Start a Project</a>
          @if ($package->upwork_url)
          <a class="button button-secondary" href="{{ $package->upwork_url }}" rel="noopener" target="_blank">Order on Upwork</a>
          @endif
        </div>
        @if ($package->sample_document)
        <a class="preview-link package-sample" href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($package->sample_document) }}" target="_blank">Download a sample report (PDF)</a>
        @endif
      </article>
      @endforeach
    </div>
  </div>
</section>
