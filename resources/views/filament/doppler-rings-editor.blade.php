{{-- Admin rings style editor: the Logo story lab, sending each change up as the field's state --}}
<div
    class="doppler-rings-editor"
    style="--color-accent: {{ \App\Models\SiteSetting::current()->accent_color ?: \App\Models\SiteSetting::DefaultAccentColor }}"
    x-data="{ state: $wire.$entangle(@js($getStatePath())) }"
    x-on:doppler-settings="state = $event.detail"
    wire:ignore
>
    <x-doppler-lab :settings="\App\Data\DopplerRingSettings::fromArray($getState() ?? [])" editor />
</div>
