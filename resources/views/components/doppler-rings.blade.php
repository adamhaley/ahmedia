@props(['settings' => \App\Data\DopplerRingSettings::defaults()])
{{--
  Doppler rings: the AH Media motif. Rings are born behind the title and travel along one
  horizon, growing and fading. Each ring's [phase, size] offset comes from the style's seed, so
  the irregularity looks hand-placed but renders the same every time. Styles are edited in the
  admin (Doppler rings); the defaults are the homepage logo arrangement.
--}}
<svg class="doppler-rings" viewBox="0 0 1000 600" preserveAspectRatio="xMaxYMid slice" style="{{ $settings->cssVariables() }}" aria-hidden="true" focusable="false">
  @foreach ($settings->perRing() as [$phase, $size])
  <circle r="1" style="--i: {{ $loop->index }}; --phase: {{ $phase }}; --size: {{ $size }}" />
  @endforeach
</svg>
