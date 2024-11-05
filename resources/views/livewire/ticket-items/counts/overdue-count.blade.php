<div wire:poll.5000ms="refreshComponent" class="flex flex-col items-center bg-white justify-between p-4 rounded-lg shadow-lg border-0 border-white transition-transform duration-300 hover:scale-105">
    <h1 class="text-md font-bold text-gray-800 mb-2">Overdue</h1>
    <p class="font-semibold text-black px-3 py-1 rounded-full text-4xl ">
         {{ $overdueCount }}
    </p>
</div>
