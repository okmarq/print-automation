<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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
                    <form method="post" action="{{ route('settings.store') }}" class="mt-6 space-y-6">
                        @csrf
                        <div class="form-group">
                            <x-input-label for="version" :value="__('Version')"/>
                            <x-text-input id="version" name="version" type="text" class="mt-1 block w-full"
                                          required
                                          autofocus
                                          autocomplete="version"/>
                            <x-input-error class="mt-2" :messages="$errors->get('version')"/>
                        </div>
                        <div class="form-group">
                            <x-input-label for="cost_bw_page" :value="__('Cost per Black & White Page (₦)')"/>
                            <x-text-input id="cost_bw_page" step="0.01" name="cost_bw_page" type="number" class="mt-1 block w-full"
                                          required
                                          autofocus
                                          autocomplete="cost_bw_page"/>
                            <x-input-error class="mt-2" :messages="$errors->get('cost_bw_page')"/>
                        </div>
                        <div class="form-group">
                            <x-input-label for="cost_color_page" :value="__('Cost per Colored Page (₦)')"/>
                            <x-text-input id="cost_color_page" step="0.01" name="cost_color_page" type="number" class="mt-1 block w-full"
                                          required
                                          autofocus
                                          autocomplete="cost_color_page"/>
                            <x-input-error class="mt-2" :messages="$errors->get('cost_color_page')"/>
                        </div>
                        <div class="form-group">
                            <x-input-label for="cost_pixel_image" :value="__('Cost per Pixel for Images (₦)')"/>
                            <x-text-input id="cost_pixel_image" step="0.00000001" name="cost_pixel_image" type="number" class="mt-1 block w-full"
                                          required
                                          autofocus
                                          autocomplete="cost_pixel_image"/>
                            <x-input-error class="mt-2" :messages="$errors->get('cost_pixel_image')"/>
                        </div>
                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Save') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
