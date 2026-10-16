@props(['eyebrow' => null, 'heading', 'copy' => null, 'buttonLabel' => null, 'buttonHref' => '/#contact', 'buttonVariant' => 'primary', 'id' => null])
@php
  // On subpages, "Start a Project" opens the contact form right here instead
  // of sending visitors to the homepage form (public/scripts.js wires every
  // /#contact link on the page to it). It starts open after a submission so
  // the thank-you message or validation errors are visible.
  $revealsContactForm = $buttonHref === '/#contact';
  $contactFormOpen = session()->has('contact_status') || (bool) session('errors')?->any();
@endphp
@if ($heading)
<section class="section" @if ($id) id="{{ $id }}" @endif>
  <div class="container">
    <div class="surface-card cta-band">
      <div>
        @if ($eyebrow)
        <p class="eyebrow">{{ $eyebrow }}</p>
        @endif
        <h2 class="section-title">{{ $heading }}</h2>
        @if ($copy)
        <p class="section-copy">{{ $copy }}</p>
        @endif
      </div>
      <div class="button-row">
        <a class="button button-{{ $buttonVariant }}" href="{{ $buttonHref }}" @if ($revealsContactForm) aria-controls="cta-contact-form" aria-expanded="{{ $contactFormOpen ? 'true' : 'false' }}" @endif>{{ $buttonLabel ?: 'Start a Project' }}</a>
      </div>
    </div>
  </div>
  @if ($revealsContactForm)
  <div id="cta-contact-form" class="cta-contact-form" @unless ($contactFormOpen) hidden @endunless>
    <x-contact-form eyebrow="Start a project" heading="Tell us about your project" copy="" />
  </div>
  @endif
</section>
@endif
