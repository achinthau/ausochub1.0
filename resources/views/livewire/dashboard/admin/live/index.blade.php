<x-app-layout>
    {{-- <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Live Dashboard') }}
        </h2>
    </x-slot> --}}

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                @if (config('auso.external_extension_url'))
                    <iframe src="{{config('auso.external_extension_url')}}" width="100%" height="600px" style="border:none;">
                    </iframe>
               
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
