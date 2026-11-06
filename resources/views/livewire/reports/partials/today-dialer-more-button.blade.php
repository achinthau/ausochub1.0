<button type="button"
    wire:key="more-{{ $rowKey }}"
    wire:click="showMoreDetails('{{ $rowKey }}')"
    wire:loading.attr="disabled"
    class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50">
    More
</button>
