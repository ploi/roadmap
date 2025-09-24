@section('canonical', route('home'))@show

<x-app>
    @if($text = app(\App\Settings\GeneralSettings::class)->welcome_text)
        <div class="prose dark:prose-invert mb-4 dark:text-gray-500">{!! __($text) !!}</div>
    @endif
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        @foreach(app(\App\Settings\GeneralSettings::class)->dashboard_items as $item)
            <div @class([
                'space-y-2',
                'col-span-1' => ($item['column_span'] ?? 1) == 1,
                'col-span-2' => ($item['column_span'] ?? 1) == 2,
                'lg:col-span-1' => ($item['column_span'] ?? 1) == 1,
                'lg:col-span-2' => ($item['column_span'] ?? 1) == 2,
            ])>
                <h2 class="text-lg tracking-tight font-bold">{{ trans('general.'. $item['type']) }}</h2>

                <livewire:is component="welcome.{{ $item['type'] }}"/>
            </div>
        @endforeach
    </div>
</x-app>
