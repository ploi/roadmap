<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

class OgImage
{
    public function __construct(
        private readonly string $filename
    ) {
    }

    public function __toString(): string
    {
        return $this->getPublicUrl();
    }

    public function getPublicUrl(): string
    {
        return asset("storage/{$this->filename}");
    }

    public function getStoragePath(): string
    {
        return storage_path("app/public/{$this->filename}");
    }

    public function exists(): bool
    {
        return File::exists($this->getStoragePath());
    }
}
