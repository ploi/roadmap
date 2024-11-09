<?php

namespace App\View\Components;

use App\Models\Project;
use App\Services\Tailwind;
use Illuminate\Support\Str;
use Illuminate\View\Component;
use App\Settings\GeneralSettings;
use Illuminate\Database\Eloquent\Collection;

class App extends Component
{
    /**
     * Collection of the projects to display.
     * 
     * @var Collection<int, Project>
     */
    public Collection $projects;
    public string $brandColors;
    public string $primaryColors;
    public ?string $logo;

    /**
     * @var array{cssValue: string, urlValue: string}
     */
    public array $fontFamily;
    public bool $blockRobots = false;
    public bool $userNeedsToVerify = false;

    /**
     * @param array<int, array{title: string, url: string}> $breadcrumbs
     */
    public function __construct(public array $breadcrumbs = [])
    {
        $this->projects = Project::query()
            ->visibleForCurrentUser()
            ->when(app(GeneralSettings::class)->show_projects_sidebar_without_boards === false, function ($query) {
                return $query->has('boards');
            })
            ->orderBy('sort_order')
            ->orderBy('group')
            ->orderBy('title')
            ->get();

        $this->blockRobots = app(GeneralSettings::class)->block_robots;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $tw = new Tailwind('brand', app(\App\Settings\ColorSettings::class)->primary);

        $this->brandColors = $tw->getCssFormat();

        $tw = new Tailwind('primary', app(\App\Settings\ColorSettings::class)->primary);

        $this->primaryColors = str($tw->getCssFormat())->replace('color-', '');

        $fontFamily = app(\App\Settings\ColorSettings::class)->fontFamily ?? "Nunito";
        $this->fontFamily = [
            'cssValue' => $fontFamily,
            'urlValue' => Str::snake($fontFamily, '-')
        ];

        $this->logo = app(\App\Settings\ColorSettings::class)->logo;

        $this->userNeedsToVerify = app(GeneralSettings::class)->users_must_verify_email &&
            auth()->check() &&
            !auth()->user()->hasVerifiedEmail();

        return view('components.app');
    }
}
