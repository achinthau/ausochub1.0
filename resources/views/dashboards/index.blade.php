<x-app-layout>
    @can('is-agent')
        @livewire('dashboard.index')
    @endcan

    @can('is-admin')
    @livewire('dashboard.admin.index')
    @endcan
</x-app-layout>
