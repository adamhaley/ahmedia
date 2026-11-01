// Doppler rings: the lab on the logo page, and the pinned homepage hero.
// Both share scrubRings(), so scroll previews in the lab match the site.
(function () {
  // Holds the rings at a point in their cycle (CSS .is-scrubbed pauses the
  // keyframes; --progress picks the frame). progress 0 is the logo
  // arrangement; a whole cycle later (1, 2, ...) is the same arrangement again.
  function scrubRings(svg, progress) {
    if (!svg.classList.contains('is-scrubbed')) {
      svg.classList.add('is-scrubbed');
      // A paused animation holds whatever time it had reached; zero it so --progress alone sets the frame
      svg.getAnimations({ subtree: true }).forEach(function (animation) { animation.currentTime = 0; });
    }
    svg.style.setProperty('--progress', progress);
  }

  function releaseRings(svg) {
    svg.classList.remove('is-scrubbed');
    svg.style.removeProperty('--progress');
  }

  // Lab: sliders rebuild the rings live. Per-ring offsets come from a seeded
  // random, so a seed always gives the same arrangement. On the Logo story
  // page nothing is saved; as the admin editor (data-doppler-editor) every
  // change is sent to the form as a doppler-settings event.
  (function () {
    const lab = document.querySelector('[data-doppler-lab]');
    if (!lab) return;

    const svg = lab.querySelector('.doppler-rings');
    const svgNamespace = 'http://www.w3.org/2000/svg';
    const ranges = lab.querySelectorAll('input[type="range"]');
    const toggles = lab.querySelectorAll('input[type="checkbox"], input[type="radio"]');
    const scrollOnly = lab.querySelectorAll('[data-scroll-only]');
    // Lab-only controls (tempo) are missing from the editor, so they read as 0
    const value = function (name) {
      const input = lab.querySelector('[name="' + name + '"]');
      return input ? Number(input.value) : 0;
    };
    const checked = function (name) { return lab.querySelector('[name="' + name + '"]').checked; };

    // mulberry32. App\Data\DopplerRingSettings::perRing() mirrors this and
    // settings() below draw for draw, so pages render what the lab previews.
    function seededRandom(seed) {
      return function () {
        seed |= 0; seed = seed + 0x6D2B79F5 | 0;
        let t = Math.imul(seed ^ seed >>> 15, 1 | seed);
        t = t + Math.imul(t ^ t >>> 7, 61 | t) ^ t;
        return ((t ^ t >>> 14) >>> 0) / 4294967296;
      };
    }

    function settings() {
      const random = seededRandom(value('seed'));
      // A separate stream, so tempo jitter never changes a seed's sizes and spacing
      const tempoRandom = seededRandom(value('seed') * 7919 + 1);
      const perRing = [];
      for (let i = 0; i < value('rings'); i++) {
        const phase = Number(((random() - 0.5) * value('spacing')).toFixed(3));
        const size = Number(((random() - 0.5) * 2 * value('size')).toFixed(3));
        random(); // a retired off-axis value; still drawn so seeds keep their arrangement
        const tempo = Number((1 + (tempoRandom() - 0.5) * 2 * value('tempo')).toFixed(3));
        perRing.push({ phase: phase, size: size, tempo: tempo });
      }

      return {
        mach: value('mach'), rings: value('rings'), period: value('period'), reach: value('reach'),
        spacing: value('spacing'), size: value('size'), seed: value('seed'), tempo: value('tempo'),
        stroke: value('stroke'), glow: value('glow'),
        axis: value('axis'), axisMobile: value('axisMobile'),
        frozen: checked('frozen'), travelLeft: checked('travelLeft'),
        motion: lab.querySelector('[name="motion"]:checked').value,
        scrollStart: value('scrollStart'), scrollEnd: value('scrollEnd'), pinLength: value('pinLength'),
        perRing: perRing,
      };
    }

    function render() {
      const current = settings();
      const style = svg.style;
      style.setProperty('--mach', current.mach);
      style.setProperty('--rings', current.rings);
      style.setProperty('--period', current.period + 's');
      style.setProperty('--reach', current.reach);
      style.setProperty('--stroke', current.stroke);
      style.setProperty('--glow', current.glow);
      style.setProperty('--direction', current.travelLeft ? -1 : 1);
      style.setProperty('--source-x', current.travelLeft ? '880px' : '120px');
      style.setProperty('--axis', current.axis);
      style.setProperty('--axis-mobile', current.axisMobile);
      svg.classList.toggle('is-frozen', current.frozen);
      ranges.forEach(function (input) {
        lab.querySelector('output[data-for="' + input.name + '"]').textContent = input.value;
      });

      if (svg.querySelectorAll('circle').length !== current.rings) {
        svg.replaceChildren();
        current.perRing.forEach(function () {
          const circle = document.createElementNS(svgNamespace, 'circle');
          circle.setAttribute('r', 1);
          svg.append(circle);
        });
      }
      const circles = Array.from(svg.querySelectorAll('circle'));
      circles.forEach(function (circle, index) {
        const ring = current.perRing[index];
        circle.style.setProperty('--i', index);
        circle.style.setProperty('--phase', ring.phase);
        circle.style.setProperty('--size', ring.size);
        circle.style.setProperty('--tempo', ring.tempo);
      });

      // Scroll motion: the preview slider stands in for scrolling through the pin
      const isScroll = current.motion === 'scroll';
      scrollOnly.forEach(function (element) { element.hidden = !isScroll; });
      if (isScroll) {
        scrubRings(svg, current.scrollStart + value('preview') * (current.scrollEnd - current.scrollStart));
      } else {
        releaseRings(svg);
      }

      if (lab.hasAttribute('data-doppler-editor')) {
        // Only what a rings style stores; the preview aids (freeze, scroll preview) aren't saved
        const stored = {};
        ['mach', 'rings', 'period', 'reach', 'spacing', 'size', 'seed', 'stroke', 'glow', 'axis', 'axisMobile',
          'scrollStart', 'scrollEnd', 'pinLength', 'travelLeft', 'motion'].forEach(function (key) { stored[key] = current[key]; });
        lab.dispatchEvent(new CustomEvent('doppler-settings', { bubbles: true, detail: stored }));
      }
    }

    // The lab page's address describes what's on screen, so a copied link
    // reopens this arrangement (PageController reads it back). Frozen, it
    // also keeps the frame ("at", the rings' time in ms). Debounced, as
    // browsers cap how often a page may rewrite its address.
    let addressTimer;
    function writeAddress() {
      clearTimeout(addressTimer);
      const current = settings();
      const params = new URLSearchParams();
      ['mach', 'rings', 'period', 'reach', 'spacing', 'size', 'seed', 'tempo', 'stroke', 'glow', 'axis', 'axisMobile', 'motion']
        .forEach(function (key) { params.set(key, current[key]); });
      params.set('travelLeft', current.travelLeft ? 1 : 0);
      if (current.motion === 'scroll') {
        ['scrollStart', 'scrollEnd', 'pinLength'].forEach(function (key) { params.set(key, current[key]); });
      } else if (current.frozen) {
        const animation = svg.getAnimations({ subtree: true })[0];
        params.set('frozen', 1);
        if (animation) params.set('at', Math.round(Number(animation.currentTime)));
      }
      history.replaceState(null, '', '?' + params.toString());
    }

    function updateAddress() {
      if (lab.hasAttribute('data-doppler-editor')) return;
      clearTimeout(addressTimer);
      addressTimer = setTimeout(writeAddress, 250);
    }

    // The first painted background behind the stage, for the image's backdrop
    function backgroundOf(element) {
      for (let node = element; node; node = node.parentElement) {
        const colour = getComputedStyle(node).backgroundColor;
        if (colour !== 'rgba(0, 0, 0, 0)' && colour !== 'transparent') return colour;
      }
      return getComputedStyle(document.documentElement).getPropertyValue('--color-page-top');
    }

    // Draws the frame on screen to a PNG. Each ring's position, size and fade
    // are read live from the page, so it matches mid-motion or frozen.
    function saveImage() {
      const current = settings();
      const stage = svg.parentElement;
      const box = stage.getBoundingClientRect();
      const pixelRatio = 2;
      const canvas = document.createElement('canvas');
      canvas.width = box.width * pixelRatio;
      canvas.height = box.height * pixelRatio;
      const context = canvas.getContext('2d');
      context.scale(pixelRatio, pixelRatio);
      context.fillStyle = backgroundOf(stage);
      context.fillRect(0, 0, box.width, box.height);
      context.lineWidth = current.stroke;
      context.strokeStyle = getComputedStyle(svg.querySelector('circle')).stroke;
      context.shadowColor = getComputedStyle(svg).getPropertyValue('--glow-color').trim() || context.strokeStyle;
      context.shadowBlur = current.glow;
      svg.querySelectorAll('circle').forEach(function (circle) {
        const ring = circle.getBoundingClientRect();
        context.globalAlpha = Number(getComputedStyle(circle).opacity);
        context.beginPath();
        context.arc(ring.left - box.left + ring.width / 2, ring.top - box.top + ring.height / 2, ring.width / 2, 0, 2 * Math.PI);
        context.stroke();
      });

      const link = document.createElement('a');
      link.download = 'doppler-rings-seed-' + current.seed + '.png';
      link.href = canvas.toDataURL('image/png');
      link.click();
    }

    lab.addEventListener('input', function () {
      render();
      updateAddress();
    });
    lab.addEventListener('click', function (event) {
      const button = event.target.closest('[data-doppler-action]');
      if (!button) return;

      const action = button.dataset.dopplerAction;
      if (action === 'reshuffle') {
        lab.querySelector('[name="seed"]').value = 1 + Math.floor(Math.random() * 999);
      } else if (action === 'reset') {
        ranges.forEach(function (input) { input.value = input.defaultValue; });
        toggles.forEach(function (input) { input.checked = input.defaultChecked; });
      } else if (action === 'save-image') {
        saveImage();
        return;
      } else if (action === 'copy-link') {
        const label = button.textContent;
        writeAddress();
        navigator.clipboard.writeText(location.href)
          .then(function () { button.textContent = 'Copied'; })
          .catch(function () { button.textContent = 'Copy failed'; })
          .finally(function () { setTimeout(function () { button.textContent = label; }, 1500); });
        return;
      }
      render();
      updateAddress();
    });

    render();
    // A shared frozen frame: put every ring back where it was
    const sharedFrame = new URLSearchParams(location.search).get('at');
    if (sharedFrame !== null && !lab.hasAttribute('data-doppler-editor') && checked('frozen')) {
      svg.getAnimations({ subtree: true }).forEach(function (animation) { animation.currentTime = Number(sharedFrame); });
    }
  })();

  // Pinned hero: while the hero is stuck (.hero-pin), scroll position drives
  // the rings instead of time. data-start and data-end (Site settings) are
  // points in the ring cycle, so 0 to 1 pins in and out without a jump.
  // Reduced-motion visitors keep the still frame.
  (function () {
    const pin = document.querySelector('[data-hero-pin]');
    if (!pin || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    const svg = pin.querySelector('.doppler-rings');
    if (!svg) return;

    const cycleStart = Number(pin.dataset.start);
    const cycleEnd = Number(pin.dataset.end);
    let frameRequested = false;

    function update() {
      frameRequested = false;
      // Measured, not assumed to be 0, so the pin works wherever it starts
      const pinTop = pin.getBoundingClientRect().top + window.scrollY;
      pin.style.setProperty('--pin-top', pinTop + 'px');
      const pinDistance = Math.max(pin.offsetHeight - (window.innerHeight - pinTop), 1);
      const scrolled = Math.min(Math.max((pinTop - pin.getBoundingClientRect().top) / pinDistance, 0), 1);
      scrubRings(svg, cycleStart + scrolled * (cycleEnd - cycleStart));
    }

    function requestUpdate() {
      if (frameRequested) return;
      frameRequested = true;
      window.requestAnimationFrame(update);
    }

    window.addEventListener('scroll', requestUpdate, { passive: true });
    window.addEventListener('resize', requestUpdate);
    update();
  })();
})();
