<?php

namespace App\Filament\Widgets;

use App\Services\SystemChecker;
use Filament\Widgets\Widget;

class SystemInfo extends Widget
{
    protected static string $view = 'filament.widgets.system-info';

    protected int | string | array $columnSpan = 2;

    public array $version = [
        'remoteVersion' => 0,
        'currentVersion' => 0
    ];
    public bool $isOutOfDate = false;
    public string $phpVersion = '8.1';

    public function mount()
    {
        $systemChecker = (new SystemChecker);

        $this->version = (array)$systemChecker->getVersions();
        $this->isOutOfDate = $systemChecker->isOutOfDate();
        $this->phpVersion = $systemChecker->getPhpVersion();
    }
}
