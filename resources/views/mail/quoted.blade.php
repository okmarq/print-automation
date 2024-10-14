<x-mail::message>
    Your Quotation is ready

    Quotation
    Pages:          {{ $printJob->total_pages }}
    Images:         {{ $printJob->total_images }}
    Colored:        {{ $printJob->colored_pages }}
    Non colored:    {{ $printJob->black_white_pages }}
    Pixels:         {{ $printJob->total_pixels }}
    Price:          {{ $printJob->amount }}

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>
