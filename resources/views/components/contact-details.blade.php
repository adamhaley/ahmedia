@props(['settings', 'address' => true])
{{-- Email, phone, address and social links from the site settings (Contact page and About) --}}
<aside {{ $attributes->class(['contact-details']) }} aria-label="Contact details">
  @if ($settings->contact_email)
  <p class="contact-kicker">Email</p>
  <p class="contact-detail"><a href="mailto:{{ $settings->contact_email }}">{{ $settings->contact_email }}</a></p>
  @endif
  @if ($settings->phone)
  <p class="contact-kicker">Phone</p>
  <p class="contact-detail"><a href="tel:{{ preg_replace('/[^\d+]/', '', $settings->phone) }}">{{ $settings->phone }}</a></p>
  @endif
  @if ($address && $settings->address)
  <p class="contact-kicker">Visit</p>
  <address class="contact-detail">{!! preg_replace('/, /', '<br />', e($settings->address), 1) !!}</address>
  <a class="contact-directions" href="https://www.google.com/maps/search/?api=1&amp;query={{ urlencode($settings->address) }}" rel="noopener" target="_blank">Get directions<span class="sr-only"> to {{ $settings->address }} (opens in a new tab)</span></a>
  @endif
  @if ($settings->facebook_url || $settings->instagram_url || $settings->linkedin_url)
  <p class="contact-kicker">Follow</p>
  <div class="footer-social contact-social">
    @if ($settings->facebook_url)
    <a href="{{ $settings->facebook_url }}" aria-label="Facebook"><x-icon.facebook /></a>
    @endif
    @if ($settings->instagram_url)
    <a href="{{ $settings->instagram_url }}" aria-label="Instagram"><x-icon.instagram /></a>
    @endif
    @if ($settings->linkedin_url)
    <a href="{{ $settings->linkedin_url }}" aria-label="LinkedIn"><x-icon.linkedin /></a>
    @endif
  </div>
  @endif
</aside>
