@component('mail::message')
**{{ trans('Hello :name', ['name' => $user->name]) }}**,

{{ trans('There is a new comment on item ":item", which you are subscribed to.', ['item' => trim($comment->item->title)]) }}

@component('mail::panel')
**{{ trans('From') }}:** {{ $comment->user->name }} {{ trans('on') }} {{ $comment->created_at->format('F j, Y H:i') }}.

**{{ trans('Comment') }}:**
{{ trim($comment->content) }}
@endcomponent

{{ trans('If you no longer wish to receive notifications about this item, you can unsubscribe from future notifications by visiting the item page and clicking "Unsubscribe". Your vote will not be changed.') }}

@component('mail::button', ['url' => $url])
    {{ trans('View comment') }}
@endcomponent

{{ trans('Regards') }},<br>
{{ config('app.name') }}
@endcomponent
