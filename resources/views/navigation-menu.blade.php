<nav class="relative z-40">
    <div x-cloak x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 bg-slate-950/40 lg:hidden" @click="sidebarOpen = false"></div>

    <aside
        class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col border-r border-slate-200 bg-white shadow-xl transition-transform duration-300"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
        <div class="flex h-16 items-center justify-between border-b border-slate-200 px-5">
            <a href="{{ route('dashboard.index') }}" class="flex items-center">
                <x-general.logo navigation="1" />
            </a>
            <button type="button" class="rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-900" @click="sidebarOpen = false">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto px-4 py-5">
            <div class="space-y-2">
                @can('can-view-dashboard')
                    <a href="{{ route('dashboard.index') }}" class="flex items-center rounded-xl px-4 py-3 text-sm font-medium transition {{ request()->routeIs('dashboard.index') ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                        {{ __('Dashboard') }}
                    </a>
                @endcan

                {{-- @can('can-view-chat')
                    <a href="{{ route('chat.index') }}" class="flex items-center rounded-xl px-4 py-3 text-sm font-medium transition {{ request()->routeIs('chat.index') ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                        {{ __('Chat') }}
                    </a>
                @endcan --}}

                @can('can-view-leads')
                    <a href="{{ route('leads.index') }}" class="flex items-center rounded-xl px-4 py-3 text-sm font-medium transition {{ request()->routeIs('leads.index') ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                        {{ __('Customers') }}
                    </a>
                @endcan

                @can('can-view-tickets')
                    <a href="{{ route('tickets.index') }}" class="flex items-center rounded-xl px-4 py-3 text-sm font-medium transition {{ request()->routeIs('tickets.index') ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                        {{ __('Tickets') }}
                    </a>
                @endcan

                @can('can-view-service-tickets')
                    <div x-data="{ servTicketsOpen: {{ request()->routeIs('cx-tickets.index') || request()->routeIs('cx-tickets-survey.index') ? 'true' : 'false' }} }" class="rounded-2xl border border-slate-200 bg-slate-50/80 p-2">
                        <button type="button" class="flex w-full items-center justify-between rounded-xl px-2 py-2 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 transition hover:bg-white hover:text-slate-700" @click="servTicketsOpen = !servTicketsOpen">
                            <span>{{ __('Serv-Tickets') }}</span>
                            <svg class="h-4 w-4 transition-transform duration-200" :class="servTicketsOpen ? 'rotate-180' : ''" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="servTicketsOpen" x-collapse class="space-y-1 pt-2">
                            <a href="{{ route('cx-tickets.index') }}" class="flex items-center rounded-xl px-4 py-3 text-sm font-medium transition {{ request()->routeIs('cx-tickets.index') ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-700 hover:bg-white hover:text-slate-900' }}">
                                {{ __('Info') }}
                            </a>
                            <a href="{{ route('cx-tickets-survey.index') }}" class="flex items-center rounded-xl px-4 py-3 text-sm font-medium transition {{ request()->routeIs('cx-tickets-survey.index') ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-700 hover:bg-white hover:text-slate-900' }}">
                                {{ __('Survey') }}
                            </a>
                        </div>
                    </div>
                @endcan

                @canany(['can-view-reports', 'can-view-cdr-reports', 'can-view-dialer-reports', 'can-view-leads'])
                    <a href="{{ route('reports.index') }}" class="flex items-center rounded-xl px-4 py-3 text-sm font-medium transition {{ request()->routeIs('reports.index') ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                        {{ __('Reports') }}
                    </a>
                @endcanany

                @canany(['is-admin', 'client-admin'])
                    <a href="{{ route('settings.index') }}" class="flex items-center rounded-xl px-4 py-3 text-sm font-medium transition {{ request()->routeIs('settings.index') ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                        {{ __('Settings') }}
                    </a>

                    <a href="{{ route('dialer.admin.dashboard') }}" class="flex items-center rounded-xl px-4 py-3 text-sm font-medium transition {{ request()->routeIs('dialer.index') || request()->routeIs('dialer.admin.dashboard') ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                        {{ __('Dialer') }}
                    </a>
                @endcanany
            </div>
        </div>
    </aside>

    <div class="fixed top-0 right-0 left-0 border-b border-slate-200 bg-white/95 backdrop-blur" :class="sidebarOpen ? 'lg:left-72' : 'lg:left-0'">
        <div class="flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-3">
                <button type="button" class="rounded-xl border border-slate-200 p-2 text-slate-600 transition hover:bg-slate-100 hover:text-slate-900" @click="sidebarOpen = !sidebarOpen">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                {{-- <div>
                    <div class="text-sm font-semibold text-slate-900">{{ config('app.name', 'Laravel') }}</div>
                    <div class="text-xs text-slate-500">{{ Auth::user()->name }}</div>
                </div> --}}
            </div>

            <div class="flex items-center gap-2 sm:gap-3">
                @can('can-view-leads')
                    @livewire('chat.chat-nav-button')
                    @livewire('whatsapp.whatsapp-nav-button')
                    @livewire('messenger.messenger-nav-button')
                    @livewire('phone.phone-nav-button')
                @endcan

                @canany(['is-admin', 'is-agent', 'nps-user'])
                    @livewire('dashboard.reminder')
                @endcanany

                @php
                use Illuminate\Support\Facades\Redis;

                                        if (env('DASHBOARD_DATA_TYPE') == 'cache') {
                                            if (env('CACHE_DRIVER') == 'file') {
                                                $inCall = isset($jsonData['status']) && $jsonData['status'] == 1;
                                                
                                            } else {
                                                $keys = Redis::connection()->client()->select(1);
                                                $keys = Redis::keys("agent_on_call-".Auth::user()->id."-*");
                                                $inCall = count($keys);
                                            }
                                        } else {
                                             $keys = Redis::connection()->client()->select(1);
                                            $keys = Redis::keys("agent_on_call-".Auth::user()->id."-*");
                                                $inCall = count($keys);
                                        }
                                    @endphp

                @can('is-agent')
                    {{-- @if (Route::is('dashboard.index')) --}}
                        <div class="hidden items-center gap-3 lg:flex">
                            @livewire('dashboard.hand-raise')
                            @livewire('dashboard.select-bound')
                        </div>
                    {{-- @endif --}}
                @endcan

                @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                    <div class="hidden md:block">
                        <x-jet-dropdown align="right" width="60">
                            <x-slot name="trigger">
                                <span class="inline-flex rounded-md">
                                    <button type="button" class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none">
                                        {{ Auth::user()->currentTeam->name }}
                                        <svg class="ml-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </span>
                            </x-slot>

                            <x-slot name="content">
                                <div class="w-60">
                                    <div class="block px-4 py-2 text-xs text-gray-400">
                                        {{ __('Manage Team') }}
                                    </div>

                                    <x-jet-dropdown-link href="{{ route('teams.show', Auth::user()->currentTeam->id) }}">
                                        {{ __('Team Settings') }}
                                    </x-jet-dropdown-link>

                                    @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                                        <x-jet-dropdown-link href="{{ route('teams.create') }}">
                                            {{ __('Create New Team') }}
                                        </x-jet-dropdown-link>
                                    @endcan

                                    <div class="border-t border-gray-100"></div>

                                    <div class="block px-4 py-2 text-xs text-gray-400">
                                        {{ __('Switch Teams') }}
                                    </div>

                                    @foreach (Auth::user()->allTeams() as $team)
                                        <x-jet-switchable-team :team="$team" />
                                    @endforeach
                                </div>
                            </x-slot>
                        </x-jet-dropdown>
                    </div>
                @endif

                <x-jet-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                            <button class="flex rounded-full border-2 border-transparent text-sm transition focus:border-slate-300 focus:outline-none">
                                <img class="h-10 w-10 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                            </button>
                        @else
                            <span class="inline-flex rounded-md">
                                <button type="button" class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none">
                                    {{ Auth::user()->name }}
                                    <svg class="ml-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </span>
                        @endif
                    </x-slot>

                    <x-slot name="content">
                        <div class="block px-4 py-2 text-xs text-gray-400">
                            {{ __('Manage Account') }}
                        </div>

                        <x-jet-dropdown-link href="{{ route('profile.show') }}">
                            {{ __('Profile') }}
                        </x-jet-dropdown-link>

                        @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                            <x-jet-dropdown-link href="{{ route('api-tokens.index') }}">
                                {{ __('API Tokens') }}
                            </x-jet-dropdown-link>
                        @endif

                        <div class="border-t border-gray-100"></div>

                        <form method="POST" action="{{ route('logout') }}" x-data="logoutHandler()">
                            @csrf

                            <x-jet-dropdown-link href="{{ route('logout') }}" @click.prevent="logout">
                                {{ __('Log Out') }}
                            </x-jet-dropdown-link>
                        </form>
                    </x-slot>
                </x-jet-dropdown>
            </div>
        </div>
    </div>

    <script>
        function logoutHandler() {
            return {
                logout() {
                    try {
                        const phoneType = "{{ strtolower(config('auso.phone_type')) }}";
                        const exten = "{{ auth()->user()->extensionData }}";

                        let url = null;

                        if (phoneType.includes('microsip')) {
                            url = "http://127.0.0.1:5001/remove/microsip";
                        } else if (phoneType.includes('zoiper')) {
                            url = "http://127.0.0.1:5001/remove/zoiper3";
                        }

                        if (url) {
                            const data = {
                                exten: exten.extension,
                                server: "123.231.74.22",
                                password: "@u5051p",
                                aa: "1",
                                autoanswerdelay: "3",
                                protocol: exten.exten_type
                            };

                            fetch(url, {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify(data),
                                credentials: 'omit',
                                keepalive: true
                            });
                        }
                    } catch (e) {
                        console.warn('Softphone disconnect failed', e);
                    }

                    this.$root.submit();
                }
            }
        }
    </script>
</nav>