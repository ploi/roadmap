<?php

namespace App\View\Components;

use Closure;
use App\Models\Project;
use App\Services\Tailwind;
use Illuminate\Support\Str;
use Illuminate\View\Component;
use App\Settings\ColorSettings;
use App\Settings\GeneralSettings;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;

class App extends Component
{
    public Collection $projects;
    public string $brandColors;
    public string $primaryColors;
    public ?string $logo;
    public array $fontFamily;
    public bool $blockRobots = false;
    public bool $userNeedsToVerify = false;

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
     * @return View|Closure|string
     */
    public function render()
    {
        $tw = new Tailwind('brand', app(ColorSettings::class)->primary);

        $this->brandColors = $tw->getCssFormat();

        $tw = new Tailwind('primary', app(ColorSettings::class)->primary);
        //dd($tw->getCssFormat());

        $this->primaryColors = str($tw->getCssFormat())->replace('color-primary', 'primary');

        $fontFamily = app(ColorSettings::class)->fontFamily ?? "Nunito";
        $this->fontFamily = [
            'cssValue' => $fontFamily,
            'urlValue' => Str::snake($fontFamily, '-')
        ];

        $this->logo = app(ColorSettings::class)->logo;

        $this->userNeedsToVerify = app(GeneralSettings::class)->users_must_verify_email &&
            auth()->check() &&
            !auth()->user()->hasVerifiedEmail();

        return view('components.app');
    }
}
