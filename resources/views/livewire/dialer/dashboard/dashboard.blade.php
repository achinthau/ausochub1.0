<div>
    @cannot('is-admin')
    @livewire('dialer.dashboard.index')
    @endcannot


    @can('is-admin')
        @livewire('dialer.dashboard.admin.index')
    @endcan
</div>