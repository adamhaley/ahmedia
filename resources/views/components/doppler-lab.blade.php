{{-- Interactive Doppler rings: starts on the live logo settings, driven by public/scripts.js --}}
<section class="section doppler-lab" data-doppler-lab>
  <div class="container">
    <p class="eyebrow">Try it</p>
    <h2 class="section-title">Make your own rings</h2>

    <div class="doppler-lab-stage">
      <x-doppler-rings />
    </div>

    <div class="doppler-lab-controls">
      <fieldset>
        <legend>Geometry</legend>
        <label>Mach (offset ratio) <output data-for="mach"></output>
          <input type="range" name="mach" min="0" max="3" step="0.05" value="2.8"></label>
        <label>Rings <output data-for="rings"></output>
          <input type="range" name="rings" min="2" max="14" step="1" value="8"></label>
        <label>Cycle (seconds) <output data-for="period"></output>
          <input type="range" name="period" min="3" max="40" step="1" value="39"></label>
        <label>Reach <output data-for="reach"></output>
          <input type="range" name="reach" min="100" max="900" step="10" value="300"></label>
      </fieldset>

      <fieldset>
        <legend>Randomness</legend>
        <label>Spacing jitter <output data-for="spacing"></output>
          <input type="range" name="spacing" min="0" max="1" step="0.05" value="1"></label>
        <label>Size jitter <output data-for="size"></output>
          <input type="range" name="size" min="0" max="1" step="0.05" value="0.55"></label>
        <label>Seed <output data-for="seed"></output>
          <input type="range" name="seed" min="1" max="999" step="1" value="7"></label>
        <label>Tempo jitter (no repeats) <output data-for="tempo"></output>
          <input type="range" name="tempo" min="0" max="0.3" step="0.01" value="0"></label>
      </fieldset>

      <fieldset>
        <legend>Style</legend>
        <label>Stroke <output data-for="stroke"></output>
          <input type="range" name="stroke" min="0.5" max="4" step="0.25" value="2"></label>
        <label>Glow <output data-for="glow"></output>
          <input type="range" name="glow" min="0" max="16" step="1" value="12"></label>
        <label class="doppler-lab-check"><input type="checkbox" name="frozen"> Freeze</label>
        <label class="doppler-lab-check"><input type="checkbox" name="travelLeft" checked> Travel left</label>
      </fieldset>

      <fieldset>
        <legend>Motion</legend>
        <label class="doppler-lab-check"><input type="radio" name="motion" value="auto" checked> Auto (timed loop)</label>
        <label class="doppler-lab-check"><input type="radio" name="motion" value="scroll"> Scroll (pinned)</label>
        <div class="doppler-lab-scroll" data-scroll-only hidden>
          <label>Scroll preview <output data-for="preview"></output>
            <input type="range" name="preview" min="0" max="1" step="0.005" value="0"></label>
          <label>Scroll start <output data-for="scrollStart"></output>
            <input type="range" name="scrollStart" min="0" max="3" step="0.05" value="0"></label>
          <label>Scroll end <output data-for="scrollEnd"></output>
            <input type="range" name="scrollEnd" min="0" max="3" step="0.05" value="1"></label>
          <label>Pin length (% of screen) <output data-for="pinLength"></output>
            <input type="range" name="pinLength" min="120" max="600" step="10" value="220"></label>
        </div>
      </fieldset>
    </div>

    <div class="button-row doppler-lab-actions">
      <button type="button" class="button button-primary" data-doppler-action="reshuffle">Reshuffle</button>
      <button type="button" class="button button-secondary" data-doppler-action="reset">Back to the logo</button>
      <button type="button" class="button button-secondary" data-doppler-action="copy">Copy settings</button>
    </div>
  </div>
</section>
