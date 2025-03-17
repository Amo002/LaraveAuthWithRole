@component('mail::message')
# Hello!

You have been invited to join **Amo World**.

@component('mail::button', ['url' => $url])
Accept Invitation
@endcomponent

If you didn't expect this invitation, you can safely ignore this email.

Thanks,<br>
**The Amo World Team**
@endcomponent
