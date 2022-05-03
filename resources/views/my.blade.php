<x-app :breadcrumbs="[
    ['title' => 'My items', 'url' => route('my')]
]">
    <main class="p-4 overflow-x-auto h-full">
        <div class="overflow-x-auto">
            <div class="inline-block min-w-full px-4 space-y-4 mb-4">
                <div class="overflow-hidden bg-white shadow rounded-xl">
                    <table class="w-full text-left divide-y table-auto">
                        <thead>
                        <tr class="divide-x bg-gray-50">
                            <th class="px-4 py-2 text-sm font-semibold text-gray-600">
                                Title
                            </th>

                            <th class="px-4 py-2 text-sm font-semibold text-gray-600">
                                Date
                            </th>
                        </tr>
                        </thead>

                        <tbody class="divide-y whitespace-nowrap">
                        @foreach($items as $item)
                        <tr class="divide-x">
                            <td class="px-4 py-3">
                                <a class="border-b font-semibold text-black" href="{{ route('projects.items.show', [$item->board->project->id, $item->id]) }}">
                                    {{ $item->title }}
                                </a>
                            </td>

                            <td class="px-4 py-3">{{ $item->created_at }}</td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            {{ $items->links() }}
            </div>
        </div>

    </main>
</x-app>
