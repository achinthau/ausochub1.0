<x-guest-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-2">
            <a href="{{ route('dashboard.index') }}"> Back</a>
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                @if (config('auso.external_extension_url'))
                    <iframe src="http://123.231.74.22:3100/" width="100%" height="600px" style="border:none;">
                    </iframe>
                @endif
            </div>
        </div>
    </div>
</x-guest-layout>
