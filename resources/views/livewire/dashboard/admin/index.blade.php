<div wire:poll.3000ms>
    <x-slot name="header">
        <div class="flex">
            <h2 class="flex-1 font-semibold text-xl text-gray-800 leading-tight ">
                {{ __('Dashboard') }}
            </h2>

            {{-- <div class="flex">
                <div>
                    <label><button type="button"
                            onclick="Livewire.emitTo('dashboard.admin.index', 'setOutbound')">Outbound</button></label>
                </div>
            </div> --}}
        </div>
    </x-slot>



    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class=" overflow-hidden {{-- shadow-xl sm:rounded-lg --}}    flex space-x-4 ">
                <div class="w-3/4 space-y-4">
                    <div class=" grid grid-cols-3 gap-4 ">
                        @php
                            $subText = '(In ' . $total_inbound_call_count . ' | Out ' . $total_outbound_call_count . ')';
                            // $_on_going = Cache('current-call-count') ?? 0;

                            $keys = Cache::keys('agent-on-call-*');

                            // Count them
                            $_on_going = count($keys);

                        @endphp

                        <x-dashboard.box title="Calls" :value="$total_inbound_call_count + $total_outbound_call_count"
                            :subText="$subText" iconBackground="bg-slate-100" name='phone' iconColor="text-slate-400" />

                        <x-dashboard.box title="Queued" :value="$total_queue_count" iconBackground="bg-yellow-100"
                            name='queued' iconColor="text-yellow-400" />
                        <x-dashboard.box title="Answered" :value="$total_answered_count" iconBackground="bg-sky-100"
                            name='answered' iconColor="text-sky-400" />
                        <x-dashboard.box title="Abandoned" :value="$abandoned_queue_count" iconBackground="bg-red-100"
                            name='abandoned' iconColor="text-red-400" />
                        <x-dashboard.box title="Ongoing" :value="$_on_going" iconBackground="bg-green-100"
                            name='phone-answer' iconColor="text-green-400" />
                        <x-dashboard.box title="Wating" :value="$queue_wating_count" iconBackground="bg-orange-100"
                            name='waiting' iconColor="text-orange-400" />




                        {{-- <x-dashboard.box title="Total Break" :value="$totalBreakTime"
                            iconBackground="bg-yellow-100" name='break' iconColor="text-yellow-400" />
                        <x-dashboard.box title="Messages" value=0 iconBackground="bg-red-100" name='message'
                            iconColor="text-red-400" /> --}}
                    </div>

                    <div class="grid grid-cols-3  gap-4">
                        @foreach ($queueWiseData as $data)
                        {{-- <div class="bg-blue-400 pt-2 px-0.5 pb-0.5"> --}}
                            <div class="bg-white p-4 space-y-2">
                                <div class="flex items-center justify-between">
                                <h1 class="font-bold">{{ $data->queuename }}</h1>
                                    <svg class="w-6 h-6 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M15.55,9a1.07,1.07,0,0,0,.39.07h4a1,1,0,0,0,0-2H18.35l3.29-3.29a1,1,0,1,0-1.41-1.41L16.94,5.65V4.06a1,1,0,1,0-2,0v4a1.07,1.07,0,0,0,.07.39A1,1,0,0,0,15.55,9Zm3.89,4c-.22,0-.45-.07-.67-.12a9.44,9.44,0,0,1-1.31-.39,2,2,0,0,0-2.48,1l-.22.45a12.18,12.18,0,0,1-2.66-2,12.18,12.18,0,0,1-2-2.66L10.52,9a2,2,0,0,0,1-2.48,10.33,10.33,0,0,1-.39-1.31c-.05-.22-.09-.45-.12-.68a3,3,0,0,0-3-2.49h-3a3,3,0,0,0-3,3.41A19,19,0,0,0,18.53,21.91l.38,0a3,3,0,0,0,2-.76,3,3,0,0,0,1-2.25v-3A3,3,0,0,0,19.44,13Zm.5,6a1,1,0,0,1-.34.75,1.06,1.06,0,0,1-.82.25A17,17,0,0,1,4.07,5.22a1.09,1.09,0,0,1,.25-.82,1,1,0,0,1,.75-.34h3a1,1,0,0,1,1,.79q.06.41.15.81a11.12,11.12,0,0,0,.46,1.55l-1.4.65a1,1,0,0,0-.49,1.33,14.49,14.49,0,0,0,7,7,1,1,0,0,0,.76,0,1,1,0,0,0,.57-.52l.62-1.4a13.69,13.69,0,0,0,1.58.46q.4.09.81.15a1,1,0,0,1,.79,1Z"></path></svg>
                                </div>
                                <hr>
                                <div class="flex">
                                    <div class="flex-1">Queue Count</div>
                                    <div>{{ $data->total_queue_count }}</div>
                                </div>
                                <div class="flex">
                                    <div class="flex-1">Answered</div>
                                    <div>{{ $data->total_answered_count }}</div>
                                </div>
                                <div class="flex">
                                    <div class="flex-1">Abandoned</div>
                                    <div>{{ $data->abandoned_queue_count < 0 ? 0 : $data->abandoned_queue_count }}</div>
                                </div>
                                <div class="flex">
                                    <div class="flex-1">Onging</div>
                                    <div>
                                        {{-- {{ $data->agent_conntected_count - $data->queue_wating_count < 0 ? 0 : $data->
                                            agent_conntected_count - $data->queue_wating_count }} --}}
                                            @php
                                                // $queueOngoing = Cache::get($data->queuename . '-current-call-count') ?? 0;
                                                
                                                $keys = Cache::keys("agent-on-call-*-{$data->queuename}");

                                                // Count them
                                                $queueOngoing = count($keys);

                                                
                                            @endphp
                                            {{$queueOngoing}}
                                    </div>
                                </div>
                                <div class="flex">
                                    <div class="flex-1">Waiting</div>
                                    <div>{{ $data->queue_wating_count }}</div>
                                </div>
                            </div>
                        {{-- </div> --}}
                        @endforeach

                    </div>
                    <div class="grid grid-cols-3  gap-4">
                        @foreach ($dialerQueueWiseData as $data)
                            {{-- <div class="bg-green-500 pt-2 px-0.5 pb-0.5"> --}}
                                <div class="bg-white p-4 space-y-2">
                                <div class="flex items-center justify-between">
                                    <h1 class="font-bold">{{ $data->queuename }}</h1>

                                    <svg class="w-6 h-6 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                        fill="currentColor" stroke="none">
                                        <path
                                            d="M19.44,13c-.22,0-.45-.07-.67-.12a9.44,9.44,0,0,1-1.31-.39,2,2,0,0,0-2.48,1l-.22.45a12.18,12.18,0,0,1-2.66-2,12.18,12.18,0,0,1-2-2.66L10.52,9a2,2,0,0,0,1-2.48,10.33,10.33,0,0,1-.39-1.31c-.05-.22-.09-.45-.12-.68a3,3,0,0,0-3-2.49h-3a3,3,0,0,0-3,3.41A19,19,0,0,0,18.53,21.91l.38,0a3,3,0,0,0,2-.76,3,3,0,0,0,1-2.25v-3A3,3,0,0,0,19.44,13Zm.5,6a1,1,0,0,1-.34.75,1.06,1.06,0,0,1-.82.25A17,17,0,0,1,4.07,5.22a1.09,1.09,0,0,1,.25-.82,1,1,0,0,1,.75-.34h3a1,1,0,0,1,1,.79q.06.41.15.81a11.12,11.12,0,0,0,.46,1.55l-1.4.65a1,1,0,0,0-.49,1.33,14.49,14.49,0,0,0,7,7,1,1,0,0,0,.76,0,1,1,0,0,0,.57-.52l.62-1.4a13.69,13.69,0,0,0,1.58.46q.4.09.81.15a1,1,0,0,1,.79,1ZM21.86,2.68a1,1,0,0,0-.54-.54,1,1,0,0,0-.38-.08h-4a1,1,0,1,0,0,2h1.58l-3.29,3.3a1,1,0,0,0,0,1.41,1,1,0,0,0,1.41,0l3.3-3.29V7.06a1,1,0,0,0,2,0v-4A1,1,0,0,0,21.86,2.68Z">
                                        </path>
                                    </svg>
                                </div>
                                <hr>
                                <div class="flex">
                                    <div class="flex-1">Dialed Count</div>
                                    <div>{{ $data->total_queue_count }}</div>
                                </div>
                                <div class="flex">
                                    <div class="flex-1">Answered</div>
                                    <div>{{ $data->answered_count }}</div>
                                </div>
                                <div class="flex">
                                    <div class="flex-1">Busy</div>
                                    <div>{{ $data->busy_count }}</div>
                                </div>
                                <div class="flex">
                                    <div class="flex-1">Not Answered</div>
                                    <div>{{ $data->no_answer_count }}</div>
                                </div>
                                <div class="flex">
                                    <div class="flex-1">Unreachable</div>
                                    <div>{{ $data->unreachable_count }}</div>
                                </div>
                                <div class="flex">
                                    <div class="flex-1">Cancelled</div>
                                    <div>{{ $data->cancel_count }}</div>
                                </div>
                            </div>
                            {{-- </div> --}}
                        @endforeach

                    </div>
                </div>
                <div class="w-1/4 ">
                    @livewire('dashboard.admin.partials.user-section')
                </div>

            </div>
        </div>
    </div>
</div>

@push('modals')

    @livewire('dashboard.partials.agent-info')
@endpush