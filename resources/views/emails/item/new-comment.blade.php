@component('mail::message')
**{{ trans('notifications.greeting', ['name' => $user->name]) }}**

{{ trans('notifications.new-comment-body', ['title' => trim($comment->item->title)]) }}

@component('mail::panel')
**{{ trans('notifications.from') }}:** {{ $comment->user->name }} {{ trans('notifications.on') }} {{ $comment->created_at->isoFormat('L LTS') }}.

**{{ trans('notifications.comment') }}:**
{{ trim($comment->content) }}
@endcomponent

{{ trans('notifications.unsubscribe-info') }}

@component('mail::button', ['url' => $url])
    {{ trans('notifications.view-comment') }}
@endcomponent

{{ trans('notifications.salutation') }}<br>
{{ config('app.name') }}
@endcomponent
