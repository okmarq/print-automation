<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Settings') }}
            </h2>

            <a class="bg-blue-500 hover:bg-blue-700 px-2 py-1 rounded-lg text-amber-50" href="{{ route('settings.create') }}">Add</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3>Settings</h3>
                    <table class="table table-bordered w-full">
                        <thead>
                        <tr>
                            <th class="px-4 py-2">ID</th>
                            <th class="px-4 py-2">Version</th>
                            <th class="px-4 py-2">Cost per Black & White Pages</th>
                            <th class="px-4 py-2">Cost per Colored Pages</th>
                            <th class="px-4 py-2">Cost per Pixel</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($settings as $setting)
                            <tr>
                                <td class="border px-4 py-2">{{ $setting->id }}</td>
                                <td class="border px-4 py-2">{{ $setting->version }}</td>
                                <td class="border px-4 py-2">{{ '₦' . number_format($setting->cost_bw_page, 2) }}</td>
                                <td class="border px-4 py-2">{{ '₦' . number_format($setting->cost_color_page, 2) }}</td>
                                <td class="border px-4 py-2">{{ '₦' . number_format($setting->cost_pixel_image, 8) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="border px-4 py-2 text-center">There are no settings available</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
