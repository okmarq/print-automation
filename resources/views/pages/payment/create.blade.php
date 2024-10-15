<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Make Payment') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 text-xl">
                    <h3>Your Quotation</h3>
                    <p><strong>Pages: {{ $printJob->total_pages }}</strong></p>
                    <p><strong>Images: {{ $printJob->total_images }}</strong></p>
                    <p><strong>Colored: {{ $printJob->colored_pages }}</strong></p>
                    <p><strong>Non colored: {{ $printJob->black_white_pages }}</strong></p>
                    <p><strong>Pixels: {{ $printJob->total_pixels }}</strong></p>
                    <p><strong>Price: {{ '₦'.number_format($printJob->amount, 2) }}</strong></p>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-4">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @elseif (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="post" action="{{ route('payment.store', $printJob) }}" class="mt-6 space-y-6">
                        @csrf
                        <div class="form-group">
                            <x-input-label for="amount" :value="__('Amount (₦)')"/>
                            <x-text-input id="amount" step="0.01" name="amount" type="number" class="mt-1 block w-full"
                                          :value="old('amount', $printJob->amount)" required
                                          autofocus
                                          autocomplete="amount"/>
                            <x-input-error class="mt-2" :messages="$errors->get('amount')"/>
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Pay') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
