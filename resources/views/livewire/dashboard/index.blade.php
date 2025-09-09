<div>
    <x-slot name="header">
        <div class="flex">
            {{-- <div class="flex justify-between">
                <div class="flex-1 ">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight ">
                        {{ __('Dashboard') }}
                    </h2>
                </div>

                {{-- <div class="absolute right-44 flex justify-between">
                    @livewire('dashboard.select-bound') not used

                </div> --}}
                {{--
            </div> --}}
            {{-- <div> --}}
                <h2 class="flex-1 font-semibold text-xl text-gray-800 leading-tight ">
                    {{ __('Dashboard') }}
                </h2>

                {{-- <div class="flex {{ !$isVisible ? 'pointer-events-none opacity-50' : '' }}"> --}}
                    <div class="flex">
                        <div class="flex">
                            {{-- @dump(Auth::user()->has_queue) --}}
                            {{-- @if (Auth::user()->has_queue) --}}

                            <div class="flex items-center space-x-2 px-4">
                                <label for="boundType" class="text-lg  font-medium text-gray-700">
                                    Mode:
                                </label>
                                <select id="boundType"
                                    class="block w-40 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 shadow-sm focus:border-indigo-800 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    onchange="Livewire.emitTo('dashboard.index', this.value === 'inbound' ? 'setInbound' : 'setOutbound')">
                                    <option value="inbound" {{ $boundType === 'inbound' ? 'selected' : '' }}>Inbound
                                    </option>
                                    <option value="dialer" {{ $boundType === 'dialer' ? 'selected' : '' }}>Outbound
                                    </option>
                                </select>
                            </div>




                            @if (Auth::user()->on_break && $user->agent_break_type != 'ACW')
                                <div class="flex space-x-4">
                                    <div class="my-auto" {{--
                                        x-data="appFooterComponent('{{ Auth::user()->break_started_at->format('Y/m/d H:i:s') }}')"
                                        --}}{{-- x-init="init()" --}}>
                                        <div>
                                            <span class="bg-red-600 text-white rounded-md p-2" {{-- x-text="getTime()"
                                                --}}>On
                                                Break</span>
                                        </div>
                                    </div>
                                    <x-button icon="clipboard-list" secondary label="End Break"
                                        onclick="Livewire.emitTo('dashboard.partials.agent-break', 'endBreak')" />

                                </div>
                            @else
                                <x-button id="start-break-button" icon="clipboard-list" secondary label="Start Break"
                                    onclick="Livewire.emitTo('dashboard.partials.agent-break', 'showCreateUserBreakModal')"
                                    :disabled="!$isVisible" />
                            @endif
                            {{-- @endif --}}

                        </div>
                        <div class=" pl-8">
                            {{-- @livewire('dashboard.select-bound') --}}
                        </div>

                        <script>
                            window.addEventListener('updateButton', event => {
                                const button = document.querySelector('#start-break-button');
                                if (event.detail.isVisible) {
                                    button.removeAttribute('disabled');
                                } else {
                                    button.setAttribute('disabled', true);
                                }
                            });
                        </script>


                    </div>
                </div>
    </x-slot>

    <div class="py-12" wire:poll.3000ms>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class=" overflow-hidden  {{-- shadow-xl sm:rounded-lg --}} space-y-4  @if (Auth::user()->on_break && $user->agent_break_type != 'ACW') blur-lg @endif   ">
                <div class="flex space-x-4">
                    <div class="w-3/4 space-y-4">
                        <div class="grid grid-cols-3 space-x-4">
                            <x-dashboard.box title="Answered" :value="$user->agent->todayQueues->count()"
                                iconBackground="bg-blue-100" name='phone' iconColor="text-blue-400" />
                            {{-- <x-dashboard.box title="Missed" :value="$user->agent->todayQueues->count()"
                                iconBackground="bg-red-100" name='phone-missed' iconColor="text-red-400" /> --}}
                            <x-dashboard.box title="Missed" :value="$user->agent->miscall_count"
                                iconBackground="bg-red-100" name='phone-missed' iconColor="text-red-400" />
                            <x-dashboard.box title="Total Break" :value="$totalBreakTime" iconBackground="bg-yellow-100"
                                name='break' iconColor="text-yellow-400" />

                        </div>

                        <div class="grid grid-cols-3 space-x-4">
                            <a href="{{ route('chat.index') }}">
                                <x-dashboard.box title="Messages" :value="$messagesCount" iconBackground="bg-red-100"
                                    name='message' iconColor="text-red-400" /> </a>
                            <div class="bg-white p-6 rounded-md shadow-md space-y-2">
                                <h1 class="text-xs text-gray-400 font-semibold">Skills</h1>
                                <hr>
                                {{-- @foreach ($skills as $key => $skill)
                                <div class="flex">
                                    <label
                                        class="flex-1  text-sm font-medium text-secondary-700 dark:text-gray-400 mr-2">
                                        {{ $skill }}
                                    </label>
                                    @if (config('auso.allow_skill_change'))
                                    <x-toggle lg wire:model="selectedSkills.{{ $skill }}" value="{{ $skill }}" />
                                    @endif
                                </div>
                                @endforeach --}}
                                @if(!empty($skills) && is_iterable($skills))
                                    @foreach ($skills as $key => $skill)
                                        <div class="flex">
                                            <label
                                                class="flex-1 text-sm font-medium text-secondary-700 dark:text-gray-400 mr-2">
                                                {{ $skill }}
                                            </label>
                                            @if (config('auso.allow_skill_change'))
                                                <x-toggle lg wire:model="selectedSkills.{{ $skill }}" value="{{ $skill }}" />
                                            @endif
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-xs text-gray-400 italic">No skills assigned.</p>
                                @endif

                            </div>

                            @if($boundType != 'dialer')

                                <div class="bg-white p-4 space-y-2">
                                    <h1 class="text-xs text-gray-400 font-semibold">Ongoing Queue Count</h1>
                                    <hr>

                                    @foreach ($queueWiseData as $data)
                                        <div class="flex ">
                                            <div class="flex-1  text-sm font-medium text-secondary-700 dark:text-gray-400 mr-2">
                                                {{ $data->queuename }}
                                            </div>
                                            <div>{{ $data->queue_wating_count }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            {{-- <div class="bg-white p-6 rounded-md shadow-md space-y-2">
                                <h1 class="text-xs text-gray-400 font-semibold">Break</h1>
                                <hr>
                                @if (Auth::user()->on_break)
                                <x-countdown class="text-green-600 font-semibold"
                                    :expires="Auth::user()->break_started_at">
                                    <span x-text="timer.hours">{{ $component->hours() }}</span> hours
                                    <span x-text="timer.minutes">{{ $component->minutes() }}</span> minutes
                                    <span x-text="timer.seconds">{{ $component->seconds() }}</span> seconds
                                </x-countdown>
                                @endif
                            </div> --}}
                        </div>

                        @if($boundType == 'dialer')
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                                @foreach ($campaigns as $campaign)
                                    <div class="bg-white rounded-lg shadow p-6">
                                        <h3 class="text-base font-semibold text-gray-700">{{ $campaign->name }}</h3>
                                        <div class="mt-2 space-y-1">
                                            <div>
                                                <span class="text-sm font-bold text-gray-700">Type:</span>
                                                <span class="text-sm">{{ $campaign->types?->name ?? 'N/A' }}</span>
                                            </div>
                                            <div>
                                                <span class="text-sm font-bold text-gray-700">Status:</span>
                                                @php
                                                    $statusMap = [
                                                        0 => 'Inactive',
                                                        1 => 'Active',
                                                        2 => 'On Hold',
                                                        3 => 'Completed',
                                                        4 => 'Canceled',
                                                    ];
                                                @endphp

                                                <span class="text-sm">
                                                    {{ $statusMap[$campaign->status] ?? 'Unknown' }}
                                                </span>

                                            </div>
                                            <div>
                                                <span class="text-sm font-bold text-gray-700">Total Numbers:</span>
                                                <span class="text-sm">{{ $campaign->contact_count ?? 0 }}</span>
                                            </div>
                                            <div>
                                                <span class="text-sm font-bold text-gray-700">Dialed Count:</span>
                                                <span class="text-sm">{{ $campaign->dialed_count ?? 0 }}</span>
                                            </div>
                                            <div>
                                                <span class="text-sm font-bold text-gray-700">Answered Count:</span>
                                                <span class="text-sm">{{ $campaign->answered_count ?? 0 }}</span>
                                            </div>
                                            <div>
                                                <span class="text-sm font-bold text-gray-700">Agents Count:</span>
                                                <span class="text-sm">{{ $campaign->agents_count ?? 0 }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

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


    {{-- catch the socket.io event --}}

    <script>
        // var SOCKET_URL = "{{ config('app.socket_server_url', 'http://localhost') }}";
        // var SOCKET_PORT = "{{ config('app.socket_server_port', '3000') }}";
        // var FULL_SOCKET_URL = SOCKET_URL + ":" + SOCKET_PORT;


        var SOCKET_URL = "<?php echo env('SOCKET_SERVER_URL', 'http://localhost'); ?>";
        var SOCKET_PORT = "<?php echo env('SOCKET_SERVER_PORT', '3000');?>";

        var FULL_SOCKET_URL = SOCKET_URL + ":" + SOCKET_PORT;

        const socket = io(FULL_SOCKET_URL, {
            transports: ['websocket'],
            secure: true
        });

        socket.on('connect', () => {
            console.log('Connected to Socket.IO server');
        });


        var openedTab = null; // Store the reference to the opened tab

        socket.on('call.answered', (data) => {

            var url = "/leads/"; // Set the target URL

            console.log('Lead Answered-----------:', data);



            if (openedTab && !openedTab.closed) {
                // If the tab is already open, update its location
                openedTab.location.href = url + data;
                openedTab.focus
            } else {
                // If no tab is open, create a new one
                openedTab = window.open(url + data, "_blank");
            }
        });

    </script>



</div>
@push('modals')
    @livewire('dashboard.partials.agent-break')


    @livewire('dashboard.partials.agent-info')
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