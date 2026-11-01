<?php

namespace App\Data;

use App\Enums\HeroRingsMotion;
use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * One Doppler rings style: the settings the lab edits, stored as JSON on a DopplerRingStyle.
 * Values from storage or the editor are clamped to Ranges, so a bad value can't break a page.
 */
final readonly class DopplerRingSettings implements Castable
{
    /**
     * Every numeric setting's slider range and default. The lab and the admin editor both render
     * their sliders from this list (components/doppler-lab), so the ranges live only here.
     *
     * @var array<string, array{label: string, group: string, min: int|float, max: int|float, step: int|float, default: int|float}>
     */
    public const Ranges = [
        'mach' => ['label' => 'Mach (offset ratio)', 'group' => 'Geometry', 'min' => 0, 'max' => 3, 'step' => 0.05, 'default' => 2.8],
        'rings' => ['label' => 'Rings', 'group' => 'Geometry', 'min' => 2, 'max' => 14, 'step' => 1, 'default' => 8],
        'period' => ['label' => 'Cycle (seconds)', 'group' => 'Geometry', 'min' => 3, 'max' => 40, 'step' => 1, 'default' => 39],
        'reach' => ['label' => 'Reach', 'group' => 'Geometry', 'min' => 100, 'max' => 900, 'step' => 10, 'default' => 300],
        'spacing' => ['label' => 'Spacing jitter', 'group' => 'Randomness', 'min' => 0, 'max' => 1, 'step' => 0.05, 'default' => 1],
        'size' => ['label' => 'Size jitter', 'group' => 'Randomness', 'min' => 0, 'max' => 1, 'step' => 0.05, 'default' => 0.55],
        'seed' => ['label' => 'Seed', 'group' => 'Randomness', 'min' => 1, 'max' => 999, 'step' => 1, 'default' => 7],
        'stroke' => ['label' => 'Stroke', 'group' => 'Style', 'min' => 0.5, 'max' => 4, 'step' => 0.25, 'default' => 2],
        'glow' => ['label' => 'Glow', 'group' => 'Style', 'min' => 0, 'max' => 16, 'step' => 1, 'default' => 12],
        'axis' => ['label' => 'Axis height', 'group' => 'Axis', 'min' => 100, 'max' => 500, 'step' => 5, 'default' => 300],
        'axisMobile' => ['label' => 'Axis height on mobile', 'group' => 'Axis', 'min' => 100, 'max' => 500, 'step' => 5, 'default' => 330],
        'scrollStart' => ['label' => 'Scroll start', 'group' => 'Motion', 'min' => 0, 'max' => 3, 'step' => 0.05, 'default' => 0],
        'scrollEnd' => ['label' => 'Scroll end', 'group' => 'Motion', 'min' => 0, 'max' => 3, 'step' => 0.05, 'default' => 1],
        'pinLength' => ['label' => 'Pin length (% of screen)', 'group' => 'Motion', 'min' => 120, 'max' => 600, 'step' => 10, 'default' => 220],
    ];

    public function __construct(
        public float $mach,
        public int $rings,
        public int $period,
        public int $reach,
        public float $spacing,
        public float $size,
        public int $seed,
        public float $stroke,
        public int $glow,
        public int $axis,
        public int $axisMobile,
        public float $scrollStart,
        public float $scrollEnd,
        public int $pinLength,
        public bool $travelLeft,
        public HeroRingsMotion $motion,
    ) {}

    /** @param array<string, mixed> $values */
    public static function fromArray(array $values): self
    {
        $number = function (string $key) use ($values): int|float {
            $range = self::Ranges[$key];
            $value = is_numeric($values[$key] ?? null) ? (float) $values[$key] : $range['default'];

            return min(max($value, $range['min']), $range['max']);
        };
        $scrollStart = (float) $number('scrollStart');

        return new self(
            mach: (float) $number('mach'),
            rings: (int) round($number('rings')),
            period: (int) round($number('period')),
            reach: (int) round($number('reach')),
            spacing: (float) $number('spacing'),
            size: (float) $number('size'),
            seed: (int) round($number('seed')),
            stroke: (float) $number('stroke'),
            glow: (int) round($number('glow')),
            axis: (int) round($number('axis')),
            axisMobile: (int) round($number('axisMobile')),
            scrollStart: $scrollStart,
            // The pin has to move forward, or scrolling would play nothing
            scrollEnd: max((float) $number('scrollEnd'), $scrollStart + self::Ranges['scrollEnd']['step']),
            pinLength: (int) round($number('pinLength')),
            travelLeft: (bool) ($values['travelLeft'] ?? true),
            motion: HeroRingsMotion::tryFrom((string) ($values['motion'] ?? '')) ?? HeroRingsMotion::Scroll,
        );
    }

    public static function defaults(): self
    {
        return self::fromArray([]);
    }

    /** @return array<string, int|float|bool|string> */
    public function toArray(): array
    {
        return [...get_object_vars($this), 'motion' => $this->motion->value];
    }

    /**
     * Each ring's fixed [phase, size] offset, from the seed. Mirrors settings() in
     * public/doppler-rings.js draw for draw, so the lab previews exactly what pages render.
     *
     * @return list<array{float, float}>
     */
    public function perRing(): array
    {
        $random = self::seededRandom($this->seed);
        $rings = [];
        for ($i = 0; $i < $this->rings; $i++) {
            $phase = round(($random() - 0.5) * $this->spacing, 3);
            $size = round(($random() - 0.5) * 2 * $this->size, 3);
            $random(); // a retired off-axis value; still drawn so seeds keep their arrangement
            $rings[] = [$phase, $size];
        }

        return $rings;
    }

    /** The SVG's CSS variables (public/doppler-rings.css reads them). */
    public function cssVariables(): string
    {
        return implode('; ', [
            "--mach: {$this->mach}",
            "--rings: {$this->rings}",
            "--period: {$this->period}s",
            "--reach: {$this->reach}",
            "--stroke: {$this->stroke}",
            "--glow: {$this->glow}",
            '--direction: '.($this->travelLeft ? -1 : 1),
            // The source sits behind the title when travelling left, by the left edge otherwise
            '--source-x: '.($this->travelLeft ? 880 : 120).'px',
            "--axis: {$this->axis}",
            "--axis-mobile: {$this->axisMobile}",
        ]);
    }

    /**
     * mulberry32, matching JavaScript's 32-bit integer maths exactly.
     *
     * @return callable(): float
     */
    private static function seededRandom(int $seed): callable
    {
        $mask = 0xFFFFFFFF;
        // Math.imul: the low 32 bits of a 32 x 32 multiply, split so PHP's 64-bit ints can't overflow
        $imul = fn (int $a, int $b): int => (($a & 0xFFFF) * $b + ((((($a >> 16) & 0xFFFF) * $b) & 0xFFFF) << 16)) & $mask;
        $seed &= $mask;

        return function () use (&$seed, $mask, $imul): float {
            $seed = ($seed + 0x6D2B79F5) & $mask;
            $t = $imul($seed ^ ($seed >> 15), 1 | $seed);
            $t = (($t + $imul($t ^ ($t >> 7), 61 | $t)) & $mask) ^ $t;

            return (($t ^ ($t >> 14)) & $mask) / 4294967296;
        };
    }

    /** @param array<array-key, mixed> $arguments */
    public static function castUsing(array $arguments): CastsAttributes
    {
        return new class implements CastsAttributes
        {
            /** @param array<string, mixed> $attributes */
            public function get(Model $model, string $key, mixed $value, array $attributes): DopplerRingSettings
            {
                return DopplerRingSettings::fromArray(json_decode((string) $value, true) ?: []);
            }

            /** @param array<string, mixed> $attributes */
            public function set(Model $model, string $key, mixed $value, array $attributes): string
            {
                $settings = $value instanceof DopplerRingSettings ? $value : DopplerRingSettings::fromArray((array) $value);

                return (string) json_encode($settings->toArray());
            }
        };
    }
}
