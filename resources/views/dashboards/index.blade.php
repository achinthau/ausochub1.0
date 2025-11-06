<x-app-layout>
    @can('is-agent')
        @livewire('dashboard.index')
    @endcan

    @canany(['is-admin','client-admin'])
    @livewire('dashboard.admin.index')
    @endcanany
</x-app-layout>
