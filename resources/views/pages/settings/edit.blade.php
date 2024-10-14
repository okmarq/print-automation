<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Update Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="post" action="{{ route('settings.update') }}" class="mt-6 space-y-6">
                        @csrf
                        @method('patch')
                        <div class="form-group">
                            <x-input-label for="cost_bw_page" :value="__('Cost per Black & White Page (₦)')"/>
                            <x-text-input id="cost_bw_page" step="0.01" name="cost_bw_page" type="number" class="mt-1 block w-full"
                                          :value="old('cost_bw_page', $settings->cost_bw_page)" required
                                          autofocus
                                          autocomplete="cost_bw_page"/>
                            <x-input-error class="mt-2" :messages="$errors->get('cost_bw_page')"/>
                        </div>
                        <div class="form-group">
                            <x-input-label for="cost_color_page" :value="__('Cost per Colored Page (₦)')"/>
                            <x-text-input id="cost_color_page" step="0.01" name="cost_color_page" type="number" class="mt-1 block w-full"
                                          :value="old('cost_color_page', $settings->cost_color_page)" required
                                          autofocus
                                          autocomplete="cost_color_page"/>
                            <x-input-error class="mt-2" :messages="$errors->get('cost_bw_page')"/>
                        </div>
                        <div class="form-group">
                            <x-input-label for="cost_pixel_image" :value="__('Cost per Pixel for Images (₦)')"/>
                            <x-text-input id="cost_pixel_image" step="0.00001" name="cost_pixel_image" type="number" class="mt-1 block w-full"
                                          :value="old('cost_pixel_image', $settings->cost_pixel_image)" required
                                          autofocus
                                          autocomplete="cost_pixel_image"/>
                            <x-input-error class="mt-2" :messages="$errors->get('cost_bw_page')"/>
                        </div>
                        <div class="block mt-4">
                            <label for="is_preferred" class="inline-flex items-center">
                                <input id="is_preferred" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm
                                focus:ring-indigo-500" name="is_preferred" value="{{ old('is_preferred', $settings->is_preferred) }}">
                                <span class="ms-2 text-sm text-gray-600">{{ __('Preferred') }}</span>
                            </label>
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
