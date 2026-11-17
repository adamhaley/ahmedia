@props([
    'eyebrow' => 'Contact',
    'heading' => "What's getting in the way?",
    'copy' => 'Send a few details, or use the chat in the corner for something quicker.',
])
{{-- With an `aside` slot (the Contact page), the form comes first with the aside beside it and
     the heading is for screen readers only. --}}
<section @class(['section', 'section-contact-first' => isset($aside)]) id="contact">
  <div @class(['container', 'contact-layout' => isset($aside)])>
    @isset($aside)
    <h2 class="sr-only">{{ $heading }}</h2>
    @else
    <div class="section-heading">
      <div>
        <p class="eyebrow">{{ $eyebrow ?: 'Contact' }}</p>
        <h2 class="section-title">{{ $heading }}</h2>
      </div>
      @if ($copy)
      <p class="section-copy">{{ $copy }}</p>
      @endif
    </div>
    @endisset
    @if (session('contact_status'))
    <p class="surface-card contact-form-status" role="status">{{ session('contact_status') }}</p>
    @else
    <form class="contact-form surface-card" action="{{ route('contact.store') }}" method="post" novalidate>
      @csrf
      <div class="contact-form-row">
        <label for="contact-form-name">Name <span class="field-required">Required</span></label>
        <input id="contact-form-name" type="text" name="name" autocomplete="name" value="{{ old('name') }}" placeholder="Your name" required maxlength="255" @error('name') aria-invalid="true" aria-describedby="contact-form-name-error" @enderror />
        @error('name')<p class="contact-form-error" id="contact-form-name-error">{{ $message }}</p>@enderror
      </div>
      <div class="contact-form-row">
        <label for="contact-form-email">Email <span class="field-required">Required</span></label>
        <input id="contact-form-email" type="email" name="email" autocomplete="email" spellcheck="false" value="{{ old('email') }}" placeholder="you@example.com" required maxlength="255" aria-describedby="contact-form-email-help{{ $errors->has('email') ? ' contact-form-email-error' : '' }}" @error('email') aria-invalid="true" @enderror />
        <p class="field-help" id="contact-form-email-help">Only used to reply to you.</p>
        @error('email')<p class="contact-form-error" id="contact-form-email-error">{{ $message }}</p>@enderror
      </div>
      <div class="contact-form-row">
        <label for="contact-form-message">Message <span class="field-required">Required</span></label>
        <p class="field-help" id="contact-form-message-help">What's getting in the way, and which tools you use today.</p>
        <textarea id="contact-form-message" name="message" rows="5" placeholder="Example: new leads sit in my inbox for days because I copy each one into a spreadsheet by hand." required maxlength="5000" aria-describedby="contact-form-message-help{{ $errors->has('message') ? ' contact-form-message-error' : '' }}" @error('message') aria-invalid="true" @enderror>{{ old('message') }}</textarea>
        @error('message')<p class="contact-form-error" id="contact-form-message-error">{{ $message }}</p>@enderror
      </div>
      {{-- Honeypot: off-screen and skipped by keyboard and screen readers, so only bots fill it in. --}}
      <div class="contact-form-honeypot" aria-hidden="true">
        <label for="contact-form-website">Website</label>
        <input id="contact-form-website" type="text" name="website" tabindex="-1" autocomplete="off" />
      </div>
      <button class="button button-primary" type="submit">Send message</button>
      <p class="form-next-step">We reply within 24 hours. Then we talk through the bottleneck, and we propose an approach and a price before any work begins.</p>
    </form>
    @endif
    {{ $aside ?? '' }}
  </div>
</section>
