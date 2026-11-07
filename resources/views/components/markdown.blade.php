@props(['text'])
{{-- CMS Markdown: raw HTML is escaped and unsafe links dropped, so admin copy can't inject markup --}}
<div {{ $attributes->class('prose') }}>
  {!! \Illuminate\Support\Str::markdown((string) $text, ['html_input' => 'escape', 'allow_unsafe_links' => false]) !!}
</div>
