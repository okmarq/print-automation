<x-mail::message>
    Your Payment of has {{ '₦' . number_format($payment->amount, 2) }} been received

    It was nice doing business with you and would love to have you back.

    Thanks
    {{ config('app.name') }}
</x-mail::message>
