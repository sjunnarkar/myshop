@component('mail::message')
# Welcome to Our Newsletter!

Hi {{ $subscriber->name ?: 'there' }},

Thank you for verifying your email address and joining our newsletter! We're excited to have you on board.

You'll now receive our latest updates, news, and exclusive offers directly in your inbox.

@component('mail::panel')
## Manage Your Preferences
You can update your newsletter preferences or unsubscribe at any time by visiting your account settings.

@component('mail::button', ['url' => route('newsletter.preferences', $subscriber->verification_token)])
Update Preferences
@endcomponent
@endcomponent

If you ever want to unsubscribe, you can do so by clicking the link below:

@component('mail::button', ['url' => route('newsletter.unsubscribe', $subscriber->verification_token), 'color' => 'secondary'])
Unsubscribe
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent 