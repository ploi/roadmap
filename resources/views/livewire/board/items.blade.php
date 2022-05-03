<ul class="w-full divide-y pr-10">
    @foreach($projectItems as $projectItem)
        <livewire:project.item-card :projectItem="$projectItem" />
    @endforeach
</ul>