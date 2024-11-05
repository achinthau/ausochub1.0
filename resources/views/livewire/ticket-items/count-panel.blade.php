<div wire:poll.2000ms="refreshComponent" class="flex justify-between">
    <p class="font-semibold bg-green-200 px-2 py-1 rounded-full text-lg mb-4">New : {{ $newCount }}</p>
    <p class="font-semibold bg-red-200 px-2 py-1 rounded-full text-lg mb-4">Open : {{ $openCount }}</p>
    <p class="font-semibold bg-gray-200 px-2 py-1 rounded-full text-lg mb-4">Overdue : {{ $overDueCount }}</p>
    <p class="font-semibold bg-yellow-100 px-2 py-1 rounded-full text-lg mb-4">Closed : {{ $closedCount }}</p>
</div>
