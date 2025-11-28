<x-mail::message>
# {{ __('mail.user-created.greeting', ['name' => $user->name]) }}

{{ __('mail.user-created.intro') }}

## {{ __('mail.user-created.credentials-title') }}

{{ __('mail.user-created.credentials-intro') }}

**{{ __('mail.user-created.email-label') }}:** {{ $user->email }}<br />
**{{ __('mail.user-created.password-label') }}:** {{ $password }}

<x-mail::button :url="$loginUrl">
{{ __('mail.user-created.login-button') }}
</x-mail::button>

---

{{ __('mail.user-created.security-notice') }}

{{ __('mail.user-created.help-text') }}

{{ __('mail.user-created.closing') }},<br>
{{ config('app.name') }}
</x-mail::message>
