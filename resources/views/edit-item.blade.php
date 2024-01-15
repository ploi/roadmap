@section('title', 'Edit ' . $item->title)
@section('image', $item->getOgImage('"' . $item->excerpt .'"', 'Roadmap - Item'))
@section('description', $item->excerpt)
@section('canonical', route('items.edit', $item))

<x-app :breadcrumbs="[
['title' =>  trans( 'resources.changelog.label' ), 'url' => route('home')],
['title' => $item->title, 'url' => route('items.show', $item)],
]">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-4">
            <livewire:item.edit :item="$item" />
        </div>
    </div>
</x-app>
