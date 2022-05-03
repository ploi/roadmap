<ul class="w-full divide-y pr-10">
    @foreach($items as $item)
        <livewire:project.item-card :item="$item" />
    @endforeach
</ul>
