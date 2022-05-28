<?php

namespace App\Services;

use Illuminate\Support\Str;
use App\Settings\ColorSettings;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class OgImageGenerator
{
    public string $title;
    public string|null $subject = null;
    public string|null $description = null;
    public string|null $templateFile = null;
    public string $imageName;
    public bool $enablePolygon = false;
    public array $polygonPoints;

    public function setTitle(string $title): static
    {
        $this->title = Str::limit($title, 28);

        return $this;
    }

    public function setSubject(string|null $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function setDescription(string|null $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function setImageTemplateFile(string $templateFile): static
    {
        $this->templateFile = $templateFile;

        return $this;
    }

    public function setImageName(string $imageName = null)
    {
        $this->imageName = $imageName;

        return $this;
    }

    public function polygon($status = true, $points = []): static
    {
        $this->enablePolygon = $status;
        $this->polygonPoints = $points ?: [
            1200, 200,
            1200, 630,
            825, 630,
        ];

        return $this;
    }

    public function getImageName(): string
    {
        if (
            $this->imageName &&
            $this->imageName !== 'og.jpg' &&
            !Str::startsWith($this->imageName, 'og-')
        ) {
            $this->imageName = 'og-' . $this->imageName;
        }

        return $this->imageName ?? 'og-' . md5(time()) . '.jpg';
    }

    public function getImageStoragePath()
    {
        return storage_path('app/public/' . $this->getImageName());
    }

    public function getImagePublicPath()
    {
        return asset('storage/' . $this->getImageName());
    }

    public function generateImage()
    {
        $pathToImage = $this->getImageStoragePath();

        if (!File::exists($pathToImage)) {
            $image = Image::canvas(1200, 627, '#ffffff');

            if ($this->templateFile) {
                $image = Image::make($this->templateFile);
            }

            $brandingColors = $this->getBrandingColors();

            $wordCount = str_word_count($this->title);
            $y = 270;

            if ($wordCount > 9) {
                $y = 240;
            }

            // Set subject
            if ($this->subject) {
                $image->text(wordwrap($this->subject, 22, PHP_EOL), 78, $y - 65, function ($font) use ($brandingColors) {
                    $font->file(public_path('fonts/Lexend-Bold.ttf'));
                    $font->size(25);
                    $font->color($brandingColors->shades['brand-500']);
                });
            }

            // Set title
            $image->text(Str::limit($this->title, 28), 75, $y, function ($font) {
                $font->file(public_path('fonts/Lexend-Bold.ttf'));
                $font->size(60);
                $font->color('#000');
            });

            if ($this->description) {
                $image->text(wordwrap($this->description, 35, PHP_EOL), 75, $y + 60, function ($font) {
                    $font->file(public_path('fonts/Lexend-SemiBold.ttf'));
                    $font->size(30);
                    $font->color('#000');
                });
            }

            if ($this->enablePolygon) {
                $image->polygon($this->polygonPoints, function ($draw) use ($brandingColors) {
                    $draw->background($brandingColors->shades['brand-100']);
                });
            }

            $image->save($pathToImage);
        }

        return $this->getImagePublicPath();
    }

    protected function getBrandingColors(): Tailwind
    {
        return new Tailwind('brand', app(ColorSettings::class)->primary);
    }
}
