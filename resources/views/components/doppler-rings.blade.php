{{--
  Doppler rings: the AH Media motif. Rings are born behind the title and travel left along one
  horizon, growing and fading. Each ring's [phase, size] offset is fixed (seed 7 in the tuner),
  so the irregularity looks hand-placed but renders the same every time. Retune with the lab on
  the Logo story page (components/doppler-lab) and paste its "Copy settings" perRing values here.
--}}
@php
  $rings = [
      [-0.488, -0.482],
      [0.199, 0.024],
      [-0.034, -0.286],
      [0.23, -0.266],
      [0.264, 0.02],
      [-0.132, -0.227],
      [-0.204, 0.526],
      [0.378, -0.34],
  ];
@endphp
<svg class="doppler-rings" viewBox="0 0 1000 600" preserveAspectRatio="xMaxYMid slice" style="--rings: {{ count($rings) }}" aria-hidden="true" focusable="false">
  @foreach ($rings as [$phase, $size])
  <circle r="1" style="--i: {{ $loop->index }}; --phase: {{ $phase }}; --size: {{ $size }}" />
  @endforeach
</svg>
