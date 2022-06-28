<ul class="w-full space-y-8">
    @foreach($changelogs as $changelog)
        <livewire:changelog.item :changelog="$changelog"/>
    @endforeach
</ul>
