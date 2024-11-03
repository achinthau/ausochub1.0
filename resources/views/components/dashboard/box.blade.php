<div class="grid grid-cols-2 bg-white p-6 rounded-md shadow-md">
    <div class="my-auto">
        <div class="text-gray-500 text-sm">{{ $title }}</div>
        @if (!is_null($value))
            @if (is_int($value))
                <div class="text-xl font-bold">{{ number_format($value, 0) }}
                    @isset($subText)
                        <span class="text-gray-500 text-sm font-semibold">{{ $subText }}</span>
                    @endisset
                </div>
            @else
                <div class="text-xl font-bold">{{ $value }}</div>
            @endif
        @else
            <div class="text-xl font-bold">--</div>
        @endif

    </div>
    <div class="grid content-center justify-items-end">
        <div class="rounded-lg w-16 h-16 {{ $iconBackground }} grid content-center justify-items-center">
            <x-icons.template :name="$name" :iconColor="$iconColor" />
        </div>
    </div>
</div>
