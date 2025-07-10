<div>
    <x-jet-dialog-modal wire:model="userInfoModal" class="max-w-xs">
        <x-slot name="title">
            <strong>{{ ucwords($user->name ?? 'Not provided') }}</strong>
            <hr>
        </x-slot>



        <x-slot name="content">

            <b>Skills </b> <br>
            <label class="ml-6">{{ $skills ?? 'No skills available' }} </label>

            <br><br>

            <hr>

            <b>Call Counts </b> <br>

            {{-- queuename{{ $data->queuename }} <br> --}}
            {{-- total_queue_count{{ $data->total_queue_count }} <br> --}}
            @if (!empty($queueWiseData) && count($queueWiseData) > 0)

                <div class="relative overflow-x-auto">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-100  dark:bg-gray-700 dark:text-gray-400">
                                <tr class="">
                                    <th scope="col" class="px-6 py-3">
                                        Skills
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right ">
                                        Offered
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right ">
                                        Answered
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right ">
                                        Missed
                                    </th>
                                </tr>
                            </thead>
                        <tbody class=" ">
                            @foreach ($mergedData as $queue)
                                <tr class="bg-white  ">
                                    <th scope="row"
                                        class="px-6 py-0.5 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ $queue['queuename'] }}
                                    </th>
                                    <td class="px-6 py-0.5 text-right">
                                        {{ $queue['call_count'] + $queue['missed_count'] }}
                                    </td>
                                    <td class="px-6 py-0.5 text-right">
                                        {{ $queue['call_count'] }}
                                    </td>
                                    <td class="px-6 py-0.5 text-right">
                                        {{ $queue['missed_count'] }}
                                    </td>

                                </tr>
                            @endforeach
                            

                        </tbody>
                    </table>
                </div>
            @else
                <p>No queue data available.</p>
            @endif

            <br>

            <hr>

            

            <b>Total Break Time : </b> <br>
            <label class="ml-6"> {{ $totalBreakTime }} </label>
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('userInfoModal')" wire:loading.attr="disabled">
                Close
            </x-jet-secondary-button>
        </x-slot>
    </x-jet-dialog-modal>

</div>
