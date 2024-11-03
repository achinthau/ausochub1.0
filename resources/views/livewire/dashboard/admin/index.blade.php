<div wire:poll.3000ms>
    <x-slot name="header">
        <div class="flex">
            <h2 class="flex-1 font-semibold text-xl text-gray-800 leading-tight ">
                {{ __('Dashboard') }}
            </h2>
        </div>
    </x-slot>



    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class=" overflow-hidden {{-- shadow-xl sm:rounded-lg --}}    flex space-x-4 ">
                <div class="w-3/4 space-y-4">
                    <div class=" grid grid-cols-3 gap-4 ">
                        @php
                            $subText = '(In ' . $total_inbound_call_count . ' | Out ' . $total_outbound_call_count . ')';
                            $_on_going=Cache('current-call-count')??0;
                                       
                        @endphp

                        <x-dashboard.box title="Calls" :value="$total_inbound_call_count + $total_outbound_call_count" :subText="$subText" iconBackground="bg-slate-100"
                            name='phone' iconColor="text-slate-400" />

                        <x-dashboard.box title="Queued" :value="$total_queue_count" iconBackground="bg-yellow-100" name='queued'
                            iconColor="text-yellow-400" />
                        <x-dashboard.box title="Answered" :value="$total_answered_count" iconBackground="bg-sky-100" name='answered'
                            iconColor="text-sky-400" />
                        <x-dashboard.box title="Abandoned" :value="$abandoned_queue_count" iconBackground="bg-red-100"
                            name='abandoned' iconColor="text-red-400" />
                        <x-dashboard.box title="Onging" :value="$_on_going" iconBackground="bg-green-100"
                            name='phone-answer' iconColor="text-green-400" />
                        <x-dashboard.box title="Wating" :value="$queue_wating_count" iconBackground="bg-orange-100" name='waiting'
                            iconColor="text-orange-400" />




                        {{-- <x-dashboard.box title="Total Break" :value="$totalBreakTime" iconBackground="bg-yellow-100" name='break'
                            iconColor="text-yellow-400" />
                        <x-dashboard.box title="Messages" value=0 iconBackground="bg-red-100" name='message'
                            iconColor="text-red-400" /> --}}
                    </div>

                    <div class="grid grid-cols-3  gap-4">
                        @foreach ($queueWiseData as $data)
                            <div class="bg-white p-4 space-y-2">
                                <h1 class="font-bold">{{ $data->queuename }}</h1>
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
                                        {{-- {{ $data->agent_conntected_count - $data->queue_wating_count < 0 ? 0 : $data->agent_conntected_count - $data->queue_wating_count }} --}}
                                        @php
                                            $queueOngoing = Cache::get($data->queuename . '-current-call-count') ?? 0;
                                        @endphp
                                        {{$queueOngoing}}
                                    </div>
                                </div>
                                <div class="flex">
                                    <div class="flex-1">Waiting</div>
                                    <div>{{ $data->queue_wating_count }}</div>
                                </div>
                            </div>
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
