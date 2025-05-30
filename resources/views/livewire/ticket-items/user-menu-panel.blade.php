<div class="mb-4">
    {{-- <label for="department">Select Department:</label> --}}
    <select wire:model="selectedItem" class="form-control rounded-md" id="department">
        <option value="0">All Tickets</option>
        {{-- @foreach($departments as $dept) --}}
            <option value="1">My Tickets</option>
            <option value="2">New Tickets</option>
        {{-- @endforeach --}}
    </select>
</div>