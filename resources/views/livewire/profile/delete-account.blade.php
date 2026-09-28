<div class="space-y-4 border-t border-gray-200 dark:border-white/10 pt-6">
    <div>
        <h2 class="text-lg tracking-tight font-bold">{{ trans('profile.delete-account') }}</h2>
        <p class="text-gray-500 text-sm max-w-2xl">{{ trans('profile.delete-account-warning') }}</p>
    </div>

    <div>
        {{ $this->deleteAction }}
    </div>
</div>
