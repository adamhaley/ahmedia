@props(['settings' => \App\Data\DopplerRingSettings::fromArray(['motion' => 'auto']), 'editor' => false])
@php
  $ranges = collect(\App\Data\DopplerRingSettings::Ranges)->groupBy('group', preserveKeys: true);
  // The admin has no site stylesheet, so the editor's buttons are styled in doppler-rings.css
  $button = fn (string $variant): string => $editor ? "doppler-lab-button doppler-lab-button-{$variant}" : "button button-{$variant}";
  $slider = fn (string $name, array $range, int|float $value): string => sprintf(
      '<label>%s <output data-for="%s"></output><input type="range" name="%2$s" min="%s" max="%s" step="%s" value="%s"></label>',
      e($range['label']), $name, $range['min'], $range['max'], $range['step'], $value,
  );
@endphp
{{--
  Interactive Doppler rings, driven by public/doppler-rings.js. On the Logo story page it's a
  standalone toy that saves nothing; with `editor` it's the admin's rings style editor, and every
  change is sent up to the form (data-doppler-editor).
--}}
<section @class(['doppler-lab', 'section' => ! $editor]) data-doppler-lab @if ($editor) data-doppler-editor @endif>
  <div @class(['container' => ! $editor])>
    @unless ($editor)
    <p class="eyebrow">Try it</p>
    <h1 class="section-title">Make your own rings</h1>
    @endunless

    <div class="doppler-lab-stage">
      <x-doppler-rings :settings="$settings" />
    </div>

    <div class="doppler-lab-controls">
      @foreach (['Geometry', 'Randomness', 'Style', 'Axis'] as $group)
      <fieldset>
        <legend>{{ $group }}</legend>
        @foreach ($ranges[$group] as $name => $range)
        {!! $slider($name, $range, $settings->{$name}) !!}
        @endforeach
        @if ($group === 'Randomness' && ! $editor)
        {!! $slider('tempo', ['label' => 'Tempo jitter (no repeats)', 'min' => 0, 'max' => 0.3, 'step' => 0.01], 0) !!}
        @endif
        @if ($group === 'Style')
        <label class="doppler-lab-check"><input type="checkbox" name="frozen"> Freeze</label>
        <label class="doppler-lab-check"><input type="checkbox" name="travelLeft" @checked($settings->travelLeft)> Travel left</label>
        @endif
      </fieldset>
      @endforeach

      <fieldset>
        <legend>Motion</legend>
        @foreach (\App\Enums\HeroRingsMotion::cases() as $motion)
        <label class="doppler-lab-check"><input type="radio" name="motion" value="{{ $motion->value }}" @checked($settings->motion === $motion)> {{ $motion->getLabel() }}</label>
        @endforeach
        <div class="doppler-lab-scroll" data-scroll-only hidden>
          {!! $slider('preview', ['label' => 'Scroll preview', 'min' => 0, 'max' => 1, 'step' => 0.005], 0) !!}
          @foreach ($ranges['Motion'] as $name => $range)
          {!! $slider($name, $range, $settings->{$name}) !!}
          @endforeach
        </div>
      </fieldset>
    </div>

    <div class="button-row doppler-lab-actions">
      <button type="button" class="{{ $button('primary') }}" data-doppler-action="reshuffle">Reshuffle</button>
      <button type="button" class="{{ $button('secondary') }}" data-doppler-action="reset">{{ $editor ? 'Back to saved' : 'Back to the logo' }}</button>
      @unless ($editor)
      <button type="button" class="{{ $button('secondary') }}" data-doppler-action="copy">Copy settings</button>
      @endunless
    </div>
  </div>
</section>
