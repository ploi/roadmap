@component('mail::message')
**Hi {{ $receiver['name'] }}**,

A new item has been created with the title **{{ $item->title }}**.

@component('mail::button', ['url' => route('items.show', $item)])
View item
@endcomponent

Best regards,<br>
{{ config('app.name') }}
@endcomponent
