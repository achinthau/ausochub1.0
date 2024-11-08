<div>
    <x-slot name="header">
        <div class="flex">
            <h2 class="flex-1 font-semibold text-xl text-gray-800 leading-tight ">
                {{ __('Dashboard') }}
            </h2>
            <div>
                {{-- @dump(Auth::user()->has_queue) --}}
                @if (Auth::user()->has_queue)



                    @if (Auth::user()->on_break)
                        <div class="flex space-x-4">
                            <div class="my-auto" {{-- x-data="appFooterComponent('{{ Auth::user()->break_started_at->format('Y/m/d H:i:s') }}')" --}}{{--  x-init="init()" --}}>
                                <div>
                                    <span class="bg-red-600 text-white rounded-md p-2"{{--  x-text="getTime()" --}}>On
                                        Break</span>
                                </div>
                            </div>
                            <x-button icon="clipboard-list" secondary label="End Break"
                                onclick="Livewire.emitTo('dashboard.partials.agent-break', 'endBreak')" />
                        </div>
                    @else
                        <x-button icon="clipboard-list" secondary label="Start Break"
                            onclick="Livewire.emitTo('dashboard.partials.agent-break', 'showCreateUserBreakModal')" />
                    @endif
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12" wire:poll.3000ms>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class=" overflow-hidden  {{-- shadow-xl sm:rounded-lg --}} space-y-4  @if (Auth::user()->on_break) blur-lg @endif   ">
                <div class="flex space-x-4">
                    <div class="w-3/4 space-y-4">
                        <div class="grid grid-cols-3 space-x-4">
                            <x-dashboard.box title="Answered" :value="$user->agent->todayQueues->count()" iconBackground="bg-blue-100"
                                name='phone' iconColor="text-blue-400" />
                           {{--  <x-dashboard.box title="Missed" :value="$user->agent->todayQueues->count()" iconBackground="bg-red-100"
                                name='phone-missed' iconColor="text-red-400" /> --}}
                            <x-dashboard.box title="Missed" :value="$user->agent->miscall_count" iconBackground="bg-red-100"
                                name='phone-missed' iconColor="text-red-400" />
                            <x-dashboard.box title="Total Break" :value="$totalBreakTime" iconBackground="bg-yellow-100"
                                name='break' iconColor="text-yellow-400" />

                        </div>

                        <div class="grid grid-cols-3 space-x-4">
                            <x-dashboard.box title="Messages" value=0 iconBackground="bg-red-100" name='message'
                                iconColor="text-red-400" />
                            <div class="bg-white p-6 rounded-md shadow-md space-y-2">
                                <h1 class="text-xs text-gray-400 font-semibold">Skills</h1>
                                <hr>
                                @foreach ($skills as $key => $skill)
                                    <div class="flex">
                                        <label
                                            class="flex-1  text-sm font-medium text-secondary-700 dark:text-gray-400 mr-2">
                                            {{ $skill }}
                                        </label>
                                        @if (config('auso.allow_skill_change'))
                                            <x-toggle lg wire:model="selectedSkills.{{ $skill }}"
                                                value="{{ $skill }}" />
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            <div class="bg-white p-4 space-y-2">
                                <h1 class="text-xs text-gray-400 font-semibold">Ongoing Queue Count</h1>
                                <hr>

                                @foreach ($queueWiseData as $data)
                                    <div class="flex ">
                                        <div
                                            class="flex-1  text-sm font-medium text-secondary-700 dark:text-gray-400 mr-2">
                                            {{ $data->queuename }}</div>
                                        <div>{{ $data->queue_wating_count }}</div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- <div class="bg-white p-6 rounded-md shadow-md space-y-2">
                        <h1 class="text-xs text-gray-400 font-semibold">Break</h1>
                        <hr>
                        @if (Auth::user()->on_break)
                            <x-countdown class="text-green-600 font-semibold" :expires="Auth::user()->break_started_at">
                                <span x-text="timer.hours">{{ $component->hours() }}</span> hours
                                <span x-text="timer.minutes">{{ $component->minutes() }}</span> minutes
                                <span x-text="timer.seconds">{{ $component->seconds() }}</span> seconds
                            </x-countdown>
                        @endif
                    </div> --}}
                        </div>

                        <div class="grid grid-cols-3  gap-4">




                        </div>
                    </div>
                    <div class="w-1/4 ">
                        @livewire('dashboard.admin.partials.user-section')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('modals')
    @livewire('dashboard.partials.agent-break')
@endpush
@push('scripts')
    <script>
        /* function appFooterComponent(breakStartedAt) {
                            console.log(breakStartedAt);
                            return {
                                time: new Date(breakStartedAt),
                                init() {
                                    setInterval(() => {
                                        // this.time = new Date();
                                        var now = new Date();
                                        var then = this.time;

                                        var duration = moment.utc(moment(now, "DD/MM/YYYY HH:mm:ss").diff(moment(then,
                                                "YYYY/MM/DD HH:mm:ss")))
                                            .format("HH:mm:ss")

                                    }, 1000);
                                },
                                getTime() {
                                    var now = new Date();
                                    var then = this.time;

                                    var duration = moment.utc(moment(now, "DD/MM/YYYY HH:mm:ss").diff(moment(then,
                                            "YYYY/MM/DD HH:mm:ss")))
                                        .format("HH:mm:ss");
                                    console.log(duration);
                                    return duration;
                                },
                            }
                        } */
    </script>
@endpush
