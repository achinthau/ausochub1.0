<x-modal.card title="Contact Data" blur wire:model="showMoreDetailsModal" max-width="4xl" align="center">
    @if (empty($moreDetails))
        <p class="text-sm text-gray-500">{{ __('No data available for this record.') }}</p>
    @else
        <div class="overflow-x-auto" style="max-height: 70vh; overflow-y: auto;">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <tbody class="divide-y divide-gray-100">
                    @foreach ($moreDetails as $key => $value)
                        <tr>
                            <th class="w-1/3 py-2 pr-4 text-left align-top font-medium text-gray-600">
                                {{ \Illuminate\Support\Str::headline(str_replace('_', ' ', (string) $key)) }}
                            </th>
                            <td class="py-2 align-top text-gray-800 break-words">
                                @if (is_array($value) || is_object($value))
                                    <pre class="whitespace-pre-wrap break-words text-xs text-gray-700">{{ json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
                                @elseif (is_bool($value))
                                    {{ $value ? 'Yes' : 'No' }}
                                @elseif (is_null($value) || $value === '')
                                    N/A
                                @else
                                    {{ $value }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <x-slot name="footer">
        <div class="flex justify-end">
            <x-button flat label="Close" x-on:click="close" />
        </div>
    </x-slot>
</x-modal.card>
