<x-app-layout>
    <x-slot name="header">
        @if(Auth::user()->hasRole(config('constants.role.admin')))
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Print Jobs') }}
            </h2>
        @else
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Quotations') }}
                </h2>

                <a class="bg-blue-500 hover:bg-blue-700 px-2 py-1 rounded-lg text-amber-50" href="{{ route('print.create') }}">Upload</a>
            </div>
        @endif
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-xl font-black text-center mb-4">Print Jobs</h3>
                    <table class="table table-bordered w-full">
                        <thead>
                        <tr>
                            <th class="px-4 py-2">Print Job ID</th>
                            @if(Auth::user()->hasRole(config('constants.role.admin')))
                                <th class="px-4 py-2">Owner</th>
                            @endif
                            <th class="px-4 py-2">Total Images</th>
                            <th class="px-4 py-2">Total Pixels</th>
                            <th class="px-4 py-2">Black & White Pages</th>
                            <th class="px-4 py-2">Colored Pages</th>
                            <th class="px-4 py-2">Total Pages</th>
                            <th class="px-4 py-2">Amount</th>
                            <th class="px-4 py-2">Uploaded File</th>
                            <th class="px-4 py-2">Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($printJobs as $job)
                            <tr>
                                <td class="border px-4 py-2">{{ $job->id }}</td>
                                @if(Auth::user()->hasRole(config('constants.role.admin')))
                                    <td class="border px-4 py-2">{{ $job->user_id }}</td>
                                @endif
                                <td class="border px-4 py-2">{{ $job->total_images ?? 'N/A' }}</td>
                                <td class="border px-4 py-2">{{ $job->total_pixels ?? 'N/A' }}</td>
                                <td class="border px-4 py-2">{{ $job->black_white_pages }}</td>
                                <td class="border px-4 py-2">{{ $job->colored_pages }}</td>
                                <td class="border px-4 py-2">{{ $job->total_pages }}</td>
                                <td class="border px-4 py-2">{{ '₦' . number_format($job->amount, 2) }}</td>
                                <td class="border px-4 py-2"><a href="{{ Storage::url($job->file_path) }}" target="_blank">View File</a></td>
                                <td class="border px-4 py-2">
                                    @if($job->status !== config('constants.status.paid'))
                                        <a class="text-red-700" href="{{ route('payment.create', $job) }}">Pay</a>
                                    @else
                                        <span class="text-green-700"></span> {{ ucfirst($job->status) }}
                                    @endif
                                </td>
                            </tr>
                        @empty
                            @if(Auth::user()->hasRole(config('constants.role.admin')))
                                <tr>
                                    <td colspan="10" class="border px-4 py-2 text-center">There are no print jobs available</td>
                                </tr>
                            @else
                                <tr>
                                    <td colspan="9" class="border px-4 py-2 text-center">You have no print jobs</td>
                                </tr>
                            @endif
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
