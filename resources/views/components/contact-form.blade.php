@props([
    'eyebrow' => 'Contact',
    'heading' => "Let's talk about what you're trying to build",
    'copy' => "Send a few details and we'll get back to you — or use the chat in the corner for something quicker.",
])
<section class="section" id="contact">
  <div class="container">
    <div class="section-heading">
      <div>
        <p class="eyebrow">{{ $eyebrow ?: 'Contact' }}</p>
        <h2 class="section-title">{{ $heading }}</h2>
      </div>
      @if ($copy)
      <p class="section-copy">{{ $copy }}</p>
      @endif
    </div>
    @if (session('contact_status'))
    <p class="surface-card contact-form-status" role="status">{{ session('contact_status') }}</p>
    @else
    <form class="contact-form surface-card" action="{{ route('contact.store') }}" method="post">
      @csrf
      <div class="contact-form-row">
        <label for="contact-form-name">Name</label>
        <input id="contact-form-name" type="text" name="name" value="{{ old('name') }}" placeholder="Your name" required maxlength="255" />
        @error('name')<p class="contact-form-error">{{ $message }}</p>@enderror
      </div>
      <div class="contact-form-row">
        <label for="contact-form-email">Email</label>
        <input id="contact-form-email" type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required maxlength="255" />
        @error('email')<p class="contact-form-error">{{ $message }}</p>@enderror
      </div>
      <div class="contact-form-row">
        <label for="contact-form-message">Message</label>
        <textarea id="contact-form-message" name="message" rows="5" placeholder="What are you trying to build?" required maxlength="5000">{{ old('message') }}</textarea>
        @error('message')<p class="contact-form-error">{{ $message }}</p>@enderror
      </div>
      {{-- Honeypot: off-screen and skipped by keyboard and screen readers, so only bots fill it in. --}}
      <div class="contact-form-honeypot" aria-hidden="true">
        <label for="contact-form-website">Website</label>
        <input id="contact-form-website" type="text" name="website" tabindex="-1" autocomplete="off" />
      </div>
      <button class="button button-primary" type="submit">Send Message</button>
    </form>
    @endif
  </div>
</section>
