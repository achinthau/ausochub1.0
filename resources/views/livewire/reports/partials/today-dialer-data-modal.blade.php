<x-modal.card title="More Details" blur wire:model="showDataModal">
    @if (! empty($dataRows))
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead>
                    <tr>
                        <th class="px-4 py-2 text-left font-semibold text-gray-700 bg-gray-50 w-1/3">
                            Field
                        </th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-700 bg-gray-50">
                            Value
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($dataRows as $row)
                        <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                            <td class="px-4 py-2 align-top font-medium text-gray-700">
                                {{ $row['label'] }}
                            </td>
                            <td class="px-4 py-2 align-top text-gray-900 break-words">
                                @if (is_bool($row['value']))
                                    {{ $row['value'] ? 'Yes' : 'No' }}
                                @elseif ($row['value'] === null || $row['value'] === '')
                                    <span class="text-gray-400">N/A</span>
                                @else
                                    {{ $row['value'] }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @elseif ($rawData !== null && $rawData !== '')
        <pre class="p-3 bg-gray-50 rounded-lg text-sm whitespace-pre-wrap break-words">{{ $rawData }}</pre>
    @else
        <p class="text-sm text-gray-500">No data available for this record.</p>
    @endif

    <x-slot name="footer">
        <div class="flex justify-end">
            <x-button flat label="Close" x-on:click="close" />
        </div>
    </x-slot>
</x-modal.card>
