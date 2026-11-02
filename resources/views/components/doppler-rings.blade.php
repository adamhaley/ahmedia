@props(['settings' => \App\Data\DopplerRingSettings::defaults()])
{{--
  Doppler rings: the AH Media motif. Rings are born behind the title and travel along one
  horizon, growing and fading. Each ring's [phase, size] offset comes from the style's seed, so
  the irregularity looks hand-placed but renders the same every time. Styles are edited in the
  admin (Doppler rings); the defaults are the homepage logo arrangement. Anchored to the top
  (xMaxYMin), so the axis sits a fixed distance below the top of the hero whatever its height:
  centred, a longer title or subtitle pushed the rings lower.
--}}
<svg @class(['doppler-rings', 'is-scrubbed' => $settings->motion === \App\Enums\HeroRingsMotion::Still]) viewBox="0 0 1000 600" preserveAspectRatio="xMaxYMin slice" style="{{ $settings->cssVariables() }}" aria-hidden="true" focusable="false">
  @foreach ($settings->perRing() as [$phase, $size])
  <circle r="1" style="--i: {{ $loop->index }}; --phase: {{ $phase }}; --size: {{ $size }}" />
  @endforeach
</svg>
