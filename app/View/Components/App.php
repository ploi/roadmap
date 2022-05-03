<?php

namespace App\View\Components;

use App\Models\Project;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\Component;

class App extends Component
{
    public Collection $projects;

    public function __construct(public array $breadcrumbs = [])
    {
        $this->projects = Project::get();
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
