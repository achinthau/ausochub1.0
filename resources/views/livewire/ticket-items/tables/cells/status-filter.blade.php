<div>
    <select wire:model.debounce.500ms="columnSearch.{{ $field }}" class="block w-full border-gray-300 rounded-md shadow-sm transition duration-150 ease-in-out sm:text-sm sm:leading-5 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
        <option value="">Any</option>
        <option value="New">New</option>
        <option value="Open">Open</option>
        <option value="Overdue">Overdue</option>
        <option value="Closed">Closed</option>
        <option value="Canceled">Canceled</option>
    </select>
</div>
