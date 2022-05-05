<?php

namespace App\View\Components;

use App\Models\Project;
use App\Settings\GeneralSettings;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\Component;

class App extends Component
{
    public Collection $projects;

    public function __construct(public array $breadcrumbs = [])
    {
        $this->projects = Project::query()
            ->when(app(GeneralSettings::class)->show_projects_sidebar_without_boards === false, function($query){
                return $query->has('boards');
            })
            ->get();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.app');
    }
}
