<div class="space-y-6">
    <x-filament::section :heading="trans('settings.mcp.how-it-works')">
        <div class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
            <p>{{ trans('settings.mcp.how-it-works-description') }}</p>
            <p>{{ trans('mcp.permissions') }}</p>
            <p>
                <x-filament::link :href="route('mcp.docs')" target="_blank" icon="heroicon-m-arrow-top-right-on-square" icon-position="after">
                    {{ trans('settings.mcp.view-docs') }}
                </x-filament::link>
            </p>
        </div>
    </x-filament::section>

    <x-filament::section :heading="trans('settings.mcp.connect-heading')" :description="trans('mcp.step-connect')">
        <x-mcp.instructions />
    </x-filament::section>

    <x-filament::section :heading="trans('mcp.tools-title')" collapsible collapsed>
        <x-mcp.tools />
    </x-filament::section>
</div>
