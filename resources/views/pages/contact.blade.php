{{-- The nav's Contact link currently points at the homepage #contact
     section, which takes its eyebrow/heading/intro from this page's fields.
     This page itself is reachable at /contact/. --}}
<x-layouts.site :page="$page">
  <x-hero
    :eyebrow="$page->section_eyebrow"
    :title="$page->hero_heading ?: $page->title"
    :copy="$page->hero_subheading"
  />
  <x-contact-form eyebrow="Send a message" heading="Tell us about your project" :copy="null" />
</x-layouts.site>
