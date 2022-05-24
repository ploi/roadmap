@component('mail::message')
**Hi {{ $user->name }}**,

The item **{{ trim($item->title) }}** you're subscribed to have been updated.

**Latest activity:**
@component('mail::table')
| Log | Date |
|:---:|:----:|
@foreach($activities as $activity)
| {{ ucfirst($activity->description) }} | {{ $activity->created_at }} |
@endforeach
@endcomponent

@component('mail::button', ['url' => route('items.show', $item)])
View item
@endcomponent

Best regards,<br>
{{ config('app.name') }}
@endcomponent
