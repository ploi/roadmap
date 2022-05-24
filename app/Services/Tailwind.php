<?php

namespace App\Services;

use Spatie\Color\Hex;
use Spatie\Color\Rgb;

class Tailwind
{
    public array $shades = [];

    protected array $intensityMap = [
        50 => 0.95,
        100 => 0.9,
        200 => 0.75,
        300 => 0.6,
        400 => 0.3,
        600 => 0.9,
        700 => 0.75,
        800 => 0.6,
        900 => 0.49,
    ];

    public function __construct(public string $name, public string $baseColor)
    {
        $this->shades = $this->generateColorShades($name, $this->baseColor);
    }

    public function getCssFormat(): string
    {
        $output = '<style>' . PHP_EOL;
        $output.= ':root {' . PHP_EOL;

        foreach ($this->shades as $shade => $color) {
            $output.= "\t--color-{$shade}: {$color};" . PHP_EOL;
        }

        $output.= '}' . PHP_EOL;
        $output.= '</style>';

        return $output;
    }

    public function generateColorShades(string $name, ?string $baseColor): array
    {
        $baseColor = Hex::fromString($baseColor);

        $colors = [];

        $lightShades = [50, 100, 200, 300, 400];
        $darkShades = [600, 700, 800, 900];

        // LightShades
        foreach ($lightShades as $shade) {
            $color = $this->lighten($baseColor, $this->intensityMap[$shade]);
            $colors[$name . '-' . $shade] = (string) $color;
        }

        $colors[$name . '-500'] = (string) Hex::fromString($baseColor)->toRgb();

        // DarkShades
        foreach ($darkShades as $shade) {
            $color = $this->darken($baseColor, $this->intensityMap[$shade]);
            $colors[$name . '-' . $shade] = (string) $color;
        }

        return $colors;
    }

    protected function lighten(Hex $hex, float $intensity): Rgb
    {
        $color = $hex->toRgb();

        $r = round($color->red() + (255 - $color->red()) * $intensity);
        $g = round($color->green() + (255 - $color->green()) * $intensity);
        $b = round($color->blue() + (255 - $color->blue()) * $intensity);

        return Rgb::fromString("rgb({$r}, {$g}, {$b})");
    }

    protected function darken(Hex $hex, float $intensity): Rgb
    {
        $color = $hex->toRgb();

        $r = round($color->red() * $intensity);
        $g = round($color->green() * $intensity);
        $b = round($color->blue() * $intensity);

        return Rgb::fromString("rgb({$r}, {$g}, {$b})");
    }
}
