@component('mail::message')
**{{ trans('notifications.greeting', ['name' => $user->name]) }}**

{{ trans('notifications.item-updated-body', ['title' => trim($item->title)]) }}

**{{ trans('notifications.latest-activity') }}**
@component('mail::table')
| {{ trans('notifications.log') }} | {{ trans('notifications.date') }} |
|:---:|:---:|
@foreach($activities as $activity)
| {{ ucfirst($activity->description) }} | {{ $activity->created_at }} |
@endforeach
@endcomponent

@component('mail::button', ['url' => route('items.show', $item)])
{{ trans('notifications.view-item') }}
@endcomponent

{{ trans('notifications.salutation') }}<br>
{{ config('app.name') }}

<a href="{{ $unsubscribeUrl }}">
    {{ trans('notifications.unsubscribe-link') }}
</a>
@endcomponent
