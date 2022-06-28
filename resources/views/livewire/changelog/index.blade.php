<ul class="w-full divide-y pr-10">
    @foreach($changelogs as $changelog)
        <livewire:changelog.item :changelog="$changelog"/>
    @endforeach
</ul>
