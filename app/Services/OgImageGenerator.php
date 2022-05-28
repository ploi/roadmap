<?php

namespace App\Services;

use Illuminate\Support\Str;
use App\Settings\ColorSettings;
use Intervention\Image\Facades\Image;

class OgImageGenerator
{
    private const DEFAULT_POLYGON_POINTS = [1200, 200, 1200, 630, 825, 630];

    private string  $title;
    private ?string $subject     = null;
    private ?string $description = null;

    private ?string $filename     = null;
    private ?string $templateFile = null;

    private bool   $polygonEnabled = false;
    private ?array $polygonPoints  = null;

    public static function make(string $title): self
    {
        return new self($title);
    }

    private function __construct(string $title)
    {
        $this->title = $title;
    }

    public function withSubject(string|null $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function withDescription(string|null $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function withTemplateFile(string $templateFile): self
    {
        $this->templateFile = $templateFile;

        return $this;
    }

    public function withFilename(?string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function withPolygonDecoration(?array $points = null): static
    {
        $this->polygonEnabled = true;
        $this->polygonPoints  = $points;

        return $this;
    }

    public function getFilename(): string
    {
        if (
            $this->filename &&
            $this->filename !== 'og.jpg' &&
            !Str::startsWith($this->filename, 'og-')
        ) {
            $this->filename = 'og-' . $this->filename;
        }

        return $this->filename ?? 'og-' . md5(time()) . '.jpg';
    }

    public function generate(): OgImage
    {
        $generated = new OgImage($this->getFilename());

        if ($generated->exists()) {
            return $generated;
        }

        if ($this->templateFile) {
            $image = Image::make($this->templateFile);
        } else {
            $image = Image::canvas(1200, 627, '#ffffff');
        }

        $y = 270;

        if (str_word_count($this->title) > 9) {
            $y = 240;
        }

        $brandingColors = $this->getBrandingColors();

        if ($this->subject) {
            $image->text(wordwrap($this->subject, 22, PHP_EOL), 78, $y - 65, static function ($font) use ($brandingColors) {
                $font->file(public_path('fonts/Lexend-Bold.ttf'));
                $font->size(25);
                $font->color($brandingColors->shades['brand-500']);
            });
        }

        $image->text(Str::limit($this->title, 28), 75, $y, static function ($font) {
            $font->file(public_path('fonts/Lexend-Bold.ttf'));
            $font->size(60);
            $font->color('#000');
        });

        if ($this->description) {
            $image->text(wordwrap($this->description, 35, PHP_EOL), 75, $y + 60, static function ($font) {
                $font->file(public_path('fonts/Lexend-SemiBold.ttf'));
                $font->size(30);
                $font->color('#000');
            });
        }

        if ($this->polygonEnabled) {
            $image->polygon(
                $this->polygonPoints ?? self::DEFAULT_POLYGON_POINTS,
                static function ($draw) use ($brandingColors) {
                    $draw->background($brandingColors->shades['brand-100']);
                }
            );
        }

        $image->save($generated->getStoragePath());

        return $generated;
    }

    protected function getBrandingColors(): Tailwind
    {
        return new Tailwind('brand', app(ColorSettings::class)->primary);
    }
}
