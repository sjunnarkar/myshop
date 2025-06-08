@component('mail::message')
# Verify Your Newsletter Subscription

Hi {{ $subscriber->name ?: 'there' }},

Thank you for subscribing to our newsletter! To complete your subscription, please click the button below to verify your email address.

@component('mail::button', ['url' => route('newsletter.verify', $subscriber->verification_token)])
Verify Email Address
@endcomponent

If you did not request this subscription, you can safely ignore this email.

Thanks,<br>
{{ config('app.name') }}

@component('mail::subcopy')
If you're having trouble clicking the "Verify Email Address" button, copy and paste the URL below into your web browser: {{ route('newsletter.verify', $subscriber->verification_token) }}
@endcomponent
@endcomponent 