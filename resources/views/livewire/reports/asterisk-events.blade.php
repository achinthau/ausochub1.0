<div>
    <x-slot name="header">
        <div class="flex">
            <h2 class="flex-1 font-semibold text-xl text-gray-800 leading-tight ">
                {{ __('Live Caller Dashboard')  }}
            </h2>

        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <table>
                <thead>
                    <tr>
                        <th>Channel</th>
                        <th>Caller ID Name</th>
                        <th>Channel State Description</th>
                        <th>Duration</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($events as $channel => $event)
                        <tr>
                            <td>{{ $channel }}</td>
                            <td>{{ $event['CallerIDName'] ?? 'N/A' }}</td>
                            <td>{{ $event['ChannelStateDesc'] ?? 'N/A' }}</td>
                            <td>{{ $event['Duration'] ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
