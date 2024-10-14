<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Upload File') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3>Upload File</h3>
                    <form method="post" action="{{ route('print.upload.store') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
                        @csrf
                        <div class="form-group">
                            <x-input-label for="file" :value="__('Upload File')"/>
                            <x-text-input id="file" name="file" type="file" class="mt-1 block w-full" accept=".pdf,.docx,image/*" required/>
                            <x-input-error class="mt-2" :messages="$errors->get('file')"/>
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Upload') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
