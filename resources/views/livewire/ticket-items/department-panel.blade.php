<div class="mb-4">
    {{-- <label for="department">Select Department:</label> --}}
    <select wire:model="selectedDepartment" class="form-control rounded-md" id="department">
        <option value="0">All Departments</option>
        @foreach($departments as $dept)
            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
        @endforeach
    </select>
</div>
